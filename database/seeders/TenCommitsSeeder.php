<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TenCommitsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Adding 10 commits per employee per day (June 1 - June 18)...');
        
        DB::disableQueryLog();

        $employeeIds = DB::table('team_members')->pluck('id')->toArray();
        $projectIds = DB::table('projects')->pluck('id')->toArray();
        
        if (empty($employeeIds) || empty($projectIds)) {
            $this->command->error('No employees or projects found.');
            return;
        }

        $startDate = Carbon::create(2026, 6, 1);
        $endDate = Carbon::create(2026, 6, 18);
        
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->toDateString();
            // Skip weekends if desired, but user didn't specify. We'll skip weekends to be safe and save some rows, or include them? The prompt just said "10 per employee per day". Let's do it for all days in the range.
            
            $this->command->info("Seeding 10 commits per employee for $dateStr...");
            
            $commits = [];
            foreach ($employeeIds as $eId) {
                // Pick a random project for this employee's commits today
                $pId = $projectIds[array_rand($projectIds)];
                
                for ($i=1; $i<=10; $i++) {
                    // Random time during the day
                    $hour = rand(9, 18);
                    $minute = rand(0, 59);
                    $second = rand(0, 59);
                    
                    $commits[] = [
                        'employee_id' => $eId,
                        'project_id' => $pId,
                        'commit_sha' => Str::random(40),
                        'message' => "Update component feature $i on $dateStr",
                        'commit_url' => 'https://gitlab.com/commit/' . Str::random(8),
                        'committed_at' => sprintf('%s %02d:%02d:%02d', $dateStr, $hour, $minute, $second),
                        'created_at' => sprintf('%s %02d:%02d:%02d', $dateStr, $hour, $minute, $second),
                        'updated_at' => sprintf('%s %02d:%02d:%02d', $dateStr, $hour, $minute, $second),
                    ];
                }
                
                // Insert in chunks to avoid memory issues
                if (count($commits) >= 500) {
                    DB::table('commits')->insert($commits);
                    $commits = [];
                }
            }
            
            if (count($commits) > 0) {
                DB::table('commits')->insert($commits);
            }
            
            $currentDate->addDay();
        }

        DB::enableQueryLog();
        $this->command->info('10 Commits Seeding Completed!');
    }
}
