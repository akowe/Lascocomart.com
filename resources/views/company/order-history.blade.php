@extends('layouts.home')

@extends('layouts.sidebar')


@section('content')

<!-- ALL CART SECTION -->
<div class="adminx-content">
      <!-- <div class="adminx-aside">

        </div> -->

      <div class="adminx-main-content">
            <div class="container-fluid">
                  <!-- container -->
                  <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb adminx-page-breadcrumb">
                              <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                              <li class="breadcrumb-item active" aria-current="page">Orders</li>
                        </ol>
                  </nav>

                  <div class="pb-3">
                        <h4>All Orders</h4>
                  </div>

                 
                  <!-- row -->
                  <div class="row">
                  <div class="col-lg-12">
                        <div class="card">
                              <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="card-header-title">Order History</div>
                              </div>
                              <div class="card-body collapse show tabel-resposive" id="card">
                                    <p class="card-text">Mark an order "Paid" only when you have collected
                                          "Physical cash or cheque". </p>
                                    <h6 class="alert-danger"> Any order you marked "Paid", the cooperative
                                          would'nt be able to pay for such order </h6>

                                    <table class="table-striped table" id="table">
                                          <thead>
                                                <tr class="small">
                                                      <th>Date</th>

                                                      <th>Cooperative</th>
                                                      <th>Amount</th>

                                                      <th>Order Number</th>
                                                      <th>Status</th>
                                                      <th>Payment</th>
                                                      <th>Type</th>

                                                </tr>
                                          </thead>
                                          <tbody>
                                                @foreach($orders as $order)
                                                <tr class="small">
                                                      <td>
                                                            {{ date('d/M/Y', strtotime($order->created_at))}}
                                                      </td>
 

                                                      <td><a href="{{ url('users_list') }}">{{$order['coopname'] }}</a>
                                                   
                                                </td>
                                                      <td>₦{{ number_format($order['grandtotal']) }}</td>

                                                      <td>
                                                            <a href="sales_invoice/{{ $order->order_number }}"
                                                                  title="Click to view">{{$order['order_number'] }}</a>
                                                      </td>
                                                      <td class="text-capitalize">{{$order['status']}}</td>

                                                      <td>
                                                            @php
                                                            $pamount = 0
                                                            @endphp
                                                            @php $ran = random_int(10000000, 99999999);
                                                            $pamount += $order['total']* 100

                                                            @endphp
                                                            @if( $order->status == 'paid' )
                                                            <span style="display:block;" class="text-success"><i
                                                                        class="fa fa-check"></i> Done</span>

                                                            <br>
                                                            <a href="order/{{ $order->order_number }}"
                                                                  class="text-sm-left">Seller
                                                                  Details</a>

                                                            @endif

                                                            @if( $order->status == 'approved' )
                                                            <p>{{$order['admin_settlement_msg'] }}</p> 
                                                            <form method="POST" action="/mark_paid"
                                                                  accept-charset="UTF-8" class="form-horizontal"
                                                                  role="form" style="display:block;">

                                                                  @csrf
                                                                  <div class="form-group">
                                                                        <input type="hidden" name="order_number"
                                                                              value="{{$order->order_number }}">
                                                                  </div>

                                                                  <button type="submit" name="submit"
                                                                        class="btn btn-outline-primary btn-xs">Mark as
                                                                        paid</button>


                                                            </form>
                                                            @endif

                                                      </td>
                                                      <td>{{$order['pay_type']}}</td>

                                                </tr>
                                                @endforeach

                                          </tbody>

                                    </table>
                                    <div class="store-filter clearfix">
                                   
                                    </div>
                              </div>
                        </div>
                  </div><!-- col-12-->

                  
                  </div>
                  <!--roww-->

            </div>

      </div> <!-- section -->

      @endsection