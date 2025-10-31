<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class PricingService
{
    public function calculateLineTotal(OrderItem $item): float
    {
        $effectivePrice = $item->manual_price ?? $item->unit_price;
        $lineTotal = ($effectivePrice * $item->qty) - $item->discount_amount;

        return max(0, round($lineTotal, 2));
    }

    public function refreshOrderTotals(Order $order): void
    {
        $subtotal = 0;
        $discountTotal = 0;

        $order->loadMissing('items');
        foreach ($order->items as $item) {
            $subtotal += ($item->unit_price * $item->qty);
            $discountTotal += $item->discount_amount;
        }

        $order->subtotal = round($subtotal, 2);
        $order->discount_total = round($discountTotal, 2);
        $order->surcharge_total = $order->surcharge_total ?? 0;
        $order->tax_total = $order->tax_total ?? 0;
        $order->grand_total = max(0, round($subtotal - $discountTotal + $order->surcharge_total + $order->tax_total, 2));
    }

    public function overridePrice(OrderItem $item, float $manualPrice, string $reason, User $actor, ?User $approver = null): void
    {
        if ($manualPrice <= 0) {
            throw ValidationException::withMessages([
                'manual_price' => 'Manual price must be greater than zero.',
            ]);
        }

        if (empty($reason)) {
            throw ValidationException::withMessages([
                'manual_reason' => 'Reason is required when overriding a price.',
            ]);
        }

        $limitPercent = (float) Setting::getValue('override_price_limit_percent', 15);
        $requireAdmin = (bool) Setting::getValue('require_admin_approval_above_limit', true);
        $basePrice = (float) $item->unit_price;
        $differencePercent = $basePrice > 0 ? abs($manualPrice - $basePrice) / $basePrice * 100 : 100;

        $needsApproval = $differencePercent > $limitPercent && $requireAdmin;

        if ($needsApproval && (! $approver || ! $approver->isAdmin())) {
            throw ValidationException::withMessages([
                'manual_price' => 'Admin approval is required to override beyond tolerance.',
            ]);
        }

        $item->manual_price = round($manualPrice, 2);
        $item->manual_reason = $reason;
        $item->manual_by = $approver?->id ?? $actor->id;
        $item->line_total = $this->calculateLineTotal($item);
        $item->save();
    }

    public function recalculateItem(OrderItem $item): void
    {
        $item->line_total = $this->calculateLineTotal($item);
        $item->save();
        $this->refreshOrderTotals($item->order);
        $item->order->save();
    }
}
