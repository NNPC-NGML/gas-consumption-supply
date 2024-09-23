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
     * Test if a list of daily volumes can be fetched with filters.
     *
     * @return void
     */
    public function test_it_can_list_daily_volumes_with_filters()
    {
        $this->actingAsAuthenticatedTestUser();

        // Create some dummy data
        DailyVolume::factory()->count(5)->create();

        // Example filter parameters
        $filters = [
            'status' => 'active',
            'customer_id' => 1,
            'created_at_from' => '2023-01-01',
            'created_at_to' => '2023-12-31',
        ];

        $response = $this->getJson('/api/daily-volumes', $filters);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(5, 'data'); // Adjust count as per created data
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

        $response = $this->getJson("/api/daily-volumes/{$dailyVolume->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $dailyVolume->id,
                    'customer_id' => $dailyVolume->customer_id,
                    'volume' => $dailyVolume->volume,
                    'rate' => $dailyVolume->rate,
                    'amount' => $dailyVolume->amount,
                    'created_at' => $dailyVolume->created_at->toISOString(),
                    'updated_at' => $dailyVolume->updated_at->toISOString(),
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
