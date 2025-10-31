@extends('layouts.app')

@section('title', 'Aturan Komisi')
@section('content')
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-semibold">Daftar Aturan Komisi</h2>
        <a href="{{ route('admin.commission_rules.create') }}" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white">Tambah Aturan</a>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="min-w-full text-sm divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Ruang Lingkup</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Nilai</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Periode</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach ($rules as $rule)
                    <tr>
                        <td class="px-4 py-3">{{ $rule->name }}</td>
                        <td class="px-4 py-3">
                            {{ ucfirst(str_replace('_', ' ', $rule->scope)) }}
                            @if ($rule->scope === 'per_service')
                                <span class="text-xs text-slate-500 block">{{ $rule->service?->name }}</span>
                            @elseif ($rule->scope === 'per_employee_level')
                                <span class="text-xs text-slate-500 block">{{ ucfirst($rule->employee_level) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ ucfirst($rule->type) }} · {{ $rule->value }}</td>
                        <td class="px-4 py-3">{{ optional($rule->start_date)->format('d M Y') }} - {{ optional($rule->end_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $rule->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.commission_rules.edit', $rule) }}" class="text-indigo-600 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('admin.commission_rules.destroy', $rule) }}" class="inline">
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
    <div class="mt-4">{{ $rules->links() }}</div>
@endsection
