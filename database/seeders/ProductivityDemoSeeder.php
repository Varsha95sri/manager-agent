<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeamMember;
use App\Models\Task;
use App\Models\GitCommit;
use App\Models\AttendanceLog;
use App\Models\MeetingNote;
use App\Models\PerformanceReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductivityDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $todayStr = Carbon::today()->toDateString();

        // 1. Clear all existing records cleanly to avoid duplicate/inconsistent data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('task_team_member')->truncate();
        DB::table('meeting_note_team_member')->truncate();
        Task::truncate();
        GitCommit::truncate();
        AttendanceLog::truncate();
        MeetingNote::truncate();
        PerformanceReport::truncate();
        TeamMember::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Define 20 team members with names, roles, and legacy fields
        $membersData = [
            [
                'name' => 'Rahul Kumar',
                'email' => 'rahul@gmail.com',
                'role' => 'Senior Frontend Developer',
                'github_id' => 'rahul-dev',
                'task_title' => 'Optimize Dashboard Charts',
                'task_commit' => 'perf: optimized charts rendering speed',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '09:00:00'
            ],
            [
                'name' => 'Arjun Singh',
                'email' => 'arjun@gmail.com',
                'role' => 'Senior Backend Developer',
                'github_id' => 'arjun-dev',
                'task_title' => 'Integrate Caching Layer',
                'task_commit' => 'feat: redis cache for report endpoints',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '08:55:00'
            ],
            [
                'name' => 'Anushka Sharma',
                'email' => 'anushka@gmail.com',
                'role' => 'QA Lead',
                'github_id' => 'anushka-qa',
                'task_title' => 'E2E Testing of Group Tasks',
                'task_commit' => 'test: add group task e2e specs',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '09:02:00'
            ],
            [
                'name' => 'Shipra Verma',
                'email' => 'shipra@gmail.com',
                'role' => 'Backend Developer',
                'github_id' => 'shipra-dev',
                'task_title' => 'Design Group Database Migrations',
                'task_commit' => 'db: migrations for task_team_member pivot',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '09:00:00'
            ],
            [
                'name' => 'Amit Patel',
                'email' => 'amit@gmail.com',
                'role' => 'UI/UX Designer',
                'github_id' => 'amit-design',
                'task_title' => 'Create Glassmorphism Layout Specs',
                'task_commit' => 'design: update dashboard cards styles',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '08:45:00'
            ],
            [
                'name' => 'Varun Joshi',
                'email' => 'varun@gmail.com',
                'role' => 'DevOps Engineer',
                'github_id' => 'varun-ops',
                'task_title' => 'Configure GitHub Actions Pipeline',
                'task_commit' => 'ci: setup automatic linting step',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '09:00:00'
            ],
            [
                'name' => 'Neha Gupta',
                'email' => 'neha@gmail.com',
                'role' => 'Fullstack Developer',
                'github_id' => 'neha-code',
                'task_title' => 'Refactor Authentication Forms',
                'task_commit' => 'refactor: simplify login error checks',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '08:50:00'
            ],
            [
                'name' => 'Priya Nair',
                'email' => 'priya@gmail.com',
                'role' => 'Product Manager',
                'github_id' => 'priya-pm',
                'task_title' => 'Draft Product Spec V2',
                'task_commit' => 'docs: update project roadmap requirements',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '08:40:00'
            ],
            [
                'name' => 'Kabir Das',
                'email' => 'kabir@gmail.com',
                'role' => 'Security Specialist',
                'github_id' => 'kabir-sec',
                'task_title' => 'Conduct Security Audit on API Auth',
                'task_commit' => 'security: patch potential CSRF loopholes',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '09:05:00'
            ],
            [
                'name' => 'Riya Sen',
                'email' => 'riya@gmail.com',
                'role' => 'Database Administrator',
                'github_id' => 'riya-db',
                'task_title' => 'Optimize DB Indexes for Logs',
                'task_commit' => 'db: added index on attendance log date',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '09:00:00'
            ],
            [
                'name' => 'Sameer Shah',
                'email' => 'sameer@gmail.com',
                'role' => 'Android Developer',
                'github_id' => 'sameer-mobi',
                'task_title' => 'Fix Push Notification Receivers',
                'task_commit' => 'fix: notification callback payload structure',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '09:10:00'
            ],
            [
                'name' => 'Pooja Hegde',
                'email' => 'pooja@gmail.com',
                'role' => 'iOS Developer',
                'github_id' => 'pooja-ios',
                'task_title' => 'Resolve Layout Constraint Warnings',
                'task_commit' => 'ui: fixed constraint overlap on iPhone SE',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '09:00:00'
            ],
            [
                'name' => 'Rohan Mehta',
                'email' => 'rohan@gmail.com',
                'role' => 'Junior Frontend Dev',
                'github_id' => 'rohan-frontend',
                'task_title' => 'Fix Color Contrast Accessibility',
                'task_commit' => 'ui: increase slate text contrast ratios',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '08:58:00'
            ],
            [
                'name' => 'Sneha Reddy',
                'email' => 'sneha@gmail.com',
                'role' => 'Data Analyst',
                'github_id' => 'sneha-data',
                'task_title' => 'Compile Weekly Productivity Sheet',
                'task_commit' => 'docs: aggregate sprint velocity spreadsheets',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '09:00:00'
            ],
            [
                'name' => 'Ishaan Kapoor',
                'email' => 'ishaan@gmail.com',
                'role' => 'Cloud Architect',
                'github_id' => 'ishaan-cloud',
                'task_title' => 'Deploy Staging DB Replication',
                'task_commit' => 'infra: multi-zone read replica replication',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '08:50:00'
            ],
            [
                'name' => 'Tanvi Shah',
                'email' => 'tanvi@gmail.com',
                'role' => 'Technical Writer',
                'github_id' => 'tanvi-docs',
                'task_title' => 'Update Swagger API Reference',
                'task_commit' => 'docs: document group-based task routes',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '09:00:00'
            ],
            [
                'name' => 'Vicky Kaushal',
                'email' => 'vicky@gmail.com',
                'role' => 'System Admin',
                'github_id' => 'vicky-sys',
                'task_title' => 'Patch Linux Kernel Vulnerability',
                'task_commit' => 'sys: install critical kernel security updates',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '09:00:00'
            ],
            [
                'name' => 'Divya Dutta',
                'email' => 'divya@gmail.com',
                'role' => 'Business Analyst',
                'github_id' => 'divya-ba',
                'task_title' => 'Refine Sprint Target Checklist',
                'task_commit' => 'docs: update sprint retrospective targets',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '08:45:00'
            ],
            [
                'name' => 'Yash Gowda',
                'email' => 'yash@gmail.com',
                'role' => 'Junior Backend Dev',
                'github_id' => 'yash-backend',
                'task_title' => 'Add Email Notification Triggers',
                'task_commit' => 'feat: trigger email alert on late attendance',
                'attendance' => 'present',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '09:00:00'
            ],
            [
                'name' => 'Meera Jasmine',
                'email' => 'meera@gmail.com',
                'role' => 'Scrum Master',
                'github_id' => 'meera-scrum',
                'task_title' => 'Resolve Sprint Impediments',
                'task_commit' => 'docs: updated team velocity roadblock board',
                'attendance' => 'late',
                'meeting_date' => $todayStr,
                'meeting_title' => 'Sprint Planning Sync',
                'task_assign_date' => $todayStr,
                'due_date' => $todayStr,
                'login_timing' => '09:15:00'
            ]
        ];

        $members = [];
        foreach ($membersData as $data) {
            $members[] = TeamMember::create($data);
        }

        // 3. Seed Attendance Logs
        foreach ($members as $m) {
            AttendanceLog::create([
                'team_member_id' => $m->id,
                'date' => $todayStr,
                'status' => $m->attendance,
                'check_in' => $m->login_timing,
            ]);
        }

        // 4. Seed Individual Tasks (High completion rate to show high productivity)
        // Assign a completed task to each of the 20 members to establish 100% individual productivity base
        foreach ($members as $m) {
            Task::create([
                'team_member_id' => $m->id,
                'title' => $m->task_title,
                'status' => 'completed',
                'due_date' => $todayStr,
            ]);
        }

        // Add additional tasks (some completed, some pending) to show high but realistic productivity
        // Rahul: 1 completed, 1 in_progress
        $task1 = Task::create([
            'team_member_id' => $members[0]->id,
            'title' => 'Refactor settings sidebar layout',
            'status' => 'completed',
            'due_date' => $todayStr,
        ]);
        // Sync to pivot
        $task1->teamMembers()->sync([$members[0]->id]);

        $task2 = Task::create([
            'team_member_id' => $members[0]->id,
            'title' => 'Implement light theme toggle',
            'status' => 'in_progress',
            'due_date' => $todayStr,
        ]);
        $task2->teamMembers()->sync([$members[0]->id]);

        // Arjun: 1 completed
        $task3 = Task::create([
            'team_member_id' => $members[1]->id,
            'title' => 'Optimize database queries',
            'status' => 'completed',
            'due_date' => $todayStr,
        ]);
        $task3->teamMembers()->sync([$members[1]->id]);

        // Sameer: 1 pending
        $task4 = Task::create([
            'team_member_id' => $members[10]->id,
            'title' => 'Investigate Android crash logs',
            'status' => 'pending',
            'due_date' => $todayStr,
        ]);
        $task4->teamMembers()->sync([$members[10]->id]);

        // Sync all default tasks created in loop to pivot table as well
        $allTasks = Task::all();
        foreach ($allTasks as $t) {
            if ($t->teamMembers()->count() === 0) {
                $t->teamMembers()->sync([$t->team_member_id]);
            }
        }

        // 5. Seed Group Tasks to show Group Productivity Analytics
        // Group A: Rahul & Arjun (100% productive - 2 completed group tasks)
        $groupTask1 = Task::create([
            'team_member_id' => $members[0]->id, // Primary Developer: Rahul
            'title' => 'Design API Caching Interface',
            'status' => 'completed',
            'due_date' => $todayStr,
        ]);
        $groupTask1->teamMembers()->sync([$members[0]->id, $members[1]->id]);

        $groupTask2 = Task::create([
            'team_member_id' => $members[0]->id,
            'title' => 'E2E Testing of Report Engine',
            'status' => 'completed',
            'due_date' => $todayStr,
        ]);
        $groupTask2->teamMembers()->sync([$members[0]->id, $members[1]->id]);

        // Group B: Neha & Amit (50% productive - 1 completed, 1 pending)
        $groupTask3 = Task::create([
            'team_member_id' => $members[6]->id, // Primary: Neha
            'title' => 'Responsive Mobile Dashboard Mockups',
            'status' => 'completed',
            'due_date' => $todayStr,
        ]);
        $groupTask3->teamMembers()->sync([$members[6]->id, $members[4]->id]);

        $groupTask4 = Task::create([
            'team_member_id' => $members[6]->id,
            'title' => 'HTML/CSS integration for designer mocks',
            'status' => 'pending',
            'due_date' => $todayStr,
        ]);
        $groupTask4->teamMembers()->sync([$members[6]->id, $members[4]->id]);

        // Group C: Pooja & Rohan & Sameer (100% productive - 1 completed task)
        $groupTask5 = Task::create([
            'team_member_id' => $members[11]->id, // Primary: Pooja
            'title' => 'Cross-Platform Client Authentication spec',
            'status' => 'completed',
            'due_date' => $todayStr,
        ]);
        $groupTask5->teamMembers()->sync([$members[11]->id, $members[12]->id, $members[10]->id]);

        // 6. Seed Git Commits (Active repo commits for all 20 members)
        foreach ($members as $m) {
            GitCommit::create([
                'team_member_id' => $m->id,
                'commit_hash' => substr(md5(uniqid()), 0, 7),
                'message' => $m->task_commit,
                'repository_name' => 'manager-agent',
                'committed_at' => Carbon::now()->subMinutes(rand(10, 480)),
            ]);
            // Rahul and Arjun have extra commits to show high activity
            if ($m->email === 'rahul@gmail.com' || $m->email === 'arjun@gmail.com') {
                for ($j = 1; $j <= 2; $j++) {
                    GitCommit::create([
                        'team_member_id' => $m->id,
                        'commit_hash' => substr(md5(uniqid()), 0, 7),
                        'message' => "refactor: optimize code iteration part $j",
                        'repository_name' => 'manager-agent',
                        'committed_at' => Carbon::now()->subMinutes(rand(10, 480)),
                    ]);
                }
            }
        }

        // 7. Seed Meeting Notes with specific attendees and timing
        $meeting1 = MeetingNote::create([
            'title' => 'Sprint Review and Architecture Sync',
            'notes' => 'Reviewed caching layers and completed group tasks dashboard. Team productivity index is stable at 90%+. High momentum maintained across all divisions.',
            'meeting_date' => $todayStr,
            'meeting_time' => '10:00'
        ]);
        $meeting1->teamMembers()->sync([$members[0]->id, $members[1]->id, $members[2]->id, $members[7]->id, $members[19]->id]);

        $meeting2 = MeetingNote::create([
            'title' => 'UI Re-design Alignment',
            'notes' => 'Aligned on high contrast slate text styling, glassmorphism accent colors, and custom checkbox select lists. Excellent feedback received.',
            'meeting_date' => $todayStr,
            'meeting_time' => '14:30'
        ]);
        $meeting2->teamMembers()->sync([$members[4]->id, $members[6]->id, $members[12]->id]);

        // 8. Generate a high-productivity performance report
        $performerNames = ['Rahul Kumar', 'Arjun Singh', 'Amit Patel'];
        $attentionNames = ['Meera Jasmine (Late Check-in)', 'Sameer Shah (1 Pending Task)'];
        $risks = ['None. Team velocity is optimal and release schedule is on track.'];
        
        $reportContent = "### Executive Performance Summary\n" .
                         "The team has demonstrated outstanding delivery output today. With **20 active developers** checked in, overall task completion rate stands at **92.5%**.\n\n" .
                         "#### Key Accomplishments\n" .
                         "- 20 baseline tasks and 3 group workflows successfully delivered.\n" .
                         "- Integrations for database caching and dashboard widgets completed on schedule.\n" .
                         "- Git commit telemetry shows high active code changes across all frontend and backend repositories.\n\n" .
                         "#### Productivity Index\n" .
                         "Current daily average is calculated at **93%**, which is well above target limits. Zero blockers identified.";

        PerformanceReport::create([
            'report_date' => $todayStr,
            'team_productivity' => 93,
            'top_performers' => $performerNames,
            'attention_required' => $attentionNames,
            'risks' => $risks,
            'full_report' => $reportContent,
        ]);
    }
}
