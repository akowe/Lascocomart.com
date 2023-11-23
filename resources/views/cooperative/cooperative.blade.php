@extends('layouts.home')

@extends('layouts.sidebar')


@section('content')
<!-- adminx-content-aside -->
<div class="adminx-content">
      <!-- <div class="adminx-aside">

        </div> -->

      <div class="adminx-main-content">
            <div class="container-fluid">
                  <!-- BreadCrumb -->
                  <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb adminx-page-breadcrumb">
                              <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                              <li class="breadcrumb-item active" aria-current="page">Cooperative. </li>
                        </ol>
                  </nav>

                  <div class="pb-3">
                        <h4>Your User ID: &nbsp;{{Auth::user()->code}}. </h4>
                        <span class="text-danger">Share it with your members; it's used in pairing a member to your
                              cooperative.

                              <br>
                        </span>

                        <h5 class="navbar bg-dark text-white" style="padding-left: 10px;">
                              Credit Balance is: ₦{{ number_format($credit->sum('credit')) }}
                        </h5>

                        <div class="card-body text-center">

                              @if (session('profile'))
                              <div class="alert alert-danger" role="alert">
                                    <a href="{{url('profile') }}" class="cursor"> {!! session('profile') !!}</a>
                              </div>
                              @endif

                              @if (session('status'))
                              <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                              </div>
                              @endif
                              @if (session('success'))
                              <div class="alert alert-success" role="alert">
                                    {!! session('success') !!}
                              </div>
                              @endif

                              @if (session('error'))
                              <div class="alert alert-danger" role="alert">
                                    {!! session('error') !!}
                              </div>
                              @endif
                        </div>

                        <div class="row">
                              <div class="col-md-6 col-lg-3 d-flex">
                                    <div class="card mb-grid w-100">
                                          <a href="{{ url('admin-order-history') }}"
                                                class="text-decoration-none text-dark">
                                                <div class="card-body d-flex flex-column">
                                                      <div class="d-flex justify-content-between mb-3">
                                                            <h5 class="card-title mb-0 small">
                                                                  Approved Orders
                                                            </h5>
                                                            <div class="card-title-sub">
                                                                  {{ $count_orders->count() }}
                                                            </div>
                                                      </div>

                                                      <div class="progress mt-auto">
                                                            <a href="{{ url('admin-order-history') }}">View Order
                                                                  History</a>
                                                      </div>
                                                </div>
                                          </a>
                                    </div>
                              </div>
                              <div class="col-md-6 col-lg-3 d-flex">
                                    <div class="card mb-grid w-100">
                                          <a href="{{ url('admin-products') }}" class="text-decoration-none text-dark">
                                                <div class="card-body d-flex flex-column">
                                                      <div class="d-flex justify-content-between mb-3">
                                                            <h5 class="card-title mb-0 small">
                                                                  Approved Products
                                                            </h5>

                                                            <div class="card-title-sub">
                                                                  {{ $count_product->count() }}
                                                            </div>
                                                      </div>

                                                      <div class="progress mt-auto">
                                                            <a href="{{ url('admin-products') }}">View All Products</a>
                                                      </div>
                                                </div>
                                          </a>
                                    </div>
                              </div>
                              <div class="col-md-6 col-lg-3 d-flex">
                                    <div class="card border-0 bg-success text-white text-center mb-grid w-100">
                                          <div class="d-flex flex-row align-items-center h-100">
                                                <div
                                                      class="card-icon d-flex align-items-center h-100 justify-content-center">
                                                      <i class="fa fa-coins"></i>

                                                      <div class="card-info-title">Online Payment</div>
                                                </div>
                                                <div class="card-body">
                                                      <h3 class="card-title mb-0">
                                                            ₦{{ number_format($sales->sum('grandtotal')) }}

                                                      </h3>
                                                </div>
                                          </div>
                                    </div>
                              </div>

                              <div class="col-md-6 col-lg-3 d-flex">
                                    <div class="card border-0 bg-dark text-white text-center mb-grid w-100">
                                          <a href="{{ url('members') }}" class="text-decoration-none text-white">
                                                <div class="d-flex flex-row align-items-center h-100">
                                                      <div
                                                            class="card-icon d-flex align-items-center h-100 justify-content-center">
                                                            <i data-feather="users"></i>
                                                      </div>
                                                      <a class="card-body text-white text-decoration-none"
                                                            href="{{url('members') }}">
                                                            <div class="card-info-title">Members</div>
                                                            <h3 class="card-title mb-0">
                                                                  {{ $members->count() }}
                                                            </h3>
                                                      </a>
                                                </div>
                                          </a>
                                    </div>
                              </div>
                        </div>

                        <div class="row">


                              <div class="col-md-6 col-lg-3 d-flex">
                                    <div class="card border-0 bg-info text-dark text-center mb-grid w-100">
                                          <a href="{{url('request-fund') }}" class="text-decoration-none text-dark">
                                                <div class="d-flex flex-row align-items-center h-100">
                                                      <div
                                                            class="card-icon d-flex align-items-center h-100 justify-content-center">
                                                            <i class="fa fa-credit-card"></i>
                                                      </div>
                                                      <a class="card-body text-dark text-decoration-none"
                                                            href="{{url('request-fund') }}">
                                                            <div class="card-info-title">Request </div>
                                                            <h3 class="card-title mb-0">
                                                                  Fund
                                                            </h3>
                                                      </a>
                                                </div>
                                          </a>
                                    </div>
                              </div>
                        </div>
                  </div>

                  <div class="container-fluid">
                        <div class="row">
                              <p class="card-text text-end">
                                    Total payment for all approved orders
                                    <strong> ₦{{number_format($sumApproveOrder->sum('grandtotal'))}} </strong>
                                    <a href="{{ route('bank-payment') }}" class="btn btn-outline-danger btn-xs">
                                          &nbsp;Pay
                                          With Bank Transfer</a>
                              </p>
                              <div class="col-md-12">
                                    <div class="card">
                                          <div class="card-header d-flex justify-content-between align-items-center">
                                                <div class="card-header-title">All orders placed by members</div>
                                          </div>
                                          <div class="card-body collapse show tabel-resposive" id="card">

                                                <p class="text-danger">For every order approved the value will be
                                                      deducted from for "Credit"</p>

                                                <table class="table-striped table" id="orders">
                                                      <thead>
                                                            <tr class="small">
                                                                  <th>Date</th>
                                                                  <th>Member</th>
                                                                  <th>Amount</th>
                                                                  <th>Order Number</th>
                                                                  <th>Status</th>
                                                                  <th>Type</th>


                                                            </tr>
                                                      </thead>
                                                      <tbody>
                                                            @foreach($orders as $order)

                                                            <tr class="small">
                                                                  <td>{{ date('m/d/Y', strtotime($order->created_at))}}
                                                                  </td>
                                                                  <td>{{$order['fname']}} {{$order['lname']}}</td>

                                                                  <td id="amount">
                                                                        {{ number_format($order['grandtotal']) }}</td>
                                                                  <td>
                                                                        <a href="invoice/{{ $order->order_number }}"
                                                                              title="Click to view">{{$order['order_number'] }}</a>
                                                                  </td>
                                                                  <td class="">
                                                                        {{$order['status']}}
                                                                        @if($order['status'] =='approved')
                                                                        @elseif($order['status'] =='paid')
                                                                        @else
                                                                        <div class="row">
                                                                              <div class="col-md-2">
                                                                                    <!-- <form action="/order-update"
                                                                                          method="post" name="submit"> -->
                                                                                          @csrf
                                                                                          <input type="hidden"
                                                                                                name="order_id"
                                                                                                value="{{$order->id}}" id="id">
                                                                                          <input type="hidden"
                                                                                                name="order_number"
                                                                                                value="{{$order->order_number}}" id="order_number">
                                                                                          <button type="submit"
                                                                                                name="submit"
                                                                                                class="btn btn-outline-success btn-sm"
                                                                                                title="Approve" onclick="approveOrder()">
                                                                                                <i
                                                                                                      class="fa fa-check"></i></button>
                                                                                    <!-- </form> -->
                                                                              </div>
                                                                              <div class="col-md-2">
                                                                              </div>

                                                                              <div class="col-md-2">

                                                                                    <a href="{{ url('cancel-new-order/'.$order->id) }}"
                                                                                          class="btn btn-outline-danger ">
                                                                                          <i
                                                                                                class="fa fa-cancel"></i></a>


                                                                              </div>
                                                                        </div>
                                                                        @endif


                                                                  </td>


                                                                  <td>{{ $order->pay_type }}</td>
                                                            </tr>

                                                            @endforeach
                                                      </tbody>

                                                </table>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
