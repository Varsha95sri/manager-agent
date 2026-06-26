<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Services\ChatbotAgentService;
use App\Models\AiInsight;

#[Signature('app:generate-ai-insights')]
#[Description('Generate a daily AI summary and insights from the database')]
class GenerateAiInsights extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ChatbotAgentService $chatbotService)
    {
        $this->info('Generating AI insights...');
        
        try {
            $prompt = "Please provide an Executive Daily Summary based on the database. Highlight top performers, low performers, delayed projects, overdue tasks, attendance exceptions, and workload alerts. Format with bullet points.";
            $response = $chatbotService->answerQuestion($prompt);
            
            AiInsight::create([
                'type' => 'daily_summary',
                'content' => $response
            ]);
            
            $this->info('AI Insights successfully generated and saved.');
        } catch (\Exception $e) {
            $this->error('Failed to generate insights: ' . $e->getMessage());
        }
    }
}
