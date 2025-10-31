@extends('layouts.app')

@section('title', 'Shift Operasional')
@section('content')
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-semibold">Daftar Shift</h2>
        <a href="{{ route('admin.shifts.create') }}" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white">Tambah Shift</a>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="min-w-full text-sm divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Mulai</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Selesai</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach ($shifts as $shift)
                    <tr>
                        <td class="px-4 py-3">{{ $shift->name }}</td>
                        <td class="px-4 py-3">{{ $shift->start_time }}</td>
                        <td class="px-4 py-3">{{ $shift->end_time }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $shift->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $shift->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.shifts.edit', $shift) }}" class="text-indigo-600 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('admin.shifts.destroy', $shift) }}" class="inline">
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
    <div class="mt-4">{{ $shifts->links() }}</div>
@endsection
