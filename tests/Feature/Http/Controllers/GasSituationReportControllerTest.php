<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\GasSituationReport;
use App\Services\GasSituationReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GasSituationReportControllerTest extends TestCase
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
        GasSituationReport::factory()->count(15)->create([ // Create 15 records
            'customer_id' => 1,
        ]);

        // Example filter parameters
        $filters = [
            'customer_id' => 1,
            'created_at_from' => '2023-01-01',
            'created_at_to' => now()->toDateString(),
        ];

        // Request the first page with a per_page limit of 10
        $response = $this->getJson('/api/gas-situation-reports?' . http_build_query(array_merge($filters, ['per_page' => 10])));

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
        $response = $this->getJson('/api/gas-situation-reports?' . http_build_query(array_merge($filters, ['per_page' => 10, 'page' => 2])));

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
        $gasSituationReport = GasSituationReport::factory()->create();

        $response = $this->getJson("/api/gas-situation-reports/{$gasSituationReport->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $gasSituationReport->id,
                    'customer_id' => $gasSituationReport->customer_id,
                    'inlet_pressure' => $gasSituationReport->inlet_pressure,
                    'outlet_pressure' => $gasSituationReport->outlet_pressure,
                    'allocation' => $gasSituationReport->allocation,
                    'nomination' => $gasSituationReport->nomination,
                    'created_at' => $gasSituationReport->created_at,
                    'updated_at' => $gasSituationReport->updated_at,
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
        $gasSituationReport = GasSituationReport::factory()->create();

        $response = $this->deleteJson("/api/gas-situation-reports/{$gasSituationReport->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('daily_volumes', [
            'id' => $gasSituationReport->id,
        ]);
    }
}
