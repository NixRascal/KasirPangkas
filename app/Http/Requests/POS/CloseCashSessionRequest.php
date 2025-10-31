<?php

namespace App\Http\Requests\POS;

use App\Models\CashSession;
use Illuminate\Foundation\Http\FormRequest;

class CloseCashSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $session = CashSession::find($this->input('cash_session_id'));

        return $session ? $this->user()?->can('close', $session) : false;
    }

    public function rules(): array
    {
        return [
            'cash_session_id' => ['required', 'exists:cash_sessions,id'],
            'counted_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
