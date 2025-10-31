<?php

use App\Http\Controllers\Admin\CommissionRuleController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\POS\CashLedgerController;
use App\Http\Controllers\POS\CashSessionController;
use App\Http\Controllers\POS\OrderController;
use App\Http\Controllers\POS\OrderItemController;
use App\Http\Controllers\POS\PosController;
use App\Http\Controllers\Stakeholder\DashboardController;
use App\Http\Controllers\Stakeholder\SnapshotController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/pos');

Route::middleware(['auth'])->group(function () {
    Route::get('pos', [PosController::class, 'index'])
        ->name('pos.index')
        ->middleware('can:create,'.Order::class);

    Route::post('pos/orders', [OrderController::class, 'store'])->name('pos.orders.store');
    Route::post('pos/orders/checkout', [OrderController::class, 'checkout'])->name('pos.orders.checkout');
    Route::post('pos/orders/void', [OrderController::class, 'void'])->name('pos.orders.void');

    Route::post('pos/orders/items', [OrderItemController::class, 'store'])->name('pos.orders.items.store');
    Route::patch('pos/orders/items/{item}', [OrderItemController::class, 'update'])->name('pos.orders.items.update');
    Route::delete('pos/orders/items/{item}', [OrderItemController::class, 'destroy'])->name('pos.orders.items.destroy');
    Route::patch('pos/orders/items/{item}/override', [OrderItemController::class, 'overridePrice'])->name('pos.orders.items.override');

    Route::post('pos/cash-sessions/open', [CashSessionController::class, 'open'])->name('pos.cash-sessions.open');
    Route::post('pos/cash-sessions/close', [CashSessionController::class, 'close'])->name('pos.cash-sessions.close');
    Route::post('pos/cash-ledgers', [CashLedgerController::class, 'store'])->name('pos.cash-ledgers.store');

    Route::prefix('admin')->name('admin.')->middleware('can:access-admin-panel')->group(function () {
        Route::resource('services', ServiceController::class);
        Route::resource('service_categories', ServiceCategoryController::class);
        Route::resource('employees', EmployeeController::class);
        Route::resource('customers', CustomerController::class);
        Route::resource('shifts', ShiftController::class);
        Route::resource('commission_rules', CommissionRuleController::class);

        Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('reports/employee', [ReportController::class, 'employee'])->name('reports.employee');
        Route::get('reports/cash', [ReportController::class, 'cash'])->name('reports.cash');
        Route::get('reports/discount', [ReportController::class, 'discount'])->name('reports.discount');
    });

    Route::prefix('stakeholder')->name('stakeholder.')->middleware('can:access-stakeholder-dashboard')->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::get('snapshot', SnapshotController::class)->name('snapshot');
    });
});
