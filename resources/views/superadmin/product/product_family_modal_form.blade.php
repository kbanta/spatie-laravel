<!-- Modal-->
<div class="modal fade" id="addProductFamilyModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
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
                        <input type="hidden" id="fid" name="fid" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Product Family</label>
                        <input autocomplete="off" type="text" id="product_family" name="product_family" value="{{ old('name') }}" class="product_family form-control" placeholder="Product Family">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="product_family-error"></strong>
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
    function addProductFamily() {
        $('#mode').val(0);
        $('#fid').val('');
        $('#product_family').val('');
        $('#modalTitle').html("Create Product Family");
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
            var title = 'Create Product Family';
            var method = 'POST';
            var url = "{{route('registerProductFamily')}}";
        } else {
            var id = $('#fid').val();
            var title = 'Update Product Family';
            var method = 'PATCH';
            var url = "product_family/update/" + id;
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
                            if (response.errors.product_family) {
                                $('#product_family-error').html(response.errors.product_family[0]);
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
                            $('#addProductFamilyModal').modal('hide');
                            var oTable = $('#datatable-crud-product-family').dataTable();
                            oTable.fnDraw(false);
                            $('#fid').val('');
                            $('#product_family').val('');
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
            url: "product_family/edit/" + id,
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                console.log(res);
                $('#modalTitle').html("Edit Product Family");
                $('#addProductFamilyModal').modal('show');
                $('#fid').val(res.id);
                $('#product_family').val(res.family_name);
                $('#mode').val(1);
            }
        });
    }
</script>