</div>
</div>

<!-- modal-->
<!--modal-->

<script>
function myFunction() {
      var credit = document.getElementById("credit").value;
      let nf = new Intl.NumberFormat('en-US');
      nf.format(credit); // "1,234,567,890"

      var show = document.getElementById('show');
      document.getElementById('show').innerHTML = nf.format(credit);

}
</script>
<script>
function approveOrder() {
var order = document.getElementById('order_number').value;
      var answer = window.confirm("Are you sure you want to approve this order  " + order );

      if (answer) {
            var id = document.getElementById('id').value;
            var showRoute = "{{ route('order-update', ':id') }}";
            url = showRoute.replace(':id', id);
            
            window.location = url;

      } else {
            // window.location.reload();
      }
}
</script>
<script>
        $(document).ready(function() {
            $('#orders').DataTable({
                  responsive: true,

                  dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                  // dom: 'Bfrtip',
                  button: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5',
                        'pdfHtml5',
                  ],

                  aLengthMenu: [
                        [5, 10, 20, -1],
                        [5, 10, 20, "All"]
                  ],
                  iDisplayLength: 5,
                  "order": [
                        [0, "desc"]
                  ],

                  "language": {
                        "lengthMenu": "_MENU_ Records per page",
                  }


            });
      });

</script>
@endsection