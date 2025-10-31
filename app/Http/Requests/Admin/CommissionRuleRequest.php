<?php

namespace App\Http\Requests\Admin;

use App\Models\CommissionRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommissionRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ability = $this->isMethod('post') ? 'create' : 'update';
        $rule = $this->route('commission_rule');

        return $this->user()?->can($ability, $rule instanceof CommissionRule ? $rule : CommissionRule::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'scope' => ['required', Rule::in(['per_service', 'per_employee_level', 'global'])],
            'service_id' => ['nullable', 'exists:services,id'],
            'employee_level' => ['nullable', Rule::in(['junior', 'senior', 'master'])],
            'type' => ['required', Rule::in(['percent', 'flat'])],
            'value' => ['required', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['boolean'],
        ];
    }
}
