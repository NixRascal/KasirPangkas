@extends('layouts.app')

@section('title', 'Master Jasa')
@section('content')
    <div class="flex items-center justify-between mb-4">
        <form method="GET" class="flex gap-2">
            <select name="category" class="rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-md bg-slate-800 px-3 py-2 text-white text-sm">Filter</button>
        </form>
        <a href="{{ route('admin.services.create') }}" class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Tambah Jasa</a>
    </div>
    <div class="overflow-x-auto bg-white border border-slate-200 rounded-xl">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Kategori</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Harga</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Durasi</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Komisi</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach ($services as $service)
                    <tr>
                        <td class="px-4 py-3">{{ $service->name }}</td>
                        <td class="px-4 py-3">{{ $service->category?->name }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($service->base_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $service->est_duration_min }} menit</td>
                        <td class="px-4 py-3">{{ ucfirst($service->commission_type) }} · {{ $service->commission_value }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $service->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $service->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.services.edit', $service) }}" class="text-indigo-600 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-2 text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $services->withQueryString()->links() }}</div>
@endsection
