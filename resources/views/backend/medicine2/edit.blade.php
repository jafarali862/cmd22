@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Edit Medicine</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('medicines2.index') }}">Medicines</a></li>
            <li class="breadcrumb-item active">Edit Medicine</li>
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
              <h3 class="card-title">Edit Medicine Details</h3>
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
              <!-- Form to edit existing medicine -->
              <form action="{{ route('medicines2.update', $medicine->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                  <label for="medicine_name">Medicine Name</label>
                  <input type="text" class="form-control" id="medicine_name" name="medicine_name" value="{{ old('medicine_name', $medicine->medicine_name) }}" required>
                </div>

                <div class="form-group">
                <label for="pharmacy_id">Pharmacy</label>
                <select name="pharmacy_id" id="pharmacy_id" class="form-control" required>
                <option value="" disabled {{ old('pharmacy_id', $medicine->pharmacy_id) ? '' : 'selected' }}>Select Pharmacy</option>
                @foreach($pharmacies as $pharmacy)
                <option value="{{ $pharmacy->id }}"
                {{ old('pharmacy_id', $medicine->pharmacy_id) == $pharmacy->id ? 'selected' : '' }}>
                {{ $pharmacy->pharmacy_name }}
                </option>
                @endforeach
                </select>
                </div>

                

                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $medicine->description) }}</textarea>
                </div>

                <div class="form-group">
                  <label for="amount">Price</label>
                  <input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount', $medicine->amount) }}" required>
                </div>

                <div class="form-group">
                  <label for="quantity">Quantity</label>
                  <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity', $medicine->quantity) }}" required>
                </div>

                <div class="form-group">
                  <label for="expiry_date">Expiry Date</label>
                  <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $medicine->expiry_date) }}" required>
                </div>

                <div class="form-group">
                  <label for="manufacturer">Manufacturer</label>
                  <input type="text" class="form-control" id="manufacturer" name="manufacturer" value="{{ old('manufacturer', $medicine->manufacturer) }}" required>
                </div>

                <button type="submit" class="btn btn-primary">Update Medicine</button>
                <a href="{{ route('medicines2.index') }}" class="btn btn-secondary">Cancel</a>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
