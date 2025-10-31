<?php

namespace Database\Factories;

use App\Models\CashSession;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_no' => Str::upper(Str::random(10)),
            'customer_id' => Customer::factory(),
            'status' => 'draft',
            'subtotal' => 0,
            'discount_total' => 0,
            'surcharge_total' => 0,
            'tax_total' => 0,
            'grand_total' => 0,
            'paid_total' => 0,
            'change_due' => 0,
            'notes' => null,
            'cashier_id' => User::factory()->state(['role' => 'kasir']),
            'shift_id' => Shift::factory(),
            'cash_session_id' => CashSession::factory(),
            'paid_at' => null,
        ];
    }

    public function paid(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => now('Asia/Jakarta'),
        ]);
    }
}
