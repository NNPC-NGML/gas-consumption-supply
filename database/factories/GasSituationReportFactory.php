<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GasSituationReport>
 */
class GasSituationReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => fake()->numberBetween(1, 10000),
            'customer_site_id' => fake()->numberBetween(1, 10000),
            'inlet_pressure' => fake()->randomFloat(2, 0, 1000000),
            'outlet_pressure' => fake()->randomFloat(2, 0, 1000000),
            'allocation' => fake()->randomFloat(2, 0, 1000000),
            'nomination' => fake()->randomFloat(2, 0, 1000000),
        ];
    }
}
