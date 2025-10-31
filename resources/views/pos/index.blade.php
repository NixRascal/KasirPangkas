@extends('layouts.app')

@section('title', 'POS Kasir')
@section('subtitle', $activeSession ? 'Kas sesi aktif: '.$activeSession->shift?->name.' · Dibuka '.$activeSession->opened_at->timezone('Asia/Jakarta')->format('d M H:i') : 'Belum ada sesi kas aktif')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                <h2 class="text-base font-semibold mb-4">Layanan</h2>
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($services as $service)
                        <div class="border border-slate-200 rounded-lg p-4 flex flex-col justify-between">
                            <div>
                                <div class="text-sm uppercase text-slate-400">{{ $service->category?->name }}</div>
                                <div class="font-semibold text-slate-800 mt-1">{{ $service->name }}</div>
                                <div class="text-sm text-slate-500">Rp {{ number_format($service->base_price, 0, ',', '.') }} · {{ $service->est_duration_min }} menit</div>
                            </div>
                            @if ($currentOrder)
                                <form method="POST" action="{{ route('pos.orders.items.store') }}" class="mt-4 space-y-2">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $currentOrder->id }}">
                                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                                    <div>
                                        <label class="sr-only" for="person-label-{{ $service->id }}">Label Orang</label>
                                        <input id="person-label-{{ $service->id }}" type="text" name="person_label" required placeholder="Person label" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
                                    </div>
                                    <div>
                                        <label class="sr-only" for="employee-{{ $service->id }}">Karyawan</label>
                                        <select id="employee-{{ $service->id }}" name="employee_id" required class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            <option value="" disabled selected>Pilih karyawan</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ $employee->name }} · {{ ucfirst($employee->level) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <input type="number" min="1" value="1" name="qty" class="w-16 rounded-md border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Tambah</button>
                                    </div>
                                </form>
                            @else
                                <p class="mt-4 text-sm text-slate-500">Buat order terlebih dahulu untuk menambahkan item.</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                <h2 class="text-base font-semibold mb-3">Order Aktif</h2>
                @if ($currentOrder)
                    <div class="text-sm text-slate-500">Nomor: {{ $currentOrder->order_no }} · Status: <span class="font-medium text-indigo-600">{{ ucfirst($currentOrder->status) }}</span></div>
                    <div class="mt-4 space-y-3">
                        @foreach ($currentOrder->items as $item)
                            <div class="border border-slate-200 rounded-lg p-3">
                                <div class="flex justify-between text-sm font-semibold text-slate-700">
                                    <span>{{ $item->service?->name }} · {{ $item->person_label }}</span>
                                    <span>Rp {{ number_format($item->line_total, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-xs text-slate-500">{{ $item->employee?->name }} · Qty {{ $item->qty }}</div>
                                <div class="mt-2 grid grid-cols-2 gap-2 text-xs">
                                    <form method="POST" action="{{ route('pos.orders.items.update', $item) }}" class="space-y-1">
                                        @csrf
                                        @method('PATCH')
                                        <label class="block text-slate-500">Karyawan</label>
                                        <select name="employee_id" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}" @selected($item->employee_id === $employee->id)>{{ $employee->name }}</option>
                                            @endforeach
                                        </select>
                                        <label class="block text-slate-500">Diskon</label>
                                        <input type="number" step="0.01" min="0" name="discount_amount" value="{{ $item->discount_amount }}" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                                        <button type="submit" class="mt-2 w-full rounded-md bg-slate-800 px-2 py-1 text-white">Simpan</button>
                                    </form>
                                    <div class="space-y-2">
                                        <form method="POST" action="{{ route('pos.orders.items.override', $item) }}" class="space-y-1 border border-amber-200 rounded-md p-2">
                                            @csrf
                                            @method('PATCH')
                                            <label class="block text-slate-500">Harga Manual</label>
                                            <input type="number" step="0.01" min="1" name="manual_price" value="{{ $item->manual_price ?? $item->unit_price }}" class="w-full rounded-md border-slate-300 focus:ring-amber-500 focus:border-amber-500">
                                            <label class="block text-slate-500">Alasan</label>
                                            <input type="text" name="manual_reason" value="{{ $item->manual_reason }}" required class="w-full rounded-md border-slate-300 focus:ring-amber-500 focus:border-amber-500">
                                            <label class="block text-slate-500">Approver (opsional)</label>
                                            <input type="text" name="approver_id" placeholder="ID Admin" class="w-full rounded-md border-slate-300 focus:ring-amber-500 focus:border-amber-500">
                                            <button type="submit" class="mt-2 w-full rounded-md bg-amber-500 px-2 py-1 text-white">Override</button>
                                        </form>
                                        <form method="POST" action="{{ route('pos.orders.items.destroy', $item) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full rounded-md border border-red-200 px-2 py-1 text-red-600 hover:bg-red-50">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 border-t border-slate-200 pt-4 space-y-1 text-sm">
                        <div class="flex justify-between"><span>Subtotal</span><span>Rp {{ number_format($currentOrder->subtotal, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span>Diskon</span><span>- Rp {{ number_format($currentOrder->discount_total, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between font-semibold text-lg"><span>Total</span><span>Rp {{ number_format($currentOrder->grand_total, 0, ',', '.') }}</span></div>
                    </div>
                    <div class="mt-4 bg-slate-100 rounded-lg p-3">
                        <form method="POST" action="{{ route('pos.orders.checkout') }}" class="space-y-2 text-sm">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $currentOrder->id }}">
                            <label class="block">Metode Pembayaran</label>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="payments[0][method]" class="rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="cash">Cash</option>
                                    <option value="qris">QRIS</option>
                                    <option value="debit">Debit</option>
                                    <option value="ewallet">E-Wallet</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                                <input type="number" name="payments[0][amount]" step="0.01" min="0" value="{{ $currentOrder->grand_total }}" class="rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                            <label class="block">Sesi Kas (opsional)</label>
                            <input type="text" name="cash_session_id" value="{{ $activeSession?->id }}" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <button type="submit" class="w-full rounded-md bg-indigo-600 px-3 py-2 font-semibold text-white">Proses Pembayaran</button>
                        </form>
                        <form method="POST" action="{{ route('pos.orders.void') }}" class="mt-3">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $currentOrder->id }}">
                            <textarea name="reason" required placeholder="Alasan void" class="w-full rounded-md border-slate-300 text-sm focus:ring-red-500 focus:border-red-500"></textarea>
                            <button type="submit" class="mt-2 w-full rounded-md border border-red-300 px-3 py-2 text-red-600 hover:bg-red-50">Void Order</button>
                        </form>
                    </div>
                @else
                    <p class="text-sm text-slate-500">Belum ada order aktif.</p>
                    <form method="POST" action="{{ route('pos.orders.store') }}" class="mt-4 space-y-2 text-sm">
                        @csrf
                        <label class="block">Sesi Kas</label>
                        <input type="text" name="cash_session_id" value="{{ $activeSession?->id }}" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <label class="block">Shift</label>
                        <input type="text" name="shift_id" value="{{ $activeSession?->shift_id }}" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit" class="w-full rounded-md bg-indigo-600 px-3 py-2 text-white font-semibold">Buat Order Draft</button>
                    </form>
                @endif
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                <h2 class="text-base font-semibold mb-3">Manajemen Kas</h2>
                <form method="POST" action="{{ route('pos.cash-sessions.open') }}" class="space-y-2 text-sm">
                    @csrf
                    <label class="block">Shift ID</label>
                    <input type="text" name="shift_id" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <label class="block">Modal Awal</label>
                    <input type="number" step="0.01" name="opening_float" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" class="w-full rounded-md bg-emerald-600 px-3 py-2 text-white font-semibold">Buka Kas</button>
                </form>
                <form method="POST" action="{{ route('pos.cash-sessions.close') }}" class="space-y-2 text-sm mt-4 border-t border-slate-200 pt-3">
                    @csrf
                    <label class="block">ID Sesi</label>
                    <input type="text" name="cash_session_id" value="{{ $activeSession?->id }}" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <label class="block">Perhitungan Fisik</label>
                    <input type="number" step="0.01" name="counted_cash" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <label class="block">Catatan</label>
                    <textarea name="notes" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    <button type="submit" class="w-full rounded-md bg-slate-800 px-3 py-2 text-white font-semibold">Tutup Kas</button>
                </form>
                <form method="POST" action="{{ route('pos.cash-ledgers.store') }}" class="space-y-2 text-sm mt-4 border-t border-slate-200 pt-3">
                    @csrf
                    <label class="block">ID Sesi</label>
                    <input type="text" name="cash_session_id" value="{{ $activeSession?->id }}" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <label class="block">Tipe</label>
                    <select name="type" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="cash_in">Kas Masuk</option>
                        <option value="cash_out">Kas Keluar</option>
                    </select>
                    <label class="block">Nominal</label>
                    <input type="number" step="0.01" name="amount" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <label class="block">Alasan</label>
                    <input type="text" name="reason" class="w-full rounded-md border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" class="w-full rounded-md bg-slate-900 px-3 py-2 text-white font-semibold">Catat Kas</button>
                </form>
            </div>
        </div>
    </div>
@endsection
