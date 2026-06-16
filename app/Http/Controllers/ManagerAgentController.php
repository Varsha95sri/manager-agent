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
    public function index()
    {
        try {
            $todayStr = Carbon::today()->toDateString();
            
            $totalMembers = TeamMember::count();
            $totalTasks = Task::count();
            
            // Count git commits logged today
            $totalCommits = GitCommit::whereDate('committed_at', $todayStr)->count();
            
            $latestReport = PerformanceReport::latest()->first();
            $reports = PerformanceReport::latest()->take(7)->get();

            // Data for dashboard modals
            $allMembers = TeamMember::all();
            $allTasks = Task::with(['teamMember', 'teamMembers'])->get();
            $allCommits = GitCommit::with('teamMember')->get();
            $allMeetings = MeetingNote::with('teamMembers')->orderBy('meeting_date', 'desc')->get();

            return view('manager.dashboard', compact(
                'totalMembers',
                'totalTasks',
                'totalCommits',
                'latestReport',
                'reports',
                'allMembers',
                'allTasks',
                'allCommits',
                'allMeetings'
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
     * Generate the daily performance report.
     */
    public function generate(Request $request)
    {
        try {
            $this->agentService->generateDailyReport();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Daily performance report generated successfully!']);
            }
            return redirect()->route('manager.dashboard')->with('success', 'Daily performance report generated successfully!');
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
        $allMembers = TeamMember::orderBy('name')->get();

        return view('manager.reports', compact('reports', 'allMembers'));
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
     * Display dedicated task entry and overview panel.
     */
    public function taskEntry(): View
    {
        $teamMembers = TeamMember::orderBy('name')->get();
        $tasks = Task::with(['teamMember', 'teamMembers'])->orderBy('due_date', 'desc')->paginate(15);
        return view('manager.tasks', compact('teamMembers', 'tasks'));
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
            'team_member_id' => 'nullable|exists:team_members,id',
            'team_member_ids' => 'nullable|array',
            'team_member_ids.*' => 'exists:team_members,id',
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'required|date',
        ]);

        $ids = [];
        if (!empty($validated['team_member_ids'])) {
            $ids = $validated['team_member_ids'];
        } elseif (!empty($validated['team_member_id'])) {
            $ids = [$validated['team_member_id']];
        }

        if (empty($ids)) {
            return redirect()->back()->withErrors(['team_member_id' => 'At least one employee must be assigned.']);
        }

        $primaryId = $ids[0];

        $task = Task::create([
            'team_member_id' => $primaryId,
            'title' => $validated['title'],
            'status' => $validated['status'],
            'due_date' => $validated['due_date'],
        ]);

        $task->teamMembers()->sync($ids);

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
            'meeting_time' => 'nullable|string',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:team_members,id',
        ]);

        $meeting = MeetingNote::create([
            'title' => $validated['title'],
            'notes' => $validated['notes'],
            'meeting_date' => $validated['meeting_date'],
            'meeting_time' => $validated['meeting_time'] ?? null,
        ]);

        if (!empty($validated['team_members'])) {
            $meeting->teamMembers()->sync($validated['team_members']);
        }

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
            'team_member_id' => 'nullable|exists:team_members,id',
            'team_member_ids' => 'nullable|array',
            'team_member_ids.*' => 'exists:team_members,id',
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'required|date',
        ]);

        $task = Task::findOrFail($id);

        $ids = [];
        if (!empty($validated['team_member_ids'])) {
            $ids = $validated['team_member_ids'];
        } elseif (!empty($validated['team_member_id'])) {
            $ids = [$validated['team_member_id']];
        }

        if (empty($ids)) {
            return redirect()->back()->withErrors(['team_member_ids' => 'At least one employee must be assigned.']);
        }

        $primaryId = $ids[0];

        $task->update([
            'team_member_id' => $primaryId,
            'title' => $validated['title'],
            'status' => $validated['status'],
            'due_date' => $validated['due_date'],
        ]);

        $task->teamMembers()->sync($ids);

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
     * Display dedicated attendance registry and statistics.
     */
    public function attendanceRegistry(Request $request): View
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $teamMembers = TeamMember::orderBy('name')->get();
        
        // Get all attendance logs for the selected date
        $attendanceLogs = AttendanceLog::with('teamMember')
            ->whereDate('date', $date)
            ->get();
            
        // Map logs by team_member_id for easy lookup in view
        $logsMap = $attendanceLogs->keyBy('team_member_id');
        
        // Calculate stats
        $totalPresent = $attendanceLogs->where('status', 'present')->count();
        $totalLate = $attendanceLogs->where('status', 'late')->count();
        $totalAbsent = $attendanceLogs->where('status', 'absent')->count();
        
        return view('manager.attendance', compact(
            'teamMembers',
            'date',
            'attendanceLogs',
            'logsMap',
            'totalPresent',
            'totalLate',
            'totalAbsent'
        ));
    }

    /**
     * Update an attendance log.
     */
    public function updateAttendance(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:present,absent,late',
            'check_in' => 'nullable|string',
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

        $tasks = Task::with('teamMember')->get();
        $columns = ['Employee Email', 'Task Title', 'Status', 'Due Date'];

        $callback = function() use($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                fputcsv($file, [
                    $task->teamMember?->email ?? '',
                    $task->title,
                    $task->status,
                    $task->due_date,
                ]);
            }

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

        $logs = AttendanceLog::with('teamMember')->get();
        $columns = ['Employee Email', 'Date', 'Status', 'Check-in Time'];

        $callback = function() use($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->teamMember?->email ?? '',
                    $log->date,
                    $log->status,
                    $log->check_in,
                ]);
            }

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
