<?php

namespace Database\Factories;

use App\Models\CashLedger;
use App\Models\CashSession;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashLedger>
 */
class CashLedgerFactory extends Factory
{
    protected $model = CashLedger::class;

    public function definition(): array
    {
        return [
            'cash_session_id' => CashSession::factory(),
            'order_id' => Order::factory(),
            'type' => fake()->randomElement(['cash_in', 'cash_out']),
            'reason' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 10000, 100000),
            'created_by' => User::factory()->state(['role' => 'kasir']),
        ];
    }
}
