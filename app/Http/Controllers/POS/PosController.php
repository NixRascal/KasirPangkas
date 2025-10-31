<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Contracts\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $services = Service::with('category')->active()->orderBy('name')->get();
        $categories = ServiceCategory::orderBy('order')->get();
        $employees = Employee::active()->orderBy('name')->get();
        $activeSession = CashSession::query()->whereNull('closed_at')->latest('opened_at')->with('shift')->first();
        $currentOrder = auth()->user()?->orders()->where('status', 'draft')->latest()->with('items.service', 'items.employee')->first();

        return view('pos.index', [
            'services' => $services,
            'categories' => $categories,
            'employees' => $employees,
            'activeSession' => $activeSession,
            'currentOrder' => $currentOrder,
        ]);
    }
}
