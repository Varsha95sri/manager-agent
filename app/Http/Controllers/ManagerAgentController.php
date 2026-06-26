<?php
// app/Http/Controllers/ManagerAgentController.php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Models\Task;
use App\Models\GitCommit;
use App\Models\AttendanceLog;
use App\Models\MeetingNote;
use App\Models\PerformanceReport;
use App\Models\Repository;
use App\Models\Project;
use App\Services\ManagerAgentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class ManagerAgentController extends Controller
{
    protected ManagerAgentService $agentService;

    public function __construct(ManagerAgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    /**
     * Display the manager dashboard.
     */
    /**
     * Display the manager dashboard.
     */
    public function index(Request $request)
    {
        try {
            $rangeType = $request->input('range_type', 'all_time');
            $baseDate = Carbon::parse($request->input('date', Carbon::today()->toDateString()));
            $today = Carbon::today();
            
            $startDate = $baseDate->copy()->toDateString();
            $endDate = $baseDate->copy()->toDateString();
            
            if ($rangeType === 'date_wise') {
                $startDate = $baseDate->toDateString();
                $endDate = $startDate;
            } elseif ($rangeType === 'week_wise') {
                $startDate = $baseDate->copy()->startOfWeek()->toDateString();
                $endDate = $baseDate->copy()->endOfWeek()->toDateString();
            } elseif ($rangeType === 'month_wise') {
                $startDate = $baseDate->copy()->startOfMonth()->toDateString();
                $endDate = $baseDate->copy()->endOfMonth()->toDateString();
            } elseif ($rangeType === 'year_wise') {
                $startDate = $baseDate->copy()->startOfYear()->toDateString();
                $endDate = $baseDate->copy()->endOfYear()->toDateString();
            } elseif ($rangeType === 'all_time') {
                $startDate = '2000-01-01'; // A date far enough in the past
                $endDate = $today->copy()->toDateString();
            } elseif ($rangeType === 'custom_range') {
                $startDate = $request->input('start_date', $today->toDateString());
                $endDate = $request->input('end_date', $today->toDateString());
            }
            
            $targetDate = $baseDate->toDateString(); // Use baseDate for calendar view so it defaults to today/selected date
            $startDateTime = $startDate . ' 00:00:00';
            $endDateTime = $endDate . ' 23:59:59';
            
            $totalMembers = TeamMember::count();
            
            // Count stats for the date range
            $totalTasks = Task::whereBetween('due_date', [$startDate, $endDate])->count();
            $totalCommits = GitCommit::whereBetween('committed_at', [$startDateTime, $endDateTime])->count();
            
            $latestReport = PerformanceReport::whereBetween('report_date', [$startDate, $endDate])->latest()->first();
            if (!$latestReport) {
                $latestReport = PerformanceReport::whereDate('report_date', '<=', $endDate)
                    ->orderBy('report_date', 'desc')
                    ->first();
            }
            
            // Get the latest 7 reports, ensuring we only take the most recent one per day
            $reports = PerformanceReport::orderBy('created_at', 'desc')
                ->get()
                ->unique(function ($item) {
                    return \Carbon\Carbon::parse($item->report_date)->format('Y-m-d');
                })
                ->take(7)
                ->values();

            // Calculate task and meeting stats
            $completedTasksCount = Task::whereBetween('due_date', [$startDate, $endDate])->where('status', 'completed')->count();
            $pendingTasksCount = Task::whereBetween('due_date', [$startDate, $endDate])->where('status', 'pending')->count();
            $totalMeetingsCount = MeetingNote::whereBetween('meeting_date', [$startDate, $endDate])->count();

            // Calculate attendance stats
            $presentCount = AttendanceLog::whereBetween('date', [$startDate, $endDate])->where('status', 'present')->count();
            $lateCount = AttendanceLog::whereBetween('date', [$startDate, $endDate])->where('status', 'late')->count();
            $leaveCount = AttendanceLog::whereBetween('date', [$startDate, $endDate])->where('status', 'leave')->count();
            
            // Attendance percentage across the range
            $presentPct = 0;
            $latePct = 0;
            $absentPct = 0;
            
            $attendanceStartDate = $startDate;
            if ($rangeType === 'all_time') {
                $firstLog = AttendanceLog::orderBy('date', 'asc')->first();
                if ($firstLog) {
                    $attendanceStartDate = $firstLog->date;
                } else {
                    $attendanceStartDate = $endDate;
                }
            }
            
            $daysInRange = Carbon::parse($attendanceStartDate)->diffInDays(Carbon::parse($endDate)) + 1;
            $totalPossibleAttendances = $totalMembers * $daysInRange;
            
            if ($totalPossibleAttendances > 0) {
                $presentPct = round(($presentCount / $totalPossibleAttendances) * 100);
                $latePct = round(($lateCount / $totalPossibleAttendances) * 100);
                $leavePct = round(($leaveCount / $totalPossibleAttendances) * 100);
                $absentPct = max(0, 100 - $presentPct - $latePct - $leavePct);
            }

            // Fetch reports for the entire month for the calendar view
            $parsedDate = Carbon::parse($targetDate);
            $monthReports = PerformanceReport::whereMonth('report_date', $parsedDate->month)
                ->whereYear('report_date', $parsedDate->year)
                ->get(['report_date', 'team_productivity'])
                ->keyBy(function($item) {
                    return Carbon::parse($item->report_date)->format('Y-m-d');
                });

            // Paginate team members for the AI audits (9 per page)
            $allMembers = TeamMember::orderBy('name')->paginate(9, ['*'], 'members_page')->withQueryString();

            // Paginate group tasks (6 per page) for targetDate
            $groupTasksPaginated = Task::whereBetween('due_date', [$startDate, $endDate])
                ->has('teamMembers', '>', 1)
                ->with('teamMembers')
                ->latest()
                ->paginate(6, ['*'], 'groups_page')
                ->withQueryString();

            // Limit meeting notes
            $allMeetings = MeetingNote::with('teamMembers')
                ->whereBetween('meeting_date', [$startDate, $endDate])
                ->orderBy('meeting_time', 'desc')
                ->take(10)
                ->get();

            if ($request->ajax()) {
                $labels = $reports->reverse()->pluck('report_date')->map(function ($date) {
                    return Carbon::parse($date)->format('M d');
                })->toArray();
                
                $scores = $reports->reverse()->pluck('team_productivity')->toArray();

                $workloadLabels = [];
                $workloadData = [];
                $teams = \App\Models\Team::with('teamMembers')->get();
                foreach($teams as $team) {
                    $memberIds = $team->teamMembers->pluck('id');
                    $pendingCount = \App\Models\Task::whereIn('team_member_id', $memberIds)
                                        ->where('status', 'pending')
                                        ->whereBetween('due_date', [$startDate, $endDate])
                                        ->count();
                    if ($pendingCount > 0) {
                        $workloadLabels[] = $team->name;
                        $workloadData[] = $pendingCount;
                    }
                }
                if (empty($workloadLabels)) {
                    $workloadLabels = ['No Pending Tasks'];
                    $workloadData = [1];
                }

                $rangeLabel = 'Today';
                if ($rangeType === 'week_wise') $rangeLabel = 'This Week';
                elseif ($rangeType === 'month_wise') $rangeLabel = 'This Month';
                elseif ($rangeType === 'year_wise') $rangeLabel = 'This Year';
                elseif ($rangeType === 'all_time') $rangeLabel = 'All Time';
                elseif ($rangeType === 'custom_range') $rangeLabel = 'Custom Range';
                elseif ($rangeType === 'date_wise') $rangeLabel = 'Daily';

                return response()->json([
                    'success' => true,
                    'totalMembers' => $totalMembers,
                    'totalTasks' => $totalTasks,
                    'totalCommits' => $totalCommits,
                    'completedTasksCount' => $completedTasksCount,
                    'taskPct' => $totalTasks > 0 ? round(($completedTasksCount / $totalTasks) * 100) : 0,
                    'orgPerformance' => $latestReport ? $latestReport->team_productivity . '%' : 'N/A',
                    'rangeLabel' => $rangeLabel,
                    'chartData' => [
                        'labels' => $labels,
                        'scores' => $scores
                    ],
                    'workload' => [
                        'labels' => $workloadLabels,
                        'data' => $workloadData
                    ]
                ]);
            }

            $workloadLabelsInit = [];
            $workloadDataInit = [];
            $dashboardTeams = \App\Models\Team::with(['teamMembers', 'leader'])->get();
            foreach($dashboardTeams as $team) {
                $memberIds = $team->teamMembers->pluck('id');
                $pendingCount = \App\Models\Task::whereIn('team_member_id', $memberIds)
                                    ->where('status', 'pending')
                                    ->whereBetween('due_date', [$startDate, $endDate])
                                    ->count();
                if ($pendingCount > 0) {
                    $workloadLabelsInit[] = $team->name;
                    $workloadDataInit[] = $pendingCount;
                }
            }
            if (empty($workloadLabelsInit)) {
                $workloadLabelsInit = ['No Pending Tasks'];
                $workloadDataInit = [1];
            }

            return view('manager.dashboard', compact(
                'totalMembers',
                'totalTasks',
                'totalCommits',
                'latestReport',
                'reports',
                'allMembers',
                'completedTasksCount',
                'pendingTasksCount',
                'totalMeetingsCount',
                'groupTasksPaginated',
                'allMeetings',
                'targetDate',
                'monthReports',
                'presentPct',
                'latePct',
                'absentPct',
                'rangeType',
                'startDate',
                'endDate',
                'workloadLabelsInit',
                'workloadDataInit',
                'dashboardTeams'
            ));
        } catch (\Throwable $e) {
            dd([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * AJAX API to return paginated team members.
     */
    public function apiMembers(Request $request)
    {
        $search = $request->input('search');
        $query = TeamMember::query();
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
        }
        
        $members = $query->orderBy('name')->paginate(10);
        
        return response()->json([
            'data' => $members->items(),
            'current_page' => $members->currentPage(),
            'last_page' => $members->lastPage(),
            'total' => $members->total(),
        ]);
    }

    /**
     * AJAX API to return paginated tasks.
     */
    public function apiTasks(Request $request)
    {
        $search = $request->input('search');
        $query = Task::with('teamMember');
        
        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhereHas('teamMember', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }
        
        $tasks = $query->orderBy('due_date', 'desc')->paginate(10);
        
        $formatted = collect($tasks->items())->map(fn($task) => [
            'id' => $task->id,
            'title' => $task->title,
            'status' => $task->status,
            'due_date' => $task->due_date,
            'member_name' => $task->teamMember?->name ?? 'Unassigned',
            'team_member_id' => $task->team_member_id,
        ]);
        
        return response()->json([
            'data' => $formatted,
            'current_page' => $tasks->currentPage(),
            'last_page' => $tasks->lastPage(),
            'total' => $tasks->total(),
        ]);
    }

    /**
     * AJAX API to return paginated commits.
     */
    public function apiCommits(Request $request)
    {
        $search = $request->input('search');
        $query = GitCommit::with('teamMember');
        
        if ($search) {
            $query->where('commit_hash', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('repository_name', 'like', "%{$search}%")
                  ->orWhereHas('teamMember', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }
        
        $commits = $query->orderBy('committed_at', 'desc')->paginate(10);
        
        $formatted = collect($commits->items())->map(fn($c) => [
            'id' => $c->id,
            'commit_hash' => $c->commit_hash,
            'message' => $c->message,
            'repository_name' => $c->repository_name,
            'committed_at' => $c->committed_at->toDateTimeString(),
            'member_name' => $c->teamMember?->name ?? 'Unknown',
            'team_member_id' => $c->team_member_id,
        ]);
        
        return response()->json([
            'data' => $formatted,
            'current_page' => $commits->currentPage(),
            'last_page' => $commits->lastPage(),
            'total' => $commits->total(),
        ]);
    }

    /**
     * Display a dedicated paginated list of commits.
     */
    public function commitsList(Request $request)
    {
        if ($request->ajax()) {
            $query = GitCommit::with('teamMember', 'repository');
            return DataTables::eloquent($query)
                ->addColumn('developer', fn($c) => $c->teamMember?->name ?? 'Unknown')
                ->addColumn('developer_email', fn($c) => $c->teamMember?->email ?? '')
                ->addColumn('gitlab_id', fn($c) => $c->teamMember?->gitlab_id ?? 'N/A')
                ->addColumn('repo_link', fn($c) => $c->repository?->url ?? null)
                ->editColumn('committed_at', fn($c) => $c->committed_at->format('M d, Y h:i A'))
                ->editColumn('commit_hash', fn($c) => substr($c->commit_hash, 0, 7))
                ->addColumn('actions', fn($c) => $c->id)
                ->rawColumns(['actions'])
                ->make(true);
        }

        $teamMembers = TeamMember::orderBy('name')->select('id', 'name', 'email')->take(500)->get();
        $repositories = Repository::with('project')->orderBy('name')->select('id', 'name', 'project_id')->get();

        return view('manager.commits', compact('teamMembers', 'repositories'));
    }

    /**
     * Display a dedicated paginated list of repositories and projects.
     */
    public function repositoriesList(Request $request)
    {
        if ($request->ajax()) {
            $query = Repository::with('project');
            return DataTables::eloquent($query)
                ->addColumn('project_name', fn($r) => $r->project?->name ?? 'N/A')
                ->addColumn('project_desc', fn($r) => $r->project?->description ?? '')
                ->editColumn('created_at', fn($r) => $r->created_at->format('M d, Y'))
                ->addColumn('actions', fn($r) => $r->id)
                ->rawColumns(['actions'])
                ->make(true);
        }

        $projects = Project::orderBy('name')->select('id', 'name')->get();

        return view('manager.repositories', compact('projects'));
    }

    /**
     * Generate the performance report.
     */
    public function generate(Request $request)
    {
        try {
            $type = $request->input('type', 'daily');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date') ?: $request->input('date');

            $reportData = $this->agentService->generateReport($type, $startDate, $endDate);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => ucfirst($type) . ' performance report generated successfully!', 'redirect_url' => route('manager.report-detail', $reportData['id'])]);
            }
            return redirect()->route('manager.report-detail', $reportData['id'])->with('success', ucfirst($type) . ' performance report generated successfully! It includes the productivity index and AI insights.');
        } catch (\Throwable $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('manager.dashboard')->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }

    /**
     * Display historical reports with search and pagination.
     */
    public function reports(Request $request): View
    {
        $query = PerformanceReport::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('report_date', 'like', "%{$search}%")
                  ->orWhere('full_report', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter_date')) {
            $filterDate = $request->input('filter_date');
            $query->whereDate('report_date', $filterDate);
        }

        if ($request->filled('filter_datetime')) {
            $filterDatetime = $request->input('filter_datetime');
            $parsedDt = Carbon::parse($filterDatetime);
            $query->whereBetween('created_at', [
                $parsedDt->copy()->startOfMinute(),
                $parsedDt->copy()->endOfMinute()
            ]);
        }

        $reports = $query->latest()->paginate(10)->withQueryString();
        $allMembers = TeamMember::orderBy('name')->paginate(12, ['*'], 'members_page')->withQueryString();

        return view('manager.reports', compact('reports', 'allMembers'));
    }

    /**
     * Display single historical report details.
     */
    public function detail($id): View
    {
        $report = PerformanceReport::findOrFail($id);
        
        $prevReport = PerformanceReport::where('report_date', '<', $report->report_date)
            ->orderBy('report_date', 'desc')
            ->first();
            
        $nextReport = PerformanceReport::where('report_date', '>', $report->report_date)
            ->orderBy('report_date', 'asc')
            ->first();

        // Get tasks for this report's date to display as task details
        $tasks = Task::with('teamMember')
            ->whereDate('due_date', $report->report_date)
            ->get();
            
        // Get commits for this report's date
        $commits = GitCommit::with('teamMember')
            ->whereDate('committed_at', $report->report_date)
            ->get();
            
        // Get attendance for this report's date
        $attendanceLogs = AttendanceLog::with('teamMember')
            ->whereDate('date', $report->report_date)
            ->get();

        return view('manager.report-detail', compact('report', 'prevReport', 'nextReport', 'tasks', 'commits', 'attendanceLogs'));
    }

    /**
     * Delete a specific performance report.
     */
    public function destroyReport($id): RedirectResponse
    {
        $report = PerformanceReport::findOrFail($id);
        $report->delete();
        return redirect()->route('manager.reports')->with('success', 'Performance report deleted successfully!');
    }

    /**
     * Display manual data entry tab logs.
     */
    public function dataEntry(): View
    {
        return view('manager.data-entry');
    }

    /**
     * Display dedicated task entry and overview panel.
     */
    public function taskEntry(Request $request): View
    {
        $startDate = $request->input('start_date', \Carbon\Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', \Carbon\Carbon::today()->endOfMonth()->toDateString());

        // Base query for the date range
        $tasksQuery = Task::whereBetween('due_date', [$startDate, $endDate]);

        $tasks = (clone $tasksQuery)->with(['teamMember', 'teamMembers'])->orderBy('due_date', 'desc')->paginate(15);
        $allTeamMembers = TeamMember::orderBy('name')->get();

        $totalTasks = (clone $tasksQuery)->count();
        $completedTasks = (clone $tasksQuery)->where('status', 'completed')->count();
        
        $completedDelayed = (clone $tasksQuery)->where('status', 'completed')
            ->whereRaw('COALESCE(completed_at, updated_at) > due_date')
            ->count();
            
        $onTimeCompleted = $completedTasks - $completedDelayed;
        
        $pendingDelayed = (clone $tasksQuery)->where('status', '!=', 'completed')
            ->where('due_date', '<', now())
            ->count();
            
        $delayedTasks = $completedDelayed + $pendingDelayed;

        // Calculate average completion hours in database
        $dbDriver = config('database.default');
        if ($dbDriver === 'sqlite') {
            $avgHoursRaw = (clone $tasksQuery)->where('status', 'completed')
                ->selectRaw('AVG((julianday(COALESCE(completed_at, updated_at)) - julianday(created_at)) * 24) as avg_hours')
                ->value('avg_hours');
        } elseif ($dbDriver === 'pgsql') {
            $avgHoursRaw = (clone $tasksQuery)->where('status', 'completed')
                ->selectRaw('AVG(EXTRACT(EPOCH FROM (COALESCE(completed_at, updated_at) - created_at)) / 3600) as avg_hours')
                ->value('avg_hours');
        } else {
            $avgHoursRaw = (clone $tasksQuery)->where('status', 'completed')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, COALESCE(completed_at, updated_at))) as avg_hours')
                ->value('avg_hours');
        }
        $avgCompletionHours = round((float)$avgHoursRaw, 1);
        
        // Productivity Score: Heavy weight on completing on time, slight penalty for delayed
        if ($totalTasks > 0) {
            $score = (($onTimeCompleted * 1.0) + ($completedTasks - $onTimeCompleted) * 0.5) / $totalTasks * 100;
            $productivityScore = round(min(100, max(0, $score)));
        } else {
            $productivityScore = 0;
        }

        return view('manager.tasks', compact(
            'tasks', 'allTeamMembers', 'totalTasks', 'completedTasks', 
            'delayedTasks', 'avgCompletionHours', 'productivityScore',
            'startDate', 'endDate'
        ));
    }

    /**
     * Validate and store team member.
     */
    public function storeTeamMember(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:team_members,email',
            'role' => 'required|string|max:255',
            'gitlab_id' => 'nullable|string|max:255',
        ]);

        TeamMember::create($validated);

        return redirect()->back()->with('success', 'Team member added successfully!')->with('active_tab', 'members');
    }

    /**
     * Validate and store task.
     */
    public function storeTask(Request $request): RedirectResponse
    {
        if ($request->filled('email')) {
            $emails = array_map('trim', explode(',', $request->input('email')));
            $foundIds = \App\Models\TeamMember::whereIn('email', $emails)->pluck('id')->toArray();
            if (!empty($foundIds)) {
                $request->merge([
                    'team_member_id' => $foundIds[0],
                    'team_member_ids' => $foundIds
                ]);
            }
        }

        $validated = $request->validate([
            'team_member_id' => 'nullable|exists:team_members,id',
            'team_member_ids' => 'nullable|array',
            'team_member_ids.*' => 'exists:team_members,id',
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'required|date',
            'priority' => 'nullable|in:low,medium,high,critical',
            'dependency_id' => 'nullable|exists:tasks,id',
            'effort_estimation' => 'nullable|numeric|min:0',
        ]);

        if (empty($validated['priority'])) {
            $validated['priority'] = 'medium';
        }

        $ids = [];
        if (!empty($validated['team_member_ids'])) {
            $ids = $validated['team_member_ids'];
        } elseif (!empty($validated['team_member_id'])) {
            $ids = [$validated['team_member_id']];
        }

        if (empty($ids)) {
            return redirect()->back()->withErrors(['team_member_id' => 'At least one employee must be assigned.']);
        }

        $task = \App\Models\Task::create($validated);
        $task->teamMembers()->attach($ids);

        return redirect()->back()->with('success', 'Daily task logged and assigned successfully!');
    }

    /**
     * Update an existing task.
     */
    public function updateTask(Request $request, $id): RedirectResponse
    {
        $task = \App\Models\Task::findOrFail($id);

        if ($request->filled('email')) {
            $emails = array_map('trim', explode(',', $request->input('email')));
            $foundIds = \App\Models\TeamMember::whereIn('email', $emails)->pluck('id')->toArray();
            if (!empty($foundIds)) {
                $request->merge([
                    'team_member_id' => $foundIds[0],
                    'team_member_ids' => $foundIds
                ]);
            }
        }

        $validated = $request->validate([
            'team_member_id' => 'nullable|exists:team_members,id',
            'team_member_ids' => 'nullable|array',
            'team_member_ids.*' => 'exists:team_members,id',
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'required|date',
            'priority' => 'nullable|in:low,medium,high,critical',
            'dependency_id' => 'nullable|exists:tasks,id',
            'effort_estimation' => 'nullable|numeric|min:0',
            'actual_time' => 'nullable|numeric|min:0',
        ]);

        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        $ids = [];
        if (!empty($validated['team_member_ids'])) {
            $ids = $validated['team_member_ids'];
        } elseif (!empty($validated['team_member_id'])) {
            $ids = [$validated['team_member_id']];
        }
        
        if (!empty($ids)) {
            $task->teamMembers()->sync($ids);
        }

        return redirect()->back()->with('success', 'Task updated successfully!');
    }

    /**
     * Validate and store git commit.
     */
    public function storeCommit(Request $request): RedirectResponse
    {
        if ($request->filled('email')) {
            $member = TeamMember::where('email', $request->input('email'))->first();
            if ($member) {
                $request->merge(['team_member_id' => $member->id]);
            }
        }

        $validated = $request->validate([
            'team_member_id' => 'required|exists:team_members,id',
            'commit_hash' => 'required|string|max:255',
            'repository_name' => 'required|string|max:255',
            'message' => 'required|string|max:255',
            'committed_at' => 'required|date',
        ]);

        GitCommit::create($validated);

        return redirect()->back()->with('success', 'Git commit logged successfully!')->with('active_tab', 'commits');
    }

    /**
     * Validate and store attendance.
     */
    public function storeAttendance(Request $request): RedirectResponse
    {
        if ($request->filled('email')) {
            $member = TeamMember::where('email', $request->input('email'))->first();
            if ($member) {
                $request->merge(['team_member_id' => $member->id]);
            }
        }

        $validated = $request->validate([
            'team_member_id' => 'required|exists:team_members,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,leave',
            'check_in' => 'nullable|string',
            'check_out' => 'nullable|string',
            'leave_type' => 'nullable|string',
        ]);

        AttendanceLog::create($validated);

        return redirect()->back()->with('success', 'Attendance logged successfully!')->with('active_tab', 'attendance');
    }

    /**
     * Store meeting notes.
     */
    public function storeMeeting(Request $request): RedirectResponse
    {
        if ($request->filled('email')) {
            $emails = array_map('trim', explode(',', $request->input('email')));
            $foundIds = TeamMember::whereIn('email', $emails)->pluck('id')->toArray();
            if (!empty($foundIds)) {
                $request->merge(['team_members' => $foundIds]);
            }
        }

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'notes'        => 'required|string',
            'meeting_date' => 'required|date',
            'meeting_time' => 'nullable|string',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:team_members,id',
        ]);

        $meeting = MeetingNote::create([
            'title'        => $validated['title'],
            'notes'        => $validated['notes'],
            'meeting_date' => $validated['meeting_date'],
            'meeting_time' => $validated['meeting_time'] ?? null,
        ]);

        if (!empty($validated['team_members'])) {
            $meeting->teamMembers()->sync($validated['team_members']);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Meeting saved successfully!']);
        }
        return redirect()->back()->with('success', 'Meeting note saved successfully!')->with('active_tab', 'meetings');
    }

    /**
     * Display paginated meetings with Yajra DataTables.
     */
    public function meetingsList(Request $request)
    {
        $rangeType = $request->input('range_type', 'all_time');

        if ($request->ajax()) {
            $query = MeetingNote::withCount('teamMembers');
            
            if ($rangeType !== 'all_time') {
                $baseDate = Carbon::parse($request->input('date', Carbon::today()->toDateString()));
                $today = Carbon::today();
                
                $startDate = $baseDate->copy()->toDateString();
                $endDate = $baseDate->copy()->toDateString();
                
                if ($rangeType === 'date_wise') {
                    $startDate = $baseDate->toDateString();
                    $endDate = $startDate;
                } elseif ($rangeType === 'week_wise') {
                    $startDate = $baseDate->copy()->startOfWeek()->toDateString();
                    $endDate = $baseDate->copy()->endOfWeek()->toDateString();
                } elseif ($rangeType === 'month_wise') {
                    $startDate = $baseDate->copy()->startOfMonth()->toDateString();
                    $endDate = $baseDate->copy()->endOfMonth()->toDateString();
                } elseif ($rangeType === 'year_wise') {
                    $startDate = $baseDate->copy()->startOfYear()->toDateString();
                    $endDate = $baseDate->copy()->endOfYear()->toDateString();
                } elseif ($rangeType === 'custom_range') {
                    $startDate = $request->input('start_date', $today->toDateString());
                    $endDate = $request->input('end_date', $today->toDateString());
                }
                
                $query->whereBetween('meeting_date', [$startDate, $endDate]);
            }

            return DataTables::eloquent($query)
                ->editColumn('meeting_date', fn($m) => Carbon::parse($m->meeting_date)->format('M d, Y'))
                ->editColumn('meeting_time', fn($m) => $m->meeting_time ? Carbon::parse($m->meeting_time)->format('h:i A') : '—')
                ->addColumn('attendees', fn($m) => $m->team_members_count)
                ->addColumn('notes_short', fn($m) => substr($m->notes, 0, 80) . (strlen($m->notes) > 80 ? '...' : ''))
                ->addColumn('actions', fn($m) => $m->id)
                ->rawColumns(['actions'])
                ->make(true);
        }
        $teamMembers = TeamMember::orderBy('name')->select('id', 'name', 'email')->take(500)->get();
        return view('manager.meetings', compact('teamMembers', 'rangeType'));
    }

    /**
     * Update meeting note.
     */
    public function updateMeeting(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'notes'          => 'required|string',
            'meeting_date'   => 'required|date',
            'meeting_time'   => 'nullable|string',
            'team_member_ids'=> 'nullable|array',
            'team_member_ids.*' => 'exists:team_members,id',
        ]);
        $meeting = MeetingNote::findOrFail($id);
        $meeting->update([
            'title'        => $validated['title'],
            'notes'        => $validated['notes'],
            'meeting_date' => $validated['meeting_date'],
            'meeting_time' => $validated['meeting_time'] ?? null,
        ]);
        if (isset($validated['team_member_ids'])) {
            $meeting->teamMembers()->sync($validated['team_member_ids']);
        }
        return redirect()->back()->with('success', 'Meeting updated successfully!');
    }

    /**
     * Delete meeting note.
     */
    public function destroyMeeting($id): RedirectResponse
    {
        $meeting = MeetingNote::findOrFail($id);
        $meeting->teamMembers()->detach();
        $meeting->delete();
        return redirect()->back()->with('success', 'Meeting deleted successfully!');
    }

    /**
     * Update team member.
     */
    public function updateTeamMember(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:team_members,email,' . $id,
            'role' => 'required|string|max:255',
            'gitlab_id' => 'nullable|string|max:255',
        ]);

        $member = TeamMember::findOrFail($id);
        $member->update($validated);

        return redirect()->back()->with('success', 'Team member updated successfully!');
    }

    /**
     * Delete team member.
     */
    public function destroyTeamMember($id): RedirectResponse
    {
        $member = TeamMember::findOrFail($id);
        $member->delete();

        return redirect()->back()->with('success', 'Team member deleted successfully!');
    }



    /**
     * Delete task.
     */
    public function destroyTask($id): RedirectResponse
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully!');
    }

    /**
     * Update git commit.
     */
    public function updateCommit(Request $request, $id): RedirectResponse
    {
        if ($request->filled('email')) {
            $member = TeamMember::where('email', $request->input('email'))->first();
            if ($member) {
                $request->merge(['team_member_id' => $member->id]);
            }
        }

        $validated = $request->validate([
            'team_member_id' => 'required|exists:team_members,id',
            'commit_hash' => 'required|string|max:255',
            'repository_name' => 'required|string|max:255',
            'message' => 'required|string|max:255',
            'committed_at' => 'required|date',
        ]);

        $commit = GitCommit::findOrFail($id);
        $commit->update($validated);

        return redirect()->back()->with('success', 'Git commit updated successfully!');
    }

    /**
     * Delete git commit.
     */
    public function destroyCommit($id): RedirectResponse
    {
        $commit = GitCommit::findOrFail($id);
        $commit->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Commit deleted successfully!']);
        }
        return redirect()->back()->with('success', 'Git commit deleted successfully!');
    }

    /**
     * Store a new repository.
     */
    public function storeRepository(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name'       => 'required|string|max:255',
            'url'        => 'nullable|url|max:255',
        ]);
        Repository::create($validated);
        return redirect()->back()->with('success', 'Repository added successfully!');
    }

    /**
     * Update a repository.
     */
    public function updateRepository(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name'       => 'required|string|max:255',
            'url'        => 'nullable|url|max:255',
        ]);
        $repo = Repository::findOrFail($id);
        $repo->update($validated);
        return redirect()->back()->with('success', 'Repository updated successfully!');
    }

    /**
     * Delete a repository.
     */
    public function destroyRepository($id): RedirectResponse
    {
        $repo = Repository::findOrFail($id);
        $repo->delete();
        return redirect()->back()->with('success', 'Repository deleted successfully!');
    }

    /**
     * Store a new project.
     */
    public function storeProject(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        Project::create($validated);
        return redirect()->back()->with('success', 'Project created successfully!');
    }

    /**
     * Generate AI-powered evening report for a specific employee.
     */
    public function employeeReport(Request $request, $id)
    {
        try {
            $member = TeamMember::findOrFail($id);
            $date = $request->query('date', Carbon::today()->toDateString());
            
            $reportMarkdown = $this->agentService->generateEmployeeReport($member, $date);

            return response()->json([
                'success' => true,
                'name' => $member->name,
                'role' => $member->role,
                'date' => $date,
                'report' => $reportMarkdown
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate employee report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate AI-powered evening report for a group of employees.
     */
    public function groupReport(Request $request)
    {
        try {
            $ids = array_map('intval', explode(',', $request->query('ids', '')));
            $ids = array_filter($ids);
            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'No member IDs provided.'], 422);
            }
            $date = $request->query('date', Carbon::today()->toDateString());
            $members = TeamMember::whereIn('id', $ids)->orderBy('name')->get();
            $groupName = $members->pluck('name')->join(' & ');
            $report = $this->agentService->generateGroupReport($ids, $date);
            return response()->json([
                'success' => true,
                'group_name' => $groupName,
                'date' => $date,
                'report' => $report,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Failed to generate group report: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display dedicated attendance registry and statistics.
     */
    public function attendanceRegistry(Request $request, \App\Services\AttendanceAnalyticsService $analyticsService): View
    {
        $startDateRaw = $request->input('start_date', Carbon::today()->toDateString());
        $endDateRaw = $request->input('end_date', Carbon::today()->toDateString());
        
        $startDate = str_replace(' ', '-', $startDateRaw);
        $endDate = str_replace(' ', '-', $endDateRaw);
        
        // Paginate team members
        $teamMembers = TeamMember::orderBy('name')->paginate(15)->withQueryString();
        
        $memberIds = $teamMembers->pluck('id');
        
        // Analytics for each member in the selected range
        $monthlyAnalytics = [];
        foreach ($teamMembers as $member) {
            $monthlyAnalytics[$member->id] = $analyticsService->getMemberAnalytics($member->id, $startDate, $endDate);
        }
        
        $teamTrends = $analyticsService->getTeamTrends($startDate, $endDate);
        
        // Raw Logs logic for the selected range (Paginated independently)
        $attendanceLogs = AttendanceLog::with('teamMember')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->paginate(15, ['*'], 'logs_page')
            ->withQueryString();
            
        // Map logs by team_member_id and then date for easy lookup in view (Only for the 15 summarized members!)
        $summaryLogs = AttendanceLog::whereBetween('date', [$startDate, $endDate])
            ->whereIn('team_member_id', $memberIds)
            ->get();
            
        $logsMap = [];
        foreach ($summaryLogs as $log) {
            $logsMap[$log->team_member_id][$log->date] = $log;
        }
        
        $allTeamMembers = collect([]); // Handled via AJAX to prevent memory exhaustion
        
        return view('manager.attendance', compact(
            'teamMembers', 'startDate', 'endDate', 'monthlyAnalytics', 'teamTrends', 'allTeamMembers', 'logsMap', 'attendanceLogs'
        ));
    }

    /**
     * Update an attendance log.
     */
    public function updateAttendance(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:present,absent,late,leave',
            'check_in' => 'nullable|string',
            'check_out' => 'nullable|string',
            'leave_type' => 'nullable|string',
        ]);

        $log = AttendanceLog::findOrFail($id);
        $log->update($validated);

        return redirect()->back()->with('success', 'Attendance log updated successfully!');
    }

    /**
     * Delete an attendance log.
     */
    public function destroyAttendance($id): RedirectResponse
    {
        $log = AttendanceLog::findOrFail($id);
        $log->delete();

        return redirect()->back()->with('success', 'Attendance log deleted successfully!');
    }

    /**
     * Export daily tasks to CSV.
     */
    public function exportTasks()
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=tasks_export_' . date('Y-m-d') . '.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $columns = ['Employee Email', 'Task Title', 'Status', 'Due Date'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Use lazy cursor to stream memory-safely
            Task::with('teamMember')->lazy()->each(function($task) use($file) {
                fputcsv($file, [
                    $task->teamMember?->email ?? '',
                    $task->title,
                    $task->status,
                    $task->due_date,
                ]);
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import daily tasks from CSV.
     */
    public function importTasks(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $fileHandle = fopen($filePath, 'r');
        $header = fgetcsv($fileHandle, 1000, ',');

        if (!$header) {
            fclose($fileHandle);
            return redirect()->back()->with('error', 'The uploaded CSV file is empty or invalid.');
        }

        $importedCount = 0;
        while (($row = fgetcsv($fileHandle, 1000, ',')) !== false) {
            if (empty($row) || count($row) < 3) continue;

            $email = trim($row[0] ?? '');
            $title = trim($row[1] ?? '');
            $status = trim($row[2] ?? 'pending');
            $dueDate = trim($row[3] ?? '');

            if (empty($email) || empty($title)) continue;

            $member = TeamMember::where('email', $email)->first();
            if (!$member) continue;

            if (empty($dueDate)) {
                $dueDate = Carbon::today()->toDateString();
            }

            $task = Task::updateOrCreate(
                [
                    'team_member_id' => $member->id,
                    'title' => $title,
                    'due_date' => $dueDate
                ],
                [
                    'status' => $status
                ]
            );
            $task->teamMembers()->sync([$member->id]);
            $importedCount++;
        }

        fclose($fileHandle);

        return redirect()->back()->with('success', "Successfully imported {$importedCount} task records.");
    }

    /**
     * Export daily attendance logs to CSV.
     */
    public function exportAttendance()
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=attendance_export_' . date('Y-m-d') . '.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $columns = ['Employee Email', 'Date', 'Status', 'Check-in Time'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Use lazy cursor to stream memory-safely
            AttendanceLog::with('teamMember')->lazy()->each(function($log) use($file) {
                fputcsv($file, [
                    $log->teamMember?->email ?? '',
                    $log->date,
                    $log->status,
                    $log->check_in,
                ]);
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import daily attendance logs from CSV.
     */
    public function importAttendance(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $fileHandle = fopen($filePath, 'r');
        $header = fgetcsv($fileHandle, 1000, ',');

        if (!$header) {
            fclose($fileHandle);
            return redirect()->back()->with('error', 'The uploaded CSV file is empty or invalid.');
        }

        $importedCount = 0;
        while (($row = fgetcsv($fileHandle, 1000, ',')) !== false) {
            if (empty($row) || count($row) < 3) continue;

            $email = trim($row[0] ?? '');
            $date = trim($row[1] ?? '');
            $status = trim($row[2] ?? 'present');
            $checkIn = trim($row[3] ?? null);

            if (empty($email) || empty($date)) continue;

            $member = TeamMember::where('email', $email)->first();
            if (!$member) continue;

            AttendanceLog::updateOrCreate(
                [
                    'team_member_id' => $member->id,
                    'date' => $date
                ],
                [
                    'status' => $status,
                    'check_in' => $checkIn ?: null
                ]
            );
            $importedCount++;
        }

        fclose($fileHandle);

        return redirect()->back()->with('success', "Successfully imported {$importedCount} attendance records.");
    }
}
