@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Clinic Prescriptions</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                        <li class="breadcrumb-item active">Clinic Prescriptions</li>
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
                                <label><input type="radio" id="status" onclick="statusChecker()" name="status_filter"
                                        value="0"> New</label>
                                <!-- <label><input type="radio" id="status" onclick="statusChecker()" name="status_filter"
                                        value="1"> Processing</label>
                                <label><input type="radio" id="status" onclick="statusChecker()" name="status_filter"
                                        value="2"> Completed</label>
                                <label><input type="radio" id="status" onclick="statusChecker()" name="status_filter"
                                        value="3"> Rejected</label> -->


                                <label><input type="radio" id="status" onclick="statusChecker()" name="status_filter"
                                        value="1"> Completed</label>
                                <label><input type="radio" id="status" onclick="statusChecker()" name="status_filter"
                                        value="2"> Rejected</label>
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
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Clinic</th>
                                        <th>Prescription Image</th>
                                        <th>Address</th>
                                        <th>Test</th>
                                        <th>Latitude & Longitude</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id='pharPresTable'>
                                    @foreach($clinicPrescriptions as $prescription)
                                    <tr>
                                        <td>{{ $prescription->user->name }}</td>

                                        <td>{{ $prescription->name }}</td>
                                        <td>{{ $prescription->age }}</td>
                                        <td>{{ $prescription->gender }}</td>

                                        <td>{{ $prescription->clinic->clinic_name }}</td>
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
                                        <td>{{ $prescription->address }}</td>
                                        @if($prescription->test)
                                        @foreach(json_decode($prescription->test, true) as $test)
                                        <td>{{ $test }}</td>
                                        @endforeach
                                        @endif
                                        <td>{{ $prescription->lat_long }}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm open-edit-modal" data-id="{{ $prescription->id }}" title="Edit">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div id="paginate" class="d-flex justify-content-center mt-5">
                                {{ $clinicPrescriptions->links('pagination::bootstrap-5') }}
                            </div>

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" backdrop="static" data-bs-backdrop="static" data-bs-keyboard="true">
    <div class="modal-dialog " style="max-width: 70%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Clinic Prescription</h5>
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

<style>
div#example1_info
 {
    display: none;
}

div#example1_paginate {
    display: none;
}
</style>

<!-- Include necessary scripts -->
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


    $('#close').on('click', function() {
        console.log('Closing modal');
        $('#editModal').modal('hide');
    });

    $(document).on('click', '.open-edit-modal', function() {
        let clinicPrescription = $(this).data('id');
        let url = `/clinic-prescriptions2/${clinicPrescription}/edit`;

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







    
    // function statusChecker() {
    //     var status = $('input[name="status_filter"]:checked').val();
    //     if (status) {
    //         $.ajax({
    //             url: '{{route("clinic-prescription.status")}}',
    //             method: 'GET',
    //             data: {
    //                 status: status
    //             },
    //             success: function(response) {
    //                 $('#pharPresTable').html(response.output);
    //                 $('#paginate').html(response.pagination);
    //             },
    //             error: function(xhr) {
    //                 alert('Failed to load prescription details. Please try again.');
    //             }
    //         });
    //     }
    // }


    $(document).on('click', '#paginate a', function (e) {
    e.preventDefault();
    const url = $(this).attr('href');
    const pageMatch = url.match(/page=(\d+)/);
    const page = pageMatch ? pageMatch[1] : 1;
    statusChecker(page);
});


    function statusChecker(page = 1) {
    const status = $('input[name="status_filter"]:checked').val();

    if (status !== undefined) {
        $.ajax({
            url: '{{ route("clinic-prescription2.status") }}',
            method: 'GET',
            data: {
                status: status,
                page: page
            },
            success: function(response) {
                $('#pharPresTable').html(response.output);
                $('#paginate').html(response.pagination); // this should be returned by controller
            },
            error: function() {
                alert('Failed to load data. Try again.');
            }
        });
    }
}
    function dateWiseSearch() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        if (startDate && endDate) {
            $.ajax({
                url: '{{ route("clinic-prescription2.dateMatch") }}',
                method: 'GET',
                data: {
                    status: status,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    console.log(response);
                    $('#pharPresTable').html(response.output);
                    $('#paginate').html(response.pagination);
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