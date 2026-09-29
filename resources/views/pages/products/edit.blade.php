<x-app-layout>

    <x-slot:topbarTitle>
        Update Product
    </x-slot>

    <div class="body-right">
        <div class="container-fluid mb-5">

            <!-- Page Heading -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="/products">Products</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="/products/{{ $product->id }}">{{ $product->name }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Update Product</li>
                </ol>
            </nav>

            <!-- Content Row -->

            <div class="container-fluid mt-5 col-lg-7 col-sm-10">
                <div class="card shadow mb-4">
                    <div class="card-header">{{ __('Product\'s Information') }}</div>

                    <div class="card-body">

                        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            {{-- // NAME --}}
                            <div class="form-group row">
                                <label for="name" class="col-md-8 col-form-label text-md-left">{{ __('Name') }} <span class="text-danger">*</span></label>

                                <div class="col-md-12">
                                    <input id="name" type="text" value="{{ $product->name }}" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- CATEGORY --}}
                            <div class="form-group row">
                                <label for="category" class="col-md-12 col-form-label text-md-left">{{ __('Category') }} <span class="text-danger">*</span></label>

                                <div class="col-md-12">
                                    <select id="category" name="category_id" class="form-control" required autofocus>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{$category->name}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('category'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('category') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- DESCRIPTION --}}
                            <div class="form-group row">
                                <label for="desc" class="col-md-12 col-form-label text-md-left">{{ __('Description') }}</label>

                                <div class="col-md-12">
                                    <textarea id="desc" type="text" class="form-control{{ $errors->has('desc') ? ' is-invalid' : '' }}" name="desc" autofocus>{{$product->desc}}</textarea>

                                    @if ($errors->has('desc'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('desc') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- STOCKS --}}
                            <div class="form-group row">
                                <label for="stocks" class="col-md-12 col-form-label text-md-left">{{ __('Stocks') }} <span class="text-danger">*</span></label>

                                <div class="col-md-12">
                                    <input id="stocks" type="number" value="{{ $product->stocks }}" class="form-control{{ $errors->has('stocks') ? ' is-invalid' : '' }}" required name="stocks" required autofocus>

                                    @if ($errors->has('stocks'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('stocks') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- PROCUREMENT --}}
                            <div class="form-group row">
                                <label for="procurement" class="col-md-12 col-form-label text-md-left">{{ __('Procurement') }} <span class="text-danger">*</span></label>

                                <div class="col-md-12">
                                    <input id="procurement" type="number" value="{{ $product->procurement }}" class="form-control{{ $errors->has('procurement') ? ' is-invalid' : '' }}" name="procurement" required autofocus>

                                    @if ($errors->has('procurement'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('procurement') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- PRICE --}}
                            <div class="form-group row">
                                <label for="price" class="col-md-12 col-form-label text-md-left">{{ __('Price') }} <span class="text-danger">*</span></label>

                                <div class="col-md-12">
                                    <input id="price" type="number" value="{{ $product->price }}" class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}" name="price" autofocus required>

                                    @if ($errors->has('price'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('price') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- SRP --}}
                            <div class="form-group row">
                                <label for="srp" class="col-md-12 col-form-label text-md-left">{{ __('Suggested Retail Price (SRP)') }} <span class="text-danger">*</span></label>

                                <div class="col-md-12">
                                    <input id="srp" type="number" value="{{ $product->srp }}" class="form-control{{ $errors->has('srp') ? ' is-invalid' : '' }}" name="srp" required autofocus>

                                    @if ($errors->has('srp'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('srp') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- EXPIRATION DATE --}}
                            <div class="form-group row">
                                <label for="expired_at" class="col-md-12 col-form-label text-md-left">{{ __('Expiration Date') }} 
                                    {{-- <span class="text-danger">*</span></label> --}}

                                <div class="col-md-12">
                                    <input id="expired_at" 
                                        value="{{ date('Y-m-d', strtotime($product->expired_at)) }}" 
                                        type="date" 
                                        class="form-control{{ $errors->has('expired_at') ? ' is-invalid' : '' }}" 
                                        name="expired_at" 
                                        autofocus
                                    >

                                    @if ($errors->has('expired_at'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('expired_at') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="custom-file">
                                <label for="cover_image" class="col-md-12 col-form-label pl-0 text-md-left">{{ __('Photo') }}</label>

                                <input type="file" name="cover_image" id="file">

                                {{-- <div class="col-md-12">
                                    <input id="cover_image" type="file" class="custom-file-input {{ $errors->has('cover_image') ? ' is-invalid' : '' }}" name="cover_image" autofocus>
                                    <label class="custom-file-label" for="cover_image">Choose file...</label>

                                    @if ($errors->has('cover_image'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('cover_image') }}</strong>
                                    </span>
                                    @endif
                                </div> --}}
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
