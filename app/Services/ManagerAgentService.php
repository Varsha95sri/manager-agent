<?php
// app/Services/ManagerAgentService.php

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

class ManagerAgentService
{
    /**
     * Fetch tasks with team member names for given date.
     */
    public function readTasks(string $date): array
    {
        return Task::with('teamMember')
            ->whereDate('due_date', $date)
            ->get()
            ->map(fn($task) => [
                'id' => $task->id,
                'team_member_id' => $task->team_member_id,
                'member_name' => $task->teamMember?->name ?? 'Unknown',
                'title' => $task->title,
                'status' => $task->status,
                'due_date' => $task->due_date,
            ])
            ->toArray();
    }

    /**
     * Fetch commits with team member names for given date.
     */
    public function readGitCommits(string $date): array
    {
        return GitCommit::with('teamMember')
            ->whereDate('committed_at', $date)
            ->get()
            ->map(fn($commit) => [
                'id' => $commit->id,
                'team_member_id' => $commit->team_member_id,
                'member_name' => $commit->teamMember?->name ?? 'Unknown',
                'github_id' => $commit->teamMember?->github_id ?? 'N/A',
                'commit_hash' => $commit->commit_hash,
                'repository_name' => $commit->repository_name ?? 'N/A',
                'message' => $commit->message,
                'committed_at' => $commit->committed_at->toDateTimeString(),
            ])
            ->toArray();
    }

    /**
     * Fetch attendance with team member names for given date.
     */
    public function readAttendance(string $date): array
    {
        return AttendanceLog::with('teamMember')
            ->whereDate('date', $date)
            ->get()
            ->map(fn($log) => [
                'id' => $log->id,
                'team_member_id' => $log->team_member_id,
                'member_name' => $log->teamMember?->name ?? 'Unknown',
                'date' => $log->date,
                'status' => $log->status,
                'check_in' => $log->check_in,
            ])
            ->toArray();
    }

    /**
     * Fetch meeting notes for given date.
     */
    public function readMeetingNotes(string $date): array
    {
        return MeetingNote::whereDate('meeting_date', $date)
            ->get()
            ->map(fn($note) => [
                'id' => $note->id,
                'title' => $note->title,
                'notes' => $note->notes,
                'meeting_date' => $note->meeting_date,
            ])
            ->toArray();
    }

