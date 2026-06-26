<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MassiveDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Scale: 2000 Employees, ~52k Tasks, ~52k Commits, ~52k Attendances. Total ~180k rows.
     */
    public function run(): void
    {
        $this->command->info('Starting Massive Demo Seeder (approx 180,000 records).');
        
        DB::disableQueryLog();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        $this->command->info('Clearing old data...');
        DB::table('departments')->truncate();
        DB::table('skills')->truncate();
        DB::table('teams')->truncate();
        DB::table('team_members')->truncate();
        DB::table('projects')->truncate();
        DB::table('repositories')->truncate();
        DB::table('tasks')->truncate();
        DB::table('task_team_member')->truncate();
        DB::table('commits')->truncate();
        DB::table('attendance_logs')->truncate();
        DB::table('leave_requests')->truncate();
        DB::table('meeting_notes')->truncate();

        $now = now()->toDateTimeString();

        // 1. Departments & Teams & Skills
        $this->command->info('Seeding Departments, Teams, and Skills...');
        $depts = [];
        for ($i=1; $i<=10; $i++) {
            $depts[] = ['id' => $i, 'name' => "Department $i", 'created_at' => $now, 'updated_at' => $now];
        }
        DB::table('departments')->insert($depts);

        $teams = [];
        for ($i=1; $i<=50; $i++) {
            $teams[] = ['id' => $i, 'name' => "Team $i", 'slug' => "team-$i", 'created_at' => $now, 'updated_at' => $now];
        }
        DB::table('teams')->insert($teams);

        $skills = [];
        for ($i=1; $i<=20; $i++) {
            $skills[] = ['id' => $i, 'name' => "Skill $i", 'created_at' => $now, 'updated_at' => $now];
        }
        DB::table('skills')->insert($skills);

        // 2. Employees (2,000)
        $this->command->info('Seeding 2,000 Employees...');
        $employees = [];
        for ($i=1; $i<=2000; $i++) {
            $employees[] = [
                'id' => $i,
                'name' => "Employee $i",
                'email' => "emp$i@example.com",
                'role' => $i % 10 == 0 ? 'Manager' : 'Developer',
                'team_id' => rand(1, 50),
                'department_id' => rand(1, 10),
                'performance_score' => rand(60, 100),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($employees) === 500) {
                DB::table('team_members')->insert($employees);
                $employees = [];
            }
        }
        if (count($employees) > 0) DB::table('team_members')->insert($employees);

        // 3. Projects and Repositories (200)
        $this->command->info('Seeding 200 Projects & Repos...');
        $projects = [];
        $repos = [];
        for ($i=1; $i<=200; $i++) {
            $projects[] = [
                'id' => $i,
                'name' => "Enterprise Project $i",
                'status' => 'active',
                'deadline' => Carbon::now()->addDays(rand(10, 100))->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $repos[] = [
                'id' => $i,
                'project_id' => $i,
                'name' => "repo-project-$i",
                'url' => "https://gitlab.com/org/repo-project-$i",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('projects')->insert($projects);
        DB::table('repositories')->insert($repos);

        // 4. Time Series Data (June 1 - June 26)
        $this->command->info('Seeding Time Series Data (Tasks, Commits, Attendance) from June 1 to June 26...');
        
        $startDate = Carbon::create(2026, 6, 1);
        $endDate = Carbon::create(2026, 6, 26);
        
        $tasksCount = 0;
        
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->toDateString();
            $isWeekend = $currentDate->isWeekend();
            
            $tasks = [];
            $commits = [];
            $attendances = [];
            
            // Loop through some employees each day (not all 2000, maybe just a subset so total = ~180k)
            // 26 days * 2000 = 52k per table. We want ~52k. Let's do all 2000 per day.
            for ($e=1; $e<=2000; $e++) {
                
                // Attendance
                if (!$isWeekend) {
                    $status = rand(1, 100) > 5 ? 'present' : 'absent';
                    $attendances[] = [
                        'team_member_id' => $e,
                        'date' => $dateStr,
                        'status' => $status,
                        'check_in' => $status == 'present' ? '09:00:00' : null,
                        'check_out' => $status == 'present' ? '18:00:00' : null,
                        'created_at' => $dateStr . ' 09:00:00',
                        'updated_at' => $dateStr . ' 18:00:00',
                    ];
                }

                // Tasks (skip weekends usually)
                if (!$isWeekend && rand(1, 10) > 2) {
                    $tasksCount++;
                    $tasks[] = [
                        'id' => $tasksCount,
                        'team_member_id' => $e,
                        'title' => "Task $tasksCount for Emp $e",
                        'status' => rand(1, 100) > 20 ? 'completed' : 'in_progress',
                        'due_date' => $dateStr,
                        'created_at' => $dateStr . ' 09:00:00',
                        'updated_at' => $dateStr . ' 18:00:00',
                        'completed_at' => rand(1, 100) > 20 ? $dateStr . ' 17:30:00' : null,
                    ];
                    
                    // Task assignments pivot
                    DB::table('task_team_member')->insert([
                        'task_id' => $tasksCount,
                        'team_member_id' => $e
                    ]);
                }

                // Commits
                if (!$isWeekend && rand(1, 10) > 3) {
                    $commits[] = [
                        'employee_id' => $e,
                        'project_id' => rand(1, 200),
                        'commit_sha' => Str::random(40),
                        'message' => "Update files for day $dateStr",
                        'commit_url' => 'https://gitlab.com',
                        'committed_at' => $dateStr . ' ' . rand(10, 17) . ':00:00',
                        'created_at' => $dateStr . ' 10:00:00',
                        'updated_at' => $dateStr . ' 10:00:00',
                    ];
                }
            }

            // Chunk Inserts for the day
            foreach (array_chunk($attendances, 500) as $chunk) { DB::table('attendance_logs')->insert($chunk); }
            foreach (array_chunk($tasks, 500) as $chunk) { DB::table('tasks')->insert($chunk); }
            foreach (array_chunk($commits, 500) as $chunk) { DB::table('commits')->insert($chunk); }

            $this->command->info("Seeded $dateStr ...");
            $currentDate->addDay();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::enableQueryLog();

        $this->command->info('Massive Demo Seeding Completed!');
    }
}
