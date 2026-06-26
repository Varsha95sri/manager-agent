<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeamMember;
use App\Models\Project;
use App\Models\Department;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JuneDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding June 1 - 25 Demo Data...');

        // 1. Clear relevant tables
        DB::table('commits')->delete();
        DB::table('gitlab_merge_requests')->delete();
        DB::table('gitlab_issues')->delete();
        DB::table('tasks')->delete();
        DB::table('attendance_logs')->delete();
        DB::table('performance_reports')->delete();
        DB::table('project_code_metrics')->delete();
        DB::table('team_members')->delete();
        DB::table('teams')->delete();
        DB::table('departments')->delete();
        DB::table('projects')->delete();

        // 2. Departments & Teams
        $dept1 = Department::create(['name' => 'Engineering', 'description' => 'Software Engineering Dept']);
        $team1 = Team::create(['name' => 'Frontend Ninjas', 'slug' => 'frontend-ninjas', 'description' => 'UI/UX and Frontend']);
        $team2 = Team::create(['name' => 'Backend Wizards', 'slug' => 'backend-wizards', 'description' => 'APIs and Services']);

        // 3. Projects
        $proj1 = Project::create(['name' => 'Manager Agent v2.0', 'description' => 'New HR tool', 'status' => 'active', 'deadline' => '2026-12-31', 'gitlab_project_id' => 101, 'gitlab_repo_url' => 'https://gitlab.com/company/manager-agent', 'progress_percent' => 85]);
        $proj2 = Project::create(['name' => 'GitLab Integration API', 'description' => 'Sync git data', 'status' => 'active', 'deadline' => '2026-10-15', 'gitlab_project_id' => 102, 'gitlab_repo_url' => 'https://gitlab.com/company/gitlab-api', 'progress_percent' => 70]);
        
        // 4. Team Members
        $members = [
            ['name' => 'Rahul Sharma', 'email' => 'rahul@example.com', 'role' => 'Senior Frontend Developer', 'gitlab_id' => 'rahul-dev', 'gitlab_username' => 'rahul.s', 'team_id' => $team1->id, 'department_id' => $dept1->id],
            ['name' => 'Priya Patel', 'email' => 'priya@example.com', 'role' => 'Frontend Developer', 'gitlab_id' => 'priya-fe', 'gitlab_username' => 'priya.p', 'team_id' => $team1->id, 'department_id' => $dept1->id],
            ['name' => 'Amit Singh', 'email' => 'amit@example.com', 'role' => 'Senior Backend Developer', 'gitlab_id' => 'amit-be', 'gitlab_username' => 'amit.s', 'team_id' => $team2->id, 'department_id' => $dept1->id],
            ['name' => 'Neha Gupta', 'email' => 'neha@example.com', 'role' => 'Backend Developer', 'gitlab_id' => 'neha-code', 'gitlab_username' => 'neha.g', 'team_id' => $team2->id, 'department_id' => $dept1->id],
            ['name' => 'Vikram Verma', 'email' => 'vikram@example.com', 'role' => 'Fullstack Developer', 'gitlab_id' => 'vikram-fs', 'gitlab_username' => 'vikram.v', 'team_id' => $team2->id, 'department_id' => $dept1->id],
        ];

        $memberModels = [];
        foreach ($members as $m) {
            $memberModels[] = TeamMember::create($m);
        }

        // 5. June 1 to June 25 Data Loop
        $startDate = Carbon::create(2026, 6, 1);
        $endDate = Carbon::create(2026, 6, 25);
        $currentDate = $startDate->copy();

        $commitTitles = ['fix: auth bug', 'feat: add dashboard widgets', 'refactor: clean up controllers', 'docs: update readme', 'chore: update deps', 'perf: optimize queries'];

        while ($currentDate <= $endDate) {
            $isWeekend = $currentDate->isWeekend();

            foreach ($memberModels as $member) {
                // Attendance
                if (!$isWeekend) {
                    $status = (rand(1, 100) > 10) ? 'present' : (rand(1,100) > 50 ? 'late' : 'absent');
                    DB::table('attendance_logs')->insert([
                        'team_member_id' => $member->id,
                        'date' => $currentDate->toDateString(),
                        'status' => $status,
                        'check_in' => $status !== 'absent' ? '09:' . str_pad(rand(0, 45), 2, '0', STR_PAD_LEFT) . ':00' : null,
                        'check_out' => $status !== 'absent' ? '18:' . str_pad(rand(0, 30), 2, '0', STR_PAD_LEFT) . ':00' : null,
                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ]);
                }

                // Tasks (Assigned occasionally)
                if (rand(1, 10) > 6) {
                    DB::table('tasks')->insert([
                        'title' => 'Implement feature block for ' . $currentDate->format('M d'),
                        'status' => rand(1, 10) > 3 ? 'completed' : 'in_progress',
                        'due_date' => $currentDate->copy()->addDays(rand(1, 3))->toDateString(),
                        'team_member_id' => $member->id,
                        'project_id' => rand(1, 10) > 5 ? $proj1->id : $proj2->id,
                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ]);
                }

                // Commits (More frequent)
                $commitCount = $isWeekend ? rand(0, 1) : rand(1, 5);
                for ($i = 0; $i < $commitCount; $i++) {
                    DB::table('commits')->insert([
                        'employee_id' => $member->id,
                        'project_id' => rand(1, 10) > 5 ? $proj1->id : $proj2->id,
                        'commit_sha' => substr(md5(rand()), 0, 40),
                        'message' => $commitTitles[array_rand($commitTitles)] . ' - ' . substr(md5(rand()), 0, 6),
                        'commit_url' => 'https://gitlab.com/commit/' . substr(md5(rand()), 0, 8),
                        'committed_at' => $currentDate->copy()->addHours(rand(9, 17))->addMinutes(rand(0, 59)),
                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ]);
                }

                // Merge Requests & Issues (Only a few)
                if (rand(1, 10) > 8) {
                    DB::table('gitlab_merge_requests')->insert([
                        'employee_id' => $member->id,
                        'project_id' => rand(1, 10) > 5 ? $proj1->id : $proj2->id,
                        'gitlab_mr_id' => rand(1000, 9999),
                        'title' => 'MR for ' . $currentDate->format('M d'),
                        'state' => rand(1, 10) > 2 ? 'merged' : 'opened',
                        'merged_at' => rand(1, 10) > 2 ? $currentDate->copy()->addHours(2) : null,
                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ]);
                }
                if (rand(1, 10) > 8) {
                    DB::table('gitlab_issues')->insert([
                        'employee_id' => $member->id,
                        'project_id' => rand(1, 10) > 5 ? $proj1->id : $proj2->id,
                        'gitlab_issue_id' => rand(1000, 9999),
                        'title' => 'Issue reported on ' . $currentDate->format('M d'),
                        'state' => rand(1, 10) > 4 ? 'closed' : 'opened',
                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ]);
                }
            }
            $currentDate->addDay();
        }

        // Metrics for projects
        DB::table('project_code_metrics')->insert([
            ['project_id' => $proj1->id, 'code_quality_score' => 92.5, 'security_score' => 88.0, 'test_coverage_score' => 76.5, 'created_at' => now(), 'updated_at' => now()],
            ['project_id' => $proj2->id, 'code_quality_score' => 85.0, 'security_score' => 91.0, 'test_coverage_score' => 82.0, 'created_at' => now(), 'updated_at' => now()]
        ]);

        $this->command->info('June 1 - 25 Demo Data Seeded Successfully!');
    }
}
