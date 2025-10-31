<?php

namespace App\Listeners;

use App\Events\PriceOverridden;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class LogPriceOverride
{
    public function handle(PriceOverridden $event): void
    {
        $item = $event->item->fresh();
        $basePrice = (float) $item->unit_price;
        $manualPrice = (float) $item->manual_price;
        $differencePercent = $basePrice > 0 ? abs($manualPrice - $basePrice) / $basePrice * 100 : 100;
        $limitPercent = (float) Setting::getValue('override_price_limit_percent', 15);

        ActivityLog::create([
            'user_id' => $event->actor->id,
            'action' => 'price_override_event',
            'subject_type' => get_class($item),
            'subject_id' => $item->id,
            'meta' => [
                'base_price' => $basePrice,
                'manual_price' => $manualPrice,
                'difference_percent' => $differencePercent,
                'limit_percent' => $limitPercent,
                'approver' => $event->approver?->only(['id', 'name']),
            ],
        ]);

        if ($differencePercent > $limitPercent) {
            Log::warning('Price override above tolerance', [
                'order_item_id' => $item->id,
                'difference_percent' => $differencePercent,
            ]);
        }
    }
}
