<x-app-layout>

    <x-slot:topbarTitle>
        Categories
    </x-slot>

    <div class="body-right">
        <div class="container-fluid mb-5">

            <!-- Page Heading -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Categories</li>
                </ol>
            </nav>

            <div class="row">
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h5 class="float-left m-0 font-weight-bold text-primary">Records</h5>
                            <a href="/categories/create" class="btn btn-outline-primary float-right"><i class="fas fa-plus"></i> Add Category</a>
                            <div class="clearfix"></div>
                        </div>

                        
                        {{-- <div class="card-header">
                            <h5 class="float-left">Tools</h5>
                            <a href="/tools/create" class="btn btn-outline-primary float-right"><i class="fas fa-plus"></i> Add Tool</a>
                            <div class="clearfix"></div>
                        </div> --}}
                        
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
                                        @foreach ($categories as $category)
                                            <tr>
                                                <td>{{ $category->id }}</td>
                                                <td><a href="/categories/{{ $category->id }}">{{ $category->name }}</a></td>
                                                <td>{{ $category->desc }}</td>
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
