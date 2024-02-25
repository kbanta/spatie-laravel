<div class="col-md-8">
    <div class="card">
        <div class="card-header">{{ __('Product Images') }}
            <div class="mb-2" style="float: right;">
                <!-- <a class="btn btn-success" onClick="addRole()" data-bs-toggle="modal" data-bs-target="#addRoleModal"> Create Product</a> -->
            </div>
        </div>
        <div class="card-body">
            <h2>Main Image</h2>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <input type="file" name="images-main" id="images-main" placeholder="Choose main image">
                    </div>
                    @error('images')
                    <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12">
                    <div class="mt-1 text-center">
                        <div class="images-preview-div-main"></div>
                        @if(!empty($product->url))
                        <input type="hidden" id="images_main_holder" name="images_main_holder" value="{{empty($product->url) ? '' : $product->url}}">
                        <img src="{{ Storage::url('product_images/' . $product->url) }}" class="images-preview-div-main-holder" alt="Image Preview" style="width: 250px;height: auto; border: 2px dashed #cacaca;">
                        @else
                        <img src="{{ '/storage/product_images/no-image-2.png' }}" class="images-preview-div-main-no-image" alt="Image Preview" style="width: 250px;height: auto; border: 2px dashed #cacaca;">
                        @endif
                    </div>
                </div>
            </div>
            <hr>
            <h2>Additional Image</h2>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <input type="file" name="images-additional[]" id="images-additional" multiple accept="image/*" placeholder="Choose additional images">
                    </div>
                    @error('images')
                    <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12">
                    <div class="mt-1 text-center">
                        <div class="images-preview-div-additional"></div>
                        @if(!empty($prod_add_image))
                        @foreach($prod_add_image as $additional_images)
                                <input type="hidden" id="images_additional_holder[]" name="images_additional_holder[]" value="{{empty($additional_images['url']) ? '' : $additional_images['url']}}">
                                <img src="{{ Storage::url('product_images/' . $additional_images['url']) }}" class="images-preview-div-additional-holder" alt="Image Preview" style="width: 250px;height: auto; border: 2px dashed #cacaca;">
                            @endforeach
                        @else
                            <img src="{{ '/storage/product_images/no-image-2.png' }}" class="images-preview-div-additional-holder" alt="Image Preview" style="width: 250px;height: auto; border: 2px dashed #cacaca;">
                        @endif
                    </div>
                </div>
            </div>
            <hr>
            <div class="col-md-12">
                <button type="submit" class="btn btn-success" id="submit" style="float: right;">Submit</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        // Single image preview with JavaScript
        var previewImage = function(input, imgPreviewPlaceholder) {

            if (input.files) {
                var filesAmount = Math.min(input.files.length, 6); // Limit to 6 images

                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();

                    reader.onload = function(event) {
                        $(imgPreviewPlaceholder).empty(); // Clear previous image
                        $('.images-preview-div-main-holder').hide();
                        $('.images-preview-div-main-no-image').hide();
                        $($.parseHTML('<img>')).attr('src', event.target.result)
                            .attr('width', '250px') // Add width attribute
                            .attr('height', 'auto') // Add height attribute
                            .css({
                                'display': 'inline-block', // Set display to inline-block
                                'margin-right': '10px',
                                'border': '2px dashed #cacaca'
                            })
                            .appendTo(imgPreviewPlaceholder);
                    }

                    reader.readAsDataURL(input.files[i]);
                }
            }

        };

        var previewAdditionalImages = function(input, imgPreviewPlaceholder) {
            $(imgPreviewPlaceholder).empty();
            if (input.files) {
                var filesAmount = Math.min(input.files.length, 5); // Limit to 5 images

                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();

                    reader.onload = function(event) {
                        $('.images-preview-div-additional-holder').hide();
                        $($.parseHTML('<img>')).attr('src', event.target.result)
                            .attr('width', '250px') // Add width attribute
                            .attr('height', 'auto') // Add height attribute
                            .css({
                                'display': 'inline-block', // Set display to inline-block
                                'margin-right': '10px',
                                'border': '2px dashed #cacaca'
                            })
                            .appendTo(imgPreviewPlaceholder);
                    }

                    reader.readAsDataURL(input.files[i]);
                }
            }

        };

        $('#images-main').on('change', function() {
            previewImage(this, 'div.images-preview-div-main');
        });

        $('#images-additional').off('change').on('change', function() {
            previewAdditionalImages(this, 'div.images-preview-div-additional');
        });
    });
</script>
