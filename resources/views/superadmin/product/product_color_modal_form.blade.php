<!-- Modal-->
<div class="modal fade" id="addColorModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
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
                        <input type="hidden" id="cid" name="fid" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Color</label>
                        <input autocomplete="off" type="text" id="color" name="color" value="{{ old('name') }}" class="color form-control" placeholder="Color">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="color-error"></strong>
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
    function addColor() {
        $('#mode').val(0);
        $('#cid').val('');
        $('#color').val('');
        $('#modalTitle').html("Create Color");
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
            var title = 'Create Color';
            var method = 'POST';
            var url = "{{route('registerColor')}}";
        } else {
            var id = $('#cid').val();
            var title = 'Update Color';
            var method = 'PATCH';
            var url = "colors/update/" + id;
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
                            if (response.errors.color) {
                                $('#color-error').html(response.errors.color[0]);
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
                            $('#addColorModal').modal('hide');
                            var oTable = $('#datatable-crud-color').dataTable();
                            oTable.fnDraw(false);
                            $('#cid').val('');
                            $('#color').val('');
                        }
                    },
                });
            }
        });
    });
    //edit
    function editColor(id) {
        $.ajax({
            type: "GET",
            url: "colors/edit/" + id,
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                console.log(res);
                $('#modalTitle').html("Edit Color");
                $('#addColorModal').modal('show');
                $('#cid').val(res.id);
                $('#color').val(res.color);
                $('#mode').val(1);
            }
        });
    }
</script>