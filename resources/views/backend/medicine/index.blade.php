@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Medicines</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item active">Medicines</li>
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
            <div class="card-header d-flex justify-content-end">


              <form action="" method="GET" class="ml-2">
                <div class="input-group">
                  <select name="pharmacy_id" id="pharmacy_id" class="form-control" onchange="pharmacyChanger()" required>
                    <option value="" disabled selected>Select Pharmacy</option>
                    @foreach($pharmacies as $pharmacy)
                    <option value="{{$pharmacy->id}}">{{$pharmacy->pharmacy_name}}</option>
                    @endforeach
                  </select>
                </div>
              </form>
              <form action="" method="GET" class="ml-2">
                <div class="input-group">
                  <input type="text" name="search" id="search" class="form-control" placeholder="Search Medicines">

                </div>
              </form>

              <form action="{{route('medicines.import')}}" method="POST" enctype="multipart/form-data" class="ml-2 pr-5">
                @csrf
                <div class="input-group mb-3">
                  <select name="pharmacy_id" class="form-control" required>
                    <option value="" disabled selected>Select Pharmacy to Import</option>
                    @foreach($pharmacies as $pharmacy)
                    <option value="{{$pharmacy->id}}">{{$pharmacy->pharmacy_name}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="input-group">
                  <input type="file" name="file" class="form-control" accept=".xls,.xlsx" placeholder="Multiple Medicines upload" required>
                  <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">
                      <i class="fas fa-upload"></i> Upload Data
                    </button>
                  </div>
                </div>
              </form>
              <div >
                <a class="btn btn-success" href="{{ route('medicines.create') }}">
                <i class="fas fa-plus"></i> Add Medicine
              </a>
            </div>
            </div>
            
            <div class="card-body">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Pharmacy</th>
                    <th>Medicine</th>
                    <th>Amount</th>
                    <th>Quantity</th>
                    <th>Expiry Date</th>
                    <th>Batch</th>
                    <th>Manufacturer</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="medicineTable">
                  @foreach($medicines as $medicine)
                  <tr>
                    <td>{{ $medicine->pharmacy->pharmacy_name }}</td>
                    <td>{{ $medicine->medicine_name }}</td>
                    <td>{{ number_format($medicine->amount, 2) }}</td>
                    <td>{{ $medicine->quantity }}</td>
                    <td>{{ $medicine->expiry_date }}</td>
                    <td>{{ $medicine->description}}</td>
                    <td>{{$medicine->manufacturer}}</td>

                    <td>
                      <a href="{{ route('medicines.edit', $medicine->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                      </a>
                      <form action="{{ route('medicines.destroy', $medicine->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this medicine?');">
                          <i class="fas fa-trash"></i> Delete
                        </button>
                      </form>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              <div id="paginate" class="d-flex justify-content-center mt-5">
                {{ $medicines->links('pagination::bootstrap-5') }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {

    $('#search').on('keyup', function() {
      let search = $(this).val();
      fetchMedicines(search, 1);
    });


    $(document).on('click', '#paginate a', function(event) {
      event.preventDefault();
      let page = $(this).attr('href').split('page=')[1];
      let search = $('#search').val();
      fetchMedicines(search, page);
    });

    function fetchMedicines(search, page) {
      $.ajax({
        url: "{{ route('medicines.search') }}",
        type: "GET",
        data: {
          search: search,
          page: page
        },
        success: function(response) {
          $('#medicineTable').html(response.output);
          $('#paginate').html(response.pagination);
        }
      });
    }
  });

  function pharmacyChanger() {

    var pharmacy_id = $('#pharmacy_id').val();
    $.ajax({
      url: "{{ route('medicines.pharmacy') }}",
      type: "GET",
      data: {
        pharmacy_id: pharmacy_id,
      },
      success: function(response) {
        $('#medicineTable').html(response.output);
        $('#paginate').html(response.pagination);
      }
    });
  }
</script>
@endsection