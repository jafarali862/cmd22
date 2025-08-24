@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Pharmacies</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Pharmacies</li>
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
              
            <!-- <a class="btn btn-success" href="{{ route('pharmacies2.create') }}">
                <i class="fas fa-plus"></i> Add Pharmacy
              </a> -->

            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Pharmacy Name</th>
                    <th>Pharmacy Address</th>
                    <th>City</th>
                    <th>Phone No</th>
                       <th>Type</th>
                    <th>Email</th>
                    <th>Pharmacy Photo</th>
                    <!-- <th>Action</th> -->
                  </tr>
                </thead>
                <tbody>
                  @foreach($pharmacies as $pharmacy)
                  <tr>
                    <td>{{ $pharmacy->pharmacy_name }}</td>
                    <td>{{ $pharmacy->pharmacy_address }}</td>
                    <td>{{ $pharmacy->city }}</td>
                    <td>{{ $pharmacy->phone_number }}</td>
                          <td> @switch($pharmacy->types)
        @case(1)
            Neethi Medicals
            @break

        @case(2)
            Central
            @break

        @default
            
    @endswitch</td>
                    <td>{{ $pharmacy->email }}</td>

                    <td>
                      @if ($pharmacy->pharmacy_photo)
                        <img src="{{ asset('storage/' . $pharmacy->pharmacy_photo) }}" alt="pharmacy Photo" width="50" height="50" class="img-thumbnail">
                      @else
                        <span>No Photo</span>
                      @endif
                    </td>

                    <!-- <td>
                      <a href="{{ route('pharmacies2.edit', $pharmacy->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                      </a>
                      <form action="{{ route('pharmacies2.destroy', $pharmacy->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this pharmacy?');">
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
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
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
