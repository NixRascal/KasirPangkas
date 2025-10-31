<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Http\Requests\POS\CheckoutOrderRequest;
use App\Http\Requests\POS\StoreOrderRequest;
use App\Http\Requests\POS\VoidOrderRequest;
use App\Models\CashSession;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Shift;
use App\Services\OrderWorkflow;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(private readonly OrderWorkflow $workflow)
    {
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = $request->user();
        $existingDraft = Order::query()
            ->where('cashier_id', $user->id)
            ->where('status', 'draft')
            ->latest()
            ->first();

        if (! $existingDraft) {
            $shift = $request->input('shift_id') ? Shift::find($request->input('shift_id')) : null;
            $session = $request->input('cash_session_id') ? CashSession::find($request->input('cash_session_id')) : null;
            $customer = $request->input('customer_id') ? Customer::find($request->input('customer_id')) : null;
            $existingDraft = $this->workflow->startDraft($user, $customer, $session, $shift);
        }

        return response()->json($existingDraft->load('items.service', 'items.employee'));
    }

    public function checkout(CheckoutOrderRequest $request): JsonResponse
    {
        $order = Order::findOrFail($request->input('order_id'));
        $session = $request->input('cash_session_id') ? CashSession::find($request->input('cash_session_id')) : null;
        $payments = $request->validated()['payments'];

        $order = $this->workflow->checkout($order, $payments, $request->user(), $session);

        return response()->json($order);
    }

    public function void(VoidOrderRequest $request): JsonResponse
    {
        $order = Order::findOrFail($request->input('order_id'));
        $order = $this->workflow->void($order, $request->user(), $request->input('reason'));

        return response()->json($order);
    }
}
