<?php

namespace Database\Factories;

use App\Models\CommissionRule;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<CommissionRule>
 */
class CommissionRuleFactory extends Factory
{
    protected $model = CommissionRule::class;

    public function definition(): array
    {
        $scope = Arr::random(['per_service', 'per_employee_level', 'global']);
        $type = Arr::random(['percent', 'flat']);

        return [
            'name' => fake()->sentence(2),
            'scope' => $scope,
            'service_id' => $scope === 'per_service' ? Service::factory() : null,
            'employee_level' => $scope === 'per_employee_level' ? Arr::random(['junior', 'senior', 'master']) : null,
            'type' => $type,
            'value' => $type === 'percent'
                ? fake()->randomFloat(2, 5, 30)
                : fake()->randomFloat(2, 5000, 50000),
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => null,
            'is_active' => true,
        ];
    }
}
