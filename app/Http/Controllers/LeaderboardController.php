<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class LeaderboardController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->get('filter', 'all');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        $startDate = null;
        $endDate = Carbon::now();

        switch ($filter) {
            case 'daily':
                $startDate = Carbon::today();
                break;
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'quarterly':
                $startDate = Carbon::now()->startOfQuarter();
                break;
            case 'yearly':
                $startDate = Carbon::now()->startOfYear();
                break;
            case 'custom':
                if ($customStart) $startDate = Carbon::parse($customStart)->startOfDay();
                if ($customEnd) $endDate = Carbon::parse($customEnd)->endOfDay();
                break;
        }

        // --- 1. Individual Leaderboard ---
        $employees = TeamMember::with('team')->withCount([
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
        ])->get();

        $employees->each(function ($employee) {
            // Task Completion Score
            $taskCompletionScore = $employee->tasks_count * 50;
            
            // Task Quality Score (Heuristic)
            $taskQualityScore = $employee->tasks_count * 15;
            
            // Attendance Score
            $attendanceScore = $employee->attendance_logs_count * 20;
            
            // GitLab Contribution Score
            $gitlabScore = $employee->commits_count * 10;
            
            // Code Quality Score (Heuristic)
            $codeQualityScore = $employee->commits_count * 5;
            
            // Project Contribution Score
            $projectContributionScore = ($employee->tasks_count > 0 || $employee->commits_count > 0) ? 50 : 0;

            $employee->score_details = [
                'task_completion' => $taskCompletionScore,
                'task_quality' => $taskQualityScore,
                'attendance' => $attendanceScore,
                'gitlab' => $gitlabScore,
                'code_quality' => $codeQualityScore,
                'project_contribution' => $projectContributionScore,
            ];

            $employee->score = array_sum($employee->score_details);
        });

        $individualLeaderboard = $employees->sortByDesc('score')->take(100)->values();

        // --- 2. Team Leaderboard (Group by Actual Team) ---
        $teams = $employees->groupBy(function($item) {
            return $item->team ? $item->team->name : 'Unassigned';
        });

        $teamLeaderboard = collect();
        foreach ($teams as $teamName => $members) {
            $totalTasks = $members->sum('tasks_count');
            $totalCommits = $members->sum('commits_count');
            $totalAttendance = $members->sum('attendance_logs_count');
            $memberCount = $members->count();

            // Calculate Team Metrics
            $teamProductivityScore = $totalTasks * 30;
            $teamDeliveryScore = $totalTasks * 20;
            $teamAttendanceScore = $totalAttendance * 10;
            $teamCodeQualityScore = $totalCommits * 5;
            $teamCollaborationScore = $memberCount * 25;
            
            $totalScore = $teamProductivityScore + $teamDeliveryScore + $teamAttendanceScore + $teamCodeQualityScore + $teamCollaborationScore;

            if ($totalScore > 0 || $memberCount > 0) {
                $teamSlug = $members->first()->team ? $members->first()->team->slug : null;
                $teamLeaderboard->push((object)[
                    'name' => $teamName,
                    'slug' => $teamSlug,
                    'member_count' => $memberCount,
                    'score' => $totalScore,
                    'score_details' => [
                        'productivity' => $teamProductivityScore,
                        'delivery' => $teamDeliveryScore,
                        'attendance' => $teamAttendanceScore,
                        'code_quality' => $teamCodeQualityScore,
                        'collaboration' => $teamCollaborationScore,
                    ]
                ]);
            }
        }
        $teamLeaderboard = $teamLeaderboard->sortByDesc('score')->values();

        // --- 3. Organization Leaderboard ---
        $orgLeaderboard = (object)[
            'top_employees' => $individualLeaderboard->take(5),
            'top_teams' => $teamLeaderboard->take(3),
            'top_departments' => $teamLeaderboard->take(3),
            'top_contributors' => $individualLeaderboard->sortByDesc(function($e) {
                return $e->commits_count;
            })->take(5)->values()
        ];

        return view('manager.leaderboard', compact('individualLeaderboard', 'teamLeaderboard', 'orgLeaderboard', 'filter', 'customStart', 'customEnd'));
    }
}
