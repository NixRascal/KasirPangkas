<?php

namespace App\Http\Requests\Admin;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ability = $this->isMethod('post') ? 'create' : 'update';
        $employee = $this->route('employee');

        return $this->user()?->can($ability, $employee instanceof Employee ? $employee : Employee::class) ?? false;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');
        $employeeId = $employee instanceof Employee ? $employee->id : null;

        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', Rule::unique('employees', 'code')->ignore($employeeId)],
            'level' => ['required', Rule::in(['junior', 'senior', 'master'])],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
            'hire_date' => ['nullable', 'date'],
        ];
    }
}
