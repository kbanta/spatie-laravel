<!-- Modal-->
<div class="modal fade" id="addSizeModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
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
                        <input type="hidden" id="sid" name="sid" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Product Size</label>
                        <input autocomplete="off" type="text" id="size" name="size" value="{{ old('name') }}" class="size form-control" placeholder="Product Size">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="size-error"></strong>
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
    function addSize() {
        $('#mode').val(0);
        $('#sid').val('');
        $('#size').val('');
        $('#modalTitle').html("Create Product Size");
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
            var title = 'Create Product Size';
            var method = 'POST';
            var url = "{{route('registerSize')}}";
        } else {
            var id = $('#sid').val();
            var title = 'Update Product Size';
            var method = 'PATCH';
            var url = "sizes/update/" + id;
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
                            $('#addSizeModal').modal('hide');
                            var oTable = $('#datatable-crud-size').dataTable();
                            oTable.fnDraw(false);
                            $('#sid').val('');
                            $('#size').val('');
                        }
                    },
                });
            }
        });
    });
    //edit
    function editSize(id) {
        $.ajax({
            type: "GET",
            url: "sizes/edit/" + id,
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                console.log(res);
                $('#modalTitle').html("Edit Product Size");
                $('#addSizeModal').modal('show');
                $('#sid').val(res.id);
                $('#size').val(res.size);
                $('#mode').val(1);
            }
        });
    }
</script>