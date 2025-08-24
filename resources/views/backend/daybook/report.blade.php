@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Report</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card">
           

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif


            <!-- <div class="col-md-12 d-flex justify-content-end mt-2 mb-4">
            <a 
            href="{{ route('daybook.export', request()->query()) }}" 
            class="btn btn-success pt-2 pb-2"
            >
            Export Excel
            </a>
            </div> -->



          
          <form method="GET" class="mb-4">
          <div class="row align-items-end" style="margin-right: 10px;margin-left: 10px;margin-top: 20px;">
          <div class="col-md-4">
          <label for="types" class="form-label">Types</label>
          <select name="types" id="types" class="form-control">
          <option value="">All Types</option>
          <option value="1" {{ request('types') == '1' ? 'selected' : '' }}>Clinics</option>
          <option value="2" {{ request('types') == '2' ? 'selected' : '' }}>Pharmacy</option>
          </select>
          </div>

          <div class="col-md-3">
          <label for="from_date" class="form-label">From Date</label>
          <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
          </div>

          <div class="col-md-3">
          <label for="to_date" class="form-label">To Date</label>
          <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
          </div>

          <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">Filter</button>
          </div>
          </div>
          </form>


      <table class="table">
      <thead>
      <tr>
      <th>Patient Name</th>
      <th>
        @if(request('types') == '1')
        Clinics
        @elseif(request('types') == '2')
        Pharmacy
        @else
        Type
        @endif
      </th>
      <th>Address</th>
      <th>Status</th>
      <th>Date</th>
      <!-- <th>Prescription</th> -->
      </tr>
      </thead>
      <tbody>
      @forelse($payments as $payment)
      <tr>
      <td>{{ $payment->name ?? 'N/A' }}</td>
      {{-- Show clinic or pharmacy name --}}
      
      <td>
      @if(request('types') == '1')
      {{ $payment->clinic->clinic_name ?? 'N/A' }}

      @elseif(request('types') == '2')
      {{ $payment->pharmacy->pharmacy_name ?? 'N/A' }}
      @else
      {{ $payment->clinic->clinic_name ?? $payment->pharmacy->pharmacy_name ?? 'N/A' }}
      @endif
      </td>

      {{-- Delivery address --}}
      <td>{{ $payment->delivery_address ?? $payment->address }}</td>

      <!-- <td>{{ $payment->status  }}</td> -->

      <td>
    @if(request('types') == '1') {{-- Clinics --}}
        @switch($payment->status)
            @case(0)
                New
                @break
            @case(1)
                Pending
                @break
            @case(2)
                Collected
                @break
            @case(3)
                Testing
                @break
            @case(4)
                Completed
                @break
            @case(5)
                Rejected
                @break
            @default
                Unknown
        @endswitch

    @elseif(request('types') == '2') {{-- Pharmacy --}}
        @switch($payment->status)
            @case(0)
                New
                @break
            @case(1)
                Processing
                @break
            @case(2)
              Confirmed
                @break
            @case(3)
                Delivery Assigned
                @break
            @case(4)
                Completed
                @break
                @case(5)
                Rejected
                @break
            @default
                Unknown
        @endswitch

    @else
        <!-- {{ $payment->status }} -->

        @switch($payment->status)
            @case(0)
                New
                @break
            @case(1)
                Processing
                @break
            @case(2)
              Confirmed
                @break
            @case(3)
                Testing
                @break

                @case(4)
                Completed
                @break

            @case(5)
                Rejected
                @break
            @default
                Unknown
        @endswitch

    @endif
</td>



      {{-- Date --}}
      <td>{{ optional($payment->created_at)->format('d M Y h:i A') }}</td>

      <!-- <td>
      @if(!empty($payment->prescriptions))
      <a href="{{ asset('storage/' . $payment->prescriptions) }}" 
      target="_blank" 
      class="btn btn-sm btn-primary">
      Download
      </a>
      @else
      N/A
      @endif
      </td> -->


      </tr>
      @empty
      <tr>
      <td colspan="6" class="text-center">No records found.</td>
      </tr>
      @endforelse
      </tbody>
      </table>

      {{-- Pagination --}}
      <!-- {{ $payments->withQueryString()->links() }} -->

      <div id="paginate" class="d-flex justify-content-center mt-5">
    {{ $payments->links('pagination::bootstrap-5') }}
    </div>
     
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- Page specific script -->
<script>
  $(function() {
    $("#example1").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
@endsection
