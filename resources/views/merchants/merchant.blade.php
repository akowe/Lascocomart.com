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
                              <li class="breadcrumb-item"><a href="{{url('/') }}">Home</a></li>
                              <li class="breadcrumb-item active" aria-current="page">Seller</li>
                        </ol>
                  </nav>

                  <div class="pb-3">
                        <h4>Seller. Your User ID: &nbsp;{{Auth::user()->code}}. </h4>
                        <div class="pb-3">
                              <h5 class="navbar bg-dark text-white" style="padding-left: 10px;">
                                    Your Wallet Balance is: ₦{{ number_format($credit->sum('credit')) }}
                              </h5>
                        </div>
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
                        </div>

                        <div class="row">
                              <div class="col-md-6 col-lg-3 d-flex">
                                    <div class="card mb-grid w-100">
                                          <div class="card-body d-flex flex-column">
                                                <div class="d-flex justify-content-between mb-3">
                                                      <h5 class="card-title mb-0 small">
                                                            Total Approved Products
                                                      </h5>

                                                      <div class="card-title-sub">
                                                            {{ $count_product->count() }}
                                                      </div>
                                                </div>

                                                <div class="progress mt-auto">
                                                      <h6>
                                                            <a href="all-products" class="text-primary"> View all >>></a>
                                                      </h6>
                                                </div>
                                          </div>
                                    </div>
                              </div>



                              <div class="col-md-6 col-lg-3 d-flex">
                                    <div class="card mb-grid w-100">
                                          <div class="card-body d-flex flex-column">
                                                <div class="d-flex justify-content-between mb-3">
                                                      <h5 class="card-title mb-0 small">
                                                            Total Sales
                                                      </h5>

                                                      <div class="card-title-sub">
                                                            {{ $count_orders->count() }}
                                                      </div>
                                                </div>

                                                <div class="progress mt-auto">
                                                      <div class="progress-bar" role="progressbar" style="width: 75%;"
                                                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                              </div>

                              <div class="col-md-3 col-lg-3 d-flex">
                                    <div class="card border-0 bg-success text-white text-center mb-grid w-100">
                                          <div class="d-flex flex-row align-items-center h-100">
                                                <div
                                                      class="card-icon d-flex align-items-center h-100 justify-content-center">
                                                      <i class="fa fa-coins"></i>
                                                      <div class="card-info-title"> Sales</div>
                                                </div>
                                                <a href="{{url('sales_preview') }}" class="card-body text-white"
                                                      title="Click to view sales" style="text-decoration:none;">

                                                      <h3 class="card-title mb-0">

                                                            @php
                                                            $company_percentage = 0
                                                            @endphp

                                                            @php
                                                            $company_percentage += $sales->sum('seller_price') * 7/ 100;
                                                            @endphp

                                                            @php
                                                            $total_sales = 0
                                                            @endphp


                                                            @php
                                                            $total_sales += $sales->sum('seller_price') -
                                                            $company_percentage;
                                                            @endphp



                                                            ₦{{ number_format($sales->sum('seller_price')) }}

                                                      </h3>
                                                </a>
                                          </div>
                                    </div>
                              </div>

                              <div class="col-md-3 col-lg-3 d-flex">
                                    <div class="card border-0 bg-primary text-white text-center mb-grid w-100">
                                          <div class="d-flex flex-row align-items-center h-100">
                                                <div
                                                      class="card-icon d-flex align-items-center h-100 justify-content-center">
                                                      <i class="fa fa-shopping-cart"></i>
                                                      <div class="card-info-title"> Orders</div>
                                                </div>
                                                <a href="" class="card-body text-white" title="Click to view sales"
                                                      style="text-decoration:none;">

                                                      <h3 class="card-title mb-0">

                                                            @php
                                                            $company_percentage = 0
                                                            @endphp

                                                            @php
                                                            $company_percentage += $approveOrders->sum('seller_price') *
                                                            7/ 100;
                                                            @endphp

                                                            @php
                                                            $total_order = 0
                                                            @endphp


                                                            @php
                                                            $total_order += $approveOrders->sum('seller_price') -
                                                            $company_percentage;
                                                            @endphp



                                                            ₦{{ number_format($approveOrders->sum('seller_price')) }}

                                                      </h3>
                                                </a>
                                          </div>
                                    </div>
                              </div>

                        </div>
                        <!--row-->


                        <div class="row">
                              <div class="col-lg-12">
                                    <div class="card">
                                          <div class="card-header d-flex justify-content-between align-items-center">
                                                <div class="card-header-title">Order History</div>


                                          </div>
                                          <div class="card-body collapse show tabel-resposive" id="card">
                                                <p class="card-text text-danger">Note that LascocoMart percentage would
                                                      be added to the price of each product on our landing page.</p>

                                                <table class="table-striped table" id="table">
                                                      <thead>
                                                            <tr class="small">
                                                                  <th>Date</th>
                                                                  <th>Buyer</th>
                                                                  <th>Item</th>
                                                                  <th>Quantity</th>
                                                                  <th>Price</th>
                                                                  <th>Order status</th>
                                                                  <th>Payment</th>


                                                            </tr>
                                                      </thead>
                                                      <tbody>
                                                            @foreach($orders as $order)


                                                            <tr class="small">
                                                                  <td> {{ date('m-d-Y', strtotime($order->created_at))}}
                                                                  </td>
                                                                  <td>{{$order->fname }}</td>
                                                                  <td>{{$order->prod_name }}</td>
                                                                  <td>{{$order->order_quantity }}</td>
                                                                  <td>{{number_format($order->seller_price)}}</td>
                                                                  <td>
                                                                        @if($order->status == 'approved')
                                                                        <span class="text-success">
                                                                              <i class="fa fa-check"></i></span>
                                                                        @else
                                                                        @endif
                                                                        {{$order->status }}
                                                                  </td>
                                                                  <td>
                                                                        @if($order->status == 'approved')
                                                                        {{$order->admin_settlement_msg }}
                                                                        @else
                                                                        @endif
                                                                  </td>




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
                  </div>
            </div>
      </div>
</div>



<!-- remove Modal -->

<div class="modal fade" id="pModal" tabindex="-1" role="dialog" aria-labelledby="pModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
            <div class="modal-content">
                  <div class="modal-header">
                        <h5 class="modal-title" id="pModalLabel">Are you sure want to remove this product?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                        </button>

                        <p></p>

                  </div>
                  <form action="/remove_product" method="post" name="submit">
                        @csrf
                        <div class="modal-body">
                              <div class="row mb-3">

                                    <input type="hidden" name="id" value="">

                                    <input type="hidden" name="prod_status" value="remove">
                              </div>


                        </div>
                        <div class="modal-footer">
                              <button type="submit" name="submit" class="btn btn-outline-danger btn-sm"><i
                                          class="fa fa-trash-o"></i> Yes. Remove</button>

                              <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">No</button>

                        </div>
                  </form>
            </div>
      </div>
</div>
</div>
<!--remove modal end-->

@endsection