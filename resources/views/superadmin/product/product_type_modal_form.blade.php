<!-- Modal-->
<div class="modal fade" id="addProductTypeModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div role="document" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitle" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" id="mode" name="mode" class="form-control" />
                        <input type="hidden" id="pid" name="pid" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Product Type</label>
                        <input autocomplete="off" type="text" id="product_type" name="product_type" value="{{ old('name') }}" class="product_type form-control" placeholder="Product Type">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="product_type-error"></strong>
                        </span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submitForm" class="btn btn-success">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function addProductType() {
        $('#mode').val(0);
        $('#pid').val('');
        $('#product_type').val('');
        $('#modalTitle').html("Create Product Type");
    }
    //Saving Process...
    $('#createForm').on('submit', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var mode = parseInt($('#mode').val());
        if (mode === 0) {
            var title = 'Create Product Type';
            var method = 'POST';
            var url = "{{route('registerProductType')}}";
        } else {
            var id = $('#pid').val();
            var title = 'Update Product Type';
            var method = 'PATCH';
            var url = "product_type/update/" + id;
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
                    data: $('#createForm').serialize(),
                    dataType: "json",
                    success: function(response) {
                        console.log(response);
                        if (response.errors) {
                            if (response.errors.product_type) {
                                $('#product_type-error').html(response.errors.product_type[0]);
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
                            $('#addProductTypeModal').modal('hide');
                            var oTable = $('#datatable-crud-product-type').dataTable();
                            oTable.fnDraw(false);
                            $('#pid').val('');
                            $('#product_type').val('');
                        }
                    },
                });
            }
        });
    });
    //edit
    function editProductType(id) {
        $.ajax({
            type: "GET",
            url: "product_type/edit/" + id,
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                console.log(res);
                $('#modalTitle').html("Edit Product Type");
                $('#addProductTypeModal').modal('show');
                $('#pid').val(res.id);
                $('#product_type').val(res.type_name);
                $('#mode').val(1);
            }
        });
    }
</script>