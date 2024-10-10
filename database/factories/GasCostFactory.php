<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GasCost>
 */
class GasCostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date_of_entry' => fake()->date(),
            'dollar_cost_per_scf' => fake()->randomFloat(2, 0, 1000000),
            'dollar_rate' => fake()->randomFloat(2, 0, 1000000),
            'status' => fake()->boolean(),
        ];
    }
}
