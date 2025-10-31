<?php

namespace App\Http\Requests\POS;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutOrderRequest extends FormRequest
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
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.method' => ['required', Rule::in(['cash', 'qris', 'debit', 'ewallet', 'transfer'])],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payments.*.reference_no' => ['nullable', 'string', 'max:100'],
            'payments.*.paid_by' => ['nullable', 'string', 'max:100'],
            'cash_session_id' => ['nullable', 'exists:cash_sessions,id'],
        ];
    }
}
