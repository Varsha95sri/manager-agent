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
            // Build the prompt for logging
            $prompt = "You are a Manager Assistant AI. You have access to this real-time database snapshot:\n\n"
                . $context . "\n\n"
                . "Use this information to answer the manager's question accurately. Keep answers professional, insightful, and concise.\n"
                . "MANDATORY BEHAVIOR: If the manager asks about or inputs a specific employee's/team member's name (e.g. \"Alice\", \"Rahul\", etc.), compile a detailed performance report for that specific employee. "
                . "Summarize how much work they completed, how many commits they made today, which tasks are assigned to them (with due dates and statuses), their attendance check-in status today, and evaluate their contribution compared to others.\n"
                . "If the database snapshot does not contain enough information to answer, state that honestly.\n\n"
                . "Question: " . $question;

            Log::info("AI Chatbot Prompt:\n" . $prompt);

            // 2. Call local Ollama API with the context
            $answer = $this->queryOllama($context, $question);
            
            Log::info("AI Chatbot Response:\n" . $answer);
            return $answer;
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            Log::error("ChatbotAgentService Error: " . $msg);

            // 3. Fallback to a smart local rule-based response with the specific error message
            $fallbackAnswer = $this->generateLocalResponse($question, $msg);
            Log::info("AI Chatbot Fallback Response:\n" . $fallbackAnswer);
            return $fallbackAnswer;
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
        $tasks = Task::with('teamMember')->get();
        $tasksCount = $tasks->count();
        $completedTasks = $tasks->where('status', 'completed')->count();
        $inProgressTasks = $tasks->where('status', 'in_progress')->count();
        $pendingTasks = $tasks->where('status', 'pending')->count();
        $overdueTasksCount = $tasks->where('status', '!=', 'completed')->filter(fn($t) => Carbon::parse($t->due_date)->lt(Carbon::today()))->count();
        
        $tasksText = "Total Tasks Count: {$tasksCount}\n"
            . "- Completed: {$completedTasks}\n"
            . "- In Progress: {$inProgressTasks}\n"
            . "- Pending: {$pendingTasks}\n"
            . "- Overdue Count: {$overdueTasksCount}\n\n"
            . "Detailed Tasks List (Assignee, Task Title, Status, Due Date):\n";
        foreach ($tasks as $t) {
            $memberName = $t->teamMember?->name ?? 'Unassigned';
            $tasksText .= "- [{$memberName}] \"{$t->title}\" (Status: {$t->status}, Due Date: {$t->due_date})\n";
        }

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
            . "=== TASK METRICS & TASKS LIST ===\n{$tasksText}\n"
            . "=== TODAY'S GIT COMMITS ===\n{$commitsText}\n"
            . "=== TODAY'S ATTENDANCE ===\n{$attText}\n"
            . "=== TODAY'S MEETING NOTES ===\n{$meetingsText}\n"
            . "=== LATEST EVALUATED REPORT ===\n{$reportText}";
    }

    /**
     * Query local Ollama API.
     */
    protected function queryOllama(string $context, string $question): string
    {
        $prompt = "You are a Manager Assistant AI. You have access to this real-time database snapshot:\n\n"
            . $context . "\n\n"
            . "Use this information to answer the manager's question accurately. Keep answers professional, insightful, and concise.\n"
            . "MANDATORY BEHAVIOR: If the manager asks about or inputs a specific employee's/team member's name (e.g. \"Alice\", \"Rahul\", etc.), compile a detailed performance report for that specific employee. "
            . "Summarize how much work they completed, how many commits they made today, which tasks are assigned to them (with due dates and statuses), their attendance check-in status today, and evaluate their contribution compared to others.\n"
            . "If the database snapshot does not contain enough information to answer, state that honestly.\n\n"
            . "Question: " . $question;

        return $this->callLLM($prompt);
    }

    /**
     * Route the query to the active configured LLM or fallback to local Ollama.
     */
    protected function callLLM(string $prompt, bool $requireJson = false): string
    {
        @set_time_limit(60);

        $userId = auth()->id() ?? (\App\Models\User::first()?->id ?? 1);
        $activeKey = \App\Models\ThirdPartyApiKey::where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        if ($activeKey) {
            $service = $activeKey->service_name;
            $apiKey = $activeKey->api_key;
            $model = $activeKey->model_name;
            $url = $activeKey->api_url;

            if ($service === 'anthropic') {
                $endpoint = $url ?: 'https://api.anthropic.com/v1/messages';
                $response = Http::timeout(30)->withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post($endpoint, [
                    'model' => $model ?: 'claude-3-5-sonnet-20241022',
                    'max_tokens' => 1524,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ]
                ]);

                if ($response->failed()) {
                    throw new \Exception("Anthropic API call failed: " . $response->body());
                }

                return $response->json('content.0.text') ?? 'Unable to parse Anthropic response.';
            }

            if ($service === 'openai') {
                $endpoint = $url ?: 'https://api.openai.com/v1/chat/completions';
                $params = [
                    'model' => $model ?: 'gpt-4o',
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ]
                ];
                if ($requireJson) {
                    $params['response_format'] = ['type' => 'json_object'];
                }
                $response = Http::timeout(30)->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post($endpoint, $params);

                if ($response->failed()) {
                    throw new \Exception("OpenAI API call failed: " . $response->body());
                }

                return $response->json('choices.0.message.content') ?? 'Unable to parse OpenAI response.';
            }

            if ($service === 'gemini') {
                $modelName = $model ?: 'gemini-1.5-flash';
                $endpoint = $url ?: "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}";
                $response = Http::timeout(30)->post($endpoint, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ]
                ]);

                if ($response->failed()) {
                    throw new \Exception("Gemini API call failed: " . $response->body());
                }

                return $response->json('candidates.0.content.parts.0.text') ?? 'Unable to parse Gemini response.';
            }

            if ($service === 'ollama') {
                $baseUrl = $url ?: env('OLLAMA_URL', 'http://127.0.0.1:11434');
                $endpoint = rtrim($baseUrl, '/') . '/api/chat';
                $params = [
                    'model' => $model ?: env('OLLAMA_MODEL', 'llama3.1:8b'),
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.2,
                        'num_predict' => 1024,
                    ]
                ];
                if ($requireJson) {
                    $params['format'] = 'json';
                }
                $response = Http::timeout(25)->post($endpoint, $params);

                if ($response->failed()) {
                    throw new \Exception("Ollama API call failed: " . $response->body());
                }

                $data = $response->json();
                return $data['message']['content'] ?? 'Unable to parse Ollama response.';
            }
        }

        // Default fallback to .env configuration if no active database key is set
        $provider = env('LLM_PROVIDER', 'ollama');

        if ($provider === 'ollama') {
            $endpoint = rtrim(env('OLLAMA_URL', 'http://127.0.0.1:11434'), '/') . '/api/chat';
            $params = [
                'model' => env('OLLAMA_MODEL', 'llama3.1:8b'),
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'stream' => false,
                'options' => [
                    'temperature' => 0.2,
                    'num_predict' => 1024,
                ]
            ];
            if ($requireJson) {
                $params['format'] = 'json';
            }
            $response = Http::timeout(25)->post($endpoint, $params);

            if ($response->failed()) {
                throw new \Exception("Ollama API call failed: " . $response->body());
            }

            $data = $response->json();
            return $data['message']['content'] ?? 'Unable to parse Ollama response.';
        }

        // Support direct env configuration for Anthropic if provider is set to anthropic
        if ($provider === 'anthropic' && env('ANTHROPIC_API_KEY')) {
            $endpoint = 'https://api.anthropic.com/v1/messages';
            $response = Http::timeout(30)->withHeaders([
                'x-api-key' => env('ANTHROPIC_API_KEY'),
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($endpoint, [
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 1524,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]);

            if ($response->failed()) {
                throw new \Exception("Anthropic API call failed: " . $response->body());
            }

            return $response->json('content.0.text') ?? 'Unable to parse Anthropic response.';
        }

        throw new \Exception("No active LLM configuration found.");
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

        $offlineReason = "the Ollama API is not running or reachable";
        if ($errorMessage) {
            if (str_contains($errorMessage, '11434') || str_contains($errorMessage, 'Connection refused') || str_contains($errorMessage, 'Failed to connect') || str_contains($errorMessage, 'Could not connect')) {
                $offlineReason = "local Ollama service is not running or not installed on port 11434";
            } elseif (str_contains($errorMessage, 'credit balance is too low') || str_contains($errorMessage, 'credit_balance')) {
                $offlineReason = "your Anthropic Claude API account credit balance is too low/exhausted";
            } elseif (str_contains($errorMessage, 'SSL certificate problem') || str_contains($errorMessage, 'local issuer certificate')) {
                $offlineReason = "a local SSL/cURL certificate configuration error on your machine";
            } else {
                $offlineReason = "an API error: " . substr($errorMessage, 0, 100);
            }
        }

        // Check if question contains a specific employee's name first to be extra helpful
        $allMembers = TeamMember::all();
        foreach ($allMembers as $member) {
            $memberNameLower = strtolower($member->name);
            $firstName = strtolower(explode(' ', $member->name)[0]);
            if (str_contains($q, $memberNameLower) || (strlen($firstName) > 2 && str_contains($q, $firstName))) {
                $empTodayStr = Carbon::today()->toDateString();
                $empAttendance = AttendanceLog::where('team_member_id', $member->id)
                    ->whereDate('date', $empTodayStr)
                    ->first();
                $empTasks = Task::where('team_member_id', $member->id)->get();
                $empCommits = GitCommit::where('team_member_id', $member->id)
                    ->whereDate('committed_at', $empTodayStr)
                    ->get();

                $attStatus = $empAttendance ? "{$empAttendance->status} (Check-in: " . ($empAttendance->check_in ?: 'N/A') . ")" : "No attendance logged today";
                $completedCount = $empTasks->where('status', 'completed')->count();
                $pendingCount = $empTasks->where('status', 'pending')->count();
                $inProgressCount = $empTasks->where('status', 'in_progress')->count();
                $commitsCount = $empCommits->count();

                $res = "Here is the performance report for **{$member->name}** ({$member->role}) [Offline Mode]:\n\n";
                $res .= "- **Attendance Today**: {$attStatus}\n";
                $res .= "- **Git Commits Today**: {$commitsCount}\n";
                $res .= "- **Tasks Summary**: {$completedCount} Completed, {$inProgressCount} In Progress, {$pendingCount} Pending\n";
                if ($empTasks->isNotEmpty()) {
                    $res .= "- **Tasks List**:\n";
                    foreach ($empTasks as $t) {
                        $res .= "  - \"{$t->title}\" (Status: {$t->status}, Due Date: {$t->due_date})\n";
                    }
                }
                return $res;
            }
        }

        if (str_contains($q, 'absent')) {
            if ($absentees->isEmpty()) {
                return "Everyone who logged check-ins is present today. No members are marked absent.";
            }
            $names = $absentees->map(fn($a) => $a->teamMember?->name)->toArray();
            return "Today, the following member(s) are absent: " . implode(', ', $names) . ".";
        }

        if (str_contains($q, 'late')) {
            $latecomers = AttendanceLog::with('teamMember')->whereDate('date', $todayStr)->where('status', 'late')->get();
            if ($latecomers->isEmpty()) {
                return "No members are marked late today.";
            }
            $names = $latecomers->map(fn($a) => $a->teamMember?->name)->toArray();
            return "Today, the following member(s) are late: " . implode(', ', $names) . ".";
        }

        if (str_contains($q, 'present')) {
            $presentList = AttendanceLog::with('teamMember')->whereDate('date', $todayStr)->where('status', 'present')->get();
            if ($presentList->isEmpty()) {
                return "No members are marked present today.";
            }
            $names = $presentList->map(fn($a) => $a->teamMember?->name)->toArray();
            return "Today, the following member(s) are present: " . implode(', ', $names) . ".";
        }

        if (str_contains($q, 'attendance') || str_contains($q, 'today')) {
            $attendance = AttendanceLog::with('teamMember')->whereDate('date', $todayStr)->get();
            if ($attendance->isEmpty()) {
                return "No attendance logged today.";
            }
            $summary = [];
            foreach ($attendance as $att) {
                $checkIn = $att->check_in ?: 'N/A';
                $summary[] = "- {$att->teamMember?->name}: {$att->status} (Check-in time: {$checkIn})";
            }
            return "Today's attendance logs:\n" . implode("\n", $summary);
        }

        if (str_contains($q, 'task') || str_contains($q, 'progress') || str_contains($q, 'completed')) {
            return "Currently, there are {$tasksCount} total tasks logged, with {$completedTasks} completed. You can view the full task lists on the Data Entry or Reports pages.";
        }

        if (str_contains($q, 'member') || str_contains($q, 'team') || str_contains($q, 'who')) {
            $list = TeamMember::pluck('name')->toArray();
            return "Our team consists of {$membersCount} members: " . implode(', ', $list) . ". Let me know if you would like me to retrieve specific details.";
        }

        return "I am currently operating in offline mode because of {$offlineReason}. "
            . "I received your question: \"{$question}\", but could not find a direct keyword match in local rule database. "
            . "However, according to my database logs, we have {$membersCount} team members and {$tasksCount} tasks registered. "
            . "Try asking about 'attendance', 'tasks', 'absent members', or a specific team member name to get detailed data!";
    }
}
