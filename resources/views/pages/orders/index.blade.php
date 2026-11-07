<x-app-layout>

    <x-slot:topbarTitle>
        Orders
    </x-slot>

    <div class="body-right">
        <div class="container-fluid mb-5">

            <!-- Page Heading -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Orders</li>
                </ol>
            </nav>

            <div class="row">
                <div class="container-fluid">
                    
                    <div class="card bg-light mb-4 shadow-sm">
                        <div class="card-body">
                            
                                <h5 class="card-title text-muted text-uppercase small font-weight-bold">
                                    Sales for {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}
                                </h5>
                                <div class="row g-3 mb-4">
                                    <!-- 1. All Combined Transactions -->
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm border-left-info">
                                            <div class="card-body">
                                                <h6 class="card-title text-muted text-uppercase small font-weight-bold">
                                                    Total Sales
                                                </h6>
                                                <h3 class="text-dark font-weight-bold mb-0">
                                                    ₱{{ number_format($totalSalesSum, 2) }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. Cash Vault Summary -->
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm border-left-success">
                                            <div class="card-body">
                                                <h6 class="card-title text-muted text-uppercase small font-weight-bold">
                                                    💵 Cash Sales
                                                </h6>
                                                <h3 class="text-success font-weight-bold mb-0">
                                                    ₱{{ number_format($totalCashSales, 2) }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 3. GCash Digital Wallet Summary -->
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow-sm" style="border-left: 4px solid #0d6efd !important;">
                                            <div class="card-body">
                                                <h6 class="card-title text-muted text-uppercase small font-weight-bold">
                                                    📱 GCash Sales
                                                </h6>
                                                <h3 class="text-primary font-weight-bold mb-0">
                                                    ₱{{ number_format($totalGCashSales, 2) }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                {{-- <div>
                                    <h5 class="card-title text-muted text-uppercase small font-weight-bold">
                                        Total Sales for {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}
                                    </h5>
                                    <h2 class="text-success font-weight-bold mb-0">
                                        ₱{{ (number_format($totalSalesSum, 2)) }}
                                    </h2>
                                </div> --}}
                                
                                <form action="{{ url()->current() }}" method="GET" class="d-sm-flex align-items-center gap-2">

                                    @if (Auth::user()->type == 'admin')
                                        <div class="mr-4">
                                            <label for="user-picker" class="fw-bold mb-0 text-nowrap">Cashier:</label>
                                            <select id="user-picker" 
                                                    name="user_id" 
                                                    class="form-control" 
                                                    onchange="this.form.submit()"> <!-- 👈 Autosubmits when user switches -->
                                                <option value="all" {{ $selectedUserId == 'all' ? 'selected' : '' }}>All Staff</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}" {{ $selectedUserId == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="mt-3 mt-sm-0">
                                        <label for="date-picker" class="fw-bold mb-0 mr-2 text-nowrap">Filter Date:</label>
                                        <input
                                            type="date" 
                                            id="date-picker" 
                                            name="date" 
                                            value="{{ $selectedDate }}" 
                                            class="form-control"
                                            onchange="this.form.submit()"
                                        >
                                    </div>
                                </form>
                            </div>

                        </div>
                        
                    </div>
                    
                    <div class="card shadow mb-4">

                        <!-- Summary Total Card -->
                        <div class="card-header py-3">
                            <h5 class="float-left m-0 font-weight-bold text-primary">Records</h5>
                            <a href="/orders/create" class="btn btn-outline-primary float-right"><i class="fas fa-plus"></i> New Order</a>
                            <div class="clearfix"></div>
                        </div>
        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Products Sold</th>
                                            <th>Total</th>
                                            <th>Amount Received</th>
                                            <th>Change</th>
                                            <th>Payment method</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Date</th>
                                            <th>Products Sold</th>
                                            <th>Total</th>
                                            <th>Amount Received</th>
                                            <th>Change</th>
                                            <th>Payment method</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($orders as $order)
                                            <tr>
                                                <td><a href="/orders/{{ $order->id }}">{{ date('M d, Y D h:i a', strtotime($order->created_at)) }}</a></td>

                                                <td>
                                                    <ul class="list-unstyled mb-0 small">
                                                        @foreach($order->order_items as $item)
                                                            <li>
                                                                <span class="text-muted">{{ $item->quantity }}x</span> 
                                                                {{ $item->product->name ?? 'Custom Product/Deleted Item' }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </td>

                                                <td>{{ $order->total_amount }}</td>
                                                <td>{{ $order->amount_received }}</td>
                                                <td>{{ $order->change }}</td>
                                                <td>{{ $order->payment_method }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
