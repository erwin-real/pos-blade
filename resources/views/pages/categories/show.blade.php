<x-app-layout>

    <x-slot:topbarTitle>
        {{ $category->name }}
    </x-slot>

    <div class="body-right">
        <div class="container-fluid mb-5">

            <!-- Page Heading -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="/categories">Categories</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                </ol>
            </nav>

            <div class="row">
                <div class="mt-1 col-lg-7 col-sm-8">
                    <div class="card shadow">
                        <div class="card-header ">
                            <h5>Category's Information</h5>
                            <div class="clearfix"></div>
                        </div>
                        <div class="card-body">

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Name') }}: </b><span>{{$category->name}}</span></label>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-12 col-form-label text-md-left"><b>{{ __('Description') }}: </b><span>{{$category->desc}}</span></label>
                            </div>

                            
                            <div class="form-group row">
                                <label for="name" class="col-md-12 col-form-label text-md-left"><b>{{ __('Products') }}</b></label>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th>Stocks</th>
                                                <th>Procurement</th>
                                                <th>Price</th>
                                                <th>SRP</th>
                                                <th>Exp date</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($category->products as $product)
                                                <tr>
                                                    <td><a href="/products/{{$product->id}}">{{$product->name}}</a></td>
                                                    <td>{{$product->desc}}</td>
                                                    <td>{{$product->stocks}}</td>
                                                    <td>{{$product->procurement}}</td>
                                                    <td>{{$product->price}}</td>
                                                    <td>{{$product->srp}}</td>
                                                    <td>{{$product->expired_at ? date('D M d, Y', strtotime($product->expired_at)) : '-'}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-info float-left mr-2"><i class="fa fa-pencil-alt"></i> Edit</a>

                                <button class="btn btn-outline-danger" data-toggle="modal" data-target="#delCategoryModal">
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

    
    <div class="modal fade" id="delCategoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure you want to delete this category?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Delete" below if you are sure on deleting this category.</div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>

                    <form id="delete" method="POST" action="{{ route('categories.destroy', $category->id) }}" class="float-left">
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
