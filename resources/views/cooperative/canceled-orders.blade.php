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
                        <h1>Canceled Orders</h1>
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
                  </div>
            </div>

            <div class="container-fluid">
                  <div class="row">
                        <div class="col-lg-12">
                              <p class="card-text text-danger">
                                    <strong>Any order that is approve can not be "canceled"</strong>
                              </p>

                              <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                          <div class="card-header-title">All canceled orders</div>
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
                                                            <th></th>

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
                                                            <td class="">{{$order['status']}}
                                                                  <form action="/order-update" method="post"
                                                                        name="submit">
                                                                        @csrf
                                                                        <input type="hidden" name="order_id"
                                                                              value="{{$order->id}}">
                                                                        <button type="submit" name="submit"
                                                                              class="btn btn-outline-success btn-sm"
                                                                              title="Approve">
                                                                              <i class="fa fa-check"></i></button>
                                                                  </form>

                                                            </td>

                                                            <td>
                                                                  @if( $order->status == 'paid' )
                                                                  <span style="display:block;" class="text-success"><i
                                                                              class="fa fa-check"></i>
                                                                  </span>
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


@endsection