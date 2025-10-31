@extends('layouts.app')

@section('title', 'Karyawan')
@section('content')
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-semibold">Daftar Karyawan</h2>
        <a href="{{ route('admin.employees.create') }}" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white">Tambah Karyawan</a>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="min-w-full text-sm divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Kode</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Level</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Telepon</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach ($employees as $employee)
                    <tr>
                        <td class="px-4 py-3">{{ $employee->name }}</td>
                        <td class="px-4 py-3">{{ $employee->code }}</td>
                        <td class="px-4 py-3">{{ ucfirst($employee->level) }}</td>
                        <td class="px-4 py-3">{{ $employee->phone }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $employee->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.employees.edit', $employee) }}" class="text-indigo-600 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}" class="inline">
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
    <div class="mt-4">{{ $employees->links() }}</div>
@endsection
