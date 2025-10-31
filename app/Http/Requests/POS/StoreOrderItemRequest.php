<?php

namespace App\Http\Requests\POS;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = Order::find($this->input('order_id'));

        return $order ? $this->user()?->can('update', $order) : false;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'exists:orders,id'],
            'service_id' => ['required', 'exists:services,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'person_label' => ['required', 'string', 'max:100'],
            'qty' => ['nullable', 'integer', 'min:1'],
            'chair_id' => ['nullable', 'exists:chairs,id'],
        ];
    }
}
