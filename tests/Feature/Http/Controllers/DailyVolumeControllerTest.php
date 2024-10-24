<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\DailyVolume;
use App\Services\DailyVolumeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyVolumeControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test it can list daily volumes with pagination.
     *
     * @return void
     */
    public function test_it_can_list_daily_volumes_with_pagination()
    {
        $this->actingAsAuthenticatedTestUser();

        // Create some dummy data
        DailyVolume::factory()->count(15)->create([ // Create 15 records
            'customer_id' => 1,
        ]);

        // Example filter parameters
        $filters = [
            'customer_id' => 1,
            'created_at_from' => '2023-01-01',
            'created_at_to' => now()->toDateString(),
        ];

        // Request the first page with a per_page limit of 10
        $response = $this->getJson('/api/daily-volumes?' . http_build_query(array_merge($filters, ['per_page' => 10])));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(10, 'data'); // Should return 10 records on the first page

        // Check for pagination metadata
        $this->assertArrayHasKey('current_page', $response->json('meta'));
        $this->assertArrayHasKey('last_page', $response->json('meta'));
        $this->assertArrayHasKey('per_page', $response->json('meta'));
        $this->assertArrayHasKey('total', $response->json('meta'));

        // Now request the second page
        $response = $this->getJson('/api/daily-volumes?' . http_build_query(array_merge($filters, ['per_page' => 10, 'page' => 2])));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(5, 'data'); // Should return 5 records on the second page
    }


    /**
     * Test if a specific daily volume can be viewed by ID.
     *
     * @return void
     */
    public function test_it_can_view_a_single_daily_volume()
    {
        $this->actingAsAuthenticatedTestUser();
        $dailyVolume = DailyVolume::factory()->create();

        $response = $this->getJson("/api/daily-volumes/view/{$dailyVolume->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $dailyVolume->id,
                    'customer_id' => $dailyVolume->customer_id,
                    'volume' => $dailyVolume->volume,
                    'created_at' => $dailyVolume->created_at,
                    'updated_at' => $dailyVolume->updated_at,
                ],
            ]);
    }

    /**
     * Test if a daily volume can be deleted.
     *
     * @return void
     */
    public function test_it_can_delete_a_daily_volume()
    {
        $this->actingAsAuthenticatedTestUser();
        $dailyVolume = DailyVolume::factory()->create();

        $response = $this->deleteJson("/api/daily-volumes/{$dailyVolume->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('daily_volumes', [
            'id' => $dailyVolume->id,
        ]);
    }
}
