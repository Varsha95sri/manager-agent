<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeamMember;
use App\Models\Team;
use App\Models\Task;
use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $teamMember = TeamMember::with(['team', 'designation'])->where('email', $user->email)->first();

        if (!$teamMember) {
            return view('user.dashboard', [
                'error' => 'Your profile is not linked to any employee record. Please contact the administrator.',
                'teamMember' => null
            ]);
        }

        // Fetch Tasks (both directly created and assigned)
        $tasks = $teamMember->tasks()->get();
        // Assuming $teamMember->assignedTasks() exists as per belongsToMany relationship, but let's stick to tasks() as a primary source for "apne saare Tasks" 
        $assignedTasks = $teamMember->assignedTasks()->get();
        $allTasks = $tasks->merge($assignedTasks)->unique('id');

        $totalTasks = $allTasks->count();
        $completedTasks = $allTasks->where('status', 'Completed')->count();
        $pendingTasks = $allTasks->where('status', '!=', 'Completed')->count();
        
        $attendanceLogs = $teamMember->attendanceLogs()->orderBy('date', 'desc')->take(30)->get();
        
        // Calculate Attendance % for current month
        $currentMonthLogs = $attendanceLogs->where('date', '>=', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $workingDays = $currentMonthLogs->count();
        $presentDays = $currentMonthLogs->where('status', 'Present')->count();
        $attendancePercentage = $workingDays > 0 ? round(($presentDays / $workingDays) * 100) : 0;

        // Leave Requests
        $leaveRequests = LeaveRequest::where('employee_id', $teamMember->id)->orderBy('created_at', 'desc')->get();

        // Team Data
        $teamMembers = null;
        if ($teamMember->team_id) {
            $team = Team::with(['teamMembers.projectAllocations.project'])->find($teamMember->team_id);
            if ($team) {
                $teamMembers = $team->teamMembers;
            }
        }

        // Charts data preparation
        $last7Days = collect(range(6, 0))->map(function($days) {
            return Carbon::now()->subDays($days)->format('Y-m-d');
        });

        $taskTrendData = $last7Days->map(function($date) use ($allTasks) {
            return $allTasks->where('status', 'Completed')
                ->where('updated_at', '>=', Carbon::parse($date)->startOfDay())
                ->where('updated_at', '<=', Carbon::parse($date)->endOfDay())
                ->count();
        })->toArray();
        
        $taskStatusData = [
            $completedTasks,
            $allTasks->where('status', 'In Progress')->count(),
            $allTasks->where('status', 'Pending')->count(),
            $allTasks->where('status', 'Overdue')->count(),
        ];

        return view('user.dashboard', compact(
            'teamMember',
            'allTasks',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'attendanceLogs',
            'attendancePercentage',
            'leaveRequests',
            'teamMembers',
            'last7Days',
            'taskTrendData',
            'taskStatusData'
        ));
    }

    public function storeLeave(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'type' => 'required|string'
        ]);

        $teamMember = TeamMember::where('email', Auth::user()->email)->first();
        if (!$teamMember) {
            return back()->with('error', 'Profile not linked.');
        }

        LeaveRequest::create([
            'employee_id' => $teamMember->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'type' => $request->type,
            'status' => 'Pending'
        ]);

        return back()->with('success', 'Leave request submitted successfully.');
    }

    public function tasks()
    {
        $teamMember = TeamMember::where('email', Auth::user()->email)->firstOrFail();
        
        $tasks = Task::where('team_member_id', $teamMember->id)
            ->orderBy('due_date', 'asc')
            ->get();
            
        return view('user.tasks.index', compact('tasks'));
    }

    public function updateTask(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed'
        ]);

        $teamMember = TeamMember::where('email', Auth::user()->email)->firstOrFail();
        $task = Task::where('id', $id)->where('team_member_id', $teamMember->id)->firstOrFail();
        
        $task->status = $request->status;
        $task->save();
        
        return back()->with('success', 'Task status updated successfully.');
    }

    public function projects()
    {
        $teamMember = TeamMember::where('email', Auth::user()->email)->firstOrFail();
        
        $allocations = \App\Models\ProjectAllocation::with('project')
            ->where('team_member_id', $teamMember->id)
            ->get();
            
        return view('user.projects.index', compact('allocations'));
    }

    public function commits()
    {
        $teamMember = TeamMember::where('email', Auth::user()->email)->firstOrFail();
        
        $commits = \App\Models\Commit::with('project')
            ->where('employee_id', $teamMember->id)
            ->orderBy('committed_at', 'desc')
            ->paginate(15);
            
        return view('user.commits.index', compact('commits'));
    }

    public function attendance()
    {
        $teamMember = TeamMember::where('email', Auth::user()->email)->firstOrFail();
        
        $logs = AttendanceLog::where('team_member_id', $teamMember->id)
            ->orderBy('date', 'desc')
            ->paginate(15);
            
        return view('user.attendance.index', compact('logs'));
    }

    public function showTeamMember($id)
    {
        $userMember = TeamMember::where('email', Auth::user()->email)->firstOrFail();
        
        // Ensure the requested team member is in the same team
        $teamMember = TeamMember::with(['designation', 'skills', 'projectAllocations.project'])
            ->where('id', $id)
            ->where('team_id', $userMember->team_id)
            ->firstOrFail();
            
        // Get tasks
        $tasks = Task::where('team_member_id', $teamMember->id)->orderBy('due_date', 'asc')->get();
        $completedTasks = $tasks->where('status', 'Completed')->count();
        $totalTasks = $tasks->count();
        
        // Get commits
        $commits = \App\Models\Commit::with('project')
            ->where('employee_id', $teamMember->id)
            ->orderBy('committed_at', 'desc')
            ->take(10)
            ->get();
            
        return view('user.team-members.show', compact('teamMember', 'tasks', 'completedTasks', 'totalTasks', 'commits'));
    }
    public function leaderboard(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        $startDate = null;
        $endDate = \Carbon\Carbon::now();

        switch ($filter) {
            case 'daily':
                $startDate = \Carbon\Carbon::today();
                break;
            case 'weekly':
                $startDate = \Carbon\Carbon::now()->startOfWeek();
                break;
            case 'monthly':
                $startDate = \Carbon\Carbon::now()->startOfMonth();
                break;
            case 'quarterly':
                $startDate = \Carbon\Carbon::now()->startOfQuarter();
                break;
            case 'yearly':
                $startDate = \Carbon\Carbon::now()->startOfYear();
                break;
            case 'custom':
                if ($customStart) $startDate = \Carbon\Carbon::parse($customStart)->startOfDay();
                if ($customEnd) $endDate = \Carbon\Carbon::parse($customEnd)->endOfDay();
                break;
        }

        $currentUser = Auth::user();
        $currentUserMember = TeamMember::where('email', $currentUser->email)->first();
        $teamId = $currentUserMember ? $currentUserMember->team_id : null;

        $employeesQuery = TeamMember::with('team', 'designation')->withCount([
            'commits' => function ($query) use ($startDate, $endDate) {
                if ($startDate) $query->whereBetween('created_at', [$startDate, $endDate]);
            },
            'tasks' => function ($query) use ($startDate, $endDate) {
                $query->whereIn('status', ['Completed', 'completed']);
                if ($startDate) $query->whereBetween('updated_at', [$startDate, $endDate]);
            },
            'attendanceLogs' => function ($query) use ($startDate, $endDate) {
                $query->whereIn('status', ['Present', 'present']);
                if ($startDate) $query->whereBetween('date', [$startDate, $endDate]);
            }
        ]);
        
        if ($teamId) {
            $employeesQuery->where('team_id', $teamId);
        } else {
            // If the user has no team, only show themselves
            $employeesQuery->where('email', $currentUser->email);
        }
        
        $employees = $employeesQuery->get();

        $employees->each(function ($employee) {
            $taskScore = $employee->tasks_count * 65; // 50 + 15
            $attendanceScore = $employee->attendance_logs_count * 20;
            $gitlabScore = $employee->commits_count * 15; // 10 + 5
            $projectScore = ($employee->tasks_count > 0 || $employee->commits_count > 0) ? 50 : 0;
            $employee->score = $taskScore + $attendanceScore + $gitlabScore + $projectScore;
        });

        $individualLeaderboard = $employees->sortByDesc('score')->take(100)->values();

        // Find current user's rank
        $currentUserEmail = Auth::user()->email;
        $currentUserRank = null;
        $currentUserData = null;
        foreach ($individualLeaderboard as $index => $emp) {
            if ($emp->email === $currentUserEmail) {
                $currentUserRank = $index + 1;
                $currentUserData = $emp;
                break;
            }
        }

        return view('user.leaderboard', compact('individualLeaderboard', 'filter', 'customStart', 'customEnd', 'currentUserRank', 'currentUserData'));
    }
}
