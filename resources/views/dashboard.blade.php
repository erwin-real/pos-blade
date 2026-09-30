<x-app-layout>

    <x-slot:topbarTitle>
        Dashboard
    </x-slot>
    
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Business Performance Dashboard</h1>
        </div>

        <!-- ROW 1: CORE EARNINGS & OPERATIONAL METRICS -->
        <div class="row">
            <!-- Daily Profit Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Profit (Today)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($dailyProfit, 2) }}</div>
                                <small class="text-muted text-xs">Revenue: ₱{{ number_format($dailyRevenue, 2) }}</small>
                            </div>
                            <div class="col-auto"><i class="fas fa-calendar-day fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weekly Profit Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Profit (This Week)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($weeklyProfit, 2) }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-calendar-week fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Profit Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Profit (This Month)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($monthlyProfit, 2) }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-chart-line fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Critical Alerts Dashboard Info Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card {{ $lowStockCount > 0 ? 'border-left-danger' : 'border-left-secondary' }} shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold {{ $lowStockCount > 0 ? 'text-danger' : 'text-secondary' }} text-uppercase mb-1">Stock Alerts & Tickets</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStockCount }} Low Items</div>
                                <small class="text-muted text-xs">{{ $totalTransactionsToday }} transactions processed today</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x {{ $lowStockCount > 0 ? 'text-danger' : 'text-gray-300' }}"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 2: DETAILED ANALYTICS AND TOP SELLING DATA TABLES -->
        <div class="row">
            <!-- Top 5 Best Selling Products Table -->
            <div class="col-lg-7 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 bg-gradient-primary text-white">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-trophy mr-2"></i>Best Selling Products</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-align-middle m-0">
                                <thead class="bg-light text-xs font-weight-bold text-gray-600 text-uppercase">
                                    <tr>
                                        <th class="pl-4">Product Name</th>
                                        <th class="text-center">Units Sold</th>
                                        <th class="text-right pr-4">Gross Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topProducts as $item)
                                    <tr class="border-bottom">
                                        <td class="pl-4 font-weight-bold text-gray-800">
                                            {{ $item->product->name ?? 'Deleted Product' }}
                                            <small class="text-muted d-block text-xs">SKU/Desc: {{ $item->product->desc ?? 'N/A' }}</small>
                                        </td>
                                        <td class="text-center font-weight-bold text-info">{{ $item->total_units_sold }}</td>
                                        <td class="text-right pr-4 font-weight-bold text-gray-700">₱{{ number_format($item->gross_revenue, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">No sales historical logs tracked yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Channels Value Breakdown Chart & List -->
            <div class="col-lg-5 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-wallet mr-2"></i>Payment Channel Splits (This Month)</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($paymentBreakdown as $payment)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <span class="font-weight-bold text-gray-800 d-block">{{ $payment->payment_method }}</span>
                                    <small class="text-muted text-xs">{{ $payment->count }} transaction instances</small>
                                </div>
                                <span class="badge badge-primary badge-pill p-2 font-weight-bold">₱{{ number_format($payment->total, 2) }}</span>
                            </li>
                            @empty
                            <li class="text-center py-5 text-muted list-group-item">No transactions compiled this month.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

</x-app-layout>
