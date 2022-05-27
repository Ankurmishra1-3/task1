<!DOCTYPE html>
<html>
<head>
    <title>Task </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body>
    
<div class="container">
    <h1>Products</h1>
    <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Create New Product</a>
    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Title</th>
                <th>Description</th>
                <th>Price</th>
                <th width="300px">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
   
<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
            </div>
            <div class="modal-body">
                <form id="productForm" name="productForm" class="form-horizontal" enctype="multipart/form-data">
                    
                   <div class="form-group">
                        <label for="image" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label">Title</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter Title" value="" maxlength="50" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-12">
                            <textarea id="description" name="description" required placeholder="Enter Description" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="price" class="col-sm-2 control-label">Price</label>
                        <div class="col-sm-12">
                            <input type="number" class="form-control" id="price" name="price" placeholder="Enter Price" value="" min="0"  required>
                        </div>
                    </div>

                    <div class="col-sm-offset-2 col-sm-10">
                     <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                     </button>
                     <button type="submit" class="btn btn-primary" id="updateBtn" data-id="" value="create">Update changes
                     </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>  
<script type="text/javascript">
  $(function () {
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('products.index') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'title', name: 'title'},
            {data: 'description', name: 'description'},
            {data: 'price', name: 'price'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
    $('#createNewProduct').click(function () {
        $('#saveBtn').val("create-product");
        $('#productForm').trigger("reset");
        $('#modelHeading').html("Create New Product");
        $('#ajaxModel').modal('show');
        $('#updateBtn').hide();
        $('#saveBtn').show();
    });
    

    $('body').on('click', '.editProduct', function () {
      var routeEditProduct = $(this).data('route');
      $.get(routeEditProduct, function (data) {
          $('#modelHeading').html("Edit Book");
          $('#saveBtn').val("edit-book");
          $('#ajaxModel').modal('show');
          $('#id').val(data.id);
          $('#title').val(data.title);
          $('#description').val(data.description);
          $('#price').val(data.price);
          $('#name').val(data.name);
          $('#saveBtn').hide();
          $('#updateBtn').show();
          $('#updateBtn').attr("data-id",btoa(data.id) );
      })
   });


    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html('Save');
        $.ajax({
          data: $('#productForm').serialize(),
          url: "{{ route('products.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
            console.log(data);
              $('#productForm').trigger("reset");
              $('#ajaxModel').modal('hide');
              table.draw();
         
          },
          error: function (data) {
              console.log('Error:', data);
              $('#saveBtn').html('Save Changes');
          }
      });
    });

    $('#updateBtn').click(function (e) {
        e.preventDefault();
        $(this).html('Save');
        var id = $('#updateBtn').attr('data-id');
        var url = '{{ route("products.update", ":id") }}';
        url = url.replace(':id', id);
        $.ajax({
          data: $('#productForm').serialize(),
          url: url,
          type: "PUT",
          success: function (data) {
            console.log(data);
              $('#productForm').trigger("reset");
              $('#ajaxModel').modal('hide');
              table.draw();
         
          },
          error: function (data) {
              console.log('Error:', data);
              $('#saveBtn').html('Save Changes');
          }
      });
    });

    
    $('body').on('click', '.deleteProduct', function () {
     
        var routeDeleteProduct = $(this).data("route");
        confirm("Are You sure want to delete !");
        
        $.ajax({
            url: routeDeleteProduct,
            type: "DELETE",
            success: function (data) {
                table.draw();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
     
  });
</script>
</body>
</html>