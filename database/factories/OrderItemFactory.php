<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 2);
        $price = fake()->randomFloat(2, 40000, 150000);

        return [
            'order_id' => Order::factory(),
            'service_id' => Service::factory()->state(['base_price' => $price]),
            'employee_id' => Employee::factory(),
            'person_label' => fake()->randomElement(['Pelanggan 1', 'Pelanggan 2', 'Anak']),
            'qty' => $qty,
            'unit_price' => $price,
            'manual_price' => null,
            'manual_reason' => null,
            'manual_by' => null,
            'discount_amount' => 0,
            'line_total' => $price * $qty,
        ];
    }
}
