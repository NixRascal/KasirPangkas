<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Http\Requests\POS\CloseCashSessionRequest;
use App\Http\Requests\POS\OpenCashSessionRequest;
use App\Models\CashSession;
use App\Models\Shift;
use App\Services\CashService;
use Illuminate\Http\JsonResponse;

class CashSessionController extends Controller
{
    public function __construct(private readonly CashService $cashService)
    {
    }

    public function open(OpenCashSessionRequest $request): JsonResponse
    {
        $shift = Shift::findOrFail($request->input('shift_id'));
        $session = $this->cashService->openSession($request->user(), $shift, $request->input('opening_float'));

        return response()->json($session);
    }

    public function close(CloseCashSessionRequest $request): JsonResponse
    {
        $session = CashSession::findOrFail($request->input('cash_session_id'));
        $session = $this->cashService->closeSession($session, $request->user(), $request->input('counted_cash'), $request->input('notes'));

        return response()->json($session);
    }
}
