<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Kasir Pangkas') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen flex">
        @auth
            <aside class="hidden lg:flex w-64 flex-col bg-white border-r border-slate-200">
                <div class="p-4 border-b border-slate-200">
                    <span class="text-lg font-semibold">{{ config('app.name', 'Kasir Pangkas') }}</span>
                    <div class="text-sm text-slate-500 mt-1">{{ auth()->user()->name }} · {{ auth()->user()->role_label }}</div>
                </div>
                <nav class="flex-1 overflow-y-auto p-4 space-y-2 text-sm">
                    <a href="{{ route('pos.index') }}" class="block px-3 py-2 rounded-md {{ request()->routeIs('pos.*') ? 'bg-indigo-100 text-indigo-700' : 'hover:bg-slate-100' }}">POS Kasir</a>
                    @can('access-admin-panel')
                        <div class="mt-4 text-xs uppercase text-slate-400">Admin</div>
                        <a href="{{ route('admin.services.index') }}" class="block px-3 py-2 rounded-md {{ request()->is('admin/services*') ? 'bg-indigo-100 text-indigo-700' : 'hover:bg-slate-100' }}">Jasa</a>
                        <a href="{{ route('admin.service_categories.index') }}" class="block px-3 py-2 rounded-md {{ request()->is('admin/service_categories*') ? 'bg-indigo-100 text-indigo-700' : 'hover:bg-slate-100' }}">Kategori</a>
                        <a href="{{ route('admin.employees.index') }}" class="block px-3 py-2 rounded-md {{ request()->is('admin/employees*') ? 'bg-indigo-100 text-indigo-700' : 'hover:bg-slate-100' }}">Karyawan</a>
                        <a href="{{ route('admin.customers.index') }}" class="block px-3 py-2 rounded-md {{ request()->is('admin/customers*') ? 'bg-indigo-100 text-indigo-700' : 'hover:bg-slate-100' }}">Pelanggan</a>
                        <a href="{{ route('admin.shifts.index') }}" class="block px-3 py-2 rounded-md {{ request()->is('admin/shifts*') ? 'bg-indigo-100 text-indigo-700' : 'hover:bg-slate-100' }}">Shift</a>
                        <a href="{{ route('admin.commission_rules.index') }}" class="block px-3 py-2 rounded-md {{ request()->is('admin/commission_rules*') ? 'bg-indigo-100 text-indigo-700' : 'hover:bg-slate-100' }}">Aturan Komisi</a>
                        <a href="{{ route('admin.reports.sales') }}" class="block px-3 py-2 rounded-md {{ request()->is('admin/reports*') ? 'bg-indigo-100 text-indigo-700' : 'hover:bg-slate-100' }}">Laporan</a>
                    @endcan
                    @can('access-stakeholder-dashboard')
                        <div class="mt-4 text-xs uppercase text-slate-400">Stakeholder</div>
                        <a href="{{ route('stakeholder.dashboard') }}" class="block px-3 py-2 rounded-md {{ request()->is('stakeholder/dashboard') ? 'bg-indigo-100 text-indigo-700' : 'hover:bg-slate-100' }}">Dashboard</a>
                    @endcan
                </nav>
                <form method="POST" action="{{ route('logout') }}" class="p-4 border-t border-slate-200">
                    @csrf
                    <button type="submit" class="w-full px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50 rounded-md">Keluar</button>
                </form>
            </aside>
        @endauth
        <main class="flex-1">
            <header class="bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold">@yield('title', 'Dashboard')</h1>
                    <p class="text-sm text-slate-500">@yield('subtitle')</p>
                </div>
                <div class="text-xs text-slate-400">Zona waktu: Asia/Jakarta · {{ now()->timezone('Asia/Jakarta')->format('d M Y H:i') }}</div>
            </header>
            <section class="p-4 lg:p-8">
                @if (session('status'))
                    <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif
                @yield('content')
            </section>
        </main>
    </div>
</body>
</html>
