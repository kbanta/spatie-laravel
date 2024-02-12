<!-- Modal-->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
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
                        <input type="hidden" id="cid" name="cid" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input autocomplete="off" type="text" id="category" name="category" value="{{ old('name') }}" class="category form-control" placeholder="Category">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="category-error"></strong>
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
    function addCategory() {
        $('#mode').val(0);
        $('#cid').val('');
        $('#category').val('');
        $('#modalTitle').html("Create Category");
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
            var title = 'Create Category';
            var method = 'POST';
            var url = "{{route('registerCategory')}}";
        } else {
            var id = $('#cid').val();
            var title = 'Update Category';
            var method = 'PATCH';
            var url = "categories/update/" + id;
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
                            if (response.errors.category) {
                                $('#category-error').html(response.errors.category[0]);
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
                            $('#addCategoryModal').modal('hide');
                            var oTable = $('#datatable-crud-category').dataTable();
                            oTable.fnDraw(false);
                            $('#cid').val('');
                            $('#category').val('');
                        }
                    },
                });
            }
        });
    });
    //edit
    function editCategory(id) {
        $.ajax({
            type: "GET",
            url: "categories/edit/" + id,
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                console.log(res);
                $('#modalTitle').html("Edit Category");
                $('#addCategoryModal').modal('show');
                $('#cid').val(res.id);
                $('#category').val(res.name);
                $('#mode').val(1);
            }
        });
    }
</script>