@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Daybook</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Daybook</li>
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


            <div class="col-md-12 d-flex justify-content-end mt-2 mb-4">
            <a 
            href="{{ route('daybook.export', request()->query()) }}" 
            class="btn btn-success pt-2 pb-2"
            >
            Export Excel
            </a>
            </div>



          
  <form method="GET" class="mb-4">
  <div class="row align-items-end" style="    margin-right: 10px;
    margin-left: 10px;
    margin-top: 20px;">
    <div class="col-md-4">
        <label for="payment_method" class="form-label">Payment Method</label>
        <select name="payment_method" id="payment_method" class="form-control">
            <option value="">All Payment Methods</option>
            <option value="1" {{ request('payment_method') == '1' ? 'selected' : '' }}>Online Payment</option>
            <option value="2" {{ request('payment_method') == '2' ? 'selected' : '' }}>Cash on Delivery</option>
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


  <table class="table" id="example1">
    <thead>
        <tr>
            <!-- <th>ID</th> -->
            <th>Patient Name</th>
            <th>Pharmacy</th>
            <th>Payment Method</th>
            <th>Amount Collected</th>
            <th>Address</th>
            <th>Delivery Agent</th>
            <!-- <th>Coordinates</th>
            <th>Status</th> -->
            
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalAmount = 0;
        @endphp

        @foreach($payments as $payment)
            @php
                $totalAmount += $payment->total_amount;
            @endphp
            <tr>
                <!-- <td>{{ $payment->id }}</td> -->
                <td>{{ $payment->name ?? 'N/A' }}</td>
                <td>{{ $payment->pharmacy->pharmacy_name ?? 'N/A' }}</td>
                <td>
                    @if($payment->payment_method == 1)
                        Online Payment
                    @elseif($payment->payment_method == 2)
                        Cash on Delivery
                    @else
                        N/A
                    @endif
                </td>
                <td>₹{{ number_format($payment->total_amount, 2) }}</td>
                <td>{{ $payment->delivery_address }}</td>
                <td>{{ $payment->deliveryAgent->name ?? 'N/A' }}</td>
                <td>{{ $payment->created_at->format('d M Y h:i A') }}</td>
            </tr>
        @endforeach
    </tbody>

    <!-- Footer with Total -->
    <tfoot>
        <tr>
            <td colspan="3" style="text-align: right;"><strong>Total Amount Collected:</strong></td>
            <td><strong>₹{{ number_format($totalAmount, 2) }}</strong></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>


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

<style>
  div#example1_filter {
    margin-right: 20px;
}
</style>
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
