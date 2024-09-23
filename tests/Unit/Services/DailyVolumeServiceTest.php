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
            'rate' => 10.00,
            'amount' => 10000.00,
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
            'rate' => 'invalid', // invalid
            'amount' => 'not a number', // invalid
        ];

        $service->validateDailyVolume($data);
    }

    /**
     * Test create method.
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
            'rate' => 10.00,
            'amount' => 10000.00,
        ];

        $dailyVolume = $service->create($data);

        $this->assertInstanceOf(DailyVolume::class, $dailyVolume);
        $this->assertEquals($data['customer_id'], $dailyVolume->customer_id);
        $this->assertEquals($data['customer_site_id'], $dailyVolume->customer_site_id);
    }

    /**
     * Test update method.
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
            'rate' => 10.00,
            'amount' => 10000.00,
        ]);

        $data = [
            'id' => $dailyVolume->id,
            'volume' => 2000.00,
            'rate' => 20.00,
            'amount' => 20000.00,
        ];

        $updatedVolume = $service->update($data);

        $this->assertEquals(2000.00, $updatedVolume->volume);
        $this->assertEquals(20.00, $updatedVolume->rate);
        $this->assertEquals(20000.00, $updatedVolume->amount);
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
            'rate' => 'invalid', // invalid
            'amount' => 'not a number', // invalid
        ];

        $service->update($data);
    }
}
