<?php

namespace App\Listeners;

use App\Events\CashSessionClosed;
use App\Models\ActivityLog;

class GenerateCashSummary
{
    public function handle(CashSessionClosed $event): void
    {
        $session = $event->session->load(['ledgers', 'orders.payments']);
        $cashPayments = $session->orders->flatMap->payments->where('method', 'cash')->sum('amount');
        $cashIn = $session->ledgers->where('type', 'cash_in')->sum('amount');
        $cashOut = $session->ledgers->where('type', 'cash_out')->sum('amount');

        ActivityLog::create([
            'user_id' => $session->closed_by,
            'action' => 'cash_session_summary',
            'subject_type' => get_class($session),
            'subject_id' => $session->id,
            'meta' => [
                'cash_payments' => $cashPayments,
                'cash_in' => $cashIn,
                'cash_out' => $cashOut,
                'variance' => $session->variance,
            ],
        ]);
    }
}
