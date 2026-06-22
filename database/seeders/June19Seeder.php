<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeamMember;
use App\Models\Project;
use App\Models\Commit;
use App\Models\AttendanceLog;
use App\Models\Task;
use App\Models\MeetingNote;
use Carbon\Carbon;
use Illuminate\Support\Str;

class June19Seeder extends Seeder
{
    public function run()
    {
        $date = Carbon::create(2026, 6, 19);

        // Get some employees and projects
        $employees = TeamMember::take(20)->get();
        $projects = Project::take(5)->get();

        if ($employees->isEmpty() || $projects->isEmpty()) {
            $this->command->info('Please ensure there are employees and projects in the database.');
            return;
        }

        // Add Attendance
        foreach ($employees as $emp) {
            AttendanceLog::updateOrCreate(
                ['team_member_id' => $emp->id, 'date' => $date->toDateString()],
                ['status' => rand(1, 10) > 2 ? 'Present' : 'Absent']
            );
        }

        // Add Tasks
        foreach ($employees->take(10) as $emp) {
            Task::create([
                'title' => 'Complete module update ' . Str::random(5),
                'status' => ['pending', 'in_progress', 'completed'][rand(0, 2)],
                'team_member_id' => $emp->id,
                'due_date' => $date->copy()->addDays(rand(1, 5))->toDateString(),
            ]);
        }

        // Add Meeting Note
        MeetingNote::updateOrCreate(
            ['meeting_date' => $date->toDateString()],
            [
                'title' => 'Daily Standup - June 19',
                'notes' => 'Daily Standup Meeting on June 19. Discussed progress on the GitLab integration and project planning.'
            ]
        );

        // Add Commits
        for ($i = 0; $i < 30; $i++) {
            $emp = $employees->random();
            $proj = $projects->random();
            
            Commit::create([
                'project_id' => $proj->id,
                'employee_id' => $emp->id,
                'commit_sha' => Str::random(40),
                'message' => 'Fix issue #' . rand(100, 999) . ' related to ' . Str::random(5),
                'commit_url' => 'https://gitlab.com/dummy/' . Str::random(10) . '/-/commit/' . Str::random(40),
                'committed_at' => $date->copy()->addHours(rand(8, 18))->addMinutes(rand(0, 59)),
            ]);
        }

        $this->command->info('Dummy data for June 19 added successfully.');
    }
}
