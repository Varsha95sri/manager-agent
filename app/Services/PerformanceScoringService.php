<?php

namespace App\Services;

use App\Models\TeamMember;

class PerformanceScoringService
{
    /**
     * Calculate score and grade for a single employee.
     */
    public function calculateForEmployee(TeamMember $employee): void
    {
        // Load counts if not already loaded
        $employee->loadCount([
            'commits',
            'tasks' => function ($query) {
                $query->where('status', 'Completed')->orWhere('status', 'completed');
            },
            'attendanceLogs' => function ($query) {
                $query->where('status', 'Present')->orWhere('status', 'present');
            }
        ]);

        // Get configurable weights (hardcoded to requested values)
        $wTaskComp   = 0.30; // 30%
        $wTaskQual   = 0.20; // 20%
        $wGitAct     = 0.15; // 15%
        $wCodeQual   = 0.15; // 15%
        $wAttendance = 0.10; // 10%
        $wCollab     = 0.10; // 10%

        // Normalize base metrics to 0-100 scale
        // Tasks: assume 20 tasks is a perfect 100 score for a period
        $taskCompScore = min(100, ($employee->tasks_count / 20) * 100);
        
        // Git: assume 50 commits is a perfect 100 score
        $gitActScore = min(100, ($employee->commits_count / 50) * 100);
        
        // Attendance: assume 20 days present is perfect 100
        $attendanceScore = min(100, ($employee->attendance_logs_count / 20) * 100);

        // Mock metrics that are not fully integrated yet, varying slightly by employee
        $taskQualScore = min(100, 75 + ($employee->id % 20));
        $codeQualScore = min(100, 80 + ($employee->id % 15));
        $collabScore   = min(100, 85 + ($employee->id % 10));

        // Calculate weighted score
        $score = ($taskCompScore * $wTaskComp)
               + ($taskQualScore * $wTaskQual)
               + ($gitActScore * $wGitAct)
               + ($codeQualScore * $wCodeQual)
               + ($attendanceScore * $wAttendance)
               + ($collabScore * $wCollab);

        $normalizedScore = min(100, round($score));

        $employee->performance_score = $normalizedScore;
        $employee->performance_grade = self::getGradeFromScore($normalizedScore);
        
        $employee->save();
    }

    /**
     * Calculate for all employees.
     */
    public function calculateForAll(): void
    {
        $employees = TeamMember::all();
        foreach ($employees as $employee) {
            $this->calculateForEmployee($employee);
        }
    }

    /**
     * Map a 0-100 score to a letter grade.
     */
    public static function getGradeFromScore(int $score): string
    {
        if ($score >= 90) return 'A+';
        if ($score >= 80) return 'A';
        if ($score >= 60) return 'B';
        if ($score >= 40) return 'C';
        return 'PIP';
    }
}
