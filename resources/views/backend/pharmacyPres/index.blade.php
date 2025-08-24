@extends('backend.layouts.app')


@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pharmacy Prescriptions</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                        <li class="breadcrumb-item active">Pharmacy Prescriptions</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div>
                                <!-- Status Filter Radio Buttons -->
                                <!-- <label><input type="radio" id="status" onclick="statusChecker()" name="status_filter"
                                        value="0" @if($val==0) checked @endif> New</label> -->


                                  <!-- <label><input type="radio" id="status" onclick="statusChecker()" name="status_filter"
                                        value="0"> New</label>      
                                <label><input type="radio" id="status" onclick="statusChecker()" name="status_filter"
                                        value="1"> Processing</label>
                                <label><input type="radio" id="status" onclick="statusChecker()" name="status_filter"
                                        value="2"> Confirmed</label>
                                <label><input type="radio" id="status" onclick="statusChecker()" name="status_filter"
                                        value="3"> Delivered</label>
                                <label><input type="radio" id="status" onclick="statusChecker()" name="status_filter"
                                        value="4"> Rejected</label> -->
                                
                                 
                                    <label>
                                    <input type="radio" onclick="statusChecker()" name="status_filter" value="0"
                                    {{ request('status_filter', '0') == '0' ? 'checked' : '' }}>
                                    New
                                    </label>

                                    <label>
                                    <input type="radio" onclick="statusChecker()" name="status_filter" value="1"
                                    {{ request('status_filter') == '1' ? 'checked' : '' }}>
                                    Processing
                                    </label>

                                    <label>
                                    <input type="radio" onclick="statusChecker()" name="status_filter" value="2"
                                    {{ request('status_filter') == '2' ? 'checked' : '' }}>
                                    Confirmed
                                    </label>


                                    <label>
                                    <input type="radio" onclick="statusChecker()" name="status_filter" value="3"
                                    {{ request('status_filter') == '3' ? 'checked' : '' }}>
                                    Assigned Delivery
                                    </label>


                                     <label>
                                    <input type="radio" onclick="statusChecker()" name="status_filter" value="4"
                                    {{ request('status_filter') == '4' ? 'checked' : '' }}>
                                    Completed
                                    </label>

                                     <label>
                                    <input type="radio" onclick="statusChecker()" name="status_filter" value="5"
                                    {{ request('status_filter') == '5' ? 'checked' : '' }}>
                                     Rejected                                   
                                     </label>


                            </div>

                            <div>
                                <p class="text-danger" id="error_in_date"></p>
                                <label for="start_date">Start Date:</label>
                                <input type="date" id="start_date">
                                <label for="end_date">End Date:</label>
                                <input type="date" id="end_date" onchange="dateWiseSearch()">
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>

                                        <th>User</th>
                                        <th>Patient</th>
                                        <th>Pharmacy</th>
                                        <th>Prescription Image</th>
                                        <th>Delivery Address</th>
                                        <th>Latitude & Longitude</th>

                                        <th class="payment-method-th">Payment Method</th>



                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="pharPresTable">
                                    @foreach($pharmacyPrescriptions as $prescription)
                                    <tr @if(!empty($prescription->receipt)) style="background-color: violet;" @endif>

                                    
                                 
                                        <td>{{ $prescription->user->name  }}</td>
                                        <td>{{ $prescription->name }}</td>
                                        <td>{{ $prescription->pharmacy->pharmacy_name }}</td>
                                        
                                        <td>
                                            @if($prescription->prescription)
                                            @php
                                            $prescriptions = json_decode($prescription->prescription, true);
                                            @endphp

                                            @if(count($prescriptions) > 1)
                                            <i class="fas fa-images" style="color: green; font-size: 20px;"> + {{ count($prescriptions) }}</i>
                                            @else
                                            <i class="fas fa-images" style="color: green; font-size: 20px;"></i>
                                            @endif

                                            @else
                                            <span>No image</span>
                                            @endif
                                        </td>

                                        <td>{{ $prescription->delivery_address }}</td>
                                        <td>{{ $prescription->lat_long }}</td>
                                          
                                            <td class="payment-method-th">
                                            {{ $prescription->payment_method == 1 ? 'Online Payment' : ($prescription->payment_method == 2 ? 'Cash on Delivery' : '-') }}</td>


                                        <td>{{$prescription->created_at}}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm open-edit-modal"
                                                data-id="{{ $prescription->id }}" title="Edit">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div id="paginate" class="d-flex justify-content-center mt-5">
                                {{ $pharmacyPrescriptions->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true" backdrop="static" data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog " style="max-width: 80%;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Pharmacy Prescription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded dynamically with AJAX -->
                </div>
            </div>
        </div>
    </div>


    
</div>

<!-- Include necessary scripts -->



<style>
div#example1_info
 {
    display: none;
}


div#example1_paginate 
{
    display: none;
}

.payment-method-th,
.payment-method-td 
{
    display: none;
}




</style>

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script> -->

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

<script>
    $(function() {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false
        });
    });

    // $('.btn-close').on('click', function() 
    // {
    //     location.reload();
    //     //$('#editModal').modal('hide');    
    // });
   
    // $('#editModal').on('hide.bs.modal', function () 
    // {
    // // Add any custom CSS or class on modal hide
    // $(this).addClass('custom-hidden-style');
    // });


    $('#close').on('click', function() {
        console.log('Closing modal');
        $('#editModal').modal('hide');
    });

    $(document).on('click', '.open-edit-modal', function() {
        let prescriptionId = $(this).data('id');
        let url = `/pharmacy-prescriptions/${prescriptionId}/edit`;

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                $('#editModal .modal-body').html(response);
                $('#editModal').modal('show');
                 
            },
            error: function(xhr) {
                alert('Failed to load prescription details. Please try again.');
            }
        });
    });

