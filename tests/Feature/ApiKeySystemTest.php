<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiKeySystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_keys_routes_require_authentication(): void
    {
        $this->getJson('/api/developer/keys')->assertStatus(401);
        $this->postJson('/api/developer/keys', ['label' => 'Test Key'])->assertStatus(401);
        $this->deleteJson('/api/developer/keys/1')->assertStatus(401);
    }

    public function test_can_generate_and_list_api_keys(): void
    {
        $user = User::factory()->create();

        // 1. Create a key
        $response = $this->actingAs($user)
            ->postJson('/api/developer/keys', [
                'label' => 'Production Key',
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'key',
            'api_key' => [
                'id',
                'label',
                'key_prefix',
                'is_active',
                'user_id',
                'created_at',
                'updated_at',
            ],
        ]);

        $plainKey = $response->json('key');
        $this->assertStringStartsWith('mgr_live_', $plainKey);

        // Assert database has key hash
        $this->assertDatabaseHas('api_keys', [
            'label' => 'Production Key',
            'user_id' => $user->id,
            'key_hash' => hash('sha256', $plainKey),
        ]);

        // 2. List the keys
        $listResponse = $this->actingAs($user)
            ->getJson('/api/developer/keys');

        $listResponse->assertStatus(200);
        $listResponse->assertJsonCount(1);
        $this->assertEquals('Production Key', $listResponse->json('0.label'));
    }

    public function test_can_soft_delete_api_key(): void
    {
        $user = User::factory()->create();
        $apiKey = ApiKey::create([
            'user_id' => $user->id,
            'label' => 'To Delete',
            'key_hash' => hash('sha256', 'mgr_live_123'),
            'key_prefix' => 'mgr_live_123',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/developer/keys/{$apiKey->id}");

        $response->assertStatus(200);

        // Check it is soft-deleted
        $this->assertSoftDeleted('api_keys', [
            'id' => $apiKey->id,
        ]);
    }

    public function test_api_key_middleware_validates_and_authenticates(): void
    {
        // Define a temporary route using the middleware for testing
        Route::get('/test-api-key-middleware', function () {
            return response()->json(['user_id' => auth()->id()]);
        })->middleware('api.key');

        $user = User::factory()->create();
        $plainKey = 'mgr_live_test_middleware_key_12345';
        ApiKey::create([
            'user_id' => $user->id,
            'label' => 'Auth Key',
            'key_hash' => hash('sha256', $plainKey),
            'key_prefix' => 'mgr_live_test_mid',
            'is_active' => true,
        ]);

        // 1. Missing key
        $this->getJson('/test-api-key-middleware')
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized: API key is missing.']);

        // 2. Invalid key
        $this->getJson('/test-api-key-middleware', ['x-api-key' => 'mgr_live_invalid'])
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized: Invalid API key.']);

        // 3. Valid key
        $response = $this->getJson('/test-api-key-middleware', ['x-api-key' => $plainKey]);
        $response->assertStatus(200);
        $response->assertJson(['user_id' => $user->id]);

        // Verify last_used_at is updated
        $apiKeyRecord = ApiKey::where('key_hash', hash('sha256', $plainKey))->first();
        $this->assertNotNull($apiKeyRecord->last_used_at);
    }
}
