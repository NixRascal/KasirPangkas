<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\ReportExportService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct(private readonly ReportExportService $exportService)
    {
        $this->middleware('can:viewAny,'.Order::class)->only(['sales', 'employee', 'cash', 'discount']);
    }

    public function sales(Request $request): Response|View
    {
        [$start, $end] = $this->resolveDateRange($request);
        $orders = Order::with(['cashier', 'payments'])->paidBetween($start->toDateTimeString(), $end->toDateTimeString())->get();
        $format = $request->query('format');

        if ($format === 'csv') {
            $rows = $orders->map(fn ($order) => [
                $order->order_no,
                optional($order->paid_at)->format('Y-m-d H:i'),
                $order->cashier?->name,
                $order->grand_total,
                $order->paid_total,
                $order->change_due,
            ]);

            return $this->exportService->streamCsv([
                'Order No', 'Paid At', 'Cashier', 'Grand Total', 'Paid Total', 'Change Due',
            ], $rows, 'sales-report.csv');
        }

        if ($format === 'pdf') {
            $lines = $orders->map(function ($order) {
                return sprintf('%s | %s | %s | %0.2f', $order->order_no, optional($order->paid_at)->format('Y-m-d H:i'), $order->cashier?->name, $order->grand_total);
            })->all();

            return $this->exportService->streamPdf('Sales Report', $lines, 'sales-report.pdf');
        }

        $daily = $orders->groupBy(fn ($order) => optional($order->paid_at)->format('Y-m-d'))
            ->map(fn ($group) => [
                'count' => $group->count(),
                'total' => $group->sum('grand_total'),
            ]);

        return view('admin.reports.sales', [
            'orders' => $orders,
            'daily' => $daily,
            'start' => $start,
            'end' => $end,
        ]);
    }

    public function employee(Request $request): Response|View
    {
        [$start, $end] = $this->resolveDateRange($request);
        $items = OrderItem::with('employee')->paidBetween($start->toDateTimeString(), $end->toDateTimeString())->get();
        $format = $request->query('format');

        $totals = $items->groupBy(fn ($item) => $item->employee?->name ?? 'Unknown')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'total' => $group->sum('line_total'),
                'commission' => $group->sum(fn ($item) => $item->commission?->commission_amount ?? 0),
            ]);

        if ($format === 'csv') {
            $rows = $totals->map(fn ($data, $name) => [$name, $data['count'], $data['total'], $data['commission']]);

            return $this->exportService->streamCsv(['Employee', 'Services', 'Revenue', 'Commission'], $rows, 'employee-report.csv');
        }

        if ($format === 'pdf') {
            $lines = $totals->map(fn ($data, $name) => sprintf('%s | %d | %0.2f | %0.2f', $name, $data['count'], $data['total'], $data['commission']))->all();

            return $this->exportService->streamPdf('Employee Performance', $lines, 'employee-report.pdf');
        }

        return view('admin.reports.employee', [
            'totals' => $totals,
            'start' => $start,
            'end' => $end,
        ]);
    }

    public function cash(Request $request): Response|View
    {
        [$start, $end] = $this->resolveDateRange($request);
        $sessions = CashSession::with(['openedBy', 'closedBy', 'shift'])
            ->whereBetween('opened_at', [$start, $end])
            ->orderBy('opened_at')
            ->get();
        $format = $request->query('format');

        if ($format === 'csv') {
            $rows = $sessions->map(fn ($session) => [
                $session->id,
                $session->shift?->name,
                optional($session->opened_at)->format('Y-m-d H:i'),
                optional($session->closed_at)->format('Y-m-d H:i'),
                $session->opening_float,
                $session->closing_cash_counted,
                $session->variance,
            ]);

            return $this->exportService->streamCsv([
                'Session ID', 'Shift', 'Opened At', 'Closed At', 'Opening Float', 'Counted', 'Variance',
            ], $rows, 'cash-report.csv');
        }

        if ($format === 'pdf') {
            $lines = $sessions->map(fn ($session) => sprintf('%s | %s | %s | %0.2f', $session->id, optional($session->opened_at)->format('Y-m-d H:i'), optional($session->closed_at)->format('Y-m-d H:i'), $session->variance ?? 0))->all();

            return $this->exportService->streamPdf('Cash Sessions', $lines, 'cash-report.pdf');
        }

        return view('admin.reports.cash', [
            'sessions' => $sessions,
            'start' => $start,
            'end' => $end,
        ]);
    }

    public function discount(Request $request): Response|View
    {
        [$start, $end] = $this->resolveDateRange($request);
        $items = OrderItem::with(['order.cashier', 'service', 'manualApprover'])
            ->paidBetween($start->toDateTimeString(), $end->toDateTimeString())
            ->get()
            ->filter(fn ($item) => $item->discount_amount > 0 || $item->manual_price !== null);
        $format = $request->query('format');

        if ($format === 'csv') {
            $discounts = $items->map(fn ($item) => [
                $item->order?->order_no,
                $item->service?->name,
                $item->person_label,
                $item->discount_amount,
                $item->manual_price,
                $item->manual_reason,
            ]);
            return $this->exportService->streamCsv([
                'Order', 'Service', 'Label', 'Discount', 'Manual Price', 'Reason',
            ], $discounts, 'discount-report.csv');
        }

        if ($format === 'pdf') {
            $lines = $items->map(fn ($item) => sprintf(
                '%s | %s | %s | %0.2f',
                $item->order?->order_no,
                $item->service?->name,
                $item->person_label,
                $item->discount_amount
            ))->all();

            return $this->exportService->streamPdf('Discount Usage', $lines, 'discount-report.pdf');
        }

        return view('admin.reports.discount', [
            'items' => $items,
            'start' => $start,
            'end' => $end,
        ]);
    }

    private function resolveDateRange(Request $request): array
    {
        $timezone = 'Asia/Jakarta';
        $start = $request->query('start_date')
            ? Carbon::parse($request->query('start_date'), $timezone)->startOfDay()
            : Carbon::now($timezone)->startOfDay();
        $end = $request->query('end_date')
            ? Carbon::parse($request->query('end_date'), $timezone)->endOfDay()
            : Carbon::now($timezone)->endOfDay();

        return [$start, $end];
    }
}
