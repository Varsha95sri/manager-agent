<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Task;
use App\Models\Commit;
use App\Models\AttendanceLog;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DummyTeamProductivitySeeder extends Seeder
{
    public function run()
    {
        // Create a Dummy Team
        $team = Team::firstOrCreate([
            'slug' => 'demo-productivity-team'
        ], [
            'name' => 'Demo Productivity Team',
            'description' => 'A demo team created to showcase productivity metrics and leaderboard functionality.',
            'status' => 'Active',
            'icon_bg' => 'primary'
        ]);

        // Get 3 random employees (or first 3)
        $employees = TeamMember::take(3)->get();

        if ($employees->count() == 0) {
            $this->command->info("No employees found to add to the dummy team.");
            return;
        }

        foreach ($employees as $employee) {
            // Assign employee to the Dummy Team
            $employee->team_id = $team->id;
            $employee->save();

            // Add dummy tasks (Completed) to boost Task Completion & Productivity
            for ($i = 0; $i < 5; $i++) {
                Task::create([
                    'title' => 'Dummy Task ' . Str::random(5),
                    'status' => 'completed',
                    'team_member_id' => $employee->id,
                    'due_date' => Carbon::now()->addDays(rand(1, 5)),
                    'priority' => 'High',
                    'project_id' => 1,
                ]);
            }

            $project = \App\Models\Project::first();
            // Add dummy commits to boost GitLab & Code Quality
            for ($i = 0; $i < 3; $i++) {
                Commit::create([
                    'employee_id' => $employee->id,
                    'commit_sha' => 'sha' . Str::random(10),
                    'message' => 'Implemented feature ' . Str::random(5),
                    'project_id' => $project ? $project->id : 1,
                    'committed_at' => Carbon::now()->subDays(rand(1, 5)),
                ]);
            }

            // Add dummy attendance to boost Attendance Score
            for ($i = 0; $i < 5; $i++) {
                AttendanceLog::create([
                    'team_member_id' => $employee->id,
                    'date' => Carbon::now()->subDays($i)->format('Y-m-d'),
                    'status' => 'Present',
                    'check_in' => '09:00:00',
                    'check_out' => '17:00:00',
                ]);
            }
        }

        $this->command->info("Dummy Team created and populated with tasks, commits, and attendance to show productivity!");
    }
}
