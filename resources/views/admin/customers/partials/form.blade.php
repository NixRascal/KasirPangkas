<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-slate-600">Nama</label>
        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Telepon</label>
        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Tipe</label>
        <select name="type" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
            @foreach (['reguler', 'member', 'vip'] as $type)
                <option value="{{ $type }}" @selected(old('type', $customer->type ?? 'reguler') === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-600">Catatan</label>
        <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $customer->notes ?? '') }}</textarea>
    </div>
</div>
