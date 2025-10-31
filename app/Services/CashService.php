<?php

namespace App\Services;

use App\Events\CashSessionClosed;
use App\Models\ActivityLog;
use App\Models\CashLedger;
use App\Models\CashSession;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class CashService
{
    public function openSession(User $user, Shift $shift, float $openingFloat): CashSession
    {
        $existing = CashSession::query()->whereNull('closed_at')->first();
        if ($existing) {
            throw ValidationException::withMessages([
                'session' => 'A cash session is already open. Close it before opening a new one.',
            ]);
        }

        return CashSession::create([
            'shift_id' => $shift->id,
            'opened_by' => $user->id,
            'opened_at' => Carbon::now('Asia/Jakarta'),
            'opening_float' => round($openingFloat, 2),
        ]);
    }

    public function closeSession(CashSession $session, User $user, float $countedCash, ?string $notes = null): CashSession
    {
        if ($session->closed_at) {
            throw ValidationException::withMessages([
                'session' => 'Session already closed.',
            ]);
        }

        $cashPayments = Payment::query()
            ->where('method', 'cash')
            ->whereHas('order', fn ($query) => $query->where('cash_session_id', $session->id))
            ->sum('amount');

        $cashIn = $session->ledgers()->where('type', 'cash_in')->sum('amount');
        $cashOut = $session->ledgers()->where('type', 'cash_out')->sum('amount');

        $expected = round($session->opening_float + $cashPayments + $cashIn - $cashOut, 2);
        $variance = round($countedCash - $expected, 2);

        $session->update([
            'closing_cash_counted' => round($countedCash, 2),
            'cash_expected' => $expected,
            'variance' => $variance,
            'closed_at' => Carbon::now('Asia/Jakarta'),
            'closed_by' => $user->id,
            'notes' => $notes,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'cash_session_closed',
            'subject_type' => CashSession::class,
            'subject_id' => $session->id,
            'meta' => [
                'variance' => $variance,
                'counted' => $countedCash,
            ],
        ]);

        CashSessionClosed::dispatch($session);

        return $session;
    }

    public function recordLedger(CashSession $session, string $type, float $amount, string $reason, User $user, ?Order $order = null): CashLedger
    {
        if (! in_array($type, ['cash_in', 'cash_out'], true)) {
            throw ValidationException::withMessages([
                'type' => 'Ledger type must be cash_in or cash_out.',
            ]);
        }

        $ledger = $session->ledgers()->create([
            'order_id' => $order?->id,
            'type' => $type,
            'reason' => $reason,
            'amount' => round($amount, 2),
            'created_by' => $user->id,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'cash_ledger_'.$type,
            'subject_type' => CashLedger::class,
            'subject_id' => $ledger->id,
            'meta' => [
                'amount' => $amount,
                'reason' => $reason,
            ],
        ]);

        return $ledger;
    }
}
