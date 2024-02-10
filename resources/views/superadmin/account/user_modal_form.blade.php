<!-- Modal-->
<!-- <div id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left"> -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div role="document" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" id="mode" name="mode" class="form-control" />
                        <input type="hidden" id="uid" name="uid" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input autocomplete="off" type="text" id="name" name="name" value="{{ old('name') }}" class="name form-control" placeholder="Name">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="name-error"></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input autocomplete="off" type="email" id="email" name="email" value="{{ old('email') }}" class="email form-control" placeholder="Email">
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="email-error"></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label for="role_id">Role</label>
                        <select name="role_id" id="role_id" class="role_id form-control">
                            <option value="" disabled selected>Select Role</option>
                            @foreach($roles as $role)
                            <option value="{{$role->name}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                        <span class="text-danger">
                            <strong id="role_id-error"></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label for="" class="">{{ __('Password') }}</label>
                        <input autocomplete="off" type="password" name="password" class="password form-control" placeholder="Password">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="password-error"></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label for="" class="">{{ __('Confirm-Password') }}</label>
                        <input autocomplete="off" type="password" name="password_confirmation" class="password_confirm form-control" placeholder="Retype password">
                        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                        <span class="text-danger">
                            <strong id="password_confirm-error"></strong>
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
    function addAccount() {
        $('#mode').val(0);
        $('#uid').val('');
        $('#name').val('');
        $('#email').val('');
        $('.password').val('');
        $('.password_confirm').val('');
        $('#role_id').val('');
        $('#exampleModalLabel').html("Create Account");
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
            var title = 'Create Account';
            var method = 'POST';
            var url = "{{route('registerUser')}}";
        } else {
            var id = $('#uid').val();
            var title = 'Update Account';
            var method = 'PATCH';
            var url = "users/update/" + id;
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
                            if (response.errors.name) {
                                $('#name-error').html(response.errors.name[0]);
                            }
                            if (response.errors.email) {
                                $('#email-error').html(response.errors.email[0]);
                            }
                            if (response.errors.password) {
                                $('#password-error').html(response.errors.password[0]);
                            }
                            if (response.errors.password_confirm) {
                                $('#password_confirm-error').html(response.errors.password_confirm[0]);
                            }
                            if (response.errors.role_id) {
                                $('#role_id-error').html(response.errors.role_id[0]);
                            }
                            if (response.errors.position) {
                                $('#position-error').html(response.errors.position[0]);
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
                            $('#addUserModal').modal('hide');
                            var oTable = $('#datatable-crud').dataTable();
                            oTable.fnDraw(false);
                            $('#uid').val('');
                            $('#name').val('');
                            $('#email').val('');
                            $('.password').val('');
                            $('.password_confirm').val('');
                            $('#role_id').val('');
                        }
                    },
                });
            }
        });
    });
    //edit
    function editUser(id) {
        $.ajax({
            type: "GET",
            url: "users/edit/" + id,
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                console.log(res);
                $('#exampleModalLabel').html("Edit Account");
                $('#addUserModal').modal('show');
                $('#uid').val(res.uid);
                $('#name').val(res.uname);
                // $('#address').val(res.address);
                $('#role_id').val(res.rolename);
                $('#email').val(res.email);
                $('#mode').val(1);
            }
        });
    }
</script>