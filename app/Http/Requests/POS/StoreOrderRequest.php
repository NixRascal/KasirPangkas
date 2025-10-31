<?php

namespace App\Http\Requests\POS;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Order::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'cash_session_id' => ['nullable', 'exists:cash_sessions,id'],
        ];
    }
}
