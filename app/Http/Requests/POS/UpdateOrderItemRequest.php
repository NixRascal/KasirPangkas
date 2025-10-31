<?php

namespace App\Http\Requests\POS;

use App\Models\OrderItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $item = OrderItem::find($this->route('item'));

        return $item ? $this->user()?->can('update', $item) : false;
    }

    public function rules(): array
    {
        return [
            'person_label' => ['sometimes', 'string', 'max:100'],
            'qty' => ['sometimes', 'integer', 'min:1'],
            'discount_amount' => ['sometimes', 'numeric', 'min:0'],
            'employee_id' => ['sometimes', 'exists:employees,id'],
            'chair_id' => ['nullable', 'exists:chairs,id'],
        ];
    }
}
