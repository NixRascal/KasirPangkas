@extends('layouts.app')

@section('title', 'Dashboard Stakeholder')
@section('content')
    <div class="grid gap-6 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase text-slate-500">Omzet Hari Ini</div>
            <div class="mt-2 text-2xl font-semibold text-slate-800">Rp {{ number_format($revenueToday, 0, ',', '.') }}</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase text-slate-500">Rata-rata Transaksi</div>
            <div class="mt-2 text-2xl font-semibold text-slate-800">Rp {{ number_format($averageTransaction ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase text-slate-500">Pelanggan Baru</div>
            <div class="mt-2 text-2xl font-semibold text-slate-800">{{ $newCustomers }}</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase text-slate-500">Komisi 30 Hari</div>
            <div class="mt-2 text-2xl font-semibold text-slate-800">Rp {{ number_format($commissionTotals, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="grid gap-6 lg:grid-cols-2 mt-6">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-700">Tren Pendapatan 30 Hari</h2>
            <ul class="mt-4 space-y-2 text-sm text-slate-600 max-h-64 overflow-y-auto">
                @foreach ($trendData as $date => $amount)
                    <li class="flex justify-between"><span>{{ $date }}</span><span>Rp {{ number_format($amount, 0, ',', '.') }}</span></li>
                @endforeach
            </ul>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-700">Jasa Terlaris</h2>
            <ul class="mt-4 space-y-2 text-sm text-slate-600">
                @foreach ($topServices as $service)
                    <li class="flex justify-between"><span>{{ $service['name'] }}</span><span>{{ $service['count'] }} layanan · Rp {{ number_format($service['revenue'], 0, ',', '.') }}</span></li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
