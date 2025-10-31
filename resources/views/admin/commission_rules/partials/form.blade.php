<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-600">Nama Aturan</label>
        <input type="text" name="name" value="{{ old('name', $rule->name ?? '') }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Scope</label>
        <select name="scope" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
            @foreach (['per_service' => 'Per Jasa', 'per_employee_level' => 'Per Level Karyawan', 'global' => 'Global'] as $value => $label)
                <option value="{{ $value }}" @selected(old('scope', $rule->scope ?? 'global') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Jasa (opsional)</label>
        <select name="service_id" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-</option>
            @foreach ($services as $serviceOption)
                <option value="{{ $serviceOption->id }}" @selected(old('service_id', $rule->service_id ?? '') == $serviceOption->id)>{{ $serviceOption->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Level Karyawan</label>
        <select name="employee_level" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-</option>
            @foreach (['junior', 'senior', 'master'] as $level)
                <option value="{{ $level }}" @selected(old('employee_level', $rule->employee_level ?? '') === $level)>{{ ucfirst($level) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Tipe</label>
        <select name="type" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="percent" @selected(old('type', $rule->type ?? '') === 'percent')>Persen</option>
            <option value="flat" @selected(old('type', $rule->type ?? '') === 'flat')>Flat</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Nilai</label>
        <input type="number" step="0.01" name="value" value="{{ old('value', $rule->value ?? '') }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Mulai Berlaku</label>
        <input type="date" name="start_date" value="{{ old('start_date', optional($rule->start_date ?? null)->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Berakhir</label>
        <input type="date" name="end_date" value="{{ old('end_date', optional($rule->end_date ?? null)->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div class="flex items-center gap-2 mt-6">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $rule->is_active ?? true)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        <label class="text-sm text-slate-600">Aktif</label>
    </div>
</div>
