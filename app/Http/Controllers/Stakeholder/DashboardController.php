<?php

namespace App\Http\Controllers\Stakeholder;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        Gate::authorize('access-stakeholder-dashboard');

        $today = Carbon::now('Asia/Jakarta')->startOfDay();
        $ordersToday = Order::where('status', 'paid')->whereBetween('paid_at', [$today, $today->copy()->endOfDay()])->get();
        $revenueToday = $ordersToday->sum('grand_total');
        $averageTransaction = $ordersToday->avg('grand_total');
        $newCustomers = Customer::where('created_at', '>=', $today)->count();

        $thirtyDaysAgo = Carbon::now('Asia/Jakarta')->subDays(30);
        $trendOrders = Order::where('status', 'paid')->where('paid_at', '>=', $thirtyDaysAgo)->get();
        $trendData = $trendOrders->groupBy(fn ($order) => optional($order->paid_at)->format('Y-m-d'))
            ->map(fn ($group) => $group->sum('grand_total'));

        $topServices = OrderItem::with('service')
            ->paidBetween($thirtyDaysAgo->toDateTimeString(), Carbon::now('Asia/Jakarta')->toDateTimeString())
            ->get()
            ->groupBy('service_id')
            ->map(fn ($group) => [
                'name' => $group->first()->service?->name,
                'count' => $group->count(),
                'revenue' => $group->sum('line_total'),
            ])->sortByDesc('revenue')->take(5);

        $commissionTotals = Commission::where('created_at', '>=', $thirtyDaysAgo)->sum('commission_amount');

        return view('stakeholder.dashboard', [
            'revenueToday' => $revenueToday,
            'averageTransaction' => $averageTransaction,
            'newCustomers' => $newCustomers,
            'trendData' => $trendData,
            'topServices' => $topServices,
            'commissionTotals' => $commissionTotals,
        ]);
    }
}
