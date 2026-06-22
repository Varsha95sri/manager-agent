<?php

namespace Tests\Feature;

use App\Models\Commit;
use App\Models\Project;
use App\Models\TeamMember;
use App\Services\GitLabService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GitLabIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gitlab.base_url' => 'https://gitlab.com']);
        config(['services.gitlab.access_token' => 'test-token']);
        config(['services.gitlab.webhook_secret' => 'webhook-secret-xyz']);
    }

    public function test_webhook_unauthorized_with_invalid_token(): void
    {
        $response = $this->postJson('/api/webhooks/gitlab', [], [
            'X-Gitlab-Token' => 'wrong-token'
        ]);

        $response->assertStatus(401);
    }

    public function test_webhook_saves_commits_successfully_matching_project_and_employee(): void
    {
        $project = Project::create([
            'name' => 'Test Project',
            'description' => 'A test project',
            'gitlab_project_id' => 15,
            'gitlab_repo_url' => 'http://gitlab.com/test/project'
        ]);

        $employee = TeamMember::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'Developer',
        ]);

        $payload = [
            'object_kind' => 'push',
            'project_id' => 15,
            'project' => [
                'id' => 15,
                'web_url' => 'http://gitlab.com/test/project'
            ],
            'commits' => [
                [
                    'id' => 'sha-123456',
                    'message' => 'First commit message',
                    'timestamp' => '2026-06-19T10:00:00Z',
                    'url' => 'http://gitlab.com/test/project/-/commit/sha-123456',
                    'author' => [
                        'name' => 'John Doe',
                        'email' => 'john@example.com'
                    ]
                ],
                [
                    'id' => 'sha-789012',
                    'message' => 'Second commit message',
                    'timestamp' => '2026-06-19T11:00:00Z',
                    'url' => 'http://gitlab.com/test/project/-/commit/sha-789012',
                    'author' => [
                        'name' => 'Unknown Author',
                        'email' => 'unknown@example.com'
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/api/webhooks/gitlab', $payload, [
            'X-Gitlab-Token' => 'webhook-secret-xyz'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'commits_processed' => 2,
            'commits_saved' => 2
        ]);

        // Check if DB records are created
        $this->assertDatabaseHas('commits', [
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'commit_sha' => 'sha-123456',
            'message' => 'First commit message',
            'commit_url' => 'http://gitlab.com/test/project/-/commit/sha-123456',
        ]);

        $this->assertDatabaseHas('commits', [
            'project_id' => $project->id,
            'employee_id' => null,
            'commit_sha' => 'sha-789012',
            'message' => 'Second commit message',
            'commit_url' => 'http://gitlab.com/test/project/-/commit/sha-789012',
        ]);
    }

    public function test_webhook_ignores_duplicate_commits(): void
    {
        $project = Project::create([
            'name' => 'Test Project',
            'gitlab_project_id' => 15
        ]);

        Commit::create([
            'project_id' => $project->id,
            'commit_sha' => 'sha-duplicate',
            'message' => 'Already exists',
            'commit_url' => 'http://gitlab.com/commit',
            'committed_at' => now()
        ]);

        $payload = [
            'object_kind' => 'push',
            'project_id' => 15,
            'commits' => [
                [
                    'id' => 'sha-duplicate',
                    'message' => 'Another description',
                    'timestamp' => '2026-06-19T10:00:00Z',
                    'url' => 'http://gitlab.com/commit',
                    'author' => [
                        'name' => 'Some Name',
                        'email' => 'some@email.com'
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/api/webhooks/gitlab', $payload, [
            'X-Gitlab-Token' => 'webhook-secret-xyz'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'commits_processed' => 1,
            'commits_saved' => 0
        ]);
    }

    public function test_gitlab_service_fetch_and_sync(): void
    {
        $project = Project::create([
            'name' => 'Test Project',
            'gitlab_project_id' => 456
        ]);

        $employee = TeamMember::create([
            'name' => 'Developer One',
            'email' => 'dev1@example.com',
            'role' => 'Lead Developer'
        ]);

        Http::fake([
            'https://gitlab.com/api/v4/projects/456/repository/commits*' => Http::response([
                [
                    'id' => 'sha-service-123',
                    'message' => 'Commit via service',
                    'web_url' => 'http://gitlab.com/commit/123',
                    'committed_date' => '2026-06-19T12:00:00Z',
                    'author_email' => 'dev1@example.com'
                ]
            ], 200)
        ]);

        $service = new GitLabService();
        $count = $service->syncCommits($project);

        $this->assertEquals(1, $count);
        $this->assertDatabaseHas('commits', [
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'commit_sha' => 'sha-service-123',
            'message' => 'Commit via service'
        ]);
    }

    public function test_gitlab_index_requires_authentication(): void
    {
        $response = $this->get('/manager-agent/gitlab');
        $response->assertRedirect('/login');
    }

    public function test_gitlab_index_displays_data(): void
    {
        $user = \App\Models\User::factory()->create();
        $project = Project::create(['name' => 'Project Alpha']);
        $employee = TeamMember::create(['name' => 'John Beta', 'email' => 'beta@example.com', 'role' => 'QA']);

        $response = $this->actingAs($user)->get('/manager-agent/gitlab');

        $response->assertStatus(200);
        $response->assertSee('Project Alpha');
        $response->assertSee('John Beta');
    }

    public function test_gitlab_update_project(): void
    {
        $user = \App\Models\User::factory()->create();
        $project = Project::create(['name' => 'Project Gamma']);

        $response = $this->actingAs($user)->put("/manager-agent/gitlab/project/{$project->id}", [
            'gitlab_project_id' => 999,
            'gitlab_repo_url' => 'https://gitlab.com/groups/project-gamma'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'gitlab_project_id' => 999,
            'gitlab_repo_url' => 'https://gitlab.com/groups/project-gamma'
        ]);
    }

    public function test_gitlab_update_employee(): void
    {
        $user = \App\Models\User::factory()->create();
        $employee = TeamMember::create(['name' => 'Dev Delta', 'email' => 'delta@example.com', 'role' => 'Dev']);

        $response = $this->actingAs($user)->put("/manager-agent/gitlab/employee/{$employee->id}", [
            'gitlab_user_id' => 888,
            'gitlab_username' => 'delta_git'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('team_members', [
            'id' => $employee->id,
            'gitlab_user_id' => 888,
            'gitlab_username' => 'delta_git'
        ]);
    }
}
