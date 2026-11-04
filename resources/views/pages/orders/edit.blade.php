<x-app-layout>

    <x-slot:topbarTitle>
        Update Order
    </x-slot>

    <div class="body-right">
        <div class="container-fluid mb-5">

            <!-- Page Heading -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="/orders">Orders</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="/orders/{{ $order->id }}">{{ date('M d, Y D h:i a', strtotime($order->created_at)) }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Update Order</li>
                </ol>
            </nav>

            <!-- Content Row -->

            <div class="container-fluid mt-5 col-lg-7 col-sm-10">
                <div class="card shadow mb-4">
                    <div class="card-header">{{ __('Order\'s Information') }}</div>

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

                        @if(Auth::user()->type == 'admin')
                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Total Profit') }}: </b><span>Php {{$order->total_profit}}</span></label>
                            </div>
                        @endif

                        <div class="form-group row">
                            <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Payment Method') }}: </b><span>{{$order->payment_method}}</span></label>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Personnel') }}: </b><span>{{$order->user->name}}</span></label>
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

                                            @if(Auth::user()->type == 'admin')
                                                <th>Unit Cost</th>
                                            @endif

                                            <th>Unit Price</th>
                                            <th>Subtotal</th>
                                            
                                            @if(Auth::user()->type == 'admin')
                                                <th>Item Profit</th>
                                            @endif
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($order->order_items as $item)
                                            <tr>
                                                <td><a href="/orders/{{$item->product->id}}">{{$item->product->name}}</a></td>
                                                <td>{{$item->quantity}}</td>

                                                @if(Auth::user()->type == 'admin')
                                                    <td>{{$item->unit_cost}}</td>
                                                @endif

                                                <td>{{$item->unit_price}}</td>
                                                <td>{{$item->subtotal}}</td>
                                                @if(Auth::user()->type == 'admin')
                                                    <td>{{$item->item_profit}}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('orders.update', $order) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group row">
                                <label for="payment-method" class="col-md-12 col-form-label text-md-left">{{ __('Payment Method') }}</label>

                                <div class="col-md-12">
                                    <select class="form-control font-weight-bold" name="payment_method" id="payment-method">
                                        <option value="Cash" {{ $order->payment_method == "Cash" ? 'selected' : '' }}>💵 Cash</option>
                                        <option value="CreditCard" {{ $order->payment_method == "CreditCard" ? 'selected' : '' }}>💳 Credit Card</option>
                                        <option value="GCash" {{ $order->payment_method == "GCash" ? 'selected' : '' }}>📱 GCash</option>
                                        <option value="Maya" {{ $order->payment_method == "Maya" ? 'selected' : '' }}>📱 Maya</option>
                                    </select>
                                </div>
                            </div>

                                                       
                            {{-- EXPIRATION DATE --}}
                            <div class="form-group row">
                                <label for="expired_at" class="col-md-12 col-form-label text-md-left">{{ __('Order Date') }} 
                                    {{-- <span class="text-danger">*</span></label> --}}

                                <div class="col-md-12">
                                    <input id="expired_at" 
                                        @if ($order->created_at)
                                            value="{{ date('Y-m-d', strtotime($order->created_at)) }}" 
                                        @endif
                                        type="date" 
                                        class="form-control{{ $errors->has('expired_at') ? ' is-invalid' : '' }}" 
                                        name="created_at" 
                                        autofocus
                                    >

                                    @if ($errors->has('expired_at'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('expired_at') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mb-0 mt-5 text-center">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fa fa-check"></i> {{ __('Save') }}
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    
    <script>
        // Add the following code if you want the name of the file appear on select
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    </script>

</x-app-layout>
