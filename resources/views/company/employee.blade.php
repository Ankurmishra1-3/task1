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
    <h1>Employees</h1>
    <a class="btn btn-success" href="javascript:void(0)" id="createNewEmployee"> Create New Employee</a>
    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
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
                <form id="employeeForm" name="employeeForm" class="form-horizontal" enctype="multipart/form-data">
                    
                   <div class="form-group">
                        <label for="image" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required>
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
        ajax: "{{ route('employees.index') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
    $('#createNewEmployee').click(function () {
        $('#saveBtn').val("create-employee");
        $('#employeeForm').trigger("reset");
        $('#modelHeading').html("Create New Employee");
        $('#ajaxModel').modal('show');
        $('#updateBtn').hide();
        $('#saveBtn').show();
    });
    

    $('body').on('click', '.editEmployee', function () {
      var routeEditEmployee = $(this).data('route');
      $.get(routeEditEmployee, function (data) {
          $('#modelHeading').html("Edit Employee");
          $('#saveBtn').val("edit-employee");
          $('#ajaxModel').modal('show');
          $('#id').val(data.id);
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
          data: $('#employeeForm').serialize(),
          url: "{{ route('employees.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
            console.log(data);
              $('#employeeForm').trigger("reset");
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
        var url = '{{ route("employees.update", ":id") }}';
        url = url.replace(':id', id);
        $.ajax({
          data: $('#employeeForm').serialize(),
          url: url,
          type: "PUT",
          success: function (data) {
            console.log(data);
              $('#employeeForm').trigger("reset");
              $('#ajaxModel').modal('hide');
              table.draw();
         
          },
          error: function (data) {
              console.log('Error:', data);
              $('#saveBtn').html('Save Changes');
          }
      });
    });

    
    $('body').on('click', '.deleteEmployee', function () {
     
        var routeDeleteEmployee = $(this).data("route");
        confirm("Are You sure want to delete !");
        
        $.ajax({
            url: routeDeleteEmployee,
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