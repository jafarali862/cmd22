@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Add New User</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">Add User</li>
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
              <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="form-group">
                  <label for="name">Full Name</label>
                  <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="form-group">
                  <label for="date_of_birth">Date of Birth</label>
                  <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                </div>

                <div class="form-group">
                  <label for="gender">Gender</label>
                  <select class="form-control" id="gender" name="gender" required>
                    <option selected disabled>Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="phone_number">Phone Number</label>
                  <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                </div>

                <div class="form-group">
                  <label for="address">Address</label>
                  <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                </div>

                <div class="form-group">
                  <label for="user_type">User Type</label>
                  <select class="form-control" id="user_type" name="user_type" onChange="UserType()">
                    <option selected disabled>Select User Type</option>
                    <option value="0">Customer</option>
                    <option value="1">Field Agent</option>
                     <!-- <option value="2">User</option> -->
                  </select>
                </div>

                 
                <div class="form-group" id="category_group" style="display: none;">
                  <label for="type">Category</label>

                  <select class="form-control" id="type" name="type" required>
                  <option selected disabled>Select Category</option>
                  <option value="1">Pharmacy</option>
                  <option value="2">Clinics</option>
                  <option value="3">Both</option>
                  <!-- <option value="4">None</option> -->
                  </select>
                  
                </div>

              

                <div class="form-group" id="emergency_contact_name_new">
                  <label for="emergency_contact_name">Emergency Contact Name</label>
                  <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name">
                </div>

                <div class="form-group">
                  <label for="emergency_contact_phone">Emergency Contact Phone</label>
                  <input type="text" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" required>
                </div>
              <div id="hider" style="display:none;">
                <div class="form-group">
                  <label for="insurance_provider">Insurance Provider</label>
                  <input type="text" class="form-control" id="insurance_provider" name="insurance_provider">
                </div>

                <div class="form-group">
                  <label for="insurance_policy_number">Insurance Policy Number</label>
                  <input type="text" class="form-control" id="insurance_policy_number" name="insurance_policy_number">
                </div>

                <div class="form-group">
                  <label for="primary_physician">Primary Physician</label>
                  <input type="text" class="form-control" id="primary_physician" name="primary_physician">
                </div>

                <div class="form-group">
                  <label for="medical_history">Medical History</label>
                  <textarea class="form-control" id="medical_history" name="medical_history" rows="2"></textarea>
                </div>

                <div class="form-group">
                  <label for="medications">Medications</label>
                  <textarea class="form-control" id="medications" name="medications" rows="2"></textarea>
                </div>

                <div class="form-group">
                  <label for="allergies">Allergies</label>
                  <textarea class="form-control" id="allergies" name="allergies" rows="2"></textarea>
                </div>

                <div class="form-group">
                  <label for="blood_type">Blood Type</label>
                  <input type="text" class="form-control" id="blood_type" name="blood_type">
                </div>
              </div>



                <div class="form-group">
                  <label for="status">Status</label>
                  <select class="form-control" id="status" name="status" required>
                    <option selected disabled>Select Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                  </select>
                </div>

                <button type="submit" class="btn btn-primary">Add User</button>
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

      $('#hider').hide();
      categoryGroup.style.display = "block";
      emergency_contact_name_new.style.display = "none";

      } 

 }

</script>
@endsection
