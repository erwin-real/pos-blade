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
                    <div class="card shadow mb-4">
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
                                            <th>Total</th>
                                            <th>Amount Received</th>
                                            <th>Change</th>
                                            <th>Payment method</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Date</th>
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
