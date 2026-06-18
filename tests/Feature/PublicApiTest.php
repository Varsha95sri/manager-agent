<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $plainKey;
    private ApiKey $apiKey;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->plainKey = 'mgr_live_test_api_key_for_public_api_12345';
        $this->apiKey = ApiKey::create([
            'user_id' => $this->user->id,
            'label' => 'Public API Key',
            'key_hash' => hash('sha256', $this->plainKey),
            'key_prefix' => 'mgr_live_test_api',
            'is_active' => true,
        ]);
    }

    public function test_endpoints_require_api_key(): void
    {
        $this->postJson('/api/v1/generate-report', [])->assertStatus(401);
        $this->postJson('/api/v1/analyze-team', [])->assertStatus(401);
        $this->postJson('/api/v1/chat', [])->assertStatus(401);
    }

    public function test_generate_report_endpoint(): void
    {
        Http::fake([
            'http://127.0.0.1:11434/api/chat' => Http::response([
                'message' => [
                    'role' => 'assistant',
                    'content' => json_encode([
                        'status' => 'excellent',
                        'productivity' => 90
                    ])
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/v1/generate-report', [
            'team_data' => ['member1' => 'commits: 5'],
            'report_type' => 'daily'
        ], [
            'x-api-key' => $this->plainKey
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'excellent',
            'productivity' => 90
        ]);
    }

    public function test_analyze_team_endpoint(): void
    {
        Http::fake([
            'http://127.0.0.1:11434/api/chat' => Http::response([
                'message' => [
                    'role' => 'assistant',
                    'content' => 'Team is doing great'
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/v1/analyze-team', [
            'team_members' => [['name' => 'Alice']],
            'metrics' => ['productivity' => 100]
        ], [
            'x-api-key' => $this->plainKey
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'analysis' => 'Team is doing great'
        ]);
    }

    public function test_chat_endpoint(): void
    {
        Http::fake([
            'http://127.0.0.1:11434/api/chat' => Http::response([
                'message' => [
                    'role' => 'assistant',
                    'content' => 'Hello there!'
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/v1/chat', [
            'prompt' => 'Hi',
            'system_message' => 'Be helpful'
        ], [
            'x-api-key' => $this->plainKey
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'reply' => 'Hello there!'
        ]);
    }

    public function test_rate_limiting(): void
    {
        Http::fake([
            'http://127.0.0.1:11434/api/chat' => Http::response([
                'message' => [
                    'role' => 'assistant',
                    'content' => 'Reply'
                ]
            ], 200)
        ]);

        // Send 20 requests successfully
        for ($i = 0; $i < 20; $i++) {
            $this->postJson('/api/v1/chat', [
                'prompt' => 'Hi'
            ], [
                'x-api-key' => $this->plainKey
            ])->assertStatus(200);
        }

        // 21st request should be throttled
        $this->postJson('/api/v1/chat', [
            'prompt' => 'Hi'
        ], [
            'x-api-key' => $this->plainKey
        ])->assertStatus(429);
    }

    public function test_api_usage_logging_and_retrieval(): void
    {
        Http::fake([
            'http://127.0.0.1:11434/api/chat' => Http::response([
                'message' => [
                    'role' => 'assistant',
                    'content' => 'Logging reply'
                ]
            ], 200)
        ]);

        // 1. Trigger the public API endpoint
        $this->postJson('/api/v1/chat', [
            'prompt' => 'Log this'
        ], [
            'x-api-key' => $this->plainKey
        ])->assertStatus(200);

        // 2. Assert that log was recorded in database
        $this->assertDatabaseHas('api_logs', [
            'user_id' => $this->user->id,
            'api_key_id' => $this->apiKey->id,
            'endpoint' => 'api/v1/chat',
            'method' => 'POST',
            'status_code' => 200,
        ]);

        // 3. Retrieve logs using Developer endpoint
        $response = $this->actingAs($this->user)
            ->getJson('/api/developer/logs');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment([
            'endpoint' => 'api/v1/chat',
            'method' => 'POST',
            'status_code' => 200,
        ]);
    }
}
