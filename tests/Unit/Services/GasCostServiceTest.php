<?php

namespace Tests\Unit\Services;

use App\Models\GasCost;
use App\Services\GasCostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GasCostServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get the service instance.
     *
     * @return GasCostService
     */
    public function getService()
    {
        return new GasCostService();
    }

    /**
     * Test validateGasCost method with valid data.
     *
     * @return void
     */
    public function testValidateWithValidData()
    {
        $service = $this->getService();

        $data = [
            'date_of_entry' => '2024-01-01',
            'dollar_cost_per_scf' => 2.5,
            'dollar_rate' => 3.0,
            'status' => true,
        ];

        $validatedData = $service->validateGasCost($data);

        $this->assertEquals($data, $validatedData);
    }

    /**
     * Test validateGasCost method with invalid data.
     *
     * @return void
     */
    public function testValidateWithInvalidData()
    {
        $this->expectException(ValidationException::class);

        $service = $this->getService();

        $data = [
            'date_of_entry' => null,
            'dollar_cost_per_scf' => -1, // invalid
            'dollar_rate' => -2, // invalid
            'status' => 'not_a_boolean', // invalid
        ];

        $service->validateGasCost($data);
    }

    /**
     * Test create method with valid data.
     *
     * @return void
     */
    public function testCreateGasCost()
    {
        $service = $this->getService();

        $data = [
            'date_of_entry' => '2024-01-01',
            'dollar_cost_per_scf' => 2.5,
            'dollar_rate' => 3.0,
            'status' => true,
        ];

        $gasCost = $service->create($data);

        $this->assertInstanceOf(GasCost::class, $gasCost);
        $this->assertEquals($data['date_of_entry'], $gasCost->date_of_entry->toDateString());
        $this->assertEquals($data['dollar_cost_per_scf'], $gasCost->dollar_cost_per_scf);
    }

    /**
     * Test create method with invalid JSON in form_field_answers.
     *
     * @return void
     */
    public function testCreateWithInvalidJson()
    {
        $this->expectException(\InvalidArgumentException::class);

        $service = $this->getService();

        $data = [
            'date_of_entry' => '2024-01-01',
            'dollar_cost_per_scf' => 2.5,
            'dollar_rate' => 3.0,
            'status' => true,
            'form_field_answers' => 'invalid_json',
        ];

        $service->create($data);
    }

    /**
     * Test update method with valid data.
     *
     * @return void
     */
    public function testUpdateGasCost()
    {
        $service = $this->getService();

        $gasCost = GasCost::factory()->create([
            'date_of_entry' => '2024-01-01',
            'dollar_cost_per_scf' => 2.5,
            'dollar_rate' => 3.0,
            'status' => true,
        ]);

        $data = [
            'id' => $gasCost->id,
            'dollar_cost_per_scf' => 3.0,
        ];

        $updatedGasCost = $service->update($data);

        $this->assertEquals(3.0, $updatedGasCost->dollar_cost_per_scf);
    }

    /**
     * Test update method with invalid data.
     *
     * @return void
     */
    public function testUpdateWithInvalidData()
    {
        $this->expectException(ValidationException::class);

        $service = $this->getService();

        $gasCost = GasCost::factory()->create();

        $data = [
            'id' => $gasCost->id,
            'dollar_cost_per_scf' => -1, // invalid
        ];

        $service->update($data);
    }

    /**
     * Test update method without an ID.
     *
     * @return void
     */
    public function testUpdateWithoutId()
    {
        $this->expectException(\InvalidArgumentException::class);

        $service = $this->getService();

        $data = [
            'dollar_cost_per_scf' => 2.0,
        ];

        $service->update($data);
    }
}
