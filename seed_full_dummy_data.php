<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TeamMember;
use App\Models\Department;
use App\Models\Skill;
use App\Models\LeaveRequest;
use App\Models\AttendanceLog;
use Carbon\Carbon;

// 1. Departments
$eng = Department::firstOrCreate(['name' => 'Engineering'], ['description' => 'Software engineering team']);
$des = Department::firstOrCreate(['name' => 'Design'], ['description' => 'UI/UX Design']);
$qa = Department::firstOrCreate(['name' => 'QA'], ['description' => 'Quality Assurance']);

// 2. Skills
$skills = [
    Skill::firstOrCreate(['name' => 'Laravel', 'category' => 'Backend']),
    Skill::firstOrCreate(['name' => 'Vue.js', 'category' => 'Frontend']),
    Skill::firstOrCreate(['name' => 'Figma', 'category' => 'Design']),
    Skill::firstOrCreate(['name' => 'PHPUnit', 'category' => 'Testing']),
];

$allMembers = TeamMember::all();

foreach ($allMembers as $m) {
    // Assign Department
    if (strpos(strtolower($m->role), 'design') !== false) {
        $m->department_id = $des->id;
    } elseif (strpos(strtolower($m->role), 'qa') !== false) {
        $m->department_id = $qa->id;
    } else {
        $m->department_id = $eng->id;
    }
    $m->save();

    // Assign Skills
    $randomSkills = [$skills[array_rand($skills)]->id, $skills[array_rand($skills)]->id];
    $m->skills()->sync([
        $randomSkills[0] => ['proficiency' => rand(3, 5)],
        $randomSkills[1] => ['proficiency' => rand(2, 4)]
    ]);

    // Create 1 Leave Request per member
    LeaveRequest::create([
        'employee_id' => $m->id,
        'leave_type' => ['Sick', 'Casual', 'Earned'][rand(0, 2)],
        'start_date' => Carbon::now()->addDays(rand(1, 10))->toDateString(),
        'end_date' => Carbon::now()->addDays(rand(12, 15))->toDateString(),
        'days' => rand(1, 4),
        'reason' => 'Dummy leave request',
        'status' => ['pending', 'approved', 'rejected'][rand(0, 2)],
    ]);

    // Create Attendance for June 1 to June 25
    $start = Carbon::create(2026, 6, 1);
    for ($i = 0; $i <= 24; $i++) {
        $currentDate = $start->copy()->addDays($i)->toDateString();
        $status = ['present', 'present', 'present', 'late', 'leave'][rand(0, 4)];
        
        $checkIn = null;
        $checkOut = null;
        if ($status === 'present') {
            $checkIn = '09:'.sprintf("%02d", rand(0, 59)).':00';
            $checkOut = '17:'.sprintf("%02d", rand(0, 59)).':00';
        } elseif ($status === 'late') {
            $checkIn = '10:'.sprintf("%02d", rand(10, 59)).':00';
            $checkOut = '17:'.sprintf("%02d", rand(0, 59)).':00';
        }

        AttendanceLog::create([
            'team_member_id' => $m->id,
            'date' => $currentDate,
            'status' => $status,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'leave_type' => $status === 'leave' ? 'Casual' : null,
        ]);
    }
}

echo "Departments, Skills, Leaves, and Attendance logs from June 1 to June 25 added successfully.\n";
