<?php

namespace Tests\Feature;

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class GroupReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_report_route_requires_authentication(): void
    {
        $response = $this->get('/manager-agent/group-report?ids=1,2');
        $response->assertRedirect('/login');
    }

    public function test_group_report_fails_without_member_ids(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/manager-agent/group-report');

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'No member IDs provided.'
        ]);
    }

    public function test_group_report_generates_report_successfully(): void
    {
        $user = User::factory()->create();
        $member1 = TeamMember::factory()->create(['name' => 'Alice']);
        $member2 = TeamMember::factory()->create(['name' => 'Bob']);

        $date = Carbon::today()->toDateString();
        $response = $this->actingAs($user)
            ->getJson("/manager-agent/group-report?ids={$member1->id},{$member2->id}&date={$date}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'group_name',
            'date',
            'report'
        ]);

        $response->assertJsonFragment([
            'success' => true,
            'group_name' => 'Alice & Bob',
            'date' => $date
        ]);
        
        $this->assertStringContainsString('Alice, Bob', $response->json('report'));
    }
}
