@extends('layouts.app')

@section('title', 'Tambah Kategori')
@section('content')
    <form method="POST" action="{{ route('admin.service_categories.store') }}" class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
        @csrf
        @include('admin.service_categories.partials.form')
        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.service_categories.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm">Batal</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Simpan</button>
        </div>
    </form>
@endsection
