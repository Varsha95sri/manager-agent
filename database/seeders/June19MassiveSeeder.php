<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class June19MassiveSeeder extends Seeder
{
    public function run(): void
    {
        $startTime = microtime(true);
        DB::connection()->disableQueryLog();

        $this->command->info('Starting massive seed for June 19th...');

        $date = '2026-06-19';
        $now = now()->toDateTimeString();

        // 1. COMMITS (10,000)
        $this->command->info('Seeding 10,000 commits for June 19...');
        $commitBuf = [];
        $messages  = [
            'feat: add new feature #%d',
            'fix: resolve bug in module #%d',
            'refactor: clean up code block #%d',
            'test: add unit tests for #%d',
            'docs: update API documentation #%d',
            'chore: upgrade dependencies #%d',
            'perf: optimize query performance #%d',
            'style: format code per lint rules #%d',
            'ci: update pipeline config #%d',
            'build: bump version to #%d',
        ];
        
        $maxCommitId = DB::table('git_commits')->max('id') ?? 0;
        for ($c = 1; $c <= 10000; $c++) {
            $commitId = $maxCommitId + $c;
            $memberId = (($commitId * 7) % 10000) + 1;
            $repoId   = (($commitId * 13) % 1000) + 1;
            $hour     = 8 + ($commitId % 10);
            $min      = $commitId % 60;
            
            $commitBuf[] = [
                'team_member_id'  => $memberId,
                'repository_id'   => $repoId,
                'commit_hash'     => md5("chash-19-{$commitId}"),
                'message'         => sprintf($messages[$commitId % count($messages)], $commitId),
                'repository_name' => "org/repo-" . ($repoId),
                'committed_at'    => Carbon::parse($date)->setTime($hour, $min, 0)->toDateTimeString(),
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
            if (count($commitBuf) >= 2000) { DB::table('git_commits')->insert($commitBuf); $commitBuf = []; }
        }
        if ($commitBuf) { DB::table('git_commits')->insert($commitBuf); }

        // 2. TASKS (10,000)
        $this->command->info('Seeding 10,000 tasks for June 19...');
        $statusWheel = array_merge(
            array_fill(0, 65, 'completed'),
            array_fill(0, 25, 'in_progress'),
            array_fill(0, 10, 'pending')
        );
        $wheelSize = count($statusWheel);
        $taskBuf = [];
        $maxTaskId = DB::table('tasks')->max('id') ?? 0;
        $taskTitles = [
            'Implement feature module #%d',
            'Review pull request #%d',
            'Fix production bug #%d',
            'Write unit tests for #%d',
            'Update documentation #%d',
            'Refactor legacy code #%d',
            'Deploy service version #%d',
            'Conduct code review #%d',
            'Optimize DB queries #%d',
            'Setup CI/CD pipeline #%d',
        ];
        
        for ($t = 1; $t <= 10000; $t++) {
            $taskId = $maxTaskId + $t;
            $memberId  = (($taskId * 11) % 10000) + 1;
            $projectId = (($taskId * 17) % 1000) + 1;
            $taskBuf[] = [
                'team_member_id' => $memberId,
                'project_id'     => $projectId,
                'title'          => sprintf($taskTitles[$taskId % count($taskTitles)], $taskId),
                'status'         => $statusWheel[$taskId % $wheelSize],
                'due_date'       => $date,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
            if (count($taskBuf) >= 2000) { DB::table('tasks')->insert($taskBuf); $taskBuf = []; }
        }
        if ($taskBuf) { DB::table('tasks')->insert($taskBuf); }

        // 3. TASK_TEAM_MEMBER PIVOT (1,000 pairs for the 19th)
        $this->command->info('Seeding group task pivots for June 19...');
        $pivotBuf = [];
        for ($i = 1; $i <= 1000; $i++) {
            $tId = $maxTaskId + $i;
            $m1 = (($i * 3)  % 10000) + 1;
            $m2 = (($i * 19) % 10000) + 1;
            if ($m1 === $m2) $m2 = ($m2 % 10000) + 1;
            $pivotBuf[] = ['task_id' => $tId, 'team_member_id' => $m1, 'created_at' => $now, 'updated_at' => $now];
            $pivotBuf[] = ['task_id' => $tId, 'team_member_id' => $m2, 'created_at' => $now, 'updated_at' => $now];
            if (count($pivotBuf) >= 2000) { DB::table('task_team_member')->insert($pivotBuf); $pivotBuf = []; }
        }
        if ($pivotBuf) { DB::table('task_team_member')->insert($pivotBuf); }

        // 4. ATTENDANCE (10,000)
        $this->command->info('Seeding 10,000 attendance logs for June 19...');
        // First delete any existing attendance for June 19 so we don't have duplicates
        DB::table('attendance_logs')->where('date', $date)->delete();
        
        $attStatuses = array_merge(
            array_fill(0, 88, 'present'),
            array_fill(0, 7, 'late'),
            array_fill(0, 5, 'absent')
        );
        $attSize = count($attStatuses);
        $attBuf  = [];
        $attId   = DB::table('attendance_logs')->max('id') ?? 0;
        
        for ($m = 1; $m <= 10000; $m++) {
            $attId++;
            $status  = $attStatuses[$attId % $attSize];
            $checkIn = $status !== 'absent' ? sprintf('09:%02d:00', $m % 60) : null;
            $attBuf[] = [
                'team_member_id' => $m,
                'date'           => $date,
                'status'         => $status,
                'check_in'       => $checkIn,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
            if (count($attBuf) >= 2000) { DB::table('attendance_logs')->insert($attBuf); $attBuf = []; }
        }
        if ($attBuf) { DB::table('attendance_logs')->insert($attBuf); }

        // 5. MEETING NOTES (3)
        $this->command->info('Seeding 3 meeting notes for June 19...');
        $meetingTypes = [
            ['Daily Standup', 'Quick sync on blockers and daily goals for June 19th. Team is aligned.', '09:00'],
            ['Code Review Session', 'Peer review of recent PRs on June 19th. Discussed refactoring.', '14:00'],
            ['Sprint Check-in', 'Mid-sprint review to ensure trajectory for June 19 targets.', '16:00'],
        ];
        
        $meetingNotes   = [];
        $meetingPivots  = [];
        
        foreach ($meetingTypes as $mt) {
            $meetingId = DB::table('meeting_notes')->insertGetId([
                'title'        => $mt[0] . ' — Jun 19',
                'notes'        => $mt[1],
                'meeting_date' => $date,
                'meeting_time' => $mt[2],
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
            
            for ($p = 0; $p < 5; $p++) {
                $memberId = (($meetingId * 31 + $p * 97) % 10000) + 1;
                $meetingPivots[] = [
                    'meeting_note_id' => $meetingId,
                    'team_member_id'  => $memberId,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }
        }
        DB::table('meeting_note_team_member')->insert($meetingPivots);

        // 6. PERFORMANCE REPORT (1)
        $this->command->info('Seeding performance report for June 19...');
        $topPerf = ['Rahul Kumar 1', 'Neha Patel 45', 'Arjun Singh 88', 'Priya Sharma 120', 'Pooja Nair 500'];
        $attReq  = ['Vijay Roy 9999 — missed 3 deadlines', 'Sanjay Prasad 8801 — low commit count'];
        $risks   = ['CI pipeline intermittently failing on staging', 'Test coverage below 60% threshold'];
        
        $fullReport = "## Daily Performance Report — {$date}\n\n"
            . "**Productivity Index:** 94% (Very High)\n\n"
            . "**Total Commits Today:** 10000\n\n"
            . "**Top Performers:**\n" . implode("\n", array_map(fn($p) => "- {$p}", $topPerf)) . "\n\n"
            . "**Needs Attention:**\n" . implode("\n", array_map(fn($a) => "- {$a}", $attReq)) . "\n\n"
            . "**Identified Risks:**\n" . implode("\n", array_map(fn($r) => "- {$r}", $risks));

        DB::table('performance_reports')->insert([
            'report_date'       => $date,
            'team_productivity' => 94,
            'top_performers'    => json_encode($topPerf),
            'attention_required'=> json_encode($attReq),
            'risks'             => json_encode($risks),
            'full_report'       => $fullReport,
            'created_at'        => $now,
            'updated_at'        => $now,
        ]);

        $elapsed = round(microtime(true) - $startTime, 2);
        $this->command->info("✅ Massive seeding for June 19 completed in {$elapsed} seconds!");
    }
}
