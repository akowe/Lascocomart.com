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

                                                      <div class="card-info-title">Transfer</div>
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
                                          <a href="{{url('request_fund') }}" class="text-decoration-none text-dark">
                                                <div class="d-flex flex-row align-items-center h-100">
                                                      <div
                                                            class="card-icon d-flex align-items-center h-100 justify-content-center">
                                                            <i class="fa fa-credit-card"></i>
                                                      </div>
                                                      <a class="card-body text-dark text-decoration-none"
                                                            href="{{url('request_fund') }}">
                                                            <div class="card-info-title">Fund </div>
                                                            <h3 class="card-title mb-0">
                                                                  Wallet
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
                                                                                    <form action="/order-update"
                                                                                          method="post" name="submit">
                                                                                          @csrf
                                                                                          <input type="hidden"
                                                                                                name="order_id"
                                                                                                value="{{$order->id}}">
                                                                                          <button type="submit"
                                                                                                name="submit"
                                                                                                class="btn btn-outline-success btn-sm"
                                                                                                title="Approve">
                                                                                                <i
                                                                                                      class="fa fa-check"></i></button>
                                                                                    </form>
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
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
            <div class="modal-content">
                  <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Are you sure you want to Cancel this order
                        </h5>

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