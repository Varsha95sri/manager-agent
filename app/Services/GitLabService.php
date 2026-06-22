<?php

namespace App\Services;

use App\Models\Project;
use App\Models\TeamMember;
use App\Models\Commit;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GitLabService
{
    protected string $baseUrl;
    protected ?string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.gitlab.base_url', 'https://gitlab.com'), '/');
        $this->token = config('services.gitlab.access_token');
    }

    /**
     * Fetch commits for a specific project from GitLab.
     *
     * @param string|int $projectId
     * @param array $params
     * @return array
     */
    public function fetchCommits($projectId, array $params = []): array
    {
        if (empty($this->token)) {
            Log::warning('GitLab API request attempted without an access token.');
            return [];
        }

        $url = "{$this->baseUrl}/api/v4/projects/" . urlencode($projectId) . "/repository/commits";

        try {
            $response = Http::withoutVerifying()
                ->withOptions(['verify' => false]) // explicitly add verify false to handle windows curl issues
                ->withHeaders(['PRIVATE-TOKEN' => $this->token])
                ->connectTimeout(2)
                ->timeout(3)
                ->get($url, $params);

            if ($response->failed()) {
                Log::error("GitLab API error for project {$projectId}: " . $response->body());
                return [];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error("Failed fetching commits from GitLab for project {$projectId}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Sync commits for a given project.
     *
     * @param Project $project
     * @param array $params
     * @return int Number of newly imported commits
     */
    public function syncCommits(Project $project, array $params = []): int
    {
        $gitlabProjectId = $project->gitlab_project_id;
        if (!$gitlabProjectId) {
            Log::warning("Project ID {$project->id} does not have a gitlab_project_id set.");
            return 0;
        }

        $commitsData = $this->fetchCommits($gitlabProjectId, $params);
        $importedCount = 0;

        foreach ($commitsData as $commitData) {
            $sha = $commitData['id'] ?? null;
            if (!$sha) {
                continue;
            }

            // Check if already exists
            if (Commit::where('commit_sha', $sha)->exists()) {
                continue;
            }

            $authorEmail = $commitData['author_email'] ?? '';
            $employee = TeamMember::where('email', $authorEmail)->first();

            Commit::create([
                'project_id' => $project->id,
                'employee_id' => $employee?->id,
                'commit_sha' => $sha,
                'message' => $commitData['message'] ?? '',
                'commit_url' => $commitData['web_url'] ?? '',
                'committed_at' => Carbon::parse($commitData['committed_date'] ?? $commitData['created_at'] ?? now()),
            ]);

            $importedCount++;
        }

        return $importedCount;
    }

    /**
     * Fetch all available projects from GitLab for the authenticated user.
     *
     * @return array
     */
    public function getProjects(): array
    {
        if (empty($this->token)) {
            return [];
        }

        try {
            $response = Http::withoutVerifying()
                ->withOptions(['verify' => false])
                ->withHeaders(['PRIVATE-TOKEN' => $this->token])
                ->connectTimeout(2)
                ->timeout(4)
                ->get("{$this->baseUrl}/api/v4/projects", [
                'membership' => 'true',
                'simple' => 'true',
                'order_by' => 'updated_at',
                'per_page' => 100
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("GitLab getProjects error: " . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("Failed fetching projects from GitLab: " . $e->getMessage());
            return [];
        }
    }
}
