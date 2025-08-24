
<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pharmacy Prescription</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pharmacy-prescriptions2.index') }}">Pharmacy Prescriptions</a></li>
                        <li class="breadcrumb-item active"> Prescription</li>
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
                            <h3 class="card-title"> Prescription</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('pharmacy-prescriptions2.update', $pharmacyPrescription->id) }}" method="POST" enctype="multipart/form-data" onsubmit="return true;">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="user_id">User</label>
                                    <input type="text" class="form-control" id="prescription" name="prescription" value="{{$pharmacyPrescription->user->name }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="pharmacy_id">Pharmacy</label>
                                    <input type="text" class="form-control" id="prescription" name="prescription" value="{{ $pharmacyPrescription->pharmacy->pharmacy_name }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="prescription">Prescription (Images)</label>

                                    @if($pharmacyPrescription->prescription)
                                    <div class="mt-2">
                                        @foreach(json_decode($pharmacyPrescription->prescription, true) as $image)
                                        <div class="d-inline-block text-center mr-2">
                                            <img src="{{ asset('storage/'.$image) }}" alt="Prescription Image" width="100" height="70" class="mb-1">
                                            <br>
                                            <button type="button" class="btn btn-info btn-sm" onclick="showImage('{{ asset('storage/'.$image) }}')">View</button>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>


                                <div class="form-group">
                                    <label for="delivery_address">Delivery Address</label>
                                    <textarea class="form-control" id="delivery_address" name="delivery_address" rows="2" required>{{ old('delivery_address', $pharmacyPrescription->delivery_address) }}</textarea>
                                </div>

                                @php
                                  $lat_long=$pharmacyPrescription->lat_long;
                                  $cordinates=explode(',',$lat_long);
                                 
                                @endphp
                                <div class="form-group">
                                    <label for="lat_long">Latitude & Longitude</label>
                                    <input type="text" class="form-control" id="lat_long" name="lat_long" value="{{ old('lat_long', $pharmacyPrescription->lat_long) }}" readonly>

                                    <a href="https://www.google.com/maps?q={{ $cordinates[0] }},{{ $cordinates[1] }}" target="_blank">
                                        View Location on Google Maps
                                    </a>
                                </div>
                                @if($pharmacyPrescription->status==0 &&  count($medicinesPhar)< 1)
                                <a href="{{ url('/pharmacy-prescription2/rejected/'.$pharmacyPrescription->id) }}" class="btn btn-danger">Rejected</a>
                                @endif
                                <button type="submit" class="btn btn-primary">Update Address</button>
                                <a href="{{ route('pharmacy-prescriptions2.index') }}" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <img id="large-prescription" src="" class="img-fluid d-none" alt="Prescription Image">
                </div>
                @if(count($medicinesPhar)< 1)
                    <div class="col-md-6 offset-md-1">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Add Medicine</h3>
                            <button type="button" class="btn btn-success btn-sm" onclick="addMedicineField()">+ Add Medicine</button>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('pharmacy-medicines2.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="pharmacy_prescription_id" value="{{ $pharmacyPrescription->id }}">

                                <div  class=" medicine-row mb-3 d-flex align-items-center pb-2">
                                    <input type="text" class="form-control mr-2 quantity" id="med" onkeypress="enterMed()" name="medicine" placeholder="Enter medicine name" >
                                    <input type="number" class="form-control mr-2 quantity" id="total_med" onkeypress="enterMed()" name="total_number" placeholder="Enter number of medicine" >
                                    <input type="number" class="form-control mr-2 amount" id='amount' name="total_amount" placeholder="total Amount"  onkeypress="enterMed()" >
                                    <input type="text" class="form-control mr-2 total" id="ref" onkeypress="enterMed()" name="refference" placeholder="Refference" >
                               </div>
                               <div id="medicine-container" class=" medicine-row ">
                                </div>
                                <div class='col-md-12'>
                                    <h4>Choose Time Frame for Medicine Delivery</h4>
                                    <label for="start_time_1">Start Time 1:</label>
                                    <input type="time" id="start_time_1" name="start_time_1" required>

                                    <label for="end_time_1">End Time 1:</label>
                                    <input type="time" id="end_time_1" name="end_time_1" required><br><br>

                                    <!-- Time Set 2 -->
                                    <label for="start_time_2">Start Time 2:</label>
                                    <input type="time" id="start_time_2" name="start_time_2" required>

                                    <label for="end_time_2">End Time 2:</label>
                                    <input type="time" id="end_time_2" name="end_time_2" required><br><br>

                                    <!-- Time Set 3 -->
                                    <label for="start_time_3">Start Time 3:</label>
                                    <input type="time" id="start_time_3" name="start_time_3" required>

                                    <label for="end_time_3">End Time 3:</label>
                                    <input type="time" id="end_time_3" name="end_time_3" required><br><br>
                                </div>
                                <div class='col-md-12'>
                               <select class="form-control" id="assigned_user" name="assigned_user" onChange="enterMed()"  required>
                                    <option selected disabled>Assign Delivery Agent*</option>
                                    @foreach($deliveryAgents as $agent)
                                        <option value="{{$agent->id}}">{{$agent->name}}</option>
                                    @endforeach
                                </select>
                               </div>
                               
                                <button type="submit" id='submit' style="display:none;" class="btn btn-primary mt-3">Save Medicines</button>
                            </form>

                        </div>
                    </div>

            </div>
            @else
            <div class="col-md-6 offset-md-1">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Medicine Name</th>
                            <th>Quantity</th>
                            <th>Amount (₹)</th>
                            <th>Total (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicinesPhar as $medicine)
                        <tr>
                            <td>{{$medicine->id}}</td>
                            <td>{{$medicine->medicine_name}}</td>
                            <td>{{$medicine->quantity}}</td>
                            <td>{{$medicine->amount}}</td>
                            <td>{{$medicine->total}}</td>
                        </tr>

                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Grand Total (₹)</th>
                            <th>{{ collect($medicinesPhar)->sum('total') }}</th>
                        </tr>
                    </tfoot>
                </table>
                <table class="table table-bordered">
                   
                    <tr>

                        <td colspan="4">
                            <div class="time-frame">
                                <h5>Time Frames:</h5>
                                <p><strong>Time Frame 1:</strong> {{ $medicinesPhar->first()->start_time_1 ? date('h:i A', strtotime($medicinesPhar->first()->start_time_1)) : 'N/A'}} --- {{ $medicinesPhar->first()->end_time_1 ? date('h:i A', strtotime($medicinesPhar->first()->end_time_1)) : 'N/A'}}</p>
                                <p><strong>Time Frame 2:</strong> {{ $medicinesPhar->first()->start_time_2 ? date('h:i A', strtotime($medicinesPhar->first()->start_time_2)) : 'N/A'}} --- {{ $medicinesPhar->first()->end_time_2 ? date('h:i A', strtotime($medicinesPhar->first()->end_time_2)) : 'N/A'}}</p>
                                <p><strong>Time Frame 3:</strong> {{ $medicinesPhar->first()->start_time_3 ? date('h:i A', strtotime($medicinesPhar->first()->start_time_3)) : 'N/A'}} --- {{ $medicinesPhar->first()->end_time_3 ? date('h:i A', strtotime($medicinesPhar->first()->end_time_3)) : 'N/A'}}</p>
                            </div>
                        </td>
                    </tr>
                    
                </table>
            </div>
            @endif
        </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.0.0-rc.4/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.0.0-rc.4/dist/js/tom-select.complete.min.js"></script>




 
<script>
    function showImage(imageUrl) {
        console.log(imageUrl);
        $('#large-prescription').attr('src', imageUrl).removeClass('d-none');
    }
</script>

<script>
    $(document).ready(function() {

        

       function addMedicineField() {
    var container = $('#medicine-container');
    var submit = $('#submit');
    submit.show();

    // Create wrapper for the full medicine block
    var div = $('<div>', {
        class: 'medicine-row mb-3 p-3 border rounded bg-light'
    });

    // Two rows of fields using Bootstrap grid system
        div.html(`<!-- Row 1 -->
        <div class="row">
        <div class="col-md-6 col-sm-12 mb-2">
        <select class="form-control medicine-select operator" name="medicines[]" id="medicine" onchange="fetchMedicineDetails(this)" required>
        <option value="">Select Medicine</option>
        @foreach($medicines as $medicine)
        <option value="{{ $medicine->medicine_name }}" data-price="{{ $medicine->amount }}">{{ $medicine->medicine_name }}</option>
        @endforeach
        </select>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
        <input type="number" class="form-control quantity" name="quantities[]" placeholder="Quantity" min="1" required oninput="calculateTotal(this)">
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
        <input type="text" class="form-control amount" id="amount" name="amounts[]" placeholder="Price per Unit" oninput="calculateTotal(this)" required>
        </div>
        </div>

        <!-- Row 2 -->
        <div class="row" style="margin-top: 3px;">
        <div class="col-md-3 col-sm-6 mb-2">
        <input type="text" class="form-control total" name="total[]" placeholder="Total" readonly>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
        <input type="text" class="form-control"  id='req_unit' name="req_unit[]" placeholder="Requested Unit" required>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
        <input type="text" class="form-control" id='avil_unit' name="avail_unit[]" placeholder="Available Unit" required>
        </div>
        <div class="col-md-3 col-sm-6 mb-2 d-flex align-items-center">
        <button type="button" class="btn btn-danger btn-sm" onclick="removeMedicineField(this)">X</button>
        </div>
        </div>`);

    // Append block and initialize TomSelect
    container.append(div);

    div.find('.medicine-select').each(function () {
        new TomSelect(this, {
            create: true,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    });
}



        function fetchMedicineDetails(selectElement) {
            var price = $(selectElement).find('option:selected').data('price');
            $(selectElement).closest('.medicine-row').find('#amount').val(price);

        }

        // Function to calculate the total
        function calculateTotal(element) {
            var row = $(element).closest('.medicine-row');
            var quantity = parseFloat(row.find('.quantity').val());
            var amount = parseFloat(row.find('.amount').val());
            var total = (quantity && amount) ? (quantity * amount).toFixed(2) : 0;
            row.find('.total').val(total);
        }

        // Function to remove a medicine field
        function removeMedicineField(button) {
            $(button).closest('.medicine-row').remove();
        }

        // Bind the addMedicineField function to the button click event
        $('#add-medicine-btn').click(function() {
            addMedicineField();
        });

        // Expose the functions to the global scope for use within inline event handlers
        window.addMedicineField = addMedicineField;
        window.fetchMedicineDetails = fetchMedicineDetails;
        window.calculateTotal = calculateTotal;
        window.removeMedicineField = removeMedicineField;
    });

    function enterMed()
    {
        var med=$('#med').val();
        var amount=$('#amount').val();
        var total_med=$('#total_med').val();
        var ref=$('#ref').val();
        var assign_user=$('#assigned_user').val();

        if(med && total_med && amount && ref && assign_user){
            var submit = $('#submit');
            submit.show();
        }
    }
</script>
