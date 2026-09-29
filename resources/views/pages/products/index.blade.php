<x-app-layout>

    <x-slot:topbarTitle>
        Products
    </x-slot>

    <div class="body-right">
        <div class="container-fluid mb-5">

            <!-- Page Heading -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Products</li>
                </ol>
            </nav>

            <div class="row">
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h5 class="float-left m-0 font-weight-bold text-primary">Records</h5>
                            <a href="/products/create" class="btn btn-outline-primary float-right"><i class="fas fa-plus"></i> Add Product</a>
                            <div class="clearfix"></div>
                        </div>
        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($products as $product)
                                            <tr>
                                                <td>{{ $product->id }}</td>
                                                <td><a href="/products/{{ $product->id }}">{{ $product->name }}</a></td>
                                                <td>{{ $product->desc }}</td>
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
