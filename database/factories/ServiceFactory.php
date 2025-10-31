<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $commissionType = fake()->randomElement(['percent', 'flat']);

        return [
            'service_category_id' => ServiceCategory::factory(),
            'name' => fake()->words(2, true),
            'code' => strtoupper(fake()->unique()->bothify('SRV###')),
            'base_price' => fake()->randomFloat(2, 30000, 150000),
            'est_duration_min' => fake()->numberBetween(15, 90),
            'commission_type' => $commissionType,
            'commission_value' => $commissionType === 'percent'
                ? fake()->randomFloat(2, 5, 30)
                : fake()->randomFloat(2, 5000, 30000),
            'is_active' => true,
        ];
    }
}
