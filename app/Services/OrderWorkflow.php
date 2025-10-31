<?php

namespace App\Services;

use App\Events\OrderPaid;
use App\Events\PriceOverridden;
use App\Models\ActivityLog;
use App\Models\CashSession;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderWorkflow
{
    public function __construct(
        private readonly PricingService $pricingService,
        private readonly CashService $cashService,
    ) {
    }

    public function startDraft(User $cashier, ?Customer $customer = null, ?CashSession $session = null, ?Shift $shift = null): Order
    {
        return Order::create([
            'customer_id' => $customer?->id,
            'cashier_id' => $cashier->id,
            'cash_session_id' => $session?->id,
            'shift_id' => $shift?->id,
            'status' => 'draft',
        ]);
    }

    public function addItem(Order $order, Service $service, Employee $employee, string $personLabel, int $qty = 1, ?string $chairId = null): OrderItem
    {
        if ($order->status !== 'draft') {
            throw ValidationException::withMessages([
                'order' => 'Cannot add items to non-draft orders.',
            ]);
        }

        $item = $order->items()->create([
            'service_id' => $service->id,
            'employee_id' => $employee->id,
            'chair_id' => $chairId,
            'person_label' => $personLabel,
            'qty' => $qty,
            'unit_price' => $service->base_price,
            'discount_amount' => 0,
            'line_total' => $service->base_price * $qty,
        ]);

        $this->pricingService->refreshOrderTotals($order->fresh('items'));
        $order->save();

        ActivityLog::create([
            'user_id' => $order->cashier_id,
            'action' => 'order_item_added',
            'subject_type' => OrderItem::class,
            'subject_id' => $item->id,
            'meta' => [
                'order_id' => $order->id,
                'service' => $service->name,
            ],
        ]);

        return $item;
    }

    public function updateItem(OrderItem $item, array $attributes): OrderItem
    {
        if ($item->order->status !== 'draft') {
            throw ValidationException::withMessages([
                'order' => 'Cannot update item on non-draft order.',
            ]);
        }

        $item->fill(Arr::only($attributes, ['person_label', 'qty', 'discount_amount', 'chair_id']));
        if (isset($attributes['employee_id'])) {
            $item->employee_id = $attributes['employee_id'];
        }

        $item->save();
        $this->pricingService->recalculateItem($item);

        ActivityLog::create([
            'user_id' => $item->order->cashier_id,
            'action' => 'order_item_updated',
            'subject_type' => OrderItem::class,
            'subject_id' => $item->id,
            'meta' => [
                'order_id' => $item->order_id,
            ],
        ]);

        return $item;
    }

    public function overrideItemPrice(OrderItem $item, float $manualPrice, string $reason, User $actor, ?User $approver = null): OrderItem
    {
        $this->pricingService->overridePrice($item, $manualPrice, $reason, $actor, $approver);
        $this->pricingService->refreshOrderTotals($item->order);
        $item->order->save();

        ActivityLog::create([
            'user_id' => $actor->id,
            'action' => 'price_adjustment',
            'subject_type' => OrderItem::class,
            'subject_id' => $item->id,
            'meta' => [
                'order_id' => $item->order_id,
                'reason' => $reason,
                'manual_price' => $manualPrice,
                'approver' => $approver?->only(['id', 'name']),
            ],
        ]);

        PriceOverridden::dispatch($item, $actor, $approver);

        return $item;
    }

    public function removeItem(OrderItem $item): void
    {
        if ($item->order->status !== 'draft') {
            throw ValidationException::withMessages([
                'order' => 'Cannot remove item on non-draft order.',
            ]);
        }

        $order = $item->order;
        $item->delete();
        $this->pricingService->refreshOrderTotals($order->fresh('items'));
        $order->save();

        ActivityLog::create([
            'user_id' => $order->cashier_id,
            'action' => 'order_item_removed',
            'subject_type' => Order::class,
            'subject_id' => $order->id,
            'meta' => [
                'order_item_id' => $item->id,
            ],
        ]);
    }

    public function checkout(Order $order, array $paymentsData, User $cashier, ?CashSession $session = null): Order
    {
        if ($order->status !== 'draft') {
            throw ValidationException::withMessages([
                'order' => 'Only draft orders can be checked out.',
            ]);
        }

        if ($order->items()->count() === 0) {
            throw ValidationException::withMessages([
                'order' => 'Order requires at least one item before checkout.',
            ]);
        }

        $this->pricingService->refreshOrderTotals($order->fresh('items'));

        $totalPaid = 0;
        foreach ($paymentsData as $payment) {
            $totalPaid += (float) ($payment['amount'] ?? 0);
        }

        if ($totalPaid < $order->grand_total) {
            throw ValidationException::withMessages([
                'payments' => 'Total payment is insufficient.',
            ]);
        }

        DB::transaction(function () use ($order, $paymentsData, $cashier, $session, $totalPaid) {
            $order->cashier_id = $cashier->id;
            $order->cash_session_id = $session?->id ?? $order->cash_session_id;
            $order->paid_total = round($totalPaid, 2);
            $order->change_due = round(max(0, $totalPaid - $order->grand_total), 2);
            $order->status = 'paid';
            $order->paid_at = Carbon::now('Asia/Jakarta');
            $order->save();

            $order->payments()->delete();
            foreach ($paymentsData as $paymentData) {
                $payment = $order->payments()->create([
                    'method' => $paymentData['method'],
                    'amount' => round($paymentData['amount'], 2),
                    'reference_no' => $paymentData['reference_no'] ?? null,
                    'paid_by' => $paymentData['paid_by'] ?? null,
                    'received_by' => $cashier->id,
                    'paid_at' => Carbon::now('Asia/Jakarta'),
                ]);

                if ($payment->method === 'cash' && $order->cash_session_id) {
                    $cashSessionModel = $session ?? $order->cashSession()->first();
                    if ($cashSessionModel) {
                        $this->cashService->recordLedger(
                            $cashSessionModel,
                            'cash_in',
                            $payment->amount,
                            'Payment for order '.$order->order_no,
                            $cashier,
                            $order
                        );
                    }
                }
            }
        });

        ActivityLog::create([
            'user_id' => $cashier->id,
            'action' => 'order_paid',
            'subject_type' => Order::class,
            'subject_id' => $order->id,
            'meta' => [
                'paid_total' => $order->paid_total,
                'change_due' => $order->change_due,
            ],
        ]);

        OrderPaid::dispatch($order);

        return $order->fresh(['items', 'payments']);
    }

    public function void(Order $order, User $actor, string $reason): Order
    {
        if ($order->status === 'void') {
            return $order;
        }

        if ($order->status === 'paid' && ! $actor->isAdmin()) {
            throw ValidationException::withMessages([
                'order' => 'Only administrators can void paid orders.',
            ]);
        }

        $order->update([
            'status' => 'void',
            'notes' => trim(($order->notes ? $order->notes.' | ' : '').'Void: '.$reason),
        ]);

        ActivityLog::create([
            'user_id' => $actor->id,
            'action' => 'order_void',
            'subject_type' => Order::class,
            'subject_id' => $order->id,
            'meta' => [
                'reason' => $reason,
            ],
        ]);

        return $order;
    }
}
