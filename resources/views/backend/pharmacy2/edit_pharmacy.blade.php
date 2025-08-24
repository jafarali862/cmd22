@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Edit Pharmacy</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pharmacies2.index') }}">Pharmacies</a></li>
            <li class="breadcrumb-item active">Edit Pharmacy</li>
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
              <h3 class="card-title">Pharmacy Details</h3>
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
              <!-- Edit Pharmacy Form -->
              <form action="{{ route('pharmacies2.update', $pharmacy->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                  <label for="pharmacy_name">Pharmacy Name</label>
                  <input type="text" class="form-control" id="pharmacy_name" name="pharmacy_name" value="{{ $pharmacy->pharmacy_name }}" required>
                </div>

                <div class="form-group">
                  <label for="pharmacy_address">Pharmacy Address</label>
                  <input type="text" class="form-control" id="pharmacy_address" name="pharmacy_address" value="{{ $pharmacy->pharmacy_address }}" required>
                </div>

                <div class="form-group">
                  <label for="city">City</label>
                  <input type="text" class="form-control" id="city" name="city" value="{{ $pharmacy->city }}" required>
                </div>

                <div class="form-group">
                <label for="types">Select Types</label>
                <select class="form-control" id="types" name="types" required>
                <option disabled {{ $pharmacy->types == null ? 'selected' : '' }}>Select Types</option>
                <option value="1" {{ $pharmacy->types == 1 ? 'selected' : '' }}>Neethi Medicals</option>
                <option value="2" {{ $pharmacy->types == 2 ? 'selected' : '' }}>Central</option>
                </select>
                </div>


                <div class="form-group">
                  <label for="phone_number">Phone Number</label>
                  <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $pharmacy->phone_number }}" required>
                </div>

                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" value="{{ $pharmacy->email }}" required>
                </div>

                <!-- Display Current Clinic Photo -->
                @if ($pharmacy->pharmacy_photo)
                  <div class="form-group">
                    <label for="current_clinic_photo">Current Pharmacy Photo</label><br>
                    <img src="{{ asset('storage/' . $pharmacy->pharmacy_photo) }}" alt="Current Pharmacy Photo" width="100" height="100">
                  </div>
                @endif

                <!-- Option to Upload New Clinic Photo -->
                <div class="form-group">
                  <label for="clinic_photo">New pharmacy Photo</label>
                  <input type="file" class="form-control" id="pharmacy_photo" name="pharmacy_photo">
                </div>

                <button type="submit" class="btn btn-primary">Update Pharmacy</button>
                <a href="{{ route('pharmacies2.index') }}" class="btn btn-secondary">Cancel</a>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
