<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Services\CommissionService;

class GenerateCommission
{
    public function __construct(private readonly CommissionService $commissionService)
    {
    }

    public function handle(OrderPaid $event): void
    {
        $order = $event->order->load('items.employee');
        $this->commissionService->generateForOrderItems($order->items, optional($order->paid_at)?->toDateString());
    }
}
