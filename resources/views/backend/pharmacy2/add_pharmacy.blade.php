@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Add New Pharmacy</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pharmacies2.index') }}">Pharmacies</a></li>
            <li class="breadcrumb-item active">Add Pharmacy</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

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
              <form action="{{ route('pharmacies2.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                  <label for="pharmacy_name">Pharmacy Name</label>
                  <input type="text" class="form-control" id="pharmacy_name" name="pharmacy_name" required>
                </div>

                <div class="form-group">
                  <label for="pharmacy_address">Pharmacy Address</label>
                  <input type="text" class="form-control" id="pharmacy_address" name="pharmacy_address" required>
                </div>

                <div class="form-group">
                  <label for="city">City</label>
                  <input type="text" class="form-control" id="city" name="city" required>
                </div>

                  <div class="form-group">
                  <label for="types">Select Types</label>
                  <select class="form-control" id="types" name="types" required>
                    <option selected disabled>Select Types</option>
                    <option value="1">Neethi Medicals</option>
                    <option value="2">Central</option>
                  </select>
                </div>


                <div class="form-group">
                  <label for="phone_number">Phone Number</label>
                  <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                </div>

                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                  <label for="clinic_photo">Clinic Photo</label>
                  <input type="file" class="form-control" id="pharmacy_photo" name="pharmacy_photo" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary">Add Pharmacy</button>
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
