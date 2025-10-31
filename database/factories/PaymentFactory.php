<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'method' => fake()->randomElement(['cash', 'qris', 'debit', 'ewallet', 'transfer']),
            'amount' => fake()->randomFloat(2, 50000, 250000),
            'reference_no' => fake()->optional()->bothify('REF####'),
            'paid_by' => fake()->name(),
            'received_by' => User::factory()->state(['role' => 'kasir']),
            'paid_at' => now('Asia/Jakarta'),
        ];
    }
}
