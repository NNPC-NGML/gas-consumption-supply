<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\GasCost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GasCostControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test it can list gas costs with pagination.
     *
     * @return void
     */
    public function test_it_can_list_gas_costs_with_pagination()
    {
        $this->actingAsAuthenticatedTestUser();

        // Create some dummy data
        GasCost::factory()->count(15)->create(); // Create 15 records

        // Request the first page with a per_page limit of 10
        $response = $this->postJson('/api/gas-costs', ["per_page" => 10]);

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
        $response = $this->postJson('/api/gas-costs', ["per_page" => 10, "page" => 2]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(5, 'data'); // Should return 5 records on the second page
    }

    /**
     * Test if a specific gas cost can be viewed by ID.
     *
     * @return void
     */
    public function test_it_can_view_a_single_gas_cost()
    {
        $this->actingAsAuthenticatedTestUser();
        $gasCost = GasCost::factory()->create();

        $response = $this->getJson("/api/gas-costs/view/{$gasCost->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $gasCost->id,
                    'date_of_entry' => $gasCost->date_of_entry->toDateString(),
                    'dollar_cost_per_scf' => $gasCost->dollar_cost_per_scf,
                    'dollar_rate' => $gasCost->dollar_rate,
                    'status' => $gasCost->status,
                    'created_at' => $gasCost->created_at->toDateTimeString(),
                    'updated_at' => $gasCost->updated_at->toDateTimeString(),
                ],
            ]);
    }

    /**
     * Test if a gas cost can be deleted.
     *
     * @return void
     */
    public function test_it_can_delete_a_gas_cost()
    {
        $this->actingAsAuthenticatedTestUser();
        $gasCost = GasCost::factory()->create();

        $response = $this->deleteJson("/api/gas-costs/delete/{$gasCost->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('gas_costs', [
            'id' => $gasCost->id,
        ]);
    }
}
