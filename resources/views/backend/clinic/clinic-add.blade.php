@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Add Clinic</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('clinic') }}">Clinics</a></li>
            <li class="breadcrumb-item active">Add Clinic</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Clinic Details</h3>
            </div>
            @if ($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
            @endif
            <div class="card-body">
              <form action="{{ route('clinics.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                  <label for="clinic_name">Clinic Name</label>
                  <input type="text" class="form-control" id="clinic_name" name="clinic_name" placeholder="Enter Clinic Name" required>
                </div>

                <div class="form-group">
                  <label for="clinic_name">Tests</label>
                  <textarea type="text" class="form-control" id="tests" name="tests" placeholder="Enter Tests"></textarea>
                </div>

                <div class="form-group">
                  <label for="clinic_address">Clinic Address</label>
                  <textarea class="form-control" id="clinic_address" name="clinic_address" rows="3" placeholder="Enter Address" required></textarea>
                </div>

                <div class="form-group">
                  <label for="city">City</label>
                  <input type="text" class="form-control" id="city" name="city" placeholder="Enter City" required>
                </div>

                <div class="form-group">
                  <label for="phone_number">Phone Number</label>
                  <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter Phone Number" required>
                </div>

                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email Address" required>
                </div>

                <div class="form-group">
                  <label for="clinic_photo">Clinic Photo</label>
                  <input type="file" class="form-control" id="clinic_photo" name="clinic_photo" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('clinic') }}" class="btn btn-secondary">Cancel</a>

              </form>
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
@endsection