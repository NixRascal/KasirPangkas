<?php

namespace App\Http\Requests\POS;

use App\Models\OrderItem;
use Illuminate\Foundation\Http\FormRequest;

class OverrideOrderItemPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $item = OrderItem::find($this->route('item'));

        return $item ? $this->user()?->can('overridePrice', $item) : false;
    }

    public function rules(): array
    {
        return [
            'manual_price' => ['required', 'numeric', 'min:1'],
            'manual_reason' => ['required', 'string', 'max:255'],
            'approver_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
