@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="row row-cols-1 row-cols-md-4 g-3">
                @foreach($products as $product)
                <div class="col mb-4">
                    <div class="card h-100 hoverable-card">
                        <div class="image-container" style="height: 250px; background-image: url('@if(!empty($product->url)) {{ Storage::url('product_images/' . $product->url) }} @else {{ Storage::url('product_images/' . $product->noImage()) }} @endif'); background-size: cover; background-position: center;">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">SKU: {{ $product->sku }}</p>
                            <a href="#" class="btn btn-primary">Add to Cart</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
<style>
    /* .hoverable-card {
        border: none;
    }

    .image-container {
        width: 100%;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }

    .card-body {
        border-bottom-left-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    } */
    .hoverable-card:hover {
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    transition: 0.3s;
}
</style>