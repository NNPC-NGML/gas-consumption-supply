<?php

namespace Tests\Unit\Services;

use App\Http\Resources\GasSituationReportResource;
use App\Models\GasSituationReport;
use App\Services\GasSituationReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GasSituationReportServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get the service instance.
     *
     * @return GasSituationReportService
     */
    public function getService()
    {
        return new GasSituationReportService();
    }

    /**
     * Test validateGasSituationReport method with valid data.
     *
     * @return void
     */
    public function testValidateWithValidData()
    {
        $service = $this->getService();

        $data = [
            'customer_id' => 1,
            'customer_site_id' => 1,
            'inlet_pressure' => 1000.00,
            'outlet_pressure' => 1000.00,
            'allocation' => 1000.00,
            'nomination' => 1000.00,
        ];

        $validatedData = $service->validateGasSituationReport($data);

        $this->assertEquals($data, $validatedData);
    }

    /**
     * Test validateGasSituationReport method with invalid data.
     *
     * @return void
     */
    public function testValidateWithInvalidData()
    {
        $this->expectException(ValidationException::class);

        $service = $this->getService();

        $data = [
            'customer_id' => null,
            'customer_site_id' => '',
            'inlet_pressure' => -100.00, // invalid
            'outlet_pressure' => -100.00, // invalid
            'allocation' => -100.00, // invalid
            'nomination' => -100.00, // invalid
        ];

        $service->validateGasSituationReport($data);
    }

    /**
     * Test create method with valid data.
     *
     * @return void
     */
    public function testCreateGasSituationReport()
    {
        $service = $this->getService();

        $data = [
            'customer_id' => 1,
            'customer_site_id' => 1,
            'inlet_pressure' => 1000.00,
            'outlet_pressure' => 2000.00,
            'allocation' => 3000.00,
            'nomination' => 4000.00,
            'form_field_answers' => json_encode([['key' => 'extra_data', 'value' => 'some_value']]),
        ];


        $gasSituationReport = $service->create($data);

        $this->assertInstanceOf(GasSituationReportResource::class, $gasSituationReport);
        $this->assertEquals($data['customer_id'], $gasSituationReport->customer_id);
        $this->assertEquals($data['customer_site_id'], $gasSituationReport->customer_site_id);
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
            'customer_id' => 1,
            'customer_site_id' => 1,
            'inlet_pressure' => 1000.00,
            'outlet_pressure' => 1000.00,
            'allocation' => 1000.00,
            'nomination' => 1000.00,
            'form_field_answers' => 'invalid_json',
        ];

        $service->create($data);
    }

    /**
     * Test update method with valid data.
     *
     * @return void
     */
    public function testUpdateGasSituationReport()
    {
        $service = $this->getService();

        $gasSituationReport = GasSituationReport::factory()->create([
            'customer_id' => 1,
            'customer_site_id' => 1,
            'inlet_pressure' => 1000.00,
            'outlet_pressure' => 1000.00,
            'allocation' => 1000.00,
            'nomination' => 1000.00,
        ]);

        $data = [
            'id' => $gasSituationReport->id,
            'inlet_pressure' => 2000.00,
            'outlet_pressure' => 3000.00,
            'allocation' => 4000.00,
            'nomination' => 5000.00,
        ];

        $updatedVolume = $service->update($data);

        $this->assertEquals(2000.00, $updatedVolume->inlet_pressure);
        $this->assertEquals(3000.00, $updatedVolume->outlet_pressure);
        $this->assertEquals(4000.00, $updatedVolume->allocation);
        $this->assertEquals(5000.00, $updatedVolume->nomination);
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

        $gasSituationReport = GasSituationReport::factory()->create();

        $data = [
            'id' => $gasSituationReport->id,
            'inlet_pressure' => -100.00, // invalid
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
            'inlet_pressure' => 2000.00,
        ];

        $service->update($data);
    }
}
