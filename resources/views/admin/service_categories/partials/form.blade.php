<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-600">Nama Kategori</label>
        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Urutan</label>
        <input type="number" name="order" value="{{ old('order', $category->order ?? 0) }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-600">Deskripsi</label>
        <textarea name="description" rows="3" class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $category->description ?? '') }}</textarea>
    </div>
</div>
