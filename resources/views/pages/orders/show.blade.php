<x-app-layout>

    <x-slot:topbarTitle>
        Date: {{ $order->created_at }}
    </x-slot>

    <div class="body-right">
        <div class="container-fluid mb-5">

            <!-- Page Heading -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="/orders">Orders</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ date('M d, Y D h:i a', strtotime($order->created_at)) }}</li>
                </ol>
            </nav>

            <div class="row">
                <div class="mt-1 col-lg-7 col-sm-8">
                    <div class="card shadow">
                        <div class="card-header ">
                            <h5>Order's Information</h5>
                            <div class="clearfix"></div>
                        </div>
                        <div class="card-body">

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Total Amount') }}: </b><span>Php {{$order->total_amount}}</span></label>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Amount Received') }}: </b><span>Php {{$order->amount_received}}</span></label>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Change') }}: </b><span>Php {{$order->change}}</span></label>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Total Profit') }}: </b><span>Php {{$order->total_profit}}</span></label>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Payment Method') }}: </b><span>{{$order->payment_method}}</span></label>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left">
                                    <b>{{ __('Date Created') }}: </b>
                                    <span id="name">{{ $order->created_at ? date('M d, Y D h:i a', strtotime($order->created_at)) : '-'}}</span>
                                </label>
                            </div>

                            
                            <div class="form-group row">
                                <label for="name" class="col-md-12 col-form-label text-md-left"><b>{{ __('Order Items') }}</b></label>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Quantity</th>
                                                <th>Unit Cost</th>
                                                <th>Unit Price</th>
                                                <th>Subtotal</th>
                                                <th>Item Profit</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($order->order_items as $item)
                                                <tr>
                                                    <td><a href="/products/{{$item->product->id}}">{{$item->product->name}}</a></td>
                                                    <td>{{$item->quantity}}</td>
                                                    <td>{{$item->unit_cost}}</td>
                                                    <td>{{$item->unit_price}}</td>
                                                    <td>{{$item->subtotal}}</td>
                                                    <td>{{$item->item_profit}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="mt-4">
                                <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-info float-left mr-2"><i class="fa fa-pencil-alt"></i> Edit</a>

                                <button class="btn btn-outline-danger" data-toggle="modal" data-target="#delOrderModal">
                                    <i class="fas fa-trash fa-sm fa-fw"></i>
                                    Delete
                                </button>
                                <div class="clearfix"></div>
                            </div> --}}
                            
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

    
    <div class="modal fade" id="delOrderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure you want to delete this order?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Delete" below if you are sure on deleting this order.</div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>

                    <form id="delete" method="POST" action="{{ route('orders.destroy', $order->id) }}" class="float-left">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div>
                            <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i> Delete</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
