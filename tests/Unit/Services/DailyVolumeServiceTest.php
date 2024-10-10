<?php

namespace Tests\Unit\Services;

use App\Models\DailyVolume;
use App\Services\DailyVolumeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DailyVolumeServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get the service instance.
     *
     * @return DailyVolumeService
     */
    public function getService()
    {
        return new DailyVolumeService();
    }

    /**
     * Test validateDailyVolume method with valid data.
     *
     * @return void
     */
    public function testValidateWithValidData()
    {
        $service = $this->getService();

        $data = [
            'customer_id' => 1,
            'customer_site_id' => 1,
            'volume' => 1000.00,
        ];

        $validatedData = $service->validateDailyVolume($data);

        $this->assertEquals($data, $validatedData);
    }

    /**
     * Test validateDailyVolume method with invalid data.
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
            'volume' => -100.00, // invalid
        ];

        $service->validateDailyVolume($data);
    }

    /**
     * Test create method with valid data.
     *
     * @return void
     */
    public function testCreateDailyVolume()
    {
        $service = $this->getService();

        $data = [
            'customer_id' => 1,
            'customer_site_id' => 1,
            'volume' => 1000.00,
            'form_field_answers' => json_encode([['key' => 'extra_data', 'value' => 'some_value']]),
        ];

        $dailyVolume = $service->create($data);

        $this->assertInstanceOf(DailyVolume::class, $dailyVolume);
        $this->assertEquals($data['customer_id'], $dailyVolume->customer_id);
        $this->assertEquals($data['customer_site_id'], $dailyVolume->customer_site_id);
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
            'volume' => 1000.00,
            'form_field_answers' => 'invalid_json',
        ];

        $service->create($data);
    }

    /**
     * Test update method with valid data.
     *
     * @return void
     */
    public function testUpdateDailyVolume()
    {
        $service = $this->getService();

        $dailyVolume = DailyVolume::factory()->create([
            'customer_id' => 1,
            'customer_site_id' => 1,
            'volume' => 1000.00,
        ]);

        $data = [
            'id' => $dailyVolume->id,
            'volume' => 2000.00,
        ];

        $updatedVolume = $service->update($data);

        $this->assertEquals(2000.00, $updatedVolume->volume);
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

        $dailyVolume = DailyVolume::factory()->create();

        $data = [
            'id' => $dailyVolume->id,
            'volume' => -100.00, // invalid
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
            'volume' => 2000.00,
        ];

        $service->update($data);
    }
}
