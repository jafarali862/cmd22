@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Edit User</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">Edit User</li>
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
              <h3 class="card-title">User Details</h3>
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
              <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                  <label for="name">Full Name</label>
                  <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" class="form-control" id="password" name="password">
                  <small class="form-text text-muted">Leave blank to keep current password.</small>
                </div>

                <div class="form-group">
                  <label for="date_of_birth">Date of Birth</label>
                  <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}" required>
                </div>

                <div class="form-group">
                  <label for="gender">Gender</label>
                  <select class="form-control" id="gender" name="gender" required>
                    <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender', $user->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="phone_number">Phone Number</label>
                  <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required>
                </div>

                <div class="form-group">
                  <label for="address">Address</label>
                  <textarea class="form-control" id="address" name="address" rows="2" required>{{ old('address', $user->address) }}</textarea>
                </div>

                <div class="form-group">
                  <label for="status">User Type</label>
                  <select class="form-control" id="user_type" onChange="UserType()" name="user_type" required>
                    <option selected disabled>Select User Type</option>
            

                    <option value="0" {{ old('user_type', $user->user_type) == 0 ? 'selected' : '' }}>Customer</option>
                    <option value="1" {{ old('user_type', $user->user_type) == 1 ? 'selected' : '' }}>Field Agent</option>

                  </select>
                </div>

                 


                <div class="form-group" id="category_group" style="display: none;">
                <label for="type">Type</label>
                <select class="form-control" id="type" name="type" required>
                <option selected disabled>Select  Type</option>
                <option value="1" {{ old('type', $user->type) == '1' ? 'selected' : '' }}>Pharmacy</option>
                <option value="2" {{ old('type', $user->type) == '2' ? 'selected' : '' }}>Clinics</option>
                <option value="3" {{ old('type', $user->type) == '3' ? 'selected' : '' }}>Both</option>

                </select>
                </div>



                <div class="form-group">
                  <label for="emergency_contact_name">Emergency Contact Name</label>
                  <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}" required>
                </div>

                <div class="form-group">
                  <label for="emergency_contact_phone">Emergency Contact Phone</label>
                  <input type="text" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}" required>
                </div>
              <div id="hider" style="display:none;">
                <div class="form-group">
                  <label for="insurance_provider">Insurance Provider</label>
                  <input type="text" class="form-control" id="insurance_provider" name="insurance_provider" value="{{ old('insurance_provider', $user->insurance_provider) }}">
                </div>

                <div class="form-group">
                  <label for="insurance_policy_number">Insurance Policy Number</label>
                  <input type="text" class="form-control" id="insurance_policy_number" name="insurance_policy_number" value="{{ old('insurance_policy_number', $user->insurance_policy_number) }}">
                </div>

                <div class="form-group">
                  <label for="primary_physician">Primary Physician</label>
                  <input type="text" class="form-control" id="primary_physician" name="primary_physician" value="{{ old('primary_physician', $user->primary_physician) }}">
                </div>

                <div class="form-group">
                  <label for="medical_history">Medical History</label>
                  <textarea class="form-control" id="medical_history" name="medical_history" rows="2">{{ old('medical_history', $user->medical_history) }}</textarea>
                </div>

                <div class="form-group">
                  <label for="medications">Medications</label>
                  <textarea class="form-control" id="medications" name="medications" rows="2">{{ old('medications', $user->medications) }}</textarea>
                </div>

                <div class="form-group">
                  <label for="allergies">Allergies</label>
                  <textarea class="form-control" id="allergies" name="allergies" rows="2">{{ old('allergies', $user->allergies) }}</textarea>
                </div>

                <div class="form-group">
                  <label for="blood_type">Blood Type</label>
                  <input type="text" class="form-control" id="blood_type" name="blood_type" value="{{ old('blood_type', $user->blood_type) }}">
                </div>
              </div>
                <div class="form-group">
                  <label for="status">Status</label>
                  <select class="form-control" id="status" name="status" required>
                    <option value="1" {{ old('status', $user->status) =='1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $user->status) == '0' ? 'selected' : '' }}>Inactive</option>
                  </select>
                </div>

                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>

function UserType()
 {
      var user_type= $('#user_type').val();
      var categoryGroup = document.getElementById("category_group");
      var emergency_contact_name_new =document.getElementById("emergency_contact_name_new");
      if(user_type==1)
      {
      $('#hider').hide();
      categoryGroup.style.display = "block";
      emergency_contact_name_new.style.display = "none";

      }
      else if(user_type==0)
      {
      categoryGroup.style.display = "none";
      emergency_contact_name_new.style.display = "block";
      $('#hider').show();
      } 

      else
      {
      //      categoryGroup.style.display = "none";
      //      emergency_contact_name_new.style.display = "block";
      // $('#hider').show();

      // $('#hider').hide();
      // categoryGroup.style.display = "block";
      // emergency_contact_name_new.style.display = "none";

      } 

 }


</script>
@endsection