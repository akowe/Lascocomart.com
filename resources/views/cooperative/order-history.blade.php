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
                                                            <td>{{ date('d/M/Y', strtotime($order->created_at))}}
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
                                                                              <form action="/order-update" method="post"
                                                                                    name="submit">
                                                                                    @csrf
                                                                                    <input type="hidden" name="order_id"
                                                                                          value="{{$order->id}}">
                                                                                    <button type="submit" name="submit"
                                                                                          class="btn btn-outline-success btn-sm"
                                                                                          title="Approve">
                                                                                          <i
                                                                                                class="fa fa-check"></i></button>
                                                                              </form>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                        </div>

                                                                        <div class="col-md-2">

                                                                              <button type="button"
                                                                                    class="btn btn-outline-danger "
                                                                                    data-toggle="modal"
                                                                                    data-target="#exampleModal">
                                                                                    <i class="fa fa-cancel"></i>
                                                                              </button>
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
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
            <div class="modal-content">
                  <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Are you sure you want to Cancel this order</h5>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                        </button>
                  </div>
                  <div class="modal-body">
                        <p> <span>Order number: 
                                    @foreach($orders as $order)
                                    {{$order->order_number}}
                                    @endforeach
                              </span></p>
                        <form action="/order-cancel" method="post" name="submit">
                              @csrf
                              @foreach($orders as $order)
                              <input type="hidden" name="order_id" value="{{$order->id}}">
                              @endforeach
                              <div class="form-group">
                                    <label for="">Enter <strong>
                                                @foreach($orders as $order)
                                                {{$order->fname}}'s
                                                @endforeach
                                          </strong> current credit worth
                                    </label>
                                    <input type="number" name="credit" value="" placeholder="Enter Amount" required
                                          class="form-control" id="credit" onkeyup="myFunction()">
                                    <span class="text-danger text-xs">enter the figures without comma.</span>
                                    <h4> ₦ <span id="show"></span></h4>
                              </div>
                              <button type="submit" name="submit" class="btn btn-outline-danger btn-xs" title="Cancel">
                                    Cancel Now</button>
                        </form>
                  </div>
                  <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
            </div>
      </div>
</div>
<script>
function myFunction() {
      var credit = document.getElementById("credit").value;
      let nf = new Intl.NumberFormat('en-US');
      nf.format(credit); // "1,234,567,890"

      var show = document.getElementById('show');
      document.getElementById('show').innerHTML = nf.format(credit);

}
</script>
@endsection