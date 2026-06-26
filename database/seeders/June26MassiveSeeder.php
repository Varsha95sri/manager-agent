<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class June26MassiveSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        
        // Disable foreign key checks for faster inserts
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clean tables to prevent duplicates on rerun
        DB::table('projects')->truncate();
        DB::table('team_members')->truncate();
        DB::table('attendance_logs')->truncate();
        DB::table('commits')->truncate();
        DB::table('git_commits')->truncate();
        DB::table('tasks')->truncate();
        DB::table('task_team_member')->truncate();
        
        // 1. Generate 1,000 Projects
        $this->command->info("Generating 1,000 Projects...");
        $projects = [];
        for ($i = 0; $i < 1000; $i++) {
            $projects[] = [
                'name' => $faker->company . ' ' . $faker->catchPhrase,
                'description' => $faker->sentence,
                'status' => $faker->randomElement(['active', 'active', 'active', 'completed', 'on_hold']),
                'progress_percent' => $faker->numberBetween(0, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        foreach (array_chunk($projects, 500) as $chunk) {
            DB::table('projects')->insert($chunk);
        }

        $projectIds = DB::table('projects')->pluck('id')->toArray();

        // 2. Generate 10,000 Team Members
        $this->command->info("Generating 10,000 Team Members...");
        $members = [];
        for ($i = 0; $i < 10000; $i++) {
            $members[] = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'role' => $faker->jobTitle,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        foreach (array_chunk($members, 1000) as $chunk) {
            DB::table('team_members')->insert($chunk);
        }
        $memberIds = DB::table('team_members')->pluck('id')->toArray();
        $totalMembersCount = count($memberIds);
        
        $startDate = Carbon::create(2026, 6, 1);
        $endDate = Carbon::create(2026, 6, 26);
        $days = $startDate->diffInDays($endDate) + 1; // 26 days

        // 3. Generate Attendance Logs
        $this->command->info("Generating 260,000 Attendance Logs...");
        $attendanceChunk = [];
        for ($d = 0; $d < $days; $d++) {
            $currentDate = $startDate->copy()->addDays($d)->toDateString();
            foreach ($memberIds as $mId) {
                $status = $faker->randomElement(['present', 'present', 'present', 'late', 'absent', 'leave']);
                $attendanceChunk[] = [
                    'team_member_id' => $mId,
                    'date' => $currentDate,
                    'status' => $status,
                    'check_in' => $status === 'absent' || $status === 'leave' ? null : $faker->time('H:i:s', '10:00:00'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($attendanceChunk) >= 2000) {
                    DB::table('attendance_logs')->insert($attendanceChunk);
                    $attendanceChunk = [];
                }
            }
        }
        if (count($attendanceChunk) > 0) {
            DB::table('attendance_logs')->insert($attendanceChunk);
        }

        // 4. Generate Commits (10 per project per day = 260,000 commits)
        $this->command->info("Generating 260,000 Commits...");
        $commitChunk = [];
        $gitCommitChunk = [];
        for ($d = 0; $d < $days; $d++) {
            $currentDate = $startDate->copy()->addDays($d);
            foreach ($projectIds as $pId) {
                for ($c = 0; $c < 10; $c++) {
                    $randomMember = $faker->randomElement($memberIds);
                    $hash = substr(hash('sha256', uniqid(mt_rand(), true)), 0, 40);
                    $msg = $faker->sentence;
                    $commitTime = $currentDate->copy()->addMinutes($faker->numberBetween(0, 1400));
                    
                    // Populate commits
                    $commitChunk[] = [
                        'project_id' => $pId,
                        'employee_id' => $randomMember,
                        'commit_sha' => $hash,
                        'message' => $msg,
                        'committed_at' => $commitTime,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Populate git_commits
                    $gitCommitChunk[] = [
                        'team_member_id' => $randomMember,
                        'commit_hash' => $hash,
                        'message' => $msg,
                        'repository_name' => 'Project-Repo-' . $pId,
                        'committed_at' => $commitTime,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($commitChunk) >= 2000) {
                        DB::table('commits')->insert($commitChunk);
                        $commitChunk = [];
                    }
                    if (count($gitCommitChunk) >= 2000) {
                        DB::table('git_commits')->insert($gitCommitChunk);
                        $gitCommitChunk = [];
                    }
                }
            }
        }
        if (count($commitChunk) > 0) DB::table('commits')->insert($commitChunk);
        if (count($gitCommitChunk) > 0) DB::table('git_commits')->insert($gitCommitChunk);

        // 5. Generate Tasks (1 per project per day)
        $this->command->info("Generating 26,000 Tasks...");
        $tasksChunk = [];
        $pivotChunk = [];
        
        $taskCounter = DB::table('tasks')->max('id') ?? 0;

        for ($d = 0; $d < $days; $d++) {
            $currentDate = $startDate->copy()->addDays($d);
            foreach ($projectIds as $pId) {
                $taskCounter++;
                $randomMember = $faker->randomElement($memberIds);
                
                $tasksChunk[] = [
                    'id' => $taskCounter,
                    'team_member_id' => $randomMember,
                    'project_id' => $pId,
                    'title' => $faker->sentence(4),
                    'status' => $faker->randomElement(['pending', 'in_progress', 'completed']),
                    'due_date' => $currentDate->copy()->addDays(rand(1, 5))->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $pivotChunk[] = [
                    'task_id' => $taskCounter,
                    'team_member_id' => $randomMember,
                ];

                if (count($tasksChunk) >= 2000) {
                    DB::table('tasks')->insert($tasksChunk);
                    DB::table('task_team_member')->insert($pivotChunk);
                    $tasksChunk = [];
                    $pivotChunk = [];
                }
            }
        }
        if (count($tasksChunk) > 0) {
            DB::table('tasks')->insert($tasksChunk);
            DB::table('task_team_member')->insert($pivotChunk);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info("Massive June Seeder completed successfully!");
    }
}
