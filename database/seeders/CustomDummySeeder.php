<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CustomDummySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting Custom Dummy Seeder: 1000 employees, 400 projects (June 1 - June 18).');
        
        DB::disableQueryLog();
        // Do not truncate, to preserve existing user data.
        
        $now = now()->toDateTimeString();

        // Ensure we have at least one department and team if none exist
        $deptId = DB::table('departments')->insertGetId(['name' => 'General Dept', 'created_at' => $now, 'updated_at' => $now]);
        $teamId = DB::table('teams')->insertGetId(['name' => 'General Team', 'slug' => 'general-team', 'created_at' => $now, 'updated_at' => $now]);

        // Get max IDs to avoid collision if we use custom IDs, or just rely on auto-increment.
        
        // 1. Add 400 Projects
        $this->command->info('Seeding 400 Projects...');
        $projects = [];
        $repos = [];
        for ($i=1; $i<=400; $i++) {
            $projects[] = [
                'name' => "Custom Project $i",
                'status' => 'active',
                'deadline' => Carbon::now()->addDays(rand(10, 100))->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // Chunk insert projects
        foreach (array_chunk($projects, 200) as $chunk) {
            DB::table('projects')->insert($chunk);
        }
        
        // Fetch project IDs
        $projectIds = DB::table('projects')->pluck('id')->toArray();

        // 2. Add 1000 Employees
        $this->command->info('Seeding 1000 Employees...');
        $employees = [];
        $users = [];
        for ($i=1; $i<=1000; $i++) {
            $uniqueHash = Str::random(8);
            $email = "dummy_emp_{$i}_{$uniqueHash}@example.com";
            
            $users[] = [
                'name' => "Dummy Employee $i",
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'Employee',
                'created_at' => $now,
                'updated_at' => $now,
            ];
            
            $employees[] = [
                'name' => "Dummy Employee $i",
                'email' => $email,
                'role' => 'Developer',
                'team_id' => $teamId,
                'department_id' => $deptId,
                'performance_score' => rand(50, 100),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($users, 250) as $chunk) {
            DB::table('users')->insert($chunk);
        }
        foreach (array_chunk($employees, 250) as $chunk) {
            DB::table('team_members')->insert($chunk);
        }

        // Fetch employee IDs
        $employeeIds = DB::table('team_members')->pluck('id')->toArray();
        $totalEmployees = count($employeeIds);

        // 3. Time Series Data (June 1 - June 18)
        $this->command->info('Seeding Time Series Data (June 1 - June 18)...');
        $startDate = Carbon::create(2026, 6, 1);
        $endDate = Carbon::create(2026, 6, 18);
        
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->toDateString();
            $isWeekend = $currentDate->isWeekend();
            
            $attendances = [];
            $tasks = [];
            $commits = [];
            
            // For a subset to keep performance reasonable, maybe 30% of employees each day do something
            $dailyEmployees = array_rand(array_flip($employeeIds), min(300, $totalEmployees));
            if (!is_array($dailyEmployees)) $dailyEmployees = [$dailyEmployees];

            foreach ($dailyEmployees as $eId) {
                // Attendance
                if (!$isWeekend) {
                    $status = rand(1, 100) > 5 ? 'Present' : 'Absent';
                    $attendances[] = [
                        'team_member_id' => $eId,
                        'date' => $dateStr,
                        'status' => $status,
                        'check_in' => $status == 'Present' ? '09:00:00' : null,
                        'check_out' => $status == 'Present' ? '18:00:00' : null,
                        'created_at' => $dateStr . ' 09:00:00',
                        'updated_at' => $dateStr . ' 18:00:00',
                    ];
                }

                // Tasks
                if (!$isWeekend && rand(1, 10) > 5) {
                    $tasks[] = [
                        'team_member_id' => $eId,
                        'title' => "Task for Emp $eId on $dateStr",
                        'status' => rand(1, 100) > 30 ? 'Completed' : 'In Progress',
                        'due_date' => $dateStr,
                        'created_at' => $dateStr . ' 09:00:00',
                        'updated_at' => $dateStr . ' 18:00:00',
                        'completed_at' => rand(1, 100) > 30 ? $dateStr . ' 17:30:00' : null,
                    ];
                }

                // Commits
                if (!$isWeekend && rand(1, 10) > 6) {
                    $commits[] = [
                        'employee_id' => $eId,
                        'project_id' => $projectIds[array_rand($projectIds)],
                        'commit_sha' => Str::random(40),
                        'message' => "Update files on $dateStr",
                        'commit_url' => 'https://gitlab.com',
                        'committed_at' => $dateStr . ' ' . rand(10, 17) . ':00:00',
                        'created_at' => $dateStr . ' 10:00:00',
                        'updated_at' => $dateStr . ' 10:00:00',
                    ];
                }
            }
            
            foreach (array_chunk($attendances, 500) as $chunk) { DB::table('attendance_logs')->insert($chunk); }
            foreach (array_chunk($tasks, 500) as $chunk) { DB::table('tasks')->insert($chunk); }
            foreach (array_chunk($commits, 500) as $chunk) { DB::table('commits')->insert($chunk); }

            $this->command->info("Seeded data for $dateStr");
            $currentDate->addDay();
        }

        DB::enableQueryLog();
        $this->command->info('Custom Seeding Completed!');
    }
}
