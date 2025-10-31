<?php

namespace Tests\Unit;

use App\Models\Commission;
use App\Models\CommissionRule;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Services\CommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_commission_using_service_rule(): void
    {
        $service = Service::factory()->create(['base_price' => 100000]);
        $employee = Employee::factory()->create(['level' => 'senior']);
        $order = Order::factory()->create();

        $item = OrderItem::factory()->for($order)->create([
            'service_id' => $service->id,
            'employee_id' => $employee->id,
            'line_total' => 100000,
        ]);

        CommissionRule::factory()->create([
            'scope' => 'per_service',
            'service_id' => $service->id,
            'type' => 'percent',
            'value' => 20,
        ]);

        CommissionRule::factory()->create([
            'scope' => 'per_employee_level',
            'employee_level' => 'senior',
            'type' => 'percent',
            'value' => 15,
        ]);

        $serviceClass = new CommissionService();
        $commission = $serviceClass->generateForItem($item, now()->toDateString());

        $this->assertInstanceOf(Commission::class, $commission);
        $this->assertSame(20000.0, (float) $commission->commission_amount);
        $this->assertSame($item->id, $commission->order_item_id);
    }

    public function test_falls_back_to_employee_level_then_global(): void
    {
        $service = Service::factory()->create(['base_price' => 120000]);
        $employee = Employee::factory()->create(['level' => 'master']);
        $order = Order::factory()->create();

        $item = OrderItem::factory()->for($order)->create([
            'service_id' => $service->id,
            'employee_id' => $employee->id,
            'line_total' => 120000,
        ]);

        CommissionRule::factory()->create([
            'scope' => 'per_employee_level',
            'employee_level' => 'master',
            'type' => 'percent',
            'value' => 25,
        ]);

        CommissionRule::factory()->create([
            'scope' => 'global',
            'type' => 'percent',
            'value' => 10,
        ]);

        $serviceClass = new CommissionService();
        $commission = $serviceClass->generateForItem($item, now()->toDateString());

        $this->assertSame(30000.0, (float) $commission->commission_amount);
        $this->assertSame('master', $commission->rule?->employee_level);
    }
}
