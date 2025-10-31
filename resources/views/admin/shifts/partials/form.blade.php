<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-slate-600">Nama Shift</label>
        <input type="text" name="name" value="{{ old('name', $shift->name ?? '') }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Jam Mulai</label>
        <input type="time" name="start_time" value="{{ old('start_time', $shift->start_time ?? '') }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-600">Jam Selesai</label>
        <input type="time" name="end_time" value="{{ old('end_time', $shift->end_time ?? '') }}" required class="mt-1 w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div class="flex items-center gap-2 mt-6">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $shift->is_active ?? true)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        <label class="text-sm text-slate-600">Aktif</label>
    </div>
</div>
