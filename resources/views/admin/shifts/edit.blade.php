@extends('layouts.app')

@section('title', 'Ubah Shift')
@section('content')
    <form method="POST" action="{{ route('admin.shifts.update', $shift) }}" class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
        @csrf
        @method('PUT')
        @include('admin.shifts.partials.form')
        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.shifts.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm">Batal</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Simpan Perubahan</button>
        </div>
    </form>
@endsection
