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

                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>

                                        <th>User</th>
                                        <th>Pharmacy</th>
                                        <th>Prescription Image</th>
                                        <th>Delivery Address</th>
                                        <th>Latitude & Longitude</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pharmacyPrescriptions as $prescription)
                                    <tr>

                                        <td>{{ $prescription->user->name }}</td>
                                        <td>{{ $prescription->pharmacy->pharmacy_name }}</td>
                                        <td>
                                            @if($prescription->prescription)
                                            @foreach(json_decode($prescription->prescription, true) as $image)
                                            <img src="{{ asset('storage/'.$image) }}" alt="Prescription Image" width="100" height="70" class="mr-1 mb-1">
                                            @endforeach
                                            @else
                                            <span>No image</span>
                                            @endif
                                        </td>
                                        <td>{{ $prescription->delivery_address }}</td>
                                        <td>{{ $prescription->lat_long }}</td>
                                        <td>
                                            <a href="{{ route('pharmacy-prescriptions.edit', $prescription->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Include necessary scripts -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
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
</script>
@endsection