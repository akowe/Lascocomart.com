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
                        <h1>Products</h1>
                       <h4 class="text-end">
                        <a href="{{ route('add_new_product') }}" class="btn btn-outline-danger"><i class="fa fa-plus"></i> Add New Product</a></h4>
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
                              <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                          <div class="card-header-title">All products</div>
                                    </div>
                                    <div class="card-body collapse show tabel-resposive" id="card">
                                          <h4 class="card-title"></h4>
                                          <p class="card-text text-danger">Note that CoopMart
                                                percentage
                                                would be added to the price of each product on our
                                                landing
                                                page.</p>

                                          <table class="table-striped table" id="table2">
                                                <thead>
                                                      <tr class="small">
                                                            <th>Date</th>
                                                            <th>Product</th>
                                                            <th>Qty.</th>
                                                            <th>Old Price</th>
                                                            <th>New Price</th>
                                                            <th>Status</th>

                                                            <th>Remove Product</th>

                                                      </tr>
                                                </thead>
                                                <tbody>
                                                      @foreach($products as $product)


                                                      <tr class="small">
                                                            <td> {{ date('d/m/y', strtotime($product->created_at))}}
                                                            </td>
                                                            <td>{{$product->prod_name }}</td>
                                                            <td>{{$product->quantity }}</td>
                                                            <td>{{number_format($product->old_price )}}
                                                            </td>
                                                            <td>{{number_format($product->seller_price)}}
                                                            </td>
                                                            <td  class="text-capitalize">
                                                                  @if($product->prod_status ==
                                                                  'approve')

                                                                  <span class="text-success">
                                                                        <i class="fa fa-check"></i></span>

                                                                  @else
                                                                  @endif
                                                                  {{$product->prod_status }}
                                                            </td>
                                                            <td>
                                                                  @if($product->prod_status ==
                                                                  'pending')
                                                                  <form action="/coopremove_product" method="post"
                                                                        name="submit">
                                                                        @csrf
                                                                        <input type="hidden" name="id"
                                                                              value="{{$product->id }}">

                                                                        <input type="hidden" name="prod_status"
                                                                              value="remove">

                                                                        <button type="submit" name="submit"
                                                                              class="btn btn-outline-danger btn-sm"><i
                                                                                    class="fa fa-trash-o"></i>
                                                                              Remove</button>
                                                                  </form>
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

@endsection