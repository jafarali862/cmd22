<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Clinic Prescription</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('clinic-prescriptions.index') }}">Clinic Prescriptions</a></li>
                    <li class="breadcrumb-item active">Edit Prescription</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 offset-md-1">
                <div class="card">
                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                    <div class="card-header">
                        <h3 class="card-title">Update Prescription</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('clinic-prescriptions.update', $clinicPrescription->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                            <label for="user_id">User</label>
                            <input type="text" class="form-control" id="prescription" name="prescription" value="{{$clinicPrescription->user->name }}" readonly>
                            </div>

                            <div class="form-group">
                            <label for="user_id">Patient</label>
                            <input type="text" class="form-control" id="prescription" name="prescription" value="{{$clinicPrescription->name }}" readonly>
                            </div>


                            <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ auth()->user()->phone_number }}" readonly>
                            </div>


                            <div class="form-group">
                            <label for="user_id">Age</label>
                            <input type="text" class="form-control" id="prescription" name="prescription" value="{{$clinicPrescription->age }}" readonly>
                            </div>

                            <div class="form-group">
                            <label for="user_id">Gender</label>
                            <input type="text" class="form-control" id="prescription" name="prescription" value="{{$clinicPrescription->gender }}" readonly>
                            </div>

                            @if($clinicPrescription->test)
                            @foreach(json_decode($clinicPrescription->test, true) as $test)
                            <div class="form-group">
                                <label for="user_id">Test</label>
                                <input type="text" class="form-control" id="prescription" name="prescription" value="{{$test}}" readonly>
                            </div>
                            @endforeach
                            @endif

                            <div class="form-group">
                                <label for="pharmacy_id">Clinic</label>
                                <input type="text" class="form-control" id="prescription" name="prescription" value="{{ $clinicPrescription->clinic->clinic_name }}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="prescription">Prescription (Image)</label>

                                @if($clinicPrescription->prescription)
                                <div class="mt-2">
                                    @foreach(json_decode($clinicPrescription->prescription, true) as $image)
                                    <div class="d-inline-block text-center mr-2">
                                        <!-- <i class="fas fa-images" style="color: green; font-size: 20px;"></i> -->
                                        <img src="{{ asset('storage/'.$image) }}" alt="Prescription Image" width="100" height="70" class="mb-1">
                                        <br>
                                        <!-- <button type="button" class="btn btn-info btn-sm" onclick="showImage('{{ asset('storage/'.$image) }}')">View</button> -->

                                <button type="button" class="btn btn-info btn-sm"  onclick="showImage(`{{ asset('storage/' . $image) }}`)">View</button>
  
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="delivery_address">Delivery Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2" required>{{ old('address', $clinicPrescription->address) }}</textarea>
                            </div>
                            @php
                            $lat_long=$clinicPrescription->lat_long;
                            $cordinates=explode(',',$lat_long);

                            @endphp
                            <div class="form-group">
                                <label for="lat_long">Latitude & Longitude</label>
                                <input type="text" class="form-control" id="lat_long" name="lat_long" value="{{ old('lat_long', $clinicPrescription->lat_long) }}" readonly>

                                <a href="https://www.google.com/maps?q={{ $cordinates[0] }},{{ $cordinates[1] }}" target="_blank">
                                    View Location on Google Maps
                                </a>
                            </div>

                              <!-- <div class="form-group">
                                <label for="start_time">Start time</label>
                                <input type="time" class="form-control" id="start_time"  name="start_time" value="{{ old('start_time', $clinicPrescription->start_time) }}">                              
                            </div>
 -->                    
                            @if($clinicPrescription->status==3)
                            <div class="form-group">
                                <label for="pres_upload">Prescription Upload</label>
                                 <input type="file" name="pres_upload" id="pres_upload" class="form-control" required>
                            </div>
                            @endif


                        @if($clinicPrescription->status == 4 && $clinicPrescription->pres_upload)
                        <div class="form-group">
                        <label for="prescription">Prescription Uploaded</label>
                        <div class="mt-2">
                        @php
                        $filePath = storage_path('app/public/' . $clinicPrescription->pres_upload);
                        $fileUrl = asset('storage/' . $clinicPrescription->pres_upload);
                        $extension = strtolower(pathinfo($clinicPrescription->pres_upload, PATHINFO_EXTENSION));
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        @endphp

                        <div class="d-inline-block text-center mr-2">
                        @if(in_array($extension, $imageExtensions))
                        <img src="{{ $fileUrl }}" alt="Prescription Image" width="100" height="70" class="mb-1">
                        <br>
                        <button type="button" class="btn btn-info btn-sm" onclick="showImage(`{{ $fileUrl }}`)">View</button>
                        @else
                        <i class="fas fa-file-alt fa-2x text-primary mb-1"></i>
                        <br>
                        <a href="{{ $fileUrl }}" class="btn btn-success btn-sm" download>Download File</a>
                        @endif
                        </div>
                        </div>
                        </div>
                        @endif

                        

                        <div class="row">
                        <div class="col-md-6">
                        @if ($clinicPrescription->scheduled_at)
                        <label for="exampleInput">Scheduled Date: {{ \Carbon\Carbon::parse($clinicPrescription->scheduled_at)->format('d-m-Y') }}</label>
                        @endif


                        @if ($clinicPrescription->delivery_id)
                        <label for="exampleInput">Delivery Agent: {{ $clinicPrescription->deliveryAgent->name ?? 'N/A' }}</label>
                        @endif

                        </div>

                        <div class="col-md-6">
                        @if ($clinicPrescription->scheduled_at && empty($clinicPrescription->from_time) && empty($clinicPrescription->to_time))
                        <label for="exampleInput">Time:Any time is available</label>
                        @else
                        @if (!empty($clinicPrescription->from_time))
                        <label for="exampleInput">From Time: {{ $clinicPrescription->from_time }}</label>
                        @endif

                        @if (!empty($clinicPrescription->to_time))
                        <label for="exampleInput">To Time: {{ $clinicPrescription->to_time }}</label>
                        @endif
                        @endif
                        </div>
                        </div>

                    
            
                            <div class="form-group">
                                <label for="status">Status</label>
                                <!-- <select class="form-control" id="status" name="status" required onchange="toggleAgentSelect()" required> -->

                                <select class="form-control" id="status" name="status"  required>
                                    <option default disabled>Please Select</option>
                                    @if($clinicPrescription->status==5)
                                    <option value="5" {{ old('status', $clinicPrescription->status) == '4' ? 'selected' : '' }}>Rejected</option>
                                    @elseif($clinicPrescription->status==1)
                                    <option value="2" {{ old('status', $clinicPrescription->status) =='1' ? 'selected' : '' }}>Sample Collected</option>
                                      @elseif($clinicPrescription->status==2)
                                    <option value="3" {{ old('status', $clinicPrescription->status) =='2' ? '' : '' }}>Completed</option>

                                     @elseif($clinicPrescription->status==3)
                                    <option value="4" {{ old('status', $clinicPrescription->status) =='3' ? 'selected' : '' }}>Testing</option>

                                     @elseif($clinicPrescription->status==4)
                                    <option value="4" {{ old('status', $clinicPrescription->status) =='4' ? 'selected' : '' }}>Completed</option>

                                    @elseif($clinicPrescription->status==0)
                                    <option value="1" {{ old('status', $clinicPrescription->status) == '1' ? 'selected' : '' }}>Accepted</option>
                                    <option value="5" {{ old('status', $clinicPrescription->status) == '5' ? 'selected' : '' }}>Rejected</option>
                                   
                                    @endif
                                </select>
                            </div>

                                <!-- Delivery Agent select, initially hidden -->

                                @if($clinicPrescription->status==0)
                            <div class="form-group" id="agentSelectDiv">
                            <select class="form-control" id="delivery_id" name="delivery_id" required>
                            <option selected disabled>Assign Delivery Agent*</option>
                            @foreach($deliveryAgents as $agent)
                            <option value="{{$agent->id}}">{{$agent->name}}</option>
                            @endforeach
                            </select>
                            </div>

                            @endif


                            <button type="submit" class="btn btn-primary">Update Address</button>
                            <a href="{{ route('clinic-prescriptions.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <img id="large-prescription" src="" class="img-fluid d-none" alt="Prescription Image">
            </div>

        </div>
    </div>
</section>

<!-- <script>
    function toggleAgentSelect() {
        const status = document.getElementById('status').value;
        const agentDiv = document.getElementById('agentSelectDiv');

        if(status === '1') {  // Accepted
            agentDiv.style.display = 'block';
            document.getElementById('delivery_id').setAttribute('required', 'required');
        } else {
            agentDiv.style.display = 'none';
            document.getElementById('delivery_id').removeAttribute('required');
        }
    }

    window.onload = function() {
        toggleAgentSelect();
    };
</script> -->


<script>
    function showImage(imageUrl) {
        let imgElement = document.getElementById('large-prescription');
        imgElement.src = imageUrl;
        imgElement.classList.remove('d-none');
    }
</script>