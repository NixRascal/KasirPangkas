<?php

namespace App\Http\Controllers\Stakeholder;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class SnapshotController extends Controller
{
    public function __invoke(): JsonResponse
    {
        Gate::authorize('access-stakeholder-dashboard');

        $start = Carbon::now('Asia/Jakarta')->subDays(7);
        $orders = Order::where('status', 'paid')->where('paid_at', '>=', $start)->get();

        return response()->json([
            'orders' => $orders->count(),
            'revenue' => $orders->sum('grand_total'),
            'average_ticket' => $orders->avg('grand_total'),
        ]);
    }
}
