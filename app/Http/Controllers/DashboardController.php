<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // 1. Multi-Period Profit Calculations
        $dailyProfit = Order::whereDate('created_at', Carbon::today())->sum('total_profit');
        $weeklyProfit = Order::whereBetween('created_at', [$now->startOfWeek()->toDateTimeString(), $now->endOfWeek()->toDateTimeString()])->sum('total_profit');
        $monthlyProfit = Order::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('total_profit');

        // 2. Relevant Operational Info Additions (Revenue & Counters)
        $dailyRevenue = Order::whereDate('created_at', Carbon::today())->sum('net_amount');
        $lowStockCount = Product::whereRaw('stocks <= procurement')->count();
        $totalTransactionsToday = Order::whereDate('created_at', Carbon::today())->count();

        // 3. Top 5 Best Selling Products (Aggregated by gross units shifted)
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_units_sold'), DB::raw('SUM(subtotal) as gross_revenue'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_units_sold')
            ->take(5)
            ->get();

        // 4. Payment Method Operational Breakdown
        $paymentBreakdown = Order::select('payment_method', DB::raw('count(*) as count'), DB::raw('SUM(net_amount) as total'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->groupBy('payment_method')
            ->get();

        // CHART
        // 1. Generate the last 30 days date sequence template structures
        $chartData = [];
        for ($i = 29; $i >= 0; $i--) {
            $dateString = Carbon::today()->subDays($i)->format('Y-m-d');
            $chartData[$dateString] = [
                'label' => Carbon::parse($dateString)->format('M d'), // e.g., "Sep 30"
                'profit' => 0.00
            ];
        }

        // 2. Fetch the actual daily database profit records group totals
        $historicalSales = Order::select(
                DB::raw('DATE(created_at) as sales_date'),
                DB::raw('SUM(total_profit) as daily_profit')
            )
            ->where('created_at', '>=', Carbon::today()->subDays(29)->startOfDay())
            ->groupBy('sales_date')
            ->get();

        // 3. Merge live transactional values into the structural array sequence
        foreach ($historicalSales as $sale) {
            if (isset($chartData[$sale->sales_date])) {
                $chartData[$sale->sales_date]['profit'] = (float) $sale->daily_profit;
            }
        }

        // 4. Split entries into simple array components for script delivery
        $graphLabels = array_column($chartData, 'label');
        $graphValues = array_column($chartData, 'profit');

        return view('dashboard', compact(
            'dailyProfit', 'weeklyProfit', 'monthlyProfit', 
            'dailyRevenue', 'lowStockCount', 'totalTransactionsToday',
            'topProducts', 'paymentBreakdown', 'graphLabels', 'graphValues'
        ));
    }
}
