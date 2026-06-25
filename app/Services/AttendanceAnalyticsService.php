<?php

namespace App\Services;

use App\Models\AttendanceLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceAnalyticsService
{
    /**
     * Calculate core attendance metrics for a given team member and date range.
     * If no date range is provided, it calculates for the current month.
     */
    public function getMemberAnalytics($teamMemberId, $startDate = null, $endDate = null): array
    {
        $startDate = $startDate ?: Carbon::now()->startOfMonth()->toDateString();
        $endDate = $endDate ?: Carbon::now()->toDateString();

        $logs = AttendanceLog::where('team_member_id', $teamMemberId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalDays = $logs->count();
        if ($totalDays === 0) {
            return $this->getEmptyAnalytics();
        }

        $presentCount = $logs->whereIn('status', ['present', 'late'])->count();
        $absentCount = $logs->where('status', 'absent')->count();
        $lateCount = $logs->where('status', 'late')->count();
        $leaveCount = $logs->where('status', 'leave')->count();

        // Calculate Attendance Percentage
        // Leaves are not typically counted as working days for percentage, or they can be counted as absent if unplanned.
        // Assuming (Present + Late) / (Total - Planned Leaves). For simplicity:
        $workingDays = $totalDays - $leaveCount;
        $attendancePercentage = $workingDays > 0 ? round(($presentCount / $workingDays) * 100, 1) : 0;

        // Calculate Score (e.g. Present=100, Late=75, Leave=50, Absent=0)
        $score = 0;
        foreach ($logs as $log) {
            if ($log->status === 'present') $score += 100;
            elseif ($log->status === 'late') $score += 75;
            elseif ($log->status === 'leave') $score += 50; // Neutral score for leave
        }
        $attendanceScore = round($score / $totalDays, 1);

        // Leave Utilization
        $leaveUtilization = $logs->where('status', 'leave')->groupBy('leave_type')->map->count()->toArray();

        return [
            'total_days' => $totalDays,
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'leave' => $leaveCount,
            'attendance_percentage' => $attendancePercentage,
            'attendance_score' => $attendanceScore,
            'leave_utilization' => $leaveUtilization,
        ];
    }

    /**
     * Calculate organization-wide attendance trends for a given date range.
     */
    public function getTeamTrends($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ?: Carbon::now()->startOfMonth()->toDateString();
        $endDate = $endDate ?: Carbon::now()->toDateString();

        $query = AttendanceLog::whereBetween('date', [$startDate, $endDate]);

        $totalLogs = $query->count();
        if ($totalLogs === 0) {
            return $this->getEmptyAnalytics();
        }

        $presentCount = (clone $query)->whereIn('status', ['present', 'late'])->count();
        $absentCount = (clone $query)->where('status', 'absent')->count();
        $lateCount = (clone $query)->where('status', 'late')->count();
        $leaveCount = (clone $query)->where('status', 'leave')->count();

        $attendancePercentage = round(($presentCount / $totalLogs) * 100, 1);
        
        // Group by date for absenteeism trend using database aggregation
        $absenteeismTrendQuery = (clone $query)
            ->selectRaw('DATE(date) as formatted_date, count(*) as count')
            ->where('status', 'absent')
            ->groupBy('formatted_date')
            ->get();
            
        $absenteeismTrend = [];
        foreach ($absenteeismTrendQuery as $row) {
            $absenteeismTrend[$row->formatted_date] = $row->count;
        }

        return [
            'total_logs' => $totalLogs,
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'leave' => $leaveCount,
            'average_percentage' => $attendancePercentage,
            'overall_percentage' => $attendancePercentage,
            'total_leaves' => $leaveCount,
            'total_lates' => $lateCount,
            'absenteeism_trend' => $absenteeismTrend,
        ];
    }

    private function getEmptyAnalytics(): array
    {
        return [
            'total_days' => 0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'leave' => 0,
            'attendance_percentage' => 0,
            'attendance_score' => 0,
            'leave_utilization' => [],
            'total_logs' => 0,
            'average_percentage' => 0,
            'overall_percentage' => 0,
            'total_leaves' => 0,
            'total_lates' => 0,
            'absenteeism_trend' => [],
        ];
    }
}
