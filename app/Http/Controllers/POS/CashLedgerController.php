<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Http\Requests\POS\StoreCashLedgerRequest;
use App\Models\CashSession;
use App\Models\Order;
use App\Services\CashService;
use Illuminate\Http\JsonResponse;

class CashLedgerController extends Controller
{
    public function __construct(private readonly CashService $cashService)
    {
    }

    public function store(StoreCashLedgerRequest $request): JsonResponse
    {
        $session = CashSession::findOrFail($request->input('cash_session_id'));
        $order = $request->input('order_id') ? Order::find($request->input('order_id')) : null;

        $ledger = $this->cashService->recordLedger(
            $session,
            $request->input('type'),
            $request->input('amount'),
            $request->input('reason'),
            $request->user(),
            $order
        );

        return response()->json($ledger);
    }
}
