<?php

namespace App\Http\Requests\POS;

use App\Models\CashSession;
use Illuminate\Foundation\Http\FormRequest;

class OpenCashSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('open', CashSession::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'shift_id' => ['required', 'exists:shifts,id'],
            'opening_float' => ['required', 'numeric', 'min:0'],
        ];
    }
}
