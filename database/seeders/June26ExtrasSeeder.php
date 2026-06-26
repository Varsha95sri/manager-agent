<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class June26ExtrasSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clean tables
        DB::table('leave_requests')->truncate();
        DB::table('meeting_notes')->truncate();
        DB::table('meeting_note_team_member')->truncate();

        $memberIds = DB::table('team_members')->pluck('id')->toArray();
        if (empty($memberIds)) {
            $this->command->error("No team members found! Please run the massive seeder first.");
            return;
        }

        $startDate = Carbon::create(2026, 6, 1);
        $endDate = Carbon::create(2026, 6, 26);
        $days = $startDate->diffInDays($endDate) + 1; // 26 days

        // 1. Generate 10,000 Leave Requests
        $this->command->info("Generating 10,000 Leave Requests...");
        $leaveChunk = [];
        $leaveTypes = ['Sick Leave', 'Casual Leave', 'Earned Leave', 'Maternity Leave', 'Paternity Leave', 'Unpaid Leave'];
        $leaveStatuses = ['pending', 'approved', 'rejected'];
        $reasons = [
            'Attending a family function out of town.',
            'Down with viral fever and doctor advised rest.',
            'Personal errand to run at the bank.',
            'Taking a mental health day to recharge.',
            'Caring for a sick family member.',
            'Scheduled medical appointment.',
            'Vacation trip with family.',
            'Relocating to a new apartment.'
        ];

        for ($i = 0; $i < 10000; $i++) {
            $leaveStart = clone $startDate;
            $leaveStart->addDays($faker->numberBetween(0, 20));
            $daysOff = $faker->numberBetween(1, 5);
            $leaveEnd = clone $leaveStart;
            $leaveEnd->addDays($daysOff - 1);

            $leaveChunk[] = [
                'employee_id' => $faker->randomElement($memberIds),
                'leave_type' => $faker->randomElement($leaveTypes),
                'start_date' => $leaveStart->toDateString(),
                'end_date' => $leaveEnd->toDateString(),
                'days' => $daysOff,
                'reason' => $faker->randomElement($reasons),
                'status' => $faker->randomElement($leaveStatuses),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($leaveChunk) >= 2000) {
                DB::table('leave_requests')->insert($leaveChunk);
                $leaveChunk = [];
            }
        }
        if (count($leaveChunk) > 0) {
            DB::table('leave_requests')->insert($leaveChunk);
        }

        // 2. Generate 500 Meeting Notes
        $this->command->info("Generating 500 Meeting Notes with Attendees...");
        $meetingChunk = [];
        $meetingPivotChunk = [];
        $meetingTitles = [
            'Daily Standup Sync',
            'Sprint Planning Session',
            'Architecture Review Board',
            'Post-Mortem: DB Outage',
            'UI/UX Design Alignment',
            'Product Roadmap Q3 Sync',
            'Weekly All-Hands',
            'Security Vulnerability Patching',
            'Client Requirements Sync',
            'API Integration Kickoff'
        ];
        
        $meetingNotes = [
            'Discussed the current blockers. Moving the caching ticket to next sprint. Need to align with DevOps on deployment.',
            'Reviewed the architecture proposal. Approved the usage of Redis for session management. Action item for backend team.',
            'General sync on progress. Frontend is slightly behind schedule due to API delays. Re-allocating resources to help.',
            'Went over the new design specs for the dashboard. Everyone is aligned on the new color palette.',
            'Discussed the security audit results. Critical vulnerabilities patched, but need to schedule a follow-up audit next week.',
            'Sprint targets discussed. Velocity is looking good. Need to clear out technical debt tickets.',
            'Reviewed the quarter goals. On track to deliver the MVP by end of month. Marketing team is preparing launch materials.'
        ];

        for ($i = 1; $i <= 500; $i++) {
            $meetDate = clone $startDate;
            $meetDate->addDays($faker->numberBetween(0, 25));

            $meetingChunk[] = [
                'id' => $i,
                'title' => $faker->randomElement($meetingTitles),
                'notes' => $faker->randomElement($meetingNotes),
                'meeting_date' => $meetDate->toDateString(),
                'meeting_time' => $faker->time('H:i:s', '16:00:00'),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Add 5 to 15 random attendees per meeting
            $attendeeCount = $faker->numberBetween(5, 15);
            $shuffledMembers = $faker->randomElements($memberIds, $attendeeCount);
            
            foreach ($shuffledMembers as $mId) {
                $meetingPivotChunk[] = [
                    'meeting_note_id' => $i,
                    'team_member_id' => $mId,
                ];
            }

            if (count($meetingChunk) >= 500) {
                DB::table('meeting_notes')->insert($meetingChunk);
                $meetingChunk = [];
            }
            if (count($meetingPivotChunk) >= 2000) {
                DB::table('meeting_note_team_member')->insert($meetingPivotChunk);
                $meetingPivotChunk = [];
            }
        }
        
        if (count($meetingChunk) > 0) DB::table('meeting_notes')->insert($meetingChunk);
        if (count($meetingPivotChunk) > 0) DB::table('meeting_note_team_member')->insert($meetingPivotChunk);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info("June Extras Seeder completed successfully!");
    }
}
