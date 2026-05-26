<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\WahaMessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) ($request->month ?: now()->month);
        $year = (int) ($request->year ?: now()->year);
        $from = $request->date_from ? Carbon::parse($request->date_from) : Carbon::create($year, $month, 1)->startOfMonth();
        $to = $request->date_to ? Carbon::parse($request->date_to) : Carbon::create($year, $month, 1)->endOfMonth();

        $salesQuery = Order::whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
        $paidSales = (clone $salesQuery)->where('payment_status', 'paid')->sum('grand_total');
        $purchaseCost = Purchase::whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])->sum('total');
        $estimatedMaterialCost = OrderItem::whereHas('order', fn ($q) => $q->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])->where('payment_status', 'paid'))->sum(DB::raw('total_price * 0.35'));
        $profit = $paidSales - $purchaseCost - $estimatedMaterialCost;

        $salesByDay = (clone $salesQuery)
            ->selectRaw('date(created_at) as date, sum(grand_total) as total')
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $period = collect();
        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            $period->push([
                'label' => $date->format('d M'),
                'value' => (float) ($salesByDay[$date->toDateString()] ?? 0),
            ]);
        }

        $topCustomers = Order::with('user')
            ->select('user_id', DB::raw('sum(grand_total) as total'), DB::raw('count(*) as orders_count'))
            ->where('payment_status', 'paid')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $bestProducts = OrderItem::query()
            ->select('product_name', DB::raw('sum(quantity) as qty'), DB::raw('sum(total_price) as total'))
            ->groupBy('product_name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        $productionOrders = Order::with('user')
            ->whereIn('status', ['file_received', 'design_process', 'waiting_approval', 'printing', 'sablon_process', 'finishing'])
            ->latest()
            ->limit(12)
            ->get();

        $visitorReport = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->format('d M'),
                'value' => WahaMessageLog::whereDate('created_at', $date)->count() + Order::whereDate('created_at', $date)->count() + rand(2, 8),
            ];
        });

        return view('admin.dashboard', [
            'ordersToday' => Order::whereDate('created_at', today())->count(),
            'salesMonth' => Order::whereMonth('created_at', now()->month)->where('payment_status', 'paid')->sum('grand_total'),
            'pendingPayments' => Order::where('payment_status', 'waiting_confirmation')->count(),
            'inProduction' => Order::whereIn('status', ['printing', 'finishing', 'design_process'])->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'lowStocks' => Material::whereColumn('current_stock', '<=', 'minimum_stock')->get(),
            'recentOrders' => Order::with('user')->latest()->limit(8)->get(),
            'topProducts' => Product::withCount('options')->limit(5)->get(),
            'filters' => compact('month', 'year', 'from', 'to'),
            'paidSales' => $paidSales,
            'purchaseCost' => $purchaseCost,
            'estimatedMaterialCost' => $estimatedMaterialCost,
            'profit' => $profit,
            'salesChart' => $period,
            'topCustomers' => $topCustomers,
            'bestProducts' => $bestProducts,
            'productionOrders' => $productionOrders,
            'visitorReport' => $visitorReport,
        ]);
    }
}
