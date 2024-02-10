<!-- Modal-->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
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
                        <input type="hidden" id="rid" name="rid" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <input autocomplete="off" type="text" id="role" name="role" value="{{ old('name') }}" class="role form-control" placeholder="Role">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="role-error"></strong>
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
    function addRole() {
        $('#mode').val(0);
        $('#rid').val('');
        $('#name').val('');
        $('#modalTitle').html("Create Role");
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
            var title = 'Create Role';
            var method = 'POST';
            var url = "{{route('registerRole')}}";
        } else {
            var id = $('#rid').val();
            var title = 'Update Role';
            var method = 'PATCH';
            var url = "roles/update/" + id;
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
                            if (response.errors.role) {
                                $('#role-error').html(response.errors.role[0]);
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
                            $('#addRoleModal').modal('hide');
                            var oTable = $('#datatable-crud-role').dataTable();
                            oTable.fnDraw(false);
                            $('#rid').val('');
                            $('#role').val('');
                            $('#role_id').val('');
                        }
                    },
                });
            }
        });
    });
    //edit
    function editRole(id) {
        $.ajax({
            type: "GET",
            url: "roles/edit/" + id,
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                console.log(res);
                $('#modalTitle').html("Edit Role");
                $('#addRoleModal').modal('show');
                $('#rid').val(res.id);
                $('#role').val(res.name);
                $('#mode').val(1);
            }
        });
    }
</script>