//    function statusChecker(page = 1) {
//     const status = $('input[name="status_filter"]:checked').val();

//     if (status !== undefined) {
//         $.ajax({
//             url: '{{ route("pharmacy-prescription.status") }}',
//             method: 'GET',
//             data: {
//                 status: status,
//                 page: page
//             },
//             success: function(response) {
//                 $('#pharPresTable').html(response.output);
//                 $('#paginate').html(response.pagination); // this should be returned by controller
//             },
//             error: function() {
//                 alert('Failed to load data. Try again.');
//             }
//         });
//     }
// }



function statusChecker(page = 1) 
{
    const status = $('input[name="status_filter"]:checked').val();

    if (status !== undefined) 
    {
    $.ajax({
        url: '{{ route("pharmacy-prescription.status") }}',
        method: 'GET',
        data: {
            status: status,
            page: page
        },
        success: function(response) {
            $('#pharPresTable').html(response.output);
            $('#paginate').html(response.pagination);

            // Show/hide Payment Method column
            if (status === '2') {
                $('.payment-method-th, .payment-method-td').show();
            } else {
                $('.payment-method-th, .payment-method-td').hide();
            }
        },
        error: function() {
            alert('Failed to load data. Try again.');
        }
    });
    }
}


    // function statusChecker(page = 1) 
    // {
    // const status = $('input[name="status_filter"]:checked').val();

    // if (status !== undefined) {
    //     localStorage.setItem('selected_status', status);

    //     $.ajax({
    //         url: '{{ route("pharmacy-prescription.status") }}',
    //         method: 'GET',
    //         data: {
    //             status: status,
    //             page: page
    //         },
    //         success: function(response) {
    //             $('#pharPresTable').html(response.output);
    //             $('#paginate').html(response.pagination);

    //             if (status === '2') 
    //             {
    //                 $('.payment-method-th, .payment-method-td').show();
    //             }
    //             else {
    //                 $('.payment-method-th, .payment-method-td').hide();
    //             }
    //         },
    //         error: function() {
    //             alert('Failed to load data. Try again.');
    //         }
    //     });
    // }
    // }


    // $(document).ready(function () 
    // {
    // const savedStatus = localStorage.getItem('selected_status');
    // if (savedStatus) 
    // {
    //     $(`input[name="status_filter"][value="${savedStatus}"]`).prop('checked', true);
    //     statusChecker(); 
    // }
    // else 
    // {
    //     $('input[name="status_filter"][value="0"]').prop('checked', true);
    //     statusChecker();
    // }
    // });


$(document).on('click', '#paginate a', function (e) {
    e.preventDefault();
    const url = $(this).attr('href');
    const pageMatch = url.match(/page=(\d+)/);
    const page = pageMatch ? pageMatch[1] : 1;
    statusChecker(page);
});





//  function statusChecker() {
//         var status = $('input[name="status_filter"]:checked').val();
//         if (status) {
//             $.ajax({
//                 url: '{{route("clinic-prescription.status")}}',
//                 method: 'GET',
//                 data: {
//                     status: status
//                 },
//                 success: function(response) {
//                     $('#pharPresTable').html(response.output);
//                     $('#paginate').html(response.pagination);
//                 },
//                 error: function(xhr) {
//                     alert('Failed to load prescription details. Please try again.');
//                 }
//             });
//         }
//     }


 $(document).ready(function () {
        // Set default start_date = yesterday, end_date = today
        let today = new Date();
        let yesterday = new Date(today);
        yesterday.setDate(today.getDate() - 1);

        let formatDate = (date) => {
            return date.toISOString().split('T')[0]; // YYYY-MM-DD
        };

        $('#start_date').val(formatDate(yesterday));
        $('#end_date').val(formatDate(today));

        // Trigger default search
        dateWiseSearch();
    });

    function dateWiseSearch() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        if (startDate && endDate) {
            $.ajax({
                url: '{{ route("pharmacy-prescription.dateMatch") }}',
                method: 'GET',
                data: {
                    status: status,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    console.log(response);
                    $('#pharPresTable').html(response.output);
                   // $('#paginate').html(response.pagination);
                },
                error: function(xhr) {
                    alert('Failed to load prescription details. Please try again.');
                }
            });
        } else {
            $('#error_in_date').text('Please enter start date and end date');
        }

    }
        
</script>
@endsection