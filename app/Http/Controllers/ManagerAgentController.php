<?php
// app/Http/Controllers/ManagerAgentController.php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Models\Task;
use App\Models\GitCommit;
use App\Models\AttendanceLog;
use App\Models\MeetingNote;
use App\Models\PerformanceReport;
use App\Services\ManagerAgentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

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
    public function index(): View
    {
        $todayStr = Carbon::today()->toDateString();
        
        $totalMembers = TeamMember::count();
        $totalTasks = Task::count();
        
        // Count git commits logged today
        $totalCommits = GitCommit::whereDate('committed_at', $todayStr)->count();
        
        $latestReport = PerformanceReport::latest()->first();
        $reports = PerformanceReport::latest()->take(7)->get();

        // Data for dashboard modals
        $allMembers = TeamMember::all();
        $allTasks = Task::with('teamMember')->get();
        $allCommits = GitCommit::with('teamMember')->get();

        return view('manager.dashboard', compact(
            'totalMembers',
            'totalTasks',
            'totalCommits',
            'latestReport',
            'reports',
            'allMembers',
            'allTasks',
            'allCommits'
        ));
    }

    /**
     * Generate the daily performance report.
     */
    public function generate(): RedirectResponse
    {
        try {
            $this->agentService->generateDailyReport();
            return redirect()->route('manager.dashboard')->with('success', 'Daily performance report generated successfully!');
        } catch (\Throwable $e) {
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

        return view('manager.reports', compact('reports'));
    }

    /**
     * Display details of a specific report.
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

        return view('manager.report-detail', compact('report', 'prevReport', 'nextReport', 'tasks'));
    }

    /**
     * Display manual data entry tab logs.
     */
    public function dataEntry(): View
    {
        $teamMembers = TeamMember::orderBy('name')->get();
        return view('manager.data-entry', compact('teamMembers'));
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
            'github_id' => 'nullable|string|max:255',
        ]);

        TeamMember::create($validated);

        return redirect()->back()->with('success', 'Team member added successfully!')->with('active_tab', 'members');
    }

    /**
     * Validate and store task.
     */
    public function storeTask(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'team_member_id' => 'required|exists:team_members,id',
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'required|date',
        ]);

        Task::create($validated);

        return redirect()->back()->with('success', 'Task recorded successfully!')->with('active_tab', 'tasks');
    }

    /**
     * Validate and store git commit.
     */
    public function storeCommit(Request $request): RedirectResponse
    {
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
        $validated = $request->validate([
            'team_member_id' => 'required|exists:team_members,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late',
            'check_in' => 'nullable|string',
        ]);

        AttendanceLog::create($validated);

        return redirect()->back()->with('success', 'Attendance logged successfully!')->with('active_tab', 'attendance');
    }

    /**
     * Validate and store meeting notes.
     */
    public function storeMeeting(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'notes' => 'required|string',
            'meeting_date' => 'required|date',
        ]);

        MeetingNote::create($validated);

        return redirect()->back()->with('success', 'Meeting note saved successfully!')->with('active_tab', 'meetings');
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
            'github_id' => 'nullable|string|max:255',
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
     * Update task.
     */
    public function updateTask(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'team_member_id' => 'required|exists:team_members,id',
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'required|date',
        ]);

        $task = Task::findOrFail($id);
        $task->update($validated);

        return redirect()->back()->with('success', 'Task updated successfully!');
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

        return redirect()->back()->with('success', 'Git commit deleted successfully!');
    }
}
