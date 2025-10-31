<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-slate-600">Nama Jasa</label>
        <input type="text" name="name" value="{{ old('name', $service->name ?? '') }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Kategori</label>
        <select name="service_category_id" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('service_category_id', $service->service_category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Kode</label>
        <input type="text" name="code" value="{{ old('code', $service->code ?? '') }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Harga Dasar</label>
        <input type="number" step="0.01" name="base_price" value="{{ old('base_price', $service->base_price ?? '') }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Durasi Estimasi (menit)</label>
        <input type="number" name="est_duration_min" value="{{ old('est_duration_min', $service->est_duration_min ?? '') }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Tipe Komisi</label>
        <select name="commission_type" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="percent" @selected(old('commission_type', $service->commission_type ?? '') === 'percent')>Persen</option>
            <option value="flat" @selected(old('commission_type', $service->commission_type ?? '') === 'flat')>Flat</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Nilai Komisi</label>
        <input type="number" step="0.01" name="commission_value" value="{{ old('commission_value', $service->commission_value ?? '') }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div class="flex items-center gap-2 mt-6">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active ?? true)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        <label class="text-sm text-slate-600">Aktif</label>
    </div>
</div>
