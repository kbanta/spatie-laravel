<!-- Modal-->
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
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
                        <input type="hidden" id="bid" name="bid" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Brand</label>
                        <input autocomplete="off" type="text" id="brand" name="brand" value="{{ old('name') }}" class="brand form-control" placeholder="Brand">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="brand-error"></strong>
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
    function addBrand() {
        $('#mode').val(0);
        $('#bid').val('');
        $('#brand').val('');
        $('#modalTitle').html("Create Brand");
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
            var title = 'Create Brand';
            var method = 'POST';
            var url = "{{route('registerBrand')}}";
        } else {
            var id = $('#bid').val();
            var title = 'Update Brand';
            var method = 'PATCH';
            var url = "brands/update/" + id;
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
                            if (response.errors.brand) {
                                $('#brand-error').html(response.errors.brand[0]);
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
                            $('#addBrandModal').modal('hide');
                            var oTable = $('#datatable-crud-brand').dataTable();
                            oTable.fnDraw(false);
                            $('#bid').val('');
                            $('#brand').val('');
                            $('#brand_id').val('');
                        }
                    },
                });
            }
        });
    });
    //edit
    function editBrand(id) {
        $.ajax({
            type: "GET",
            url: "brands/edit/" + id,
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                console.log(res);
                $('#modalTitle').html("Edit Brand");
                $('#addBrandModal').modal('show');
                $('#bid').val(res.id);
                $('#brand').val(res.name);
                $('#mode').val(1);
            }
        });
    }
</script>