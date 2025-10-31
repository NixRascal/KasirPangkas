<?php

namespace Database\Factories;

use App\Models\Chair;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Chair>
 */
class ChairFactory extends Factory
{
    protected $model = Chair::class;

    public function definition(): array
    {
        return [
            'name' => 'Kursi ' . fake()->unique()->numerify('##'),
            'location' => fake()->optional()->streetName(),
            'is_active' => true,
        ];
    }
}
