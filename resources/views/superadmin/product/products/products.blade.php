@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Product List') }}
                    <div class="mb-2" style="float: right;">
                        <a class="btn btn-success" href="{{ route('productForm') }}"> Create Product</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-responsive" id="datatable-crud-product">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>SKU</th>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Created at</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                        <div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#datatable-crud-product').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('products') }}",
            columns: [{
                    data: 'pid',
                    name: 'pid'
                },
                {
                    data: 'sku',
                    name: 'sku'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'url',
                    "render": function(data, type, row) {
                        return '<div style="text-align: center;"><img src="' + data + '" alt="Image" style="width:100px;"></div>';
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data) {
                        // Assuming data is in ISO format like "YYYY-MM-DD HH:MM:SS"
                        return moment(data).format('MMMM Do YYYY, h:mm:ss a'); // Format as desired
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false
                },
            ],
            order: [
                [0, 'desc']
            ]
        });

        $('body').on('click', '.delete-product', function(e) {
            e.preventDefault(); // Prevent default link action

            var deleteUrl = $(this).attr('href'); // Get the delete URL from the button's href attribute

            // Display SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this record!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with delete action
                    var id = $(this).data('id');
                    // ajax
                    $.ajax({
                        type: "PATCH",
                        url: "products/deleteproduct/" + id,
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        success: function(res) {
                            var oTable = $('#datatable-crud-product').dataTable();
                            oTable.fnDraw(false);
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Product has been deleted.',
                                showConfirmButton: false,
                                timer: 1000
                            });
                        }
                    });
                }
            });
        });

    });
</script>
@endsection