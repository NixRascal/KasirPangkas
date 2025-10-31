<?php

namespace App\Http\Requests\POS;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class VoidOrderRequest extends FormRequest
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
            'reason' => ['required', 'string', 'max:255'],
        ];
    }
}
