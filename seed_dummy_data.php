<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Task;
use App\Models\PerformanceReport;
use Carbon\Carbon;

$frontend = Team::firstOrCreate(['slug' => 'frontend'], ['name' => 'Frontend Team', 'description' => 'Frontend team', 'status' => 'Good', 'status_color' => 'success', 'icon_bg' => 'primary']);
$backend = Team::firstOrCreate(['slug' => 'backend'], ['name' => 'Backend Team', 'description' => 'Backend team', 'status' => 'Good', 'status_color' => 'success', 'icon_bg' => 'warning']);
$qa = Team::firstOrCreate(['slug' => 'qa'], ['name' => 'QA Team', 'description' => 'QA team', 'status' => 'Good', 'status_color' => 'success', 'icon_bg' => 'danger']);

$members = [
    ['name' => 'Aarav Gupta', 'email' => 'aarav@test.com', 'role' => 'Frontend Developer', 'team_id' => $frontend->id, 'gitlab_username' => 'aarav_dev'],
    ['name' => 'Neha Sharma', 'email' => 'neha@test.com', 'role' => 'Backend Developer', 'team_id' => $backend->id, 'gitlab_username' => 'neha_backend'],
    ['name' => 'Rahul Verma', 'email' => 'rahul@test.com', 'role' => 'QA Tester', 'team_id' => $qa->id, 'gitlab_username' => 'rahul_qa'],
    ['name' => 'Priya Singh', 'email' => 'priya@test.com', 'role' => 'UI/UX Designer', 'team_id' => $frontend->id, 'gitlab_username' => 'priya_ui'],
    ['name' => 'Rohan Das', 'email' => 'rohan@test.com', 'role' => 'API Engineer', 'team_id' => $backend->id, 'gitlab_username' => 'rohan_api'],
    ['name' => 'Ananya Patel', 'email' => 'ananya@test.com', 'role' => 'Automation QA', 'team_id' => $qa->id, 'gitlab_username' => 'ananya_auto'],
];

foreach($members as $m) {
    TeamMember::updateOrCreate(['email' => $m['email']], $m);
}

$allMembers = TeamMember::all();
$statuses = ['pending', 'completed', 'in_progress'];
$priorities = ['low', 'medium', 'high'];

// Create 50 tasks
for($i = 1; $i <= 50; $i++) {
    $member = $allMembers->random();
    Task::create([
        'team_member_id' => $member->id,
        'title' => 'Implement Feature ' . rand(100, 999) . ' for ' . $member->role,
        'status' => $statuses[array_rand($statuses)],
        'due_date' => Carbon::now()->addDays(rand(-7, 7))->toDateString(),
        'priority' => $priorities[array_rand($priorities)],
    ]);
}

// Generate reports for the last 7 days
for($i = 6; $i >= 0; $i--) {
    $date = Carbon::now()->subDays($i)->toDateString();
    
    // Check if report exists
    if(PerformanceReport::whereDate('report_date', $date)->exists()) {
        continue;
    }

    PerformanceReport::create([
        'report_date' => $date,
        'team_id' => $frontend->id, // just picking one
        'team_productivity' => rand(65, 98),
        'risks' => ['Delayed tasks', 'High workload for some devs'],
        'top_performers' => ['Aarav Gupta', 'Neha Sharma'],
        'attention_required' => ['Rahul Verma'],
        'workload_analysis' => 'The team has a moderate to high workload.',
        'recommendations' => ['Reassign tasks from Rahul.', 'Schedule a sync meeting.'],
        'full_report' => 'This is a comprehensive AI generated performance report for ' . $date . '. It details the overall productivity, bottlenecks, and recommendations for the team.',
    ]);
}

echo "Dummy data seeded successfully.\n";
