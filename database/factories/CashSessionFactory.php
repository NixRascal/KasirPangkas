<?php

namespace Database\Factories;

use App\Models\CashSession;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<CashSession>
 */
class CashSessionFactory extends Factory
{
    protected $model = CashSession::class;

    public function definition(): array
    {
        $openedAt = Carbon::now('Asia/Jakarta')->subHours(fake()->numberBetween(1, 12));

        return [
            'shift_id' => Shift::factory(),
            'opened_by' => User::factory()->state(['role' => 'kasir']),
            'closed_by' => null,
            'opened_at' => $openedAt,
            'opening_float' => fake()->randomFloat(2, 100000, 300000),
            'closing_cash_counted' => null,
            'cash_expected' => null,
            'variance' => null,
        ];
    }

    public function closed(): self
    {
        return $this->state(function (array $attributes) {
            $closedAt = Carbon::parse($attributes['opened_at'])->addHours(8);
            $expected = $attributes['opening_float'] + 150000;

            return [
                'closed_by' => $attributes['opened_by'],
                'closed_at' => $closedAt,
                'cash_expected' => $expected,
                'closing_cash_counted' => $expected,
                'variance' => 0,
            ];
        });
    }
}
