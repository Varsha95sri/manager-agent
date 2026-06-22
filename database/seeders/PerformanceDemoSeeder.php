<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PerformanceDemoSeeder extends Seeder
{
    /**
     * Optimized bulk seeder.
     * - 10,000 employees
     * - 1,000 projects + 1,000 repositories
     * - 180,000 commits (10 commits × 10 employees × 18 days × 1,000 repos proportionally)
     * - Daily tasks June 1-18 (10,000/day = 180,000 total)
     * - Attendance logs (10,000 × 18 = 180,000)
     * - Meeting notes (3 per day = 54 total, with team members)
     * - Performance reports (18 days, mostly HIGH, occasionally medium/low)
     */
    public function run(): void
    {
        $startTime = microtime(true);

        DB::connection()->disableQueryLog();
        Schema::disableForeignKeyConstraints();

        DB::table('git_commits')->truncate();
        DB::table('tasks')->truncate();
        DB::table('task_team_member')->truncate();
        DB::table('attendance_logs')->truncate();
        DB::table('repositories')->truncate();
        DB::table('projects')->truncate();
        DB::table('team_members')->truncate();
        DB::table('performance_reports')->truncate();
        DB::table('meeting_note_team_member')->truncate();
        DB::table('meeting_notes')->truncate();

        Schema::enableForeignKeyConstraints();

        $this->command->info('Tables cleared. Starting optimized seed...');

        // ─── Date range: June 1–18 ────────────────────────────────────────────────
        $dateRange = [];
        $startDate = Carbon::create(2026, 6, 1);
        for ($i = 0; $i < 18; $i++) {
            $dateRange[] = $startDate->copy()->addDays($i)->toDateString();
        }
        $now = now()->toDateTimeString();

        // ─── 1. TEAM MEMBERS (10,000) ─────────────────────────────────────────────
        $this->command->info('Seeding 10,000 team members...');
        $firstNames = ['Rahul', 'Arjun', 'Anushka', 'Shipra', 'Amit', 'Varun', 'Neha', 'Priya', 'Kabir', 'Riya',
                       'Sameer', 'Pooja', 'Rohan', 'Sneha', 'Ishaan', 'Tanvi', 'Vicky', 'Divya', 'Yash', 'Meera',
                       'Aarav', 'Vihaan', 'Aditya', 'Sai', 'Karan', 'Dev', 'Raj', 'Ravi', 'Sanjay', 'Vijay'];
        $lastNames  = ['Kumar', 'Singh', 'Sharma', 'Verma', 'Patel', 'Joshi', 'Gupta', 'Nair', 'Das', 'Sen',
                       'Shah', 'Hegde', 'Mehta', 'Reddy', 'Kapoor', 'Kaushal', 'Dutta', 'Gowda', 'Pillai', 'Rao',
                       'Iyer', 'Chatterjee', 'Banerjee', 'Mukherjee', 'Bose', 'Ray', 'Roy', 'Prasad', 'Mishra', 'Tiwari'];
        $roles = ['Frontend Developer', 'Backend Developer', 'Fullstack Developer', 'DevOps Engineer', 'QA Engineer', 'UI/UX Designer'];

        $buf = [];
        $fc = count($firstNames); $lc = count($lastNames); $rc = count($roles);
        for ($i = 1; $i <= 10000; $i++) {
            $fn = $firstNames[($i - 1) % $fc];
            $ln = $lastNames[(int)(($i - 1) / $fc) % $lc];
            $buf[] = [
                'id'         => $i,
                'name'       => "{$fn} {$ln} {$i}",
                'email'      => "employee{$i}@company.dev",
                'role'       => $roles[($i - 1) % $rc],
                'gitlab_id'  => "dev_{$i}",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if (count($buf) >= 2000) { DB::table('team_members')->insert($buf); $buf = []; }
        }
        if ($buf) { DB::table('team_members')->insert($buf); $buf = []; }

        // ─── 2. PROJECTS (1,000) + REPOSITORIES (1,000) ──────────────────────────
        $this->command->info('Seeding 1,000 projects & repositories...');
        $projBuf = []; $repoBuf = [];
        $projectTypes = ['Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon', 'Zeta', 'Eta', 'Theta', 'Iota', 'Kappa'];
        for ($i = 1; $i <= 1000; $i++) {
            $type = $projectTypes[($i - 1) % count($projectTypes)];
            $projBuf[] = [
                'id'          => $i,
                'name'        => "Project {$type} {$i}",
                'description' => "Core {$type} development module for sprint cycle {$i}",
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
            $repoBuf[] = [
                'id'         => $i,
                'project_id' => $i,
                'name'       => "org/repo-{$type}-{$i}",
                'url'        => "https://gitlab.com/org/repo-{$type}-{$i}",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if (count($projBuf) >= 500) {
                DB::table('projects')->insert($projBuf);
                DB::table('repositories')->insert($repoBuf);
                $projBuf = []; $repoBuf = [];
            }
        }
        if ($projBuf) { DB::table('projects')->insert($projBuf); DB::table('repositories')->insert($repoBuf); }

        // ─── 3. COMMITS (180,000 total) ───────────────────────────────────────────
        // Strategy: 10,000 commits per day (18 days). Each day each of the 1,000 repos
        // gets ~10 commits from different employees.
        // Daily productivity shapes the exact count:
        // Mostly HIGH (90-99), occasionally MEDIUM (55-75), rarely LOW (35-50).
        $this->command->info('Seeding 180,000 commits (10 per employee/repo, productivity-weighted)...');

        // 18 days of productivity: mostly high, a few medium/low days
        $dailyProductivity = [96, 72, 48, 98, 88, 62, 94, 56, 91, 99, 65, 85, 93, 42, 78, 97, 89, 82];
        $sumProd = array_sum($dailyProductivity); // used to distribute 180,000
        $totalCommits = 180000;
        $commitsPerDay = [];
        $running = 0;
        for ($d = 0; $d < 18; $d++) {
            $dc = (int) round($totalCommits * ($dailyProductivity[$d] / $sumProd));
            $commitsPerDay[$d] = $dc;
            $running += $dc;
        }
        $commitsPerDay[17] += ($totalCommits - $running); // correct rounding remainder

        $commitBuf = [];
        $commitId  = 1;
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
        for ($dayIdx = 0; $dayIdx < 18; $dayIdx++) {
            $date       = $dateRange[$dayIdx];
            $dayCount   = $commitsPerDay[$dayIdx];
            for ($c = 0; $c < $dayCount; $c++) {
                $memberId = (($commitId * 7) % 10000) + 1;
                $repoId   = (($commitId * 13) % 1000) + 1;
                $hour     = 8 + ($commitId % 10);
                $min      = $commitId % 60;
                $commitBuf[] = [
                    'id'              => $commitId,
                    'team_member_id'  => $memberId,
                    'repository_id'   => $repoId,
                    'commit_hash'     => md5("chash-{$commitId}"),
                    'message'         => sprintf($messages[$commitId % count($messages)], $commitId),
                    'repository_name' => "org/repo-" . ($repoId),
                    'committed_at'    => Carbon::parse($date)->setTime($hour, $min, 0)->toDateTimeString(),
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
                $commitId++;
                if (count($commitBuf) >= 2000) { DB::table('git_commits')->insert($commitBuf); $commitBuf = []; }
            }
        }
        if ($commitBuf) { DB::table('git_commits')->insert($commitBuf); }

        // ─── 4. TASKS (180,000 total — 10,000 per day for 18 days) ───────────────
        // High completed ratio, decent in_progress, small pending.
        $this->command->info('Seeding 180,000 daily tasks (June 1-18, 10,000/day)...');
        // Status distribution: 65% completed, 25% in_progress, 10% pending
        $statusWheel = array_merge(
            array_fill(0, 65, 'completed'),
            array_fill(0, 25, 'in_progress'),
            array_fill(0, 10, 'pending')
        );
        $wheelSize = count($statusWheel);
        $taskBuf = [];
        $taskId  = 1;
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
        foreach ($dateRange as $date) {
            for ($t = 0; $t < 10000; $t++) {
                $memberId  = (($taskId * 11) % 10000) + 1;
                $projectId = (($taskId * 17) % 1000) + 1;
                $taskBuf[] = [
                    'id'             => $taskId,
                    'team_member_id' => $memberId,
                    'project_id'     => $projectId,
                    'title'          => sprintf($taskTitles[$taskId % count($taskTitles)], $taskId),
                    'status'         => $statusWheel[$taskId % $wheelSize],
                    'due_date'       => $date,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
                $taskId++;
                if (count($taskBuf) >= 2000) { DB::table('tasks')->insert($taskBuf); $taskBuf = []; }
            }
        }
        if ($taskBuf) { DB::table('tasks')->insert($taskBuf); }

        // ─── 5. TASK_TEAM_MEMBER PIVOT (group tasks — 20,000 pairs) ──────────────
        $this->command->info('Seeding group task pivots...');
        $pivotBuf = [];
        for ($i = 1; $i <= 10000; $i++) {
            $m1 = (($i * 3)  % 10000) + 1;
            $m2 = (($i * 19) % 10000) + 1;
            if ($m1 === $m2) $m2 = ($m2 % 10000) + 1;
            $pivotBuf[] = ['task_id' => $i, 'team_member_id' => $m1, 'created_at' => $now, 'updated_at' => $now];
            $pivotBuf[] = ['task_id' => $i, 'team_member_id' => $m2, 'created_at' => $now, 'updated_at' => $now];
            if (count($pivotBuf) >= 2000) { DB::table('task_team_member')->insert($pivotBuf); $pivotBuf = []; }
        }
        if ($pivotBuf) { DB::table('task_team_member')->insert($pivotBuf); }

        // ─── 6. ATTENDANCE (180,000: 10,000 × 18 days) ───────────────────────────
        // present ~88%, late ~7%, absent ~5%
        $this->command->info('Seeding 180,000 attendance logs...');
        $attStatuses = array_merge(
            array_fill(0, 88, 'present'),
            array_fill(0, 7, 'late'),
            array_fill(0, 5, 'absent')
        );
        $attSize = count($attStatuses);
        $attBuf  = [];
        $attId   = 1;
        foreach ($dateRange as $date) {
            for ($m = 1; $m <= 10000; $m++) {
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
                $attId++;
                if (count($attBuf) >= 2000) { DB::table('attendance_logs')->insert($attBuf); $attBuf = []; }
            }
        }
        if ($attBuf) { DB::table('attendance_logs')->insert($attBuf); }

        // ─── 7. MEETING NOTES (3 per day = 54 total) with team members ────────────
        $this->command->info('Seeding 54 meeting notes (3/day)...');
        $meetingTypes = [
            ['Daily Standup', 'Quick sync on blockers and daily goals. Review sprint backlog and reassign tasks as needed.', '09:00'],
            ['Code Review Session', 'Peer review of recent PRs. Discussed best practices and refactoring opportunities.', '14:00'],
            ['Sprint Planning', 'Planned tasks and milestones for the upcoming sprint. Set velocity targets and priorities.', '16:00'],
        ];
        $meetingNotes   = [];
        $meetingPivots  = [];
        $meetingId      = 1;
        foreach ($dateRange as $date) {
            foreach ($meetingTypes as $mt) {
                $meetingNotes[] = [
                    'id'           => $meetingId,
                    'title'        => $mt[0] . ' — ' . Carbon::parse($date)->format('M d'),
                    'notes'        => $mt[1],
                    'meeting_date' => $date,
                    'meeting_time' => $mt[2],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
                // Assign 5 random employees to each meeting
                for ($p = 0; $p < 5; $p++) {
                    $memberId = (($meetingId * 31 + $p * 97) % 10000) + 1;
                    $meetingPivots[] = [
                        'meeting_note_id' => $meetingId,
                        'team_member_id'  => $memberId,
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ];
                }
                $meetingId++;
            }
        }
        DB::table('meeting_notes')->insert($meetingNotes);
        DB::table('meeting_note_team_member')->insert($meetingPivots);

        // ─── 8. PERFORMANCE REPORTS (18 days, mostly HIGH productivity) ──────────
        $this->command->info('Seeding 18 performance reports with daily productivity index...');

        // Productivity labels: HIGH ≥80, MEDIUM 50-79, LOW <50
        $prodLabels = [
            96 => 'Very High', 72 => 'Medium', 48 => 'Low',
            98 => 'Very High', 88 => 'High',   62 => 'Medium',
            94 => 'Very High', 56 => 'Medium', 91 => 'Very High',
            99 => 'Very High', 65 => 'Medium', 85 => 'High',
            93 => 'Very High', 42 => 'Low',    78 => 'High',
            97 => 'Very High', 89 => 'High',   82 => 'High',
        ];

        $topPerformersPool = [
            'Rahul Kumar 1', 'Neha Patel 45', 'Arjun Singh 88', 'Priya Sharma 120',
            'Kabir Joshi 200', 'Sameer Gupta 350', 'Pooja Nair 500', 'Rohan Verma 750',
            'Anushka Das 90', 'Ishaan Shah 310', 'Divya Reddy 440', 'Yash Mehta 600',
        ];
        $attentionPool = [
            'Vijay Roy 9999 — missed 3 deadlines', 'Sanjay Prasad 8801 — low commit count',
            'Vicky Tiwari 7234 — attendance gap', 'Riya Mishra 6500 — no pushes this week',
            'Dev Iyer 5100 — blocked on PR review', 'Raj Pillai 4300 — delayed deployments',
        ];
        $risksPool = [
            'CI pipeline intermittently failing on staging', 'Database migration pending for repo-Delta-450',
            'API rate limits reached for third-party services', 'Two senior devs on leave simultaneously',
            'Test coverage below 60% threshold in module-Gamma', 'Deployment window conflicts next sprint',
        ];

        $perfReports = [];
        foreach ($dateRange as $dayIdx => $date) {
            $prod = $dailyProductivity[$dayIdx];
            $label = array_values($prodLabels)[$dayIdx];
            $commitCount = $commitsPerDay[$dayIdx];

            // Shuffle top performers & attention lists per day
            $topPerf = array_slice($topPerformersPool, $dayIdx % 6, 5);
            $attReq  = ($prod < 70) ? array_slice($attentionPool, 0, 3) : array_slice($attentionPool, $dayIdx % 3, 2);
            $risks   = ($prod < 60) ? array_slice($risksPool, 0, 3) : array_slice($risksPool, $dayIdx % 4, 2);

            $fullReport = "## Daily Performance Report — {$date}\n\n"
                . "**Productivity Index:** {$prod}% ({$label})\n\n"
                . "**Total Commits Today:** {$commitCount}\n\n"
                . "**Top Performers:**\n" . implode("\n", array_map(fn($p) => "- {$p}", $topPerf)) . "\n\n"
                . "**Needs Attention:**\n" . implode("\n", array_map(fn($a) => "- {$a}", $attReq)) . "\n\n"
                . "**Identified Risks:**\n" . implode("\n", array_map(fn($r) => "- {$r}", $risks));

            $perfReports[] = [
                'report_date'       => $date,
                'team_productivity' => $prod,
                'top_performers'    => json_encode($topPerf),
                'attention_required'=> json_encode($attReq),
                'risks'             => json_encode($risks),
                'full_report'       => $fullReport,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }
        DB::table('performance_reports')->insert($perfReports);

        $elapsed = round(microtime(true) - $startTime, 2);
        $this->command->info("✅ Seeding completed successfully in {$elapsed} seconds!");
        $this->command->info('  • 10,000 employees');
        $this->command->info('  • 1,000 projects + 1,000 repositories');
        $this->command->info("  • 180,000 commits (productivity-weighted, {$totalCommits} total)");
        $this->command->info('  • 180,000 tasks (Jun 1–18, 10k/day, 65% completed)');
        $this->command->info('  • 180,000 attendance logs (88% present, 7% late, 5% absent)');
        $this->command->info('  • 54 meeting notes (3/day × 18 days)');
        $this->command->info('  • 18 performance reports (mostly HIGH productivity)');
    }
}
