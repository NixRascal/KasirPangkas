<?php

namespace Tests\Feature;

use App\Events\OrderPaid;
use App\Models\CashSession;
use App\Models\Commission;
use App\Models\CommissionRule;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class POSWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        cache()->flush();
        Setting::updateOrCreate(['key' => 'override_price_limit_percent'], ['value' => '15', 'type' => 'numeric']);
        Setting::updateOrCreate(['key' => 'require_admin_approval_above_limit'], ['value' => '1', 'type' => 'boolean']);
    }

    public function test_cashier_flow_add_override_checkout_generates_commission(): void
    {
        Event::fakeExcept([OrderPaid::class]);

        $cashier = User::factory()->create(['role' => 'kasir']);
        $admin = User::factory()->admin()->create();
        $shift = Shift::factory()->create();
        $service = Service::factory()->create(['base_price' => 100000]);
        $employee = Employee::factory()->create(['level' => 'master']);
        CommissionRule::factory()->create([
            'scope' => 'global',
            'type' => 'percent',
            'value' => 10,
        ]);
        $session = CashSession::factory()->create([
            'shift_id' => $shift->id,
            'opened_by' => $cashier->id,
            'opened_at' => now('Asia/Jakarta')->subHour(),
            'opening_float' => 200000,
        ]);

        $this->actingAs($cashier);

        $orderResponse = $this->postJson(route('pos.orders.store'), [
            'shift_id' => $shift->id,
            'cash_session_id' => $session->id,
        ])->assertOk();

        $orderId = $orderResponse->json('id');

        $itemResponse = $this->postJson(route('pos.orders.items.store'), [
            'order_id' => $orderId,
            'service_id' => $service->id,
            'employee_id' => $employee->id,
            'person_label' => 'Pelanggan 1',
            'qty' => 1,
        ])->assertOk();

        $itemId = $itemResponse->json('id');

        $this->patchJson(route('pos.orders.items.override', $itemId), [
            'manual_price' => 80000,
            'manual_reason' => 'Diskon loyalti',
        ])->assertStatus(422);

        $this->patchJson(route('pos.orders.items.override', $itemId), [
            'manual_price' => 80000,
            'manual_reason' => 'Diskon loyalti',
            'approver_id' => $admin->id,
        ])->assertOk();

        $checkoutResponse = $this->postJson(route('pos.orders.checkout'), [
            'order_id' => $orderId,
            'cash_session_id' => $session->id,
            'payments' => [
                ['method' => 'cash', 'amount' => 90000],
                ['method' => 'qris', 'amount' => 10000],
            ],
        ])->assertOk();

        $checkoutData = $checkoutResponse->json();
        $this->assertSame('paid', $checkoutData['status']);
        $this->assertSame(100000.0, (float) $checkoutData['grand_total']);
        $this->assertSame(100000.0, (float) $checkoutData['paid_total']);

        Event::assertDispatched(OrderPaid::class);
        $this->assertGreaterThan(0, Commission::count());
    }
}
