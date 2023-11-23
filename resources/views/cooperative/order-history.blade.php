@extends('layouts.home')

@extends('layouts.sidebar')

@section('content')
<div class="adminx-content">
      <div class="adminx-main-content">
            <div class="container-fluid">
                  <!-- BreadCrumb -->
                  <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb adminx-page-breadcrumb">
                              <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                              <li class="breadcrumb-item active" aria-current="page">Cooperative</li>
                        </ol>
                  </nav>

                  <div class="pb-3">
                        <h1>Order History</h1>
                        </h5>

                        <h5 class="navbar bg-dark text-white" style="padding-left: 10px;">
                              Credit Balance is: ₦{{ number_format($credit->sum('credit')) }}
                        </h5>
                  </div>
            </div>
            <div class="container-fluid">
                  <div class="row">
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
            </div>

            <div class="container-fluid">
                  <div class="row">
                        <div class="col-lg-12">
                              <p class="card-text">
                                    Total payment for all approved orders
                                    <strong> ₦{{number_format($sumApproveOrder->sum('grandtotal'))}} </strong>
                                    <a href="{{ route('bank-payment') }}" class="btn btn-outline-danger btn-xs">
                                          &nbsp;Pay
                                          With Bank Transfer</a>
                              </p>
                              <p class="text-danger">For every order approved the value will be deducted from for "Credit"</p>

                              <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                          <div class="card-header-title">All orders placed by members</div>
                                    </div>
                                    <div class="card-body collapse show tabel-resposive" id="card">
                                          <p class="card-text"></p>

                                          <table class="table-striped table" id="table3">
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
                                          <div class="store-filter clearfix">

                                          </div>
                                    </div>
                              </div>
                        </div>

                  </div>
            </div>
      </div>
</div>

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
@endsection