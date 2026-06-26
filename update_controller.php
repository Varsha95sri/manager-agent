<?php

$file = 'app/Http/Controllers/ManagerAgentController.php';
$content = file_get_contents($file);

// 1. Fix AJAX response to include attendance
$ajaxSearch = "'workload' => [
                        'labels' => \$workloadLabels,
                        'data' => \$workloadData
                    ]
                ]);";
$ajaxReplace = "'workload' => [
                        'labels' => \$workloadLabels,
                        'data' => \$workloadData
                    ],
                    'attendance' => [
                        'present' => \$presentPct,
                        'late' => \$latePct,
                        'absent' => \$absentPct
                    ]
                ]);";
$content = str_replace($ajaxSearch, $ajaxReplace, $content);

// 2. Fix Workload grouping (Top 5 + Others) for both AJAX and Initial Render
// Initial render replacement:
$workloadInitSearch = "\$workloadLabelsInit = [];
            \$workloadDataInit = [];
            \$teamsInit = \App\Models\Team::with('teamMembers')->get();
            foreach(\$teamsInit as \$team) {
                \$memberIds = \$team->teamMembers->pluck('id');
                \$pendingCount = \App\Models\Task::whereIn('team_member_id', \$memberIds)
                                    ->where('status', 'pending')
                                    ->whereBetween('due_date', [\$startDate, \$endDate])
                                    ->count();
                if (\$pendingCount > 0) {
                    \$workloadLabelsInit[] = \$team->name;
                    \$workloadDataInit[] = \$pendingCount;
                }
            }
            if (empty(\$workloadLabelsInit)) {
                \$workloadLabelsInit = ['No Pending Tasks'];
                \$workloadDataInit = [1];
            }";

$workloadInitReplace = "\$teamPendingCountsInit = [];
            \$teamsInit = \App\Models\Team::with('teamMembers')->get();
            foreach(\$teamsInit as \$team) {
                \$memberIds = \$team->teamMembers->pluck('id');
                \$pendingCount = \App\Models\Task::whereIn('team_member_id', \$memberIds)
                                    ->where('status', 'pending')
                                    ->whereBetween('due_date', [\$startDate, \$endDate])
                                    ->count();
                if (\$pendingCount > 0) {
                    \$teamPendingCountsInit[\$team->name] = \$pendingCount;
                }
            }
            arsort(\$teamPendingCountsInit);
            \$workloadLabelsInit = [];
            \$workloadDataInit = [];
            \$otherCountInit = 0;
            \$countInit = 0;
            foreach (\$teamPendingCountsInit as \$tName => \$pCount) {
                if (\$countInit < 5) {
                    \$workloadLabelsInit[] = \$tName;
                    \$workloadDataInit[] = \$pCount;
                } else {
                    \$otherCountInit += \$pCount;
                }
                \$countInit++;
            }
            if (\$otherCountInit > 0) {
                \$workloadLabelsInit[] = 'Other Teams';
                \$workloadDataInit[] = \$otherCountInit;
            }
            if (empty(\$workloadLabelsInit)) {
                \$workloadLabelsInit = ['No Pending Tasks'];
                \$workloadDataInit = [1];
            }";
$content = str_replace($workloadInitSearch, $workloadInitReplace, $content);

// AJAX render replacement:
$workloadAjaxSearch = "\$workloadLabels = [];
                \$workloadData = [];
                \$teams = \App\Models\Team::with('teamMembers')->get();
                foreach(\$teams as \$team) {
                    \$memberIds = \$team->teamMembers->pluck('id');
                    \$pendingCount = \App\Models\Task::whereIn('team_member_id', \$memberIds)
                                        ->where('status', 'pending')
                                        ->whereBetween('due_date', [\$startDate, \$endDate])
                                        ->count();
                    if (\$pendingCount > 0) {
                        \$workloadLabels[] = \$team->name;
                        \$workloadData[] = \$pendingCount;
                    }
                }
                if (empty(\$workloadLabels)) {
                    \$workloadLabels = ['No Pending Tasks'];
                    \$workloadData = [1];
                }";

$workloadAjaxReplace = "\$teamPendingCounts = [];
                \$teams = \App\Models\Team::with('teamMembers')->get();
                foreach(\$teams as \$team) {
                    \$memberIds = \$team->teamMembers->pluck('id');
                    \$pendingCount = \App\Models\Task::whereIn('team_member_id', \$memberIds)
                                        ->where('status', 'pending')
                                        ->whereBetween('due_date', [\$startDate, \$endDate])
                                        ->count();
                    if (\$pendingCount > 0) {
                        \$teamPendingCounts[\$team->name] = \$pendingCount;
                    }
                }
                arsort(\$teamPendingCounts);
                \$workloadLabels = [];
                \$workloadData = [];
                \$otherCount = 0;
                \$count = 0;
                foreach (\$teamPendingCounts as \$tName => \$pCount) {
                    if (\$count < 5) {
                        \$workloadLabels[] = \$tName;
                        \$workloadData[] = \$pCount;
                    } else {
                        \$otherCount += \$pCount;
                    }
                    \$count++;
                }
                if (\$otherCount > 0) {
                    \$workloadLabels[] = 'Other Teams';
                    \$workloadData[] = \$otherCount;
                }
                if (empty(\$workloadLabels)) {
                    \$workloadLabels = ['No Pending Tasks'];
                    \$workloadData = [1];
                }";
$content = str_replace($workloadAjaxSearch, $workloadAjaxReplace, $content);

file_put_contents($file, $content);
echo "Updated ManagerAgentController.php successfully!\n";
