<?php

namespace App\Http\Requests\POS;

use App\Models\CashLedger;
use App\Models\CashSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCashLedgerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $session = CashSession::find($this->input('cash_session_id'));

        return $session ? $this->user()?->can('create', CashLedger::class) : false;
    }

    public function rules(): array
    {
        return [
            'cash_session_id' => ['required', 'exists:cash_sessions,id'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'type' => ['required', Rule::in(['cash_in', 'cash_out'])],
            'reason' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
