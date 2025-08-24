@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Clinics</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Clinics</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card">
            <div class="card-header d-flex justify-content-end">

              <!-- <a class="btn btn-success" href="{{route('add-new-clinics2')}}">
                <i class="fas fa-plus"></i> Add Clinic
              </a> -->


            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Clinic Name</th>
                    <th>Tests</th>
                    <th>Clinic Address</th>
                    <th>City</th>
                    <th>Phone No</th>
                    <th>Email</th>
                    <th>Clinic Photo</th>

                    <!-- <th>Action</th> -->

                  </tr>
                </thead>
                <tbody>
                  @foreach($clinics as $clinic)
                  <tr>
                    <td>{{ $clinic->clinic_name }}</td>
                    <td>{{ json_decode($clinic->tests) }}</td>
                    <td>{{ $clinic->clinic_address }}</td>
                    <td>{{ $clinic->city }}</td>
                    <td>{{ $clinic->phone_number }}</td>
                    <td>{{ $clinic->email }}</td>
                    <td>
                      @if ($clinic->clinic_photo)
                        <img src="{{ asset('storage/' . $clinic->clinic_photo) }}" alt="Clinic Photo" width="50" height="50" class="img-thumbnail">
                      @else
                        <span>No Photo</span>
                      @endif
                    </td>

                    <!-- <td>
                      <a href="{{ route('clinics2.edit', $clinic->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                      </a>
                      <form action="{{ route('clinics2.destroy', $clinic->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this clinic?');">
                          <i class="fas fa-trash"></i> Delete
                        </button>
                      </form>
                    </td> -->


                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Page specific script -->
<script>
  $(function() {
    $("#example1").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
@endsection