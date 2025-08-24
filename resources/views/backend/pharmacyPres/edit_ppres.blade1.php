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
                       <li class="breadcrumb-item"><a href="{{ route('pharmacy-prescriptions.index') }}">Pharmacy Prescriptions</a></li>
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
                           <form action="{{ route('pharmacy-prescriptions.update', $pharmacyPrescription->id) }}" method="POST" enctype="multipart/form-data">
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
                                          <!-- <i class="fas fa-images" style="color: green; font-size: 20px;"></i> -->
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
                               <button type="submit" class="btn btn-primary">Update Address</button>
                               <a href="{{ route('pharmacy-prescriptions.index') }}" class="btn btn-secondary">Cancel</a>
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
                           <form id="medicine-form" action="{{ route('pharmacy-medicines.store') }}" method="POST">
                               @csrf
                               <input type="hidden" name="pharmacy_prescription_id" value="{{ $pharmacyPrescription->id }}">

                               <div id="medicine-container" class=" medicine-row mb-3 d-flex align-items-center">
                                <input type="text" class="form-control mr-2 quantity" name="medicine" placeholder="type medicine" >
                                <input type="text" class="form-control mr-2 amount" id='amount' name="amounts" placeholder="total Unit"   >
                                <input type="text" class="form-control mr-2 total" name="refference" placeholder="Refference" >
                               </div>
                               <div class='col-md-12'>
                                   <h4>Choose Time Frame for Medicine Delivery</h4>
                                   <label for="start_time_1">Start Time 1:</label>
                                   <input type="time" id="start_time_1" name="start_time_1" required>

                                   <label for="end_time_1">End Time 1:</label>
                                   <input type="time" id="end_time_1" name="end_time_1" onChange="startTime1()" required><br><br>

                                   <!-- Time Set 2 -->
                                   <label for="start_time_2">Start Time 2:</label>
                                   <input type="time" id="start_time_2" name="start_time_2" required>

                                   <label for="end_time_2">End Time 2:</label>
                                   <input type="time" id="end_time_2" name="end_time_2" onChange="startTime2()" required><br><br>

                                   <!-- Time Set 3 -->
                                   <label for="start_time_3">Start Time 3:</label>
                                   <input type="time" id="start_time_3" name="start_time_3" required>

                                   <label for="end_time_3">End Time 3:</label>
                                   <input type="time" id="end_time_3" name="end_time_3" onChange="startTime3()" required><br><br>
                               </div>
                               <div class='col-md-12'>
                               <select class="form-control" id="assigned_user" name="assigned_user"  required>
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
                           <th>Medicine Name</th>
                           <th>Quantity</th>
                           <th>Amount (₹)</th>
                           <th>Total (₹)</th>
                       </tr>
                   </thead>
                   <tbody>
                       @foreach($medicinesPhar as $medicine)
                       <tr>
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
   </section>

   <!-- Load jQuery first-->
  

   <script>
       function showImage(imageUrl) {
           $('#large-prescription').attr('src', imageUrl).removeClass('d-none');
       }
   </script>

   <script>
       $(document).ready(function() {



           function addMedicineField() {
               var container = $('#medicine-container');
               var submit = $('#submit');
               submit.show();

               var div = $('<div>', {
                   class: 'medicine-row mb-3 d-flex align-items-center'
               });

               div.html(`
                <select class="form-control "  name="medicines[]" onChange="fetchMedicineDetails(this)" required>'
                    <option class="" value="">Select Medicine</option>
                   
                        @foreach($medicines as $medicine)
        
                        
                            <option value="{{ $medicine->medicine_name }}" data="{{ $medicine->amount }}">{{ $medicine->medicine_name }}</option>
                        @endforeach
                    
                </select>
                <input type="number" class="form-control mr-2 quantity" name="quantities[]" placeholder="Quantity" required oninput="calculateTotal(this)">
                <input type="text" class="form-control mr-2 amount" id='amount' name="amounts[]" placeholder="Price per Unit" oninput="calculateTotal(this)" required >
                <input type="text" class="form-control mr-2 total" name="total[]" placeholder="Total" readonly>
                <input type="text" class="form-control mr-2 " id='req_unit' name="req_unit[]" placeholder="Requested Unit"  required >
                <input type="text" class="form-control mr-2 " id='req_unit' name="avail_unit[]" placeholder="Available Unit"  required >

                <button type="button" class="btn btn-danger btn-sm" onclick="removeMedicineField(this)">X</button>
            `);

               container.append(div);

               $('select').selectize({
                    sortField: 'text'
                });
           }


           function fetchMedicineDetails(selectElement) {
               console.log(selectElement);
               var price = $(selectElement).find('option:selected').data('price');
               console.log(price);
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


           function getCurrentTime() {
               let now = new Date();
               let hours = now.getHours().toString().padStart(2, '0');
               let minutes = now.getMinutes().toString().padStart(2, '0');
               return hours + ':' + minutes;
           }

           let currentTime = getCurrentTime();
       });


       function startTime1() {
           var start1 = $('#start_time_1').val();
           var end1 = $('#end_time_1').val();

           var start1Minutes = convertTimeToMinutes(start1);
           var end1Minutes = convertTimeToMinutes(end1);
           if (start1 && end1) {
               if (end1Minutes <= start1Minutes) {
                   alert("End time must be greater than start time.");
                    $('#end_time_1').val("");
               }
           }else{
            alert("please enter start time 1 and  end time 1");
            $('#start_time_1').val("");
            $('#end_time_1').val("");
           }

       }

       function startTime2() {
           var start2 = $('#start_time_2').val();
           var end2 = $('#end_time_2').val();

           var start1Minutes = convertTimeToMinutes(start2);
           var end1Minutes = convertTimeToMinutes(end2);


           if (start2 && end2) {
               if (end1Minutes <= start1Minutes) {
                   alert("End time must be greater than start time.");
                    $('#end_time_2').val("");
               }
           }else{
            alert("please enter start time 2 and  end time 2");
            $('#start_time_2').val("");
            $('#end_time_2').val("");
           }
       }

       function startTime3() {
           var start3 = $('#start_time_3').val();
           var end3 = $('#end_time_3').val();

           var start1Minutes = convertTimeToMinutes(start3);
           var end1Minutes = convertTimeToMinutes(end3);


           if (start3 && end3) {
               if (end1Minutes <= start1Minutes) {
                   alert("End time must be greater than start time.");
                    $('#end_time_3').val("");
               }
           }else{
            alert("please enter start-time-3 and end-time-3");
            $('#start_time_3').val("");
            $('#end_time_3').val("");
           }
       }

       function convertTimeToMinutes(time) {
           var timeParts = time.split(':');
           var hours = parseInt(timeParts[0]);
           var minutes = parseInt(timeParts[1]);
           return (hours * 60) + minutes;
       }
   </script>
   </body>

   </html>