    /**
     * Send all data to LLM API (Ollama).
     */
    public function analyzeWithLLM(array $tasks, array $commits, array $attendance, array $meetings): array
    {
        // Fetch team members list for context
        $teamMembers = TeamMember::all()->map(fn($m) => [
            'id' => $m->id,
            'name' => $m->name,
            'role' => $m->role,
            'email' => $m->email,
        ])->toArray();

        // Fetch historical performance reports for context (e.g., trends)
        $historicalReports = PerformanceReport::latest()
            ->limit(3)
            ->get()
            ->map(fn($r) => [
                'report_date' => $r->report_date,
                'team_productivity' => $r->team_productivity,
                'top_performers' => $r->top_performers,
                'attention_required' => $r->attention_required,
                'risks' => $r->risks,
            ])
            ->toArray();

        $dataContext = [
            'team_members' => $teamMembers,
            'historical_performance_reports' => $historicalReports,
            'today_git_commits' => $commits,
            'today_tasks' => $tasks,
            'today_attendance' => $attendance,
            'today_meeting_notes' => $meetings,
        ];

        $dataJson = json_encode($dataContext, JSON_PRETTY_PRINT);

        $prompt = "You are the Manager Agent, a production-grade AI system designed to analyze daily team activities, git commits, attendance logs, tasks, meeting notes, and historical performance reports.\n"
            . "Your goal is to act as an objective, strategic manager. Evaluate the daily team metrics and produce a comprehensive, professional, and structured report.\n"
            . "You must output ONLY a valid JSON object matching the requested schema. Do not output any Markdown wrapping, code block markers, or additional text. Ensure all JSON fields are populated correctly.\n\n"
            . "Requested JSON Schema:\n"
            . "{\n"
            . "  \"team_productivity\": (integer between 0 and 100, representing overall team productivity for the day),\n"
            . "  \"top_performers\": (array of strings, names of the team members who showed exceptional contribution today),\n"
            . "  \"attention_required\": (array of strings, listing members or issues needing direct managerial intervention, e.g. \"Shipra (Absent)\", \"Rahul (Overdue task: optimize queries)\"),\n"
            . "  \"risks\": (array of strings, detailing any project risks, blockers, delayed timelines, or resource shortages),\n"
            . "  \"full_report\": (string, a concise and detailed markdown-formatted executive report containing sections: Executive Summary, Key Achievements, Team Member Status Breakdown (MANDATORY: list every team member by name and summarize their individual commits, attendance, and tasks next to their name), Activity Details, and Recommendations. Write in a formal, professional management tone. Keep sections concise and focused, using bullet points and summaries instead of long paragraphs to optimize response times)\n"
            . "}\n\n"
            . "Analyze the following team activities context and generate a daily report matching the schema exactly:\n\n"
            . "Context:\n{$dataJson}";

        $responseText = $this->callLLM($prompt, true);

        // Handle possible JSON wrappers like ```json ... ```
        $cleanText = trim($responseText);
        if (str_starts_with($cleanText, '```json')) {
            $cleanText = substr($cleanText, 7);
        }
        if (str_ends_with($cleanText, '```')) {
            $cleanText = substr($cleanText, 0, -3);
        }
        $cleanText = trim($cleanText);

        $decoded = json_decode($cleanText, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Failed to decode JSON from LLM response. JSON Error: " . json_last_error_msg() . ". Raw text: " . $responseText);
        }

        // Validate required schema elements
        $requiredKeys = ['team_productivity', 'top_performers', 'attention_required', 'risks', 'full_report'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $decoded)) {
                throw new \Exception("Missing required key '{$key}' in LLM JSON response.");
            }
        }

        return $decoded;
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
                    'temperature' => 0.1,
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
     * Generate daily report, save, and return.
     */
    public function generateDailyReport(?string $date = null): array
    {
        @set_time_limit(180);
        @ini_set('max_execution_time', '180');

        $targetDate = $date ?: Carbon::today()->toDateString();

        // 1. Fetch all data for the given date
        $tasks = $this->readTasks($targetDate);
        $commits = $this->readGitCommits($targetDate);
        $attendance = $this->readAttendance($targetDate);
        $meetings = $this->readMeetingNotes($targetDate);

        try {
            // 2. Call LLM API (Ollama) to analyze
            $reportData = $this->analyzeWithLLM($tasks, $commits, $attendance, $meetings);
        } catch (\Throwable $e) {
            // Log the error
            Log::error("ManagerAgentService report generation error: " . $e->getMessage());

            // 3. Robust Fallback in case of API failure or missing keys
            $reportData = $this->generateFallbackReport($tasks, $commits, $attendance, $meetings, $targetDate);
        }

        // 4. Save to performance_reports table
        $report = PerformanceReport::create([
            'report_date' => $targetDate,
            'team_productivity' => (int) ($reportData['team_productivity'] ?? 80),
            'top_performers' => $reportData['top_performers'] ?? [],
            'attention_required' => $reportData['attention_required'] ?? [],
            'risks' => $reportData['risks'] ?? [],
            'full_report' => $reportData['full_report'] ?? 'Standard report could not be generated.',
        ]);

        return $report->toArray();
    }

    /**
     * Generate a realistic local report when the API is unavailable.
     */
    protected function generateFallbackReport(array $tasks, array $commits, array $attendance, array $meetings, string $date): array
    {
        // 1. Calculate productivity based on attendance and task status
        $totalMembers = TeamMember::count() ?: 1;
        $presentCount = collect($attendance)->whereIn('status', ['present', 'late'])->count();
        $absentCount = collect($attendance)->where('status', 'absent')->count();
        $completedTasks = collect($tasks)->where('status', 'completed')->count();
        $totalTasks = collect($tasks)->count();

        // Base attendance factor
        $attendanceScore = ($presentCount / $totalMembers) * 100;
        
        // Task completion factor
        $taskScore = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 85;

        $productivity = (int) (($attendanceScore * 0.4) + ($taskScore * 0.6));
        if ($productivity < 10) $productivity = 82; // Fallback default
        if ($productivity > 100) $productivity = 100;

        // 2. Determine top performers based on commits and completed tasks
        $performersMap = [];
        foreach ($commits as $c) {
            $name = $c['member_name'];
            $performersMap[$name] = ($performersMap[$name] ?? 0) + 1.5;
        }
        foreach ($tasks as $t) {
            if ($t['status'] === 'completed') {
                $name = $t['member_name'];
                $performersMap[$name] = ($performersMap[$name] ?? 0) + 1;
            }
        }
        arsort($performersMap);
        $topPerformers = array_slice(array_keys($performersMap), 0, 2);
        if (empty($topPerformers)) {
            $topPerformers = []; // No dummy default performers
        }

        // 3. Attention required based on absentees, lates, or pending tasks
        $attentionList = [];
        foreach ($attendance as $att) {
            if ($att['status'] === 'absent') {
                $attentionList[] = $att['member_name'] . ' (Absent)';
            } elseif ($att['status'] === 'late') {
                $attentionList[] = $att['member_name'] . ' (Late check-in)';
            }
        }
        $overdueTasks = Task::with('teamMember')
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereDate('due_date', '<', $date)
            ->get();

        foreach ($overdueTasks as $task) {
            $memberName = $task->teamMember?->name ?? 'Unknown';
            $desc = $memberName . ' (Overdue task: ' . $task->title . ')';
            if (!in_match_name($attentionList, $memberName)) {
                $attentionList[] = $desc;
            }
        }
        if (empty($attentionList)) {
            $attentionList = []; // No dummy attention items
        }

        // 4. Risks list
        $risks = [];
        if ($absentCount > 1) {
            $risks[] = "Multiple team members absent ({$absentCount} members). Potential timeline impact.";
        }
        $overdueCount = Task::whereIn('status', ['pending', 'in_progress'])
            ->whereDate('due_date', '<', $date)
            ->count();
        if ($overdueCount > 0) {
            $risks[] = "{$overdueCount} tasks are currently overdue. Milestones might be delayed.";
        }
        foreach ($meetings as $m) {
            if (stripos($m['notes'], 'delay') !== false || stripos($m['notes'], 'block') !== false) {
                $risks[] = "Meeting note highlight: " . substr($m['notes'], 0, 60) . "...";
            }
        }
        if (empty($risks)) {
            $risks[] = "None identified. Keep monitoring pending task statuses.";
        }

        // 5. Generate detailed text report
        $formattedDate = Carbon::parse($date)->format('F j, Y');
        $commitsCount = count($commits);
        
        $fullReport = "### Manager Agent Performance Report for {$formattedDate}\n\n";
        $fullReport .= "#### Executive Summary\n";
        $fullReport .= "Today, the team recorded a productivity index of **{$productivity}%**. ";
        $fullReport .= "A total of **{$commitsCount} git commits** were pushed to the repository, and **{$completedTasks} / {$totalTasks} tasks** were completed.\n\n";
        
        $fullReport .= "#### Key Achievements\n";
        if ($commitsCount > 0) {
            $fullReport .= "- Active codebase contribution with {$commitsCount} commits, indicating high development momentum.\n";
        }
        if ($completedTasks > 0) {
            $fullReport .= "- {$completedTasks} tasks successfully completed and pushed to production/testing stages.\n";
        } else {
            $fullReport .= "- Focused on codebase restructuring and design alignment, with no tasks completed today.\n";
        }

        $fullReport .= "\n#### Activity Details\n";
        $fullReport .= "- **Attendance Summary**: {$presentCount} present/late, {$absentCount} absent out of {$totalMembers} total members.\n";
        if (count($meetings) > 0) {
            $fullReport .= "- **Meetings**: " . count($meetings) . " sync sessions occurred. Key notes suggest ongoing progress in general development lanes.\n";
        }

        $fullReport .= "\n*Note: This report was compiled using local automated statistics due to Claude API server integration fallback.*";

        return [
            'team_productivity' => $productivity,
            'top_performers' => $topPerformers,
            'attention_required' => $attentionList,
            'risks' => $risks,
            'full_report' => $fullReport,
        ];
    }

    /**
     * Generate an AI-powered evening performance report for a specific employee.
     */
    public function generateEmployeeReport(TeamMember $member, string $date): string
    {
        // 1. Fetch data for this specific member
        $tasks = Task::where(function($query) use ($member) {
                $query->where('team_member_id', $member->id)
                      ->orWhereHas('teamMembers', function($q) use ($member) {
                          $q->where('team_member_id', $member->id);
                      });
            })
            ->whereDate('due_date', $date)
            ->get()
            ->map(fn($t) => ['title' => $t->title, 'status' => $t->status, 'due_date' => $t->due_date])
            ->toArray();

        $commits = GitCommit::where('team_member_id', $member->id)
            ->whereDate('committed_at', $date)
            ->get()
            ->map(fn($c) => ['message' => $c->message, 'hash' => $c->commit_hash, 'repo' => $c->repository_name])
            ->toArray();

        $attendance = AttendanceLog::where('team_member_id', $member->id)
            ->whereDate('date', $date)
            ->first();

        $attendanceStatus = $attendance ? $attendance->status : 'no record';
        $checkInTime = $attendance && $attendance->check_in ? $attendance->check_in : 'N/A';

        $dataContext = [
            'employee' => [
                'name' => $member->name,
                'role' => $member->role,
                'email' => $member->email
            ],
            'date' => $date,
            'today_attendance' => [
                'status' => $attendanceStatus,
                'check_in' => $checkInTime
            ],
            'today_tasks' => $tasks,
            'today_commits' => $commits,
        ];

        $dataJson = json_encode($dataContext, JSON_PRETTY_PRINT);

        $prompt = "You are the Manager Agent, a strategic engineering manager. Analyze the daily activities of team member {$member->name} (Role: {$member->role}) for date {$date} and produce a concise, professional evening performance report.\n\n"
            . "Context:\n{$dataJson}\n\n"
            . "Generate the report in clean Markdown format containing exactly these sections:\n"
            . "### 📊 Summary of Today's Work\n"
            . "(Summarize completed and pending tasks, commits and general activity)\n"
            . "### ⚡ Productivity & Momentum Review\n"
            . "(Evaluate code contribution, check-in status, and work pace compared to expectations)\n"
            . "### 🚀 Actionable Recommendations\n"
            . "(Provide 1-2 professional suggestions or next steps for tomorrow, including any blockers or attention items)\n\n"
            . "Keep the report brief, professional, and clear. Output ONLY the markdown report text.";

        try {
            return $this->callLLM($prompt);
        } catch (\Throwable $e) {
            Log::error("Failed to generate employee report for {$member->name}: " . $e->getMessage());
            
            // Generate robust fallback text
            $formattedDate = Carbon::parse($date)->format('F j, Y');
            $completedCount = collect($tasks)->where('status', 'completed')->count();
            $totalCount = count($tasks);
            $commitsCount = count($commits);

            $output = "### 📊 Summary of Today's Work\n";
            $output .= "Today on **{$formattedDate}**, **{$member->name}** completed **{$completedCount} / {$totalCount} assigned tasks** ";
            $output .= "and pushed **{$commitsCount} git commits** to version control.\n\n";

            $output .= "### ⚡ Productivity & Momentum Review\n";
            $output .= "- **Attendance Status**: Marked as **" . ucfirst($attendanceStatus) . "** (Check-in time: {$checkInTime}).\n";
            if ($commitsCount > 0) {
                $output .= "- **Code Contribution**: Pushed {$commitsCount} commits, demonstrating active development contribution today.\n";
            } else {
                $output .= "- **Code Contribution**: No git commits logged today.\n";
            }
            if ($totalCount > 0) {
                $output .= "- **Task Progress**: Task completion rate is **" . (int)(($completedCount / $totalCount) * 100) . "%**.\n";
            } else {
                $output .= "- **Task Progress**: No workflow tasks scheduled for today.\n";
            }

            $output .= "\n### 🚀 Actionable Recommendations\n";
            if ($attendanceStatus === 'absent') {
                $output .= "1. Follow up with team members to catch up on today's missed syncs and updates.\n";
            } elseif ($attendanceStatus === 'late') {
                $output .= "1. Aim to sync earlier in the morning for standard standup alignment.\n";
            }
            if ($totalCount > $completedCount) {
                $output .= "1. Prioritize completing remaining pending tasks: " . implode(', ', collect($tasks)->where('status', '!=', 'completed')->pluck('title')->toArray()) . " tomorrow morning.\n";
            } else {
                $output .= "1. Maintain the current consistent velocity. Ready to be assigned new tickets in the next sprint sync.\n";
            }

            $output .= "\n\n*Note: This report was compiled using local automated statistics due to Claude/Ollama API server integration fallback.*";
            return $output;
        }
    }
}

/**
 * Simple helper to check if name is in list.
 */
function in_match_name(array $list, string $name): bool
{
    foreach ($list as $item) {
        if (stripos($item, $name) !== false) {
            return true;
        }
    }
    return false;
}
