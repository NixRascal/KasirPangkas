<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-slate-600">Nama</label>
        <input type="text" name="name" value="{{ old('name', $employee->name ?? '') }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Kode</label>
        <input type="text" name="code" value="{{ old('code', $employee->code ?? '') }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Level</label>
        <select name="level" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
            @foreach (['junior', 'senior', 'master'] as $level)
                <option value="{{ $level }}" @selected(old('level', $employee->level ?? '') === $level)>{{ ucfirst($level) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Telepon</label>
        <input type="text" name="phone" value="{{ old('phone', $employee->phone ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Tanggal Masuk</label>
        <input type="date" name="hire_date" value="{{ old('hire_date', optional($employee->hire_date ?? null)->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div class="flex items-center gap-2 mt-6">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $employee->is_active ?? true)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        <label class="text-sm text-slate-600">Aktif</label>
    </div>
</div>
