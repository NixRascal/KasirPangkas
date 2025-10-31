@extends('layouts.app')

@section('title', 'Laporan Kas Harian')
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
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Session</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Shift</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Kasir Buka</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Dibuka</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Ditutup</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Modal Awal</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Perkiraan Kas</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Kas Dihitung</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Selisih</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($sessions as $session)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ $session->id }}</td>
                        <td class="px-4 py-3">{{ $session->shift?->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $session->openedBy?->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $session->opened_at?->setTimezone('Asia/Jakarta')->format('d M Y H:i') ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $session->closed_at?->setTimezone('Asia/Jakarta')->format('d M Y H:i') ?? '-' }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($session->opening_float, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($session->cash_expected ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($session->closing_cash_counted ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 {{ ($session->variance ?? 0) !== 0 ? 'text-red-600 font-semibold' : '' }}">
                            Rp {{ number_format($session->variance ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">{{ $session->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-6 text-center text-slate-500">Belum ada data sesi kas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
