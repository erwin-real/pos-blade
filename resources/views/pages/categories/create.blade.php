<x-app-layout>

    <x-slot:topbarTitle>
        Add Category
    </x-slot>

    <div class="body-right">
        <div class="container-fluid mb-5">

            <!-- Page Heading -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="/categories">Categories</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Add Category</li>
                </ol>
            </nav>

            <!-- Content Row -->

            <div class="container-fluid mt-5 col-lg-7 col-sm-10">
                <div class="card shadow mb-4">
                    <div class="card-header">{{ __('Category\'s Information') }}</div>

                    <div class="card-body">

                        <form method="POST" action="/categories" onsubmit="return check()">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-md-8 col-form-label text-md-left">{{ __('Category Name') }} <span class="text-danger">*</span></label>

                                <div class="col-md-12">
                                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="desc" class="col-md-12 col-form-label text-md-left">{{ __('Description') }}</label>

                                <div class="col-md-12">
                                    <textarea id="desc" type="text" class="form-control{{ $errors->has('desc') ? ' is-invalid' : '' }}" name="desc" autofocus></textarea>

                                    @if ($errors->has('desc'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('desc') }}</strong>
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

</x-app-layout>
