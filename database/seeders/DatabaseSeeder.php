<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TeamMember;
use App\Models\Task;
use App\Models\GitCommit;
use App\Models\AttendanceLog;
use App\Models\MeetingNote;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a test user for login (password is "password" by default in Laravel Breeze)
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Varsha Manager',
                'email' => 'test@example.com',
            ]);
        }

        // 2. Create 5 team members
        $membersData = [
            ['name' => 'Rahul', 'email' => 'rahul@example.com', 'role' => 'Backend Dev'],
            ['name' => 'Arjun', 'email' => 'arjun@example.com', 'role' => 'Frontend Dev'],
            ['name' => 'Priya', 'email' => 'priya@example.com', 'role' => 'Designer'],
            ['name' => 'Shipra', 'email' => 'shipra@example.com', 'role' => 'QA'],
            ['name' => 'Anushka', 'email' => 'anushka@example.com', 'role' => 'DevOps'],
        ];

        $members = [];
        foreach ($membersData as $member) {
            $existing = TeamMember::where('email', $member['email'])->first();
            $members[$member['name']] = $existing ?: TeamMember::create($member);
        }

        $today = Carbon::today()->toDateString();

        // 3. Create Tasks per member (due date today or in future/past)
        $tasksData = [
            'Rahul' => [
                ['title' => 'Implement API endpoints for data feed', 'status' => 'completed', 'due_date' => $today],
                ['title' => 'Optimize Postgres query indices', 'status' => 'completed', 'due_date' => Carbon::yesterday()->toDateString()],
                ['title' => 'Integrate redis cache configuration', 'status' => 'in_progress', 'due_date' => Carbon::tomorrow()->toDateString()],
                ['title' => 'Refactor database migration structures', 'status' => 'pending', 'due_date' => Carbon::today()->addDays(2)->toDateString()],
            ],
            'Arjun' => [
                ['title' => 'Create React Dashboard widgets', 'status' => 'completed', 'due_date' => $today],
                ['title' => 'Build Form component with tabs', 'status' => 'completed', 'due_date' => Carbon::yesterday()->toDateString()],
                ['title' => 'Refactor Inertia State Management', 'status' => 'in_progress', 'due_date' => Carbon::tomorrow()->toDateString()],
                ['title' => 'Fix mobile header navigation layout', 'status' => 'pending', 'due_date' => Carbon::today()->addDays(3)->toDateString()],
            ],
            'Priya' => [
                ['title' => 'Design dashboard visual layout mockups', 'status' => 'completed', 'due_date' => Carbon::yesterday()->toDateString()],
                ['title' => 'Create SVG progress bar graphics', 'status' => 'completed', 'due_date' => $today],
                ['title' => 'Define typography scale and color system', 'status' => 'in_progress', 'due_date' => Carbon::tomorrow()->toDateString()],
            ],
            'Shipra' => [
                ['title' => 'Write backend authentication unit tests', 'status' => 'completed', 'due_date' => Carbon::yesterday()->toDateString()],
                ['title' => 'Perform E2E test verification', 'status' => 'in_progress', 'due_date' => $today],
                ['title' => 'Review API schema documentation edits', 'status' => 'completed', 'due_date' => $today],
                ['title' => 'Setup Cypress visual integration tests', 'status' => 'pending', 'due_date' => Carbon::today()->addDays(2)->toDateString()],
            ],
            'Anushka' => [
                ['title' => 'Configure GitHub actions workflow', 'status' => 'completed', 'due_date' => Carbon::yesterday()->toDateString()],
                ['title' => 'Deploy application to staging server', 'status' => 'completed', 'due_date' => $today],
                ['title' => 'Investigate memory usage logs on AWS', 'status' => 'pending', 'due_date' => Carbon::tomorrow()->toDateString()],
            ],
        ];

        foreach ($tasksData as $name => $tasks) {
            $member = $members[$name];
            foreach ($tasks as $task) {
                $exists = Task::where('team_member_id', $member->id)
                              ->where('title', $task['title'])
                              ->exists();
                if (!$exists) {
                    Task::create(array_merge($task, ['team_member_id' => $member->id]));
                }
            }
        }

        // 4. Create Git Commits per member for today
        $commitsData = [
            'Rahul' => [
                ['commit_hash' => 'd51a672', 'message' => 'feat: endpoint integration and schema definitions', 'committed_at' => Carbon::today()->setTime(10, 15, 0)],
                ['commit_hash' => 'f8a42bc', 'message' => 'fix: index optimization queries', 'committed_at' => Carbon::today()->setTime(14, 30, 0)],
                ['commit_hash' => 'e91244a', 'message' => 'chore: add redis connector configuration', 'committed_at' => Carbon::today()->setTime(17, 45, 0)],
            ],
            'Arjun' => [
                ['commit_hash' => 'a9b231d', 'message' => 'feat: layout framework and dashboard views', 'committed_at' => Carbon::today()->setTime(11, 0, 0)],
                ['commit_hash' => 'c40212f', 'message' => 'style: glassmorphic styles and custom borders', 'committed_at' => Carbon::today()->setTime(16, 20, 0)],
            ],
            'Priya' => [
                ['commit_hash' => 'p10214c', 'message' => 'design: export vectors and style variables', 'committed_at' => Carbon::today()->setTime(12, 10, 0)],
                ['commit_hash' => 'p29381f', 'message' => 'design: circular chart color guides', 'committed_at' => Carbon::today()->setTime(15, 50, 0)],
            ],
            'Shipra' => [
                ['commit_hash' => 's98213e', 'message' => 'test: add database transaction test units', 'committed_at' => Carbon::today()->setTime(10, 0, 0)],
                ['commit_hash' => 's02914a', 'message' => 'test: user flow and role security testing suite', 'committed_at' => Carbon::today()->setTime(15, 30, 0)],
            ],
            'Anushka' => [
                ['commit_hash' => 'd10294b', 'message' => 'ops: actions auto compile config file', 'committed_at' => Carbon::today()->setTime(9, 45, 0)],
                ['commit_hash' => 'd98231c', 'message' => 'ops: dockerize staging node dependencies container', 'committed_at' => Carbon::today()->setTime(14, 15, 0)],
            ],
        ];

        foreach ($commitsData as $name => $commits) {
            $member = $members[$name];
            foreach ($commits as $commit) {
                $exists = GitCommit::where('commit_hash', $commit['commit_hash'])->exists();
                if (!$exists) {
                    GitCommit::create(array_merge($commit, ['team_member_id' => $member->id]));
                }
            }
        }

        // 5. Create Attendance logs for today
        $attendanceData = [
            'Rahul' => ['status' => 'present', 'check_in' => '09:00:00'],
            'Arjun' => ['status' => 'present', 'check_in' => '09:12:00'],
            'Priya' => ['status' => 'late', 'check_in' => '10:05:00'],
            'Shipra' => ['status' => 'present', 'check_in' => '09:28:00'],
            'Anushka' => ['status' => 'absent', 'check_in' => null],
        ];

        foreach ($attendanceData as $name => $att) {
            $member = $members[$name];
            $exists = AttendanceLog::where('team_member_id', $member->id)
                                  ->where('date', $today)
                                  ->exists();
            if (!$exists) {
                AttendanceLog::create(array_merge($att, [
                    'team_member_id' => $member->id,
                    'date' => $today,
                ]));
            }
        }

        // 6. Create Meeting notes for today
        if (!MeetingNote::where('title', 'Daily Team Status Sync')->where('meeting_date', $today)->exists()) {
            MeetingNote::create([
                'title' => 'Daily Team Status Sync',
                'notes' => 'Rahul finished database indexing. Arjun completed widgets layout. Priya updated HSL themes. Shipra flagged that the QA testing sandbox had a 2-hour delay. Anushka reported docker containers are deployed to staging.',
                'meeting_date' => $today,
            ]);
        }

        if (!MeetingNote::where('title', 'Architecture and Integration Sync')->where('meeting_date', $today)->exists()) {
            MeetingNote::create([
                'title' => 'Architecture and Integration Sync',
                'notes' => 'Discussed automated performance report triggers. External reporting service webhook is delayed 2 days. Discussed fallback to local analytics model if API key is not ready.',
                'meeting_date' => $today,
            ]);
        }

        // 7. Automatically generate the daily performance report if it doesn't exist
        if (!\App\Models\PerformanceReport::where('report_date', $today)->exists()) {
            try {
                $reportService = app(\App\Services\ManagerAgentService::class);
                $reportService->generateDailyReport();
            } catch (\Throwable $e) {
                // Silently handle exceptions in seeder
            }
        }
    }
}
