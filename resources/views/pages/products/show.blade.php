<x-app-layout>

    <x-slot:topbarTitle>
        {{ $product->name }}
    </x-slot>

    <div class="body-right">
        <div class="container-fluid mb-5">

            <!-- Page Heading -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="/products">Products</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>

            <div class="row">
                <div class="mt-1 col-lg-7 col-sm-8">
                    <div class="card shadow">
                        <div class="card-header ">
                            <h5>Product's Information</h5>
                            <div class="clearfix"></div>
                        </div>
                        <div class="card-body">

                            <div class="form-group row d-block text-left">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Photo') }}</b></label>
                                @if ($product->cover_image != 'noimage.jpg')
                                    <img class="img-thumbnail rounded" src="/storage/products/{{$product->cover_image}}" alt="">
                                @else
                                    <span class="ml-5">None</span>
                                @endif
                            </div>

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Name') }}: </b><span>{{$product->name}}</span></label>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Description') }}: </b><span>{{$product->desc}}</span></label>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Category') }}: </b><span>{{$product->category->name}}</span></label>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left {{$product->stocks <= $product->procurement ? 'text-danger font-weight-bolder' : ''}}">
                                    <b>{{ __('Stocks') }}: </b>
                                    <span>{{$product->stocks}}</span>
                                </label>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Procurement') }}: </b><span>{{$product->procurement}}</span></label>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Price') }}: </b><span>PHP {{$product->price}}</span></label>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('SRP') }}: </b><span>PHP {{$product->srp}}</span></label>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left">
                                    <b>{{ __('Expiration Date') }}: </b>
                                    <span id="name">{{ $product->expired_at ? date('D M d, Y', strtotime($product->expired_at)) : '-'}}</span>
                                </label>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-info float-left mr-2"><i class="fa fa-pencil-alt"></i> Edit</a>

                                <button class="btn btn-outline-danger" data-toggle="modal" data-target="#delProductModal">
                                    <i class="fas fa-trash fa-sm fa-fw"></i>
                                    Delete
                                </button>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

    
    <div class="modal fade" id="delProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure you want to delete this product?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Delete" below if you are sure on deleting this product.</div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>

                    <form id="delete" method="POST" action="{{ route('products.destroy', $product->id) }}" class="float-left">
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
