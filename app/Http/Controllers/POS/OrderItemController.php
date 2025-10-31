<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Http\Requests\POS\OverrideOrderItemPriceRequest;
use App\Http\Requests\POS\StoreOrderItemRequest;
use App\Http\Requests\POS\UpdateOrderItemRequest;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\User;
use App\Services\OrderWorkflow;
use Illuminate\Http\JsonResponse;

class OrderItemController extends Controller
{
    public function __construct(private readonly OrderWorkflow $workflow)
    {
    }

    public function store(StoreOrderItemRequest $request): JsonResponse
    {
        $order = Order::findOrFail($request->input('order_id'));
        $service = Service::findOrFail($request->input('service_id'));
        $employee = Employee::findOrFail($request->input('employee_id'));

        $item = $this->workflow->addItem(
            $order,
            $service,
            $employee,
            $request->input('person_label'),
            $request->input('qty', 1),
            $request->input('chair_id')
        );

        return response()->json($item->load('service', 'employee'));
    }

    public function update(UpdateOrderItemRequest $request, OrderItem $item): JsonResponse
    {
        $validated = $request->validated();
        if (isset($validated['employee_id'])) {
            $validated['employee_id'] = Employee::findOrFail($validated['employee_id'])->id;
        }

        $item = $this->workflow->updateItem($item, $validated);

        return response()->json($item->load('service', 'employee'));
    }

    public function overridePrice(OverrideOrderItemPriceRequest $request, OrderItem $item): JsonResponse
    {
        $approver = $request->input('approver_id') ? User::find($request->input('approver_id')) : null;
        $item = $this->workflow->overrideItemPrice(
            $item,
            $request->input('manual_price'),
            $request->input('manual_reason'),
            $request->user(),
            $approver
        );

        return response()->json($item);
    }

    public function destroy(OrderItem $item): JsonResponse
    {
        $this->authorize('delete', $item);
        $this->workflow->removeItem($item);

        return response()->json(['status' => 'deleted']);
    }
}
