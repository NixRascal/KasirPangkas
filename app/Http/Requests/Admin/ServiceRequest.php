<?php

namespace App\Http\Requests\Admin;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ability = $this->isMethod('post') ? 'create' : 'update';
        $service = $this->route('service');

        return $this->user()?->can($ability, $service instanceof Service ? $service : Service::class) ?? false;
    }

    public function rules(): array
    {
        $service = $this->route('service');
        $serviceId = $service instanceof Service ? $service->id : null;

        return [
            'service_category_id' => ['required', 'exists:service_categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', Rule::unique('services', 'code')->ignore($serviceId)],
            'base_price' => ['required', 'numeric', 'min:0'],
            'est_duration_min' => ['required', 'integer', 'min:5'],
            'commission_type' => ['required', Rule::in(['percent', 'flat'])],
            'commission_value' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
