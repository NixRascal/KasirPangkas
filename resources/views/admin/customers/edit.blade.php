@extends('layouts.app')

@section('title', 'Ubah Pelanggan')
@section('content')
    <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
        @csrf
        @method('PUT')
        @include('admin.customers.partials.form')
        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.customers.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm">Batal</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Simpan Perubahan</button>
        </div>
    </form>
@endsection
