<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('manager:calculate-scores')]
#[Description('Calculate performance scores and grades for all team members')]
class CalculatePerformanceScores extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting performance score calculation...');
        
        $service = new \App\Services\PerformanceScoringService();
        $service->calculateForAll();
        
        $this->info('Performance scores calculated successfully!');
    }
}
