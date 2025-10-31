<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\CommissionRule;
use App\Models\OrderItem;
use Carbon\Carbon;

class CommissionService
{
    public function generateForItem(OrderItem $item, ?string $paidAt = null): Commission
    {
        $item->loadMissing('employee');
        $date = $paidAt ? Carbon::parse($paidAt)->toDateString() : now()->toDateString();
        $baseQuery = CommissionRule::query()->effective($date);

        $rule = (clone $baseQuery)->where('scope', 'per_service')->where('service_id', $item->service_id)->first()
            ?? (clone $baseQuery)->where('scope', 'per_employee_level')->where('employee_level', $item->employee->level ?? null)->first()
            ?? (clone $baseQuery)->where('scope', 'global')->first();

        $baseAmount = (float) $item->line_total;
        $commissionAmount = 0.0;
        $ruleId = null;

        if ($rule) {
            $ruleId = $rule->id;
            if ($rule->type === 'percent') {
                $commissionAmount = round($baseAmount * ((float) $rule->value) / 100, 2);
            } else {
                $commissionAmount = round((float) $rule->value, 2);
            }
        }

        return Commission::query()->updateOrCreate(
            ['order_item_id' => $item->id],
            [
                'employee_id' => $item->employee_id,
                'rule_id' => $ruleId,
                'base_amount' => $baseAmount,
                'commission_amount' => $commissionAmount,
            ]
        );
    }

    public function generateForOrderItems(iterable $items, ?string $paidAt = null): void
    {
        foreach ($items as $item) {
            $this->generateForItem($item, $paidAt);
        }
    }
}
