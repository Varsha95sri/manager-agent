<?php
// app/Services/ChatbotAgentService.php

namespace App\Services;

use App\Models\TeamMember;
use App\Models\Task;
use App\Models\GitCommit;
use App\Models\AttendanceLog;
use App\Models\MeetingNote;
use App\Models\PerformanceReport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChatbotAgentService
{
    /**
     * Answer manager question using database context and Claude API.
     */
    public function answerQuestion(string $question): string
    {
        // 1. Gather live database context
        $context = $this->buildDatabaseContext();

        try {
            // 2. Call Claude API with the context
            return $this->queryClaude($context, $question);
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            Log::error("ChatbotAgentService Error: " . $msg);

            // 3. Fallback to a smart local rule-based response with the specific error message
            return $this->generateLocalResponse($question, $msg);
        }
    }

    /**
     * Compile database tables into a single context text block.
     */
    protected function buildDatabaseContext(): string
    {
        $todayStr = Carbon::today()->toDateString();

        // Query team members
        $members = TeamMember::all();
        $membersText = "";
        foreach ($members as $m) {
            $membersText .= "- ID {$m->id}: {$m->name} (Role: {$m->role}, Email: {$m->email})\n";
        }

        // Query tasks status
        $tasks = Task::all();
        $tasksCount = $tasks->count();
        $completedTasks = $tasks->where('status', 'completed')->count();
        $inProgressTasks = $tasks->where('status', 'in_progress')->count();
        $pendingTasks = $tasks->where('status', 'pending')->count();
        $overdueTasksCount = $tasks->where('status', '!=', 'completed')->filter(fn($t) => Carbon::parse($t->due_date)->lt(Carbon::today()))->count();
        
        $tasksText = "Total Tasks: {$tasksCount}\n- Completed: {$completedTasks}\n- In Progress: {$inProgressTasks}\n- Pending: {$pendingTasks}\n- Overdue (Uncompleted past due date): {$overdueTasksCount}\n";

        // Query today's commits
        $commits = GitCommit::with('teamMember')->whereDate('committed_at', $todayStr)->get();
        $commitsText = "";
        foreach ($commits as $c) {
            $commitsText .= "- [Hash: {$c->commit_hash}] {$c->teamMember?->name}: \"{$c->message}\"\n";
        }
        if ($commits->isEmpty()) {
            $commitsText = "No commits pushed today.\n";
        }

        // Query today's attendance logs
        $attendance = AttendanceLog::with('teamMember')->whereDate('date', $todayStr)->get();
        $attText = "";
        foreach ($attendance as $att) {
            $checkIn = $att->check_in ?: 'N/A';
            $attText .= "- {$att->teamMember?->name}: {$att->status} (Check-in time: {$checkIn})\n";
        }
        if ($attendance->isEmpty()) {
            $attText = "No attendance logged today.\n";
        }

        // Query today's meeting notes
        $meetings = MeetingNote::whereDate('meeting_date', $todayStr)->get();
        $meetingsText = "";
        foreach ($meetings as $m) {
            $meetingsText .= "- \"{$m->title}\": {$m->notes}\n";
        }
        if ($meetings->isEmpty()) {
            $meetingsText = "No meetings recorded today.\n";
        }

        // Query latest performance report
        $latestReport = PerformanceReport::latest()->first();
        $reportText = "No performance reports found in database yet.\n";
        if ($latestReport) {
            $perfDate = Carbon::parse($latestReport->report_date)->format('Y-m-d');
            $performers = implode(', ', $latestReport->top_performers);
            $attention = implode(', ', $latestReport->attention_required);
            $risks = implode(' | ', $latestReport->risks);
            
            $reportText = "Date: {$perfDate}\nProductivity Score: {$latestReport->team_productivity}%\nTop Performers: {$performers}\nRequires Attention: {$attention}\nIdentified Risks: {$risks}\n";
        }

        return "LIVE DATABASE TEAM CONTEXT (As of " . Carbon::now()->toDateTimeString() . "):\n\n"
            . "=== TEAM MEMBERS ===\n{$membersText}\n"
            . "=== TASK METRICS ===\n{$tasksText}\n"
            . "=== TODAY'S GIT COMMITS ===\n{$commitsText}\n"
            . "=== TODAY'S ATTENDANCE ===\n{$attText}\n"
            . "=== TODAY'S MEETING NOTES ===\n{$meetingsText}\n"
            . "=== LATEST EVALUATED REPORT ===\n{$reportText}";
    }

    /**
     * Query Claude API.
     */
    protected function queryClaude(string $context, string $question): string
    {
        $apiKey = env('ANTHROPIC_API_KEY') ?: config('services.anthropic.key');

        if (empty($apiKey) || $apiKey === 'your_key_here') {
            throw new \Exception("Anthropic API key is not configured.");
        }

        $prompt = "You are a Manager Assistant AI. You have access to this real-time database snapshot:\n\n"
            . $context . "\n\n"
            . "Use this information to answer the manager's question accurately. Keep answers professional, insightful, and concise. "
            . "If the database snapshot does not contain enough information to answer, state that honestly.\n\n"
            . "Question: " . $question;

        $response = Http::timeout(30)
            ->withoutVerifying()
            ->withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 1000,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new \Exception("Claude API call failed: " . $response->body());
        }

        $data = $response->json();
        return $data['content'][0]['text'] ?? 'Unable to parse response.';
    }

    /**
     * Local fallback response when Claude is offline.
     */
    protected function generateLocalResponse(string $question, ?string $errorMessage = null): string
    {
        $q = strtolower($question);
        $todayStr = Carbon::today()->toDateString();

        // Gather database models for simple rule-based replies
        $membersCount = TeamMember::count();
        $tasksCount = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();
        $absentees = AttendanceLog::with('teamMember')->whereDate('date', $todayStr)->where('status', 'absent')->get();

        $offlineReason = "the Claude API integration is not configured or reachable";
        if ($errorMessage) {
            if (str_contains($errorMessage, 'credit balance is too low') || str_contains($errorMessage, 'credit_balance')) {
                $offlineReason = "your Anthropic Claude API account credit balance is too low/exhausted";
            } elseif (str_contains($errorMessage, 'SSL certificate problem') || str_contains($errorMessage, 'local issuer certificate')) {
                $offlineReason = "a local SSL/cURL certificate configuration error on your machine";
            } else {
                $offlineReason = "an API error: " . substr($errorMessage, 0, 100);
            }
        }

        if (str_contains($q, 'member') || str_contains($q, 'team') || str_contains($q, 'who')) {
            $list = TeamMember::pluck('name')->toArray();
            return "Our team consists of {$membersCount} members: " . implode(', ', $list) . ". Let me know if you would like me to retrieve specific details.";
        }

        if (str_contains($q, 'task') || str_contains($q, 'progress') || str_contains($q, 'completed')) {
            return "Currently, there are {$tasksCount} total tasks logged, with {$completedTasks} completed. You can view the full task lists on the Data Entry or Reports pages.";
        }

        if (str_contains($q, 'absent') || str_contains($q, 'attendance') || str_contains($q, 'today')) {
            if ($absentees->isEmpty()) {
                return "Everyone who logged check-ins is present today. No members are marked absent.";
            }
            $names = $absentees->map(fn($a) => $a->teamMember?->name)->toArray();
            return "Today, the following member(s) are absent: " . implode(', ', $names) . ".";
        }

        return "I am currently operating in offline mode because of {$offlineReason}. "
            . "However, according to my database logs, we have {$membersCount} team members and {$tasksCount} tasks registered. "
            . "Please resolve this issue to unlock full conversational intelligence!";
    }
}
