@extends('layouts.app')

@section('title', 'Laporan Diskon dan Override Harga')
@section('content')
    <form method="GET" class="flex flex-wrap items-end gap-3 mb-4">
        <div>
            <label class="block text-xs font-semibold text-slate-500">Mulai</label>
            <input type="date" name="start_date" value="{{ request('start_date', $start->format('Y-m-d')) }}"
                class="rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500">Selesai</label>
            <input type="date" name="end_date" value="{{ request('end_date', $end->format('Y-m-d')) }}"
                class="rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <button type="submit" class="rounded-md bg-slate-800 px-3 py-2 text-sm text-white">Terapkan</button>
        <a href="{{ request()->fullUrlWithQuery(['format' => 'csv']) }}"
            class="rounded-md border border-slate-300 px-3 py-2 text-sm">Unduh CSV</a>
        <a href="{{ request()->fullUrlWithQuery(['format' => 'pdf']) }}"
            class="rounded-md border border-slate-300 px-3 py-2 text-sm">Unduh PDF</a>
    </form>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="min-w-full text-sm divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Order</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Jasa</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Label</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Diskon</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Harga Manual</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Alasan</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Disetujui Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($items as $item)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ $item->order?->order_no }}</td>
                        <td class="px-4 py-3">{{ $item->service?->name }}</td>
                        <td class="px-4 py-3">{{ $item->person_label }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($item->discount_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $item->manual_price !== null ? 'Rp ' . number_format($item->manual_price, 0, ',', '.') : '-' }}</td>
                        <td class="px-4 py-3">{{ $item->manual_reason ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->manualApprover?->name ?? $item->order?->cashier?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-slate-500">Belum ada diskon atau override harga.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
