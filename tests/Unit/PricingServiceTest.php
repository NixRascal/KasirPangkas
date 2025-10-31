<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        cache()->flush();
    }

    public function test_calculate_line_total_with_manual_price_and_discount(): void
    {
        $service = Service::factory()->create(['base_price' => 80000]);
        $order = Order::factory()->create();

        $item = OrderItem::factory()->for($order)->create([
            'service_id' => $service->id,
            'unit_price' => 80000,
            'manual_price' => 75000,
            'discount_amount' => 5000,
            'qty' => 1,
            'line_total' => 0,
        ]);

        $serviceClass = new PricingService();
        $lineTotal = $serviceClass->calculateLineTotal($item);

        $this->assertSame(70000.0, $lineTotal);
    }

    public function test_override_price_requires_admin_beyond_tolerance(): void
    {
        Setting::updateOrCreate(['key' => 'override_price_limit_percent'], ['value' => '10', 'type' => 'numeric']);
        Setting::updateOrCreate(['key' => 'require_admin_approval_above_limit'], ['value' => '1', 'type' => 'boolean']);

        $cashier = User::factory()->create(['role' => 'kasir']);
        $admin = User::factory()->admin()->create();
        $service = Service::factory()->create(['base_price' => 100000]);
        $order = Order::factory()->for($cashier, 'cashier')->create();

        $item = OrderItem::factory()->for($order)->create([
            'service_id' => $service->id,
            'unit_price' => 100000,
            'line_total' => 100000,
            'discount_amount' => 0,
        ]);

        $pricingService = new PricingService();

        $this->expectException(ValidationException::class);
        $pricingService->overridePrice($item, 60000, 'Diskon besar', $cashier);

        $pricingService->overridePrice($item->fresh(), 60000, 'Diskon besar', $cashier, $admin);

        $item->refresh();
        $this->assertSame(60000.0, (float) $item->manual_price);
        $this->assertSame('Diskon besar', $item->manual_reason);
        $this->assertSame($admin->id, $item->manual_by);
    }
}
