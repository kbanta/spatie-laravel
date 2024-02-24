@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8" style="margin-bottom: 20px;">
            <div class="card">
                <div class="card-header">{{ __('General') }}
                    <div class="mb-2" style="float: right;">
                        <!-- <a class="btn btn-success" onClick="addRole()" data-bs-toggle="modal" data-bs-target="#addRoleModal"> Create Product</a> -->
                    </div>
                </div>
                <div class="card-body">
                    <form id="createForm">
                        @csrf
                        <div class="row">
                            <div class="form-group">
                                <input type="hidden" id="mode" name="mode" class="form-control" value="{{empty($product->id) ? '' : 1}}"/>
                                <input type="hidden" id="pid" name="pid" class="form-control" value="{{empty($product->id) ? '' : $product->id}}" />
                            </div>
                            <div class="form-group">
                                <label>SKU</label>
                                <input autocomplete="off" type="text" id="skuInput" name="skuInput" value="{{empty($product->sku) ? '' : $product->sku}}" class="skuInput form-control" placeholder="" disabled>
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="skuInput-error"></strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <select name="product_type_id" id="product_type_id" class="product_type_id form-control">
                                    <option value="" disabled selected>Select Type</option>
                                    @foreach($prod_type as $product_types)
                                    <option value="{{$product_types->id}}" {{ !empty($product->type_id) && $product->type_id == $product_types->id ? 'selected' : '' }}>{{$product_types->type_name}}</option>
                                    @endforeach
                                </select>
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="product_types-error"></strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Family</label>
                                <select name="family_id" id="family_id" class="family_id form-control">
                                    <option value="" disabled selected>Select Family</option>
                                    @foreach($family as $families)
                                    <option value="{{$families->id}}" {{ !empty($product->family_id) && $product->family_id == $families->id ? 'selected' : '' }}>{{$families->family_name}}</option>
                                    @endforeach
                                </select>
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="family-error"></strong>
                                </span>
                            </div>
                            <!-- <div class="form-group">
                                <label>Product Number</label>
                                <input autocomplete="off" type="text" id="product_number" name="product_number" value="{{ old('name') }}" class="product_number form-control" placeholder="Product Number">
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="product_number-error"></strong>
                                </span>
                            </div> -->
                            <div class="form-group">
                                <label>Product Name</label>
                                <input autocomplete="off" type="text" id="product_name" name="product_name" value="{{empty($product->name) ? '' : $product->name}}" class="product_name form-control" placeholder="Product Name">
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="product_name-error"></strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Product Quantity</label>
                                <input autocomplete="off" type="number" id="product_quantity" name="product_quantity" value="{{empty($product->inventory) ? '' : $product->inventory}}" class="product_quantity form-control" placeholder="Product Quantity">
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="product_quantity-error"></strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" id="" cols="30" rows="5" style="resize: none;">{{empty($product->description) ? '' : $product->description}}</textarea>
                                <!-- <input autocomplete="off" type="text" id="description" name="description" value="{{ old('name') }}" class="description form-control" placeholder="Description"> -->
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="description-error"></strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category_id" id="category_id" class="category_id form-control">
                                    <option value="" disabled selected>Select Category</option>
                                    @foreach($category as $categories)
                                    <option value="{{$categories->id}}" {{ !empty($product->category_id) && $product->category_id == $categories->id ? 'selected' : '' }}>{{$categories->name}}</option>
                                    @endforeach
                                </select>
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="category_id-error"></strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Brand</label>
                                <select name="brand_id" id="brand_id" class="brand_id form-control">
                                    <option value="" disabled selected>Select Brand</option>
                                    @foreach($brand as $brands)
                                    <option value="{{$brands->id}}" {{ !empty($product->brand_id) && $product->brand_id == $brands->id ? 'selected' : '' }}>{{$brands->name}}</option>
                                    @endforeach
                                </select>
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="brand_id-error"></strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Color</label>
                                <select name="color_id" id="color_id" class="color_id form-control">
                                    <option value="" disabled selected>Select Color</option>
                                    @foreach($color as $colors)
                                    <option value="{{$colors->id}}" {{ !empty($product->color_id) && $product->color_id == $colors->id ? 'selected' : '' }}>{{$colors->color}}</option>
                                    @endforeach
                                </select>
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="color_id-error"></strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label>Size</label>
                                <select name="size_id" id="size_id" class="size_id form-control">
                                    <option value="" disabled selected>Select Size</option>
                                    @foreach($size as $sizies)
                                    <option value="{{$sizies->id}}" {{ !empty($product->size_id) && $product->size_id == $sizies->id ? 'selected' : '' }}>{{$sizies->size}}</option>
                                    @endforeach
                                </select>
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="size_id-error"></strong>
                                </span>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Weight</label>
                                    <div class="input-group flex-nowrap">
                                        <input autocomplete="off" type="number" id="weight" name="weight" value="{{empty($product->weight) ? '' : $product->weight}}" class="weight form-control" placeholder="Weight">
                                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                        <span class="text-danger">
                                            <strong id="weight-error"></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Dimension</label>
                                    <div class="input-group flex-nowrap">
                                        <input autocomplete="off" type="text" id="dimension" name="dimension" value="{{empty($product->dimension) ? '' : $product->dimension}}" class="dimension form-control" placeholder="Dimension">
                                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                        <span class="text-danger">
                                            <strong id="dimension-error"></strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        <div class="col-md-8" style="margin-bottom: 20px;">
            <div class="card">
                <div class="card-header">{{ __('Product Price') }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Price</label>
                            <div class="input-group flex-nowrap">
                                <span class="input-group-text" id="addon-wrapping">$</span>
                                <input autocomplete="off" type="number" id="price" name="price" value="{{empty($product->price) ? '' : $product->price}}" class="price form-control" placeholder="00.00" style="font-weight: bold;">
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="price-error"></strong>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Cost</label>
                            <div class="input-group flex-nowrap">
                                <span class="input-group-text" id="addon-wrapping">$</span>
                                <input autocomplete="off" type="number" id="cost" name="cost" value="{{empty($product->cost) ? '' : $product->cost}}" class="cost form-control" placeholder="Cost">
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="cost-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Discount</label>
                            <div class="input-group flex-nowrap">
                                <input autocomplete="off" type="number" id="discount" name="discount" value="{{empty($product->discount) ? '' : $product->discount}}" class="discount form-control" placeholder="Discount">
                                <span class="input-group-text" id="addon-wrapping">%</span>
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="discount-error"></strong>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Special Price Start</label>
                            <div class="input-group flex-nowrap">
                                <input autocomplete="off" type="datetime-local" id="special_price_start" name="special_price_start" value="{{empty($product->special_price_start) ? '' : $product->special_price_start}}" class="special_price_start form-control" placeholder="Special Price Start">
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="special_price_start-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Special Price End</label>
                            <div class="input-group flex-nowrap">
                                <input autocomplete="off" type="datetime-local" id="special_price_end" name="special_price_end" value="{{empty($product->special_price_end) ? '' : $product->special_price_end}}" class="special_price_end form-control" placeholder="Special Price End">
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="text-danger">
                                    <strong id="special_price_end-error"></strong>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('superadmin.product.products.product_image_div')
        </form>
    </div>
</div>
<script type="text/javascript">
    //Saving Process...
    
    // Get references to HTML elements
    const type_id = document.getElementById('product_type_id');
    const family_id = document.getElementById('family_id');
    const category_id = document.getElementById('category_id');
    const brand_id = document.getElementById('brand_id');
    const color_id = document.getElementById('color_id');
    const size_id = document.getElementById('size_id');


    // Add event listeners to the select elements
    type_id.addEventListener('change', updateSKU);
    family_id.addEventListener('change', updateSKU);
    category_id.addEventListener('change', updateSKU);
    brand_id.addEventListener('change', updateSKU);
    color_id.addEventListener('change', updateSKU);
    size_id.addEventListener('change', updateSKU);

    // Function to update the SKU based on selected size and color
    function updateSKU() {
        const type = type_id.value;
        const family = family_id.value;
        const category = category_id.value;
        const brand = brand_id.value;
        const color = color_id.value;
        const size = size_id.value;

        const sku = 'SKU' + '-' + type + family + category + '-' + brand + color + size;
        skuInput.value = sku;
    }

    // Initial call to update SKU when the page loads
    updateSKU();

    $('#createForm').on('submit', function(e) {
        e.preventDefault();
        const sku = skuInput.value;
        var formData = new FormData($('#createForm')[0]);

        formData.append('sku', sku);
        formData.append('_method', 'POST');

        // alert(sku);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var mode = parseInt($('#mode').val());
        if (mode === 1) {
            var id = $('#pid').val();
            var title = 'Update Product';
            var method = 'POST';
            var url = "/superadmin/superhome/products/update/" + id;
        } else {
            var title = 'Create Product';
            var method = 'POST';
            var url = "{{route('productFormSave')}}";
        }
        Swal.fire({
            title: title,
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, send it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: method,
                    url: url,
                    data: formData,
                    processData: false, // Prevent jQuery from processing the data
                    contentType: false, // Prevent jQuery from setting the content type
                    // dataType: "json",
                    success: function(response) {
                        console.log(response);
                        if (response.errors) {
                            if (response.errors.size) {
                                $('#size-error').html(response.errors.size[0]);
                            }
                        }
                        if (response.success) {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Your work has been saved',
                                showConfirmButton: false,
                                timer: 1000
                            });
                            location.href = "http://127.0.0.1:8000/superadmin/superhome/products";
                            // location.reload();
                            // $('#addSizeModal').modal('hide');
                            // var oTable = $('#datatable-crud-product').dataTable();
                            // oTable.fnDraw(false);
                            // $('#sid').val('');
                            // $('#size').val('');
                        }
                    },
                });
            }
        });
    });
</script>
@endsection