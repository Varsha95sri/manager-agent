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
    public function answerQuestion(string $question, array $chatHistory = []): string
    {
        // 1. Gather live database context using the question to pull relevant details
        $context = $this->buildDatabaseContext($question);

        try {
            // Format chat history
            $historyText = "";
            if (!empty($chatHistory)) {
                $historyText = "=== PREVIOUS CONVERSATION HISTORY ===\n";
                // Get last 6 messages (3 interactions) to save tokens
                $recentHistory = array_slice($chatHistory, -6);
                foreach ($recentHistory as $msg) {
                    $role = $msg['role'] === 'user' ? 'Manager' : 'Assistant';
                    $historyText .= "{$role}: {$msg['text']}\n";
                }
                $historyText .= "=====================================\n\n";
            }

            // Build the prompt for logging
            $prompt = "You are a Manager Assistant AI. You have access to this real-time database snapshot:\n\n"
                . $context . "\n\n"
                . $historyText
                . "Use this information to answer the manager's question accurately. Keep answers professional, insightful, and concise.\n"
                . "MANDATORY BEHAVIOR: If the manager asks about or inputs a specific employee's/team member's name (e.g. \"Alice\", \"Rahul\", etc.), compile a detailed performance report for that specific employee. "
                . "Summarize how much work they completed, how many commits they made today, which tasks are assigned to them (with due dates and statuses), their attendance check-in status today, and evaluate their contribution compared to others.\n"
                . "If the database snapshot does not contain enough information to answer, state that honestly.\n\n"
                . "Manager Question: " . $question;

            Log::info("AI Chatbot Prompt:\n" . $prompt);

            // 2. Call local Ollama API with the context
            $answer = $this->queryOllama($context, $question, $historyText);
            
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
     * Answer employee question using database context restricted to the employee and Claude API.
     */
    public function answerEmployeeQuestion(string $question, TeamMember $employee, array $chatHistory = []): string
    {
        $context = $this->buildEmployeeDatabaseContext($employee, $question);

        try {
            // Format chat history
            $historyText = "";
            if (!empty($chatHistory)) {
                $historyText = "=== PREVIOUS CONVERSATION HISTORY ===\n";
                $recentHistory = array_slice($chatHistory, -6);
                foreach ($recentHistory as $msg) {
                    $role = $msg['role'] === 'user' ? 'Employee' : 'Assistant';
                    $historyText .= "{$role}: {$msg['text']}\n";
                }
                $historyText .= "=====================================\n\n";
            }

            $prompt = "You are an Employee Assistant AI. You only have access to information about {$employee->name}. Answer questions based on their commits, tasks, productivity, and attendance. Do not answer questions about other employees. You must refuse to provide data on other team members.\n\n"
                . $context . "\n\n"
                . $historyText
                . "Use this information to answer the employee's question accurately. Be helpful and encouraging.\n"
                . "Employee Question: " . $question;

            Log::info("Employee AI Chatbot Prompt:\n" . $prompt);

            $answer = $this->queryOllama($context, $question, $historyText, true);
            
            Log::info("Employee AI Chatbot Response:\n" . $answer);
            return $answer;
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            Log::error("ChatbotAgentService (Employee) Error: " . $msg);

            // Fallback response
            return "I am currently operating in offline mode because the AI service is unavailable. However, I can confirm you have " . $employee->tasks()->count() . " total tasks assigned.";
        }
    }

    /**
     * Compile comprehensive database tables and aggregates into a single context text block.
     * @param string $question The manager's question for dynamic context retrieval
     */
    protected function buildDatabaseContext(string $question = ''): string
    {
        $q = strtolower($question);
        
        // 1. Custom Date Parsing
        $targetDate = Carbon::today();
        $dateLabel = "Today";
        if (str_contains($q, 'yesterday')) {
            $targetDate = Carbon::yesterday();
            $dateLabel = "Yesterday";
        } elseif (str_contains($q, 'last week')) {
            $targetDate = Carbon::now()->subWeek();
            $dateLabel = "Last Week";
        } elseif (str_contains($q, 'last month')) {
            $targetDate = Carbon::now()->subMonth();
            $dateLabel = "Last Month";
        }
        
        $targetDateStr = $targetDate->toDateString();
        $contextText = "LIVE DATABASE TEAM CONTEXT (As of " . Carbon::now()->toDateTimeString() . ", Target Date: {$dateLabel} {$targetDateStr}):\n\n";

        // 2. Team Members & Role/Team Aggregation
        $members = TeamMember::orderBy('name')->get();
        $totalMembersCount = $members->count();
        $roleStats = [];
        $membersText = "Total Team Members Count: {$totalMembersCount}\nLatest Registered Team Members (Preview):\n";
        foreach ($members as $m) {
            $score = $m->performance_score ?? 0;
            $grade = $m->performance_grade ?? 'N/A';
            $role = $m->role ?? 'Unassigned';
            $membersText .= "- ID {$m->id}: {$m->name} (Role/Team: {$role}, Score: {$score}, Grade: {$grade})\n";
            
            if (!isset($roleStats[$role])) {
                $roleStats[$role] = ['count' => 0, 'total_score' => 0];
            }
            $roleStats[$role]['count']++;
            $roleStats[$role]['total_score'] += $score;
        }

        $teamComparisonText = "=== TEAM / DEPARTMENT COMPARISON ===\n";
        foreach ($roleStats as $role => $stats) {
            $avgScore = $stats['count'] > 0 ? round($stats['total_score'] / $stats['count'], 1) : 0;
            $teamComparisonText .= "- Team {$role}: {$stats['count']} members, Average Score: {$avgScore}\n";
        }

        // 2. Task Metrics & Workload
        $tasksCount = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();
        $pendingTasks = Task::where('status', 'pending')->count();
        $overdueTasksCount = Task::where('status', '!=', 'completed')->whereDate('due_date', '<', Carbon::today())->count();
        
        $detailedTasks = Task::with('teamMember')->where('status', '!=', 'completed')->get();
        $workloadByMember = [];
        $workloadByRole = [];
        foreach ($detailedTasks as $t) {
            $mName = $t->teamMember?->name ?? 'Unassigned';
            $mRole = $t->teamMember?->role ?? 'Unassigned';
            $workloadByMember[$mName] = ($workloadByMember[$mName] ?? 0) + 1;
            $workloadByRole[$mRole] = ($workloadByRole[$mRole] ?? 0) + 1;
        }
        arsort($workloadByMember);
        arsort($workloadByRole);
        
        $tasksText = "=== TASK METRICS & WORKLOAD ===\n"
            . "Total Tasks: {$tasksCount} | Completed: {$completedTasks} | Pending: {$pendingTasks} | Overdue: {$overdueTasksCount}\n"
            . "Most Overloaded Employees (Pending Tasks):\n";
        foreach (array_slice($workloadByMember, 0, 5) as $name => $count) {
            $tasksText .= "- {$name}: {$count} tasks\n";
        }
        $tasksText .= "Delayed/Pending Tasks by Team:\n";
        foreach (array_slice($workloadByRole, 0, 5) as $role => $count) {
            $tasksText .= "- {$role}: {$count} tasks\n";
        }

        // 3. Organization Historical Trend (Last 6 Months)
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $historicalReports = PerformanceReport::whereDate('report_date', '>=', $sixMonthsAgo)
            ->orderBy('report_date', 'asc')
            ->get()
            ->groupBy(function($val) {
                return Carbon::parse($val->report_date)->format('Y-m');
            });
            
        $trendText = "=== ORGANIZATION HISTORICAL TREND (Last 6 Months) ===\n";
        if ($historicalReports->isEmpty()) {
            $trendText .= "Not enough historical data available.\n";
        } else {
            foreach ($historicalReports as $month => $reports) {
                $avgProd = round($reports->avg('team_productivity'), 1);
                $trendText .= "- {$month}: {$avgProd}% average productivity\n";
            }
        }

        // 4. Target Date Commits & Attendance
        $commitsCount = GitCommit::whereDate('committed_at', $targetDateStr)->count();
        $attendance = AttendanceLog::with('teamMember')->whereDate('date', $targetDateStr)->get();
        $presentCount = $attendance->where('status', 'present')->count();
        $lateCount = $attendance->where('status', 'late')->count();
        $absentCount = $attendance->where('status', 'absent')->count();
        
        $dailyText = "=== {$dateLabel}'S OPERATIONS ===\n"
            . "Total Git Commits {$dateLabel}: {$commitsCount}\n"
            . "Attendance Summary -> Present: {$presentCount}, Late: {$lateCount}, Absent: {$absentCount}\n";
        $absentMembers = $attendance->where('status', 'absent');
        if ($absentMembers->isNotEmpty()) {
            $dailyText .= "Absent Members:\n";
            foreach ($absentMembers as $att) {
                $dailyText .= "- {$att->teamMember?->name}\n";
            }
        }

        // 5. Active Projects & Health
        $projects = \App\Models\Project::whereNotIn('status', ['completed', 'archived'])->get();
        $projectsText = "=== ACTIVE PROJECTS ===\n";
        foreach ($projects as $p) {
            $projectsText .= "- \"{$p->name}\" (Health: {$p->health_score}%, Status: {$p->status}, Deadline: {$p->deadline})\n";
        }
        if ($projects->isEmpty()) {
            $projectsText .= "No active projects found.\n";
        }

        // 6. GitLab Issues & Tech Debt
        $issues = \App\Models\GitLabIssue::all();
        $issueCount = $issues->count();
        $openIssues = $issues->where('state', 'opened')->count();
        $gitlabText = "=== GITLAB ISSUES ===\n";
        $gitlabText .= "Total Issues: {$issueCount} | Open: {$openIssues}\n";

        // 7. Latest Performance Report
        $latestReport = PerformanceReport::latest()->first();
        $reportText = "=== LATEST EVALUATED REPORT ===\nNo performance reports found in database yet.\n";
        if ($latestReport) {
            $perfDate = Carbon::parse($latestReport->report_date)->format('Y-m-d');
            $performers = implode(', ', $latestReport->top_performers);
            $attention = implode(', ', $latestReport->attention_required);
            $risks = implode(' | ', $latestReport->risks);
            $reportText = "Date: {$perfDate}\nProductivity Score: {$latestReport->team_productivity}%\nTop Performers: {$performers}\nRequires Attention: {$attention}\nIdentified Risks: {$risks}\n";
        }

        $finalContext = $contextText 
            . "=== TEAM MEMBERS ===\n{$membersText}\n"
            . "{$teamComparisonText}\n"
            . "{$tasksText}\n"
            . "{$trendText}\n"
            . "{$dailyText}\n"
            . "{$projectsText}\n"
            . "{$gitlabText}\n"
            . "{$reportText}\n";

        // Dynamic Context Expansion (RAG-lite)
        $dynamicContext = "\n=== DYNAMIC CONTEXT (Based on User Query) ===\n";
        $addedDynamic = false;

        // Organization/Leaderboard Intent
        if (str_contains($q, 'top') || str_contains($q, 'best') || str_contains($q, 'lowest') || str_contains($q, 'perform') || str_contains($q, 'promot')) {
            $topMembers = TeamMember::orderBy('performance_score', 'desc')->take(10)->get();
            $bottomMembers = TeamMember::orderBy('performance_score', 'asc')->take(10)->get();
            $dynamicContext .= "Top 10 Performers:\n";
            foreach ($topMembers as $m) {
                $dynamicContext .= "- {$m->name} (Score: {$m->performance_score})\n";
            }
            $dynamicContext .= "Lowest 10 Performers:\n";
            foreach ($bottomMembers as $m) {
                $dynamicContext .= "- {$m->name} (Score: {$m->performance_score})\n";
            }
            $addedDynamic = true;
        }

        // Employee Comparison Intent
        $allMembers = TeamMember::all();
        $mentionedMembers = [];
        foreach ($allMembers as $member) {
            $firstName = strtolower(explode(' ', $member->name)[0]);
            $memberNameLower = strtolower($member->name);
            if (preg_match("/\b" . preg_quote($memberNameLower, '/') . "\b/i", $q) || 
               (strlen($firstName) > 2 && preg_match("/\b" . preg_quote($firstName, '/') . "\b/i", $q))) {
                $mentionedMembers[] = $member;
            }
        }
        
        if (!empty($mentionedMembers)) {
            foreach ($mentionedMembers as $member) {
                $empTasks = Task::where('team_member_id', $member->id)->get();
                $empCommits = GitCommit::where('team_member_id', $member->id)->orderBy('committed_at', 'desc')->take(5)->get();
                $empAttendance = AttendanceLog::where('team_member_id', $member->id)->orderBy('date', 'desc')->take(7)->get();
                
                $dynamicContext .= "Detailed History for {$member->name}:\n";
                $dynamicContext .= "- Tasks: " . $empTasks->where('status', 'completed')->count() . " completed out of " . $empTasks->count() . "\n";
                if ($empTasks->where('status', '!=', 'completed')->isNotEmpty()) {
                    $dynamicContext .= "- Pending Tasks: " . implode(', ', $empTasks->where('status', '!=', 'completed')->pluck('title')->toArray()) . "\n";
                }
                $dynamicContext .= "- Recent Commits: " . $empCommits->count() . "\n";
                $dynamicContext .= "- Recent Attendance: " . implode(', ', $empAttendance->map(fn($a) => $a->date . ': ' . $a->status)->toArray()) . "\n";
            }
            $addedDynamic = true;
        }

        if ($addedDynamic) {
            $finalContext .= $dynamicContext;
        }

        return $finalContext;
    }

    /**
     * Compile context only for a specific employee.
     */
    protected function buildEmployeeDatabaseContext(TeamMember $employee, string $question = ''): string
    {
        $targetDateStr = Carbon::today()->toDateString();
        $contextText = "LIVE EMPLOYEE CONTEXT (As of " . Carbon::now()->toDateTimeString() . "):\n\n";
        
        $contextText .= "Employee Details:\n";
        $contextText .= "- Name: {$employee->name}\n";
        $contextText .= "- Role/Team: {$employee->role}\n";
        $contextText .= "- Performance Score: " . ($employee->performance_score ?? 'N/A') . "\n";
        $contextText .= "- Grade: " . ($employee->performance_grade ?? 'N/A') . "\n\n";

        // Tasks
        $tasks = Task::where('team_member_id', $employee->id)->get();
        $completed = $tasks->where('status', 'completed')->count();
        $pending = $tasks->where('status', 'pending')->count();
        $inProgress = $tasks->where('status', 'in_progress')->count();
        
        $contextText .= "Tasks Summary: {$completed} Completed, {$inProgress} In Progress, {$pending} Pending\n";
        if ($tasks->where('status', '!=', 'completed')->isNotEmpty()) {
            $contextText .= "Pending/Active Tasks:\n";
            foreach ($tasks->where('status', '!=', 'completed') as $t) {
                $contextText .= "- \"{$t->title}\" (Due: {$t->due_date}, Status: {$t->status})\n";
            }
        }
        $contextText .= "\n";

        // Commits
        $recentCommits = GitCommit::where('team_member_id', $employee->id)->orderBy('committed_at', 'desc')->take(10)->get();
        $contextText .= "Recent Git Commits: " . $recentCommits->count() . " found\n";
        foreach ($recentCommits as $c) {
            $contextText .= "- {$c->message} (Date: {$c->committed_at})\n";
        }
        $contextText .= "\n";

        // Attendance
        $attendance = AttendanceLog::where('team_member_id', $employee->id)->orderBy('date', 'desc')->take(5)->get();
        $contextText .= "Recent Attendance:\n";
        foreach ($attendance as $att) {
            $contextText .= "- {$att->date}: {$att->status} (Check-in: " . ($att->check_in ?: 'N/A') . ")\n";
        }

        return $contextText;
    }

    /**
     * Query local Ollama API.
     */
    protected function queryOllama(string $context, string $question, string $historyText = "", bool $isEmployee = false): string
    {
        $persona = $isEmployee 
            ? "You are an Employee Assistant AI. You only have access to information about the logged-in employee."
            : "You are an Executive AI Assistant for managers.";
            
        $rules = $isEmployee
            ? "1. Always base your answers on the employee's personal data.\n2. Do NOT provide data about other employees.\n3. Be helpful, direct, and encouraging."
            : "1. Always justify your answers with the provided data metrics (Attendance, GitLab, Tasks, Performance Scores).\n2. If the manager asks about specific employees, summarize their tasks, commits, attendance, and contribution.\n3. If the database snapshot does not contain enough information to answer, state 'Data not available' honestly. Do not hallucinate.";

        $prompt = "{$persona} You have access to the following real-time database snapshot:\n\n"
            . $context . "\n\n"
            . $historyText
            . "Use this information to answer the question accurately. Keep answers professional and concise.\n"
            . "MANDATORY BEHAVIOR:\n"
            . "{$rules}\n\n"
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
                        'temperature' => 0.1,
                        'num_predict' => 2048,
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
                    'temperature' => 0.1,
                    'num_predict' => 2048,
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
            if (preg_match("/\b" . preg_quote($memberNameLower, '/') . "\b/i", $q) || 
               (strlen($firstName) > 2 && preg_match("/\b" . preg_quote($firstName, '/') . "\b/i", $q))) {
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

        // Leaderboard / Top Contributors rule
        if (str_contains($q, 'top') || str_contains($q, 'best') || str_contains($q, 'contributor') || str_contains($q, 'perform')) {
            $topMembers = TeamMember::orderBy('performance_score', 'desc')->take(5)->get();
            if ($topMembers->isEmpty()) {
                return "No performance data is available to determine top contributors.";
            }
            $res = "Here are the top contributors based on performance scores [Offline Mode]:\n\n";
            foreach ($topMembers as $idx => $m) {
                $rank = $idx + 1;
                $score = $m->performance_score ?? 'N/A';
                $grade = $m->performance_grade ?? 'N/A';
                $res .= "{$rank}. **{$m->name}** - Score: {$score} (Grade: {$grade})\n";
            }
            return $res;
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
