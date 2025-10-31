<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'code' => strtoupper(fake()->unique()->bothify('EMP###')),
            'level' => fake()->randomElement(['junior', 'senior', 'master']),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
            'hire_date' => fake()->date(),
        ];
    }
}
