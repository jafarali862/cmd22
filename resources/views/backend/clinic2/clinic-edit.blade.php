@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Edit Clinic</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('clinic2') }}">Clinics</a></li>
            <li class="breadcrumb-item active">Edit Clinic</li>
          </ol>
        </div>
      </div>
    </div>
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
              <form action="{{ route('clinics2.update', $clinic->id) }}" method="POST" enctype="multipart/form-data" >
                @csrf
                @method('PUT')
                
                <div class="form-group">
                  <label for="clinic_name">Clinic Name</label>
                  <input type="text" class="form-control" id="clinic_name" name="clinic_name" value="{{ $clinic->clinic_name }}" required>
                </div>

                <div class="form-group">
                  <label for="clinic_name">Tests</label>
                  <textarea type="text" class="form-control" id="tests" name="tests" placeholder="Enter Tests" required>{{ json_decode($clinic->tests)}}</textarea>
                </div>

                <div class="form-group">
                  <label for="clinic_address">Clinic Address</label>
                  <textarea class="form-control" id="clinic_address" name="clinic_address" rows="3" required>{{ $clinic->clinic_address }}</textarea>
                </div>

                <div class="form-group">
                  <label for="city">City</label>
                  <input type="text" class="form-control" id="city" name="city" value="{{ $clinic->city }}" required>
                </div>

                <div class="form-group">
                  <label for="phone_number">Phone Number</label>
                  <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $clinic->phone_number }}" required>
                </div>

                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" value="{{ $clinic->email }}" required>
                </div>

                <!-- Display Current Clinic Photo -->
                @if ($clinic->clinic_photo)
                  <div class="form-group">
                    <label for="current_clinic_photo">Current Clinic Photo</label><br>
                    <img src="{{ asset('storage/' . $clinic->clinic_photo) }}" alt="Current Clinic Photo" width="100" height="100">
                  </div>
                @endif

                <!-- Option to Upload New Clinic Photo -->
                <div class="form-group">
                  <label for="clinic_photo">New Clinic Photo</label>
                  <input type="file" class="form-control" id="clinic_photo" name="clinic_photo">
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('clinic2') }}" class="btn btn-secondary">Cancel</a>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
