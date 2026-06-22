<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TeamMember;
use App\Models\Commit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GitLabWebhookController extends Controller
{
    /**
     * Handle the incoming GitLab Webhook push event.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleWebhook(Request $request)
    {
        // 1. Verify X-Gitlab-Token header
        $receivedToken = $request->header('X-Gitlab-Token');
        $configuredToken = config('services.gitlab.webhook_secret');

        if (empty($configuredToken) || $receivedToken !== $configuredToken) {
            Log::warning('GitLab Webhook verification failed: Invalid X-Gitlab-Token header.');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 2. Parse payload
        $payload = $request->all();
        
        // Log the received payload for debugging
        Log::info('GitLab Webhook received', ['event' => $request->header('X-Gitlab-Event')]);

        // We only process push events
        $objectKind = $payload['object_kind'] ?? '';
        if ($objectKind !== 'push') {
            return response()->json(['message' => 'Event type not processed'], 200);
        }

        // 3. Find project
        $gitlabProjectId = $payload['project']['id'] ?? $payload['project_id'] ?? null;
        $project = null;

        if ($gitlabProjectId) {
            $project = Project::where('gitlab_project_id', $gitlabProjectId)->first();
        }

        if (!$project) {
            $repoUrl = $payload['project']['web_url'] ?? $payload['project']['http_url'] ?? null;
            if ($repoUrl) {
                $project = Project::where('gitlab_repo_url', $repoUrl)->first();
            }
        }

        if (!$project) {
            Log::warning('GitLab Webhook: Project not found in database.', [
                'gitlab_project_id' => $gitlabProjectId,
                'repo_url' => $payload['project']['web_url'] ?? null
            ]);
            return response()->json(['message' => 'Project not found/tracked'], 200);
        }

        // 4. Save commits
        $commits = $payload['commits'] ?? [];
        $savedCount = 0;

        foreach ($commits as $commitData) {
            $sha = $commitData['id'] ?? null;
            if (!$sha) {
                continue;
            }

            // Skip duplicate commits
            if (Commit::where('commit_sha', $sha)->exists()) {
                continue;
            }

            $authorEmail = $commitData['author']['email'] ?? '';
            $employee = TeamMember::where('email', $authorEmail)->first();

            Commit::create([
                'project_id' => $project->id,
                'employee_id' => $employee?->id,
                'commit_sha' => $sha,
                'message' => $commitData['message'] ?? '',
                'commit_url' => $commitData['url'] ?? '',
                'committed_at' => Carbon::parse($commitData['timestamp'] ?? now()),
            ]);

            $savedCount++;
        }

        return response()->json([
            'message' => 'Webhook processed successfully',
            'commits_processed' => count($commits),
            'commits_saved' => $savedCount
        ], 200);
    }
}
