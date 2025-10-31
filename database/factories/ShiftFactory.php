<?php

namespace Database\Factories;

use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Shift>
 */
class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    public function definition(): array
    {
        $start = Carbon::createFromTime(fake()->numberBetween(6, 12), 0);
        $end = (clone $start)->addHours(8);

        return [
            'name' => fake()->randomElement(['Pagi', 'Siang', 'Sore', 'Malam']),
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'is_active' => true,
        ];
    }
}
