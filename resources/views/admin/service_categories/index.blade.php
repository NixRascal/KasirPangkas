@extends('layouts.app')

@section('title', 'Kategori Layanan')
@section('content')
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-semibold">Daftar Kategori</h2>
        <a href="{{ route('admin.service_categories.create') }}" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white">Tambah Kategori</a>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="min-w-full text-sm divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Urutan</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Deskripsi</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach ($categories as $category)
                    <tr>
                        <td class="px-4 py-3">{{ $category->name }}</td>
                        <td class="px-4 py-3">{{ $category->order }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $category->description }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.service_categories.edit', $category) }}" class="text-indigo-600 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('admin.service_categories.destroy', $category) }}" class="inline">
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
    <div class="mt-4">{{ $categories->links() }}</div>
@endsection
