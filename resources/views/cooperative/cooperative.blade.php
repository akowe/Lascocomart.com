@extends('layouts.home')
@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
      <div class="container-xl">
            <div class="row g-2 align-items-center">
                  <div class="col">
                        <!-- Page pre-title -->
                        <div class="page-pretitle">
                              Dashboard
                        </div>
                        <h2 class="page-title">
                              <span class=" d-none  d-md-block">Cooperative ID:&nbsp;</span> {{Auth::user()->code}}

                              <a href="" alt="Copy" title="Copy" class="text-danger"
                                    onclick="copyToClipboard('{{Auth::user()->code}}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-copy  "
                                          width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5"
                                          stroke="currentColor" fill="none" stroke-linecap="round"
                                          stroke-linejoin="round">
                                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                          <path
                                                d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" />
                                          <path
                                                d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" />
                                    </svg></a>
                        </h2>
                  </div>
                  <!-- Page title actions -->
                  <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                              <span class="d-block ">
                                  
                                    <div class="input-group " id="show_hide_wallet">
                                          <span class="input-group-text">
                                                Wallet
                                          </span>
                                          <input type="password" value="₦ " class="btn" style="width:140px; color:#00;" disabled>
                                          <span class="input-group-text">
                                                <a href="" class="text-secondary">
                                                      <i class="fa fa-eye-slash"></i>
                                                </a>
                                          </span>
                                    </div>
                              </span>
                              <a href="#" class="btn btn-danger d-none d-sm-inline-block" data-bs-toggle="modal"
                                    data-bs-target="#modal-wallet">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                          viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                          stroke-linecap="round" stroke-linejoin="round">
                                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                          <path d="M12 5l0 14" />
                                          <path d="M5 12l14 0" />
                                    </svg>
                                    Fund Wallet
                              </a>
                              <a href="#" class="btn btn-danger d-sm-none btn-icon"  data-bs-toggle="modal"
                                    data-bs-target="#modal-wallet">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                          viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                          stroke-linecap="round" stroke-linejoin="round">
                                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                          <path d="M12 5l0 14" />
                                          <path d="M5 12l14 0" />
                                    </svg>
                              </a>
                        </div>
                  </div>
            </div>
      </div>
</div>

<!-- Page body -->
<div class="page-body">
      <div class="container-xl">
            <div class="row row-deck row-cards">
                  <div class="col-12">
                        <div class="row row-cards">
                              <div class="col-sm-6 col-lg-3">
                                    <div class="card card-sm">
                                          <div class="card-body">
                                                <div class="row align-items-center">
                                                      <div class="col-auto">

                                                            <span class="bg-success text-white avatar">
                                                                  <a href="{{ url('cooperative-sales')}}"
                                                                        class="text-white ">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                              class="icon icon-tabler icon-tabler-currency-naira"
                                                                              width="24" height="24" viewBox="0 0 24 24"
                                                                              stroke-width="1.5" stroke="currentColor"
                                                                              fill="none" stroke-linecap="round"
                                                                              stroke-linejoin="round">
                                                                              <path stroke="none" d="M0 0h24v24H0z"
                                                                                    fill="none" />
                                                                              <path
                                                                                    d="M7 18v-10.948a1.05 1.05 0 0 1 1.968 -.51l6.064 10.916a1.05 1.05 0 0 0 1.968 -.51v-10.948" />
                                                                              <path d="M5 10h14" />
                                                                              <path d="M5 14h14" />
                                                                        </svg>
                                                                  </a>

                                                            </span>
                                                      </div>
                                                      <div class="col">
                                                            <div class="font-weight-medium">
                                                                  {{ number_format($sales->sum('seller_price')) }}
                                                                  Sales
                                                            </div>
                                                            <div class="text-secondary">
                                                                  {{ $countSoldProducts->count() }} products
                                                            </div>
                                                      </div>
                                                      <div class="col-auto">
                                                            <div class="dropdown">
                                                                  <a class="text-secondary" href="#"
                                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                                        aria-expanded="false">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                              class="icon icon-tabler icon-tabler-dots-vertical"
                                                                              width="24" height="24" viewBox="0 0 24 24"
                                                                              stroke-width="1.5" stroke="currentColor"
                                                                              fill="none" stroke-linecap="round"
                                                                              stroke-linejoin="round">
                                                                              <path stroke="none" d="M0 0h24v24H0z"
                                                                                    fill="none" />
                                                                              <path
                                                                                    d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                              <path
                                                                                    d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                              <path
                                                                                    d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                        </svg>
                                                                  </a>
                                                                  <div class="dropdown-menu dropdown-menu-end">
                                                                        <a class="dropdown-item active"
                                                                              href="{{ url('cooperative-sales') }}">Last
                                                                              7
                                                                              days</a>
                                                                        <a class="dropdown-item"
                                                                              href="{{ url('cooperative-sales') }}">Last
                                                                              30
                                                                              days</a>
                                                                        <a class="dropdown-item"
                                                                              href="{{ url('cooperative-sales') }}">Last
                                                                              3
                                                                              months</a>
                                                                  </div>
                                                            </div>
                                                      </div>

                                                </div>
                                          </div>
                                    </div>
                              </div>


                              <div class="col-sm-6 col-lg-3">
                                    <div class="card card-sm">
                                          <div class="card-body">
                                                <div class="row align-items-center">
                                                      <div class="col-auto">
                                                            <span class="bg-primary text-white avatar">
                                                                  <a href="{{ url('admin-member-order') }}"
                                                                        class="text-white" cursor>
                                                                        <!-- Download SVG icon from http://tabler-icons.io/i/shopping-cart -->
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                              class="icon" width="24" height="24"
                                                                              viewBox="0 0 24 24" stroke-width="2"
                                                                              stroke="currentColor" fill="none"
                                                                              stroke-linecap="round"
                                                                              stroke-linejoin="round">
                                                                              <path stroke="none" d="M0 0h24v24H0z"
                                                                                    fill="none" />
                                                                              <path
                                                                                    d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                                              <path
                                                                                    d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                                              <path d="M17 17h-11v-14h-2" />
                                                                              <path d="M6 5l14 1l-1 7h-13" />
                                                                        </svg>
                                                                  </a>
                                                            </span>
                                                      </div>
                                                      <div class="col">
                                                            <div class="font-weight-medium">

                                                                  {{ $memberOrders->count() }} Member
                                                                  Order(s)
                                                            </div>

                                                            <div class="text-secondary">
                                                                  {{  $sumApproveOrder->count() }} approved
                                                            </div>
                                                      </div>
                                                      <div class="col-auto">
                                                            <div class="dropdown">
                                                                  <a class="text-secondary" href="#"
                                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                                        aria-expanded="false">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                              class="icon icon-tabler icon-tabler-dots-vertical"
                                                                              width="24" height="24" viewBox="0 0 24 24"
                                                                              stroke-width="1.5" stroke="currentColor"
                                                                              fill="none" stroke-linecap="round"
                                                                              stroke-linejoin="round">
                                                                              <path stroke="none" d="M0 0h24v24H0z"
                                                                                    fill="none" />
                                                                              <path
                                                                                    d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                              <path
                                                                                    d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                              <path
                                                                                    d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                        </svg>
                                                                  </a>
                                                                  <div class="dropdown-menu dropdown-menu-end">
                                                                        <a class="dropdown-item active"
                                                                              href="{{ url('admin-member-order') }}">
                                                                              Last 7 days
                                                                        </a>
                                                                        <a class="dropdown-item"
                                                                              href="{{ url('admin-member-order') }}">Last
                                                                              30 days
                                                                        </a>
                                                                        <a class="dropdown-item"
                                                                              href="{{ url('admin-order-history') }}">Last
                                                                              3 months</a>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                              </div>

                              <div class="col-sm-6 col-lg-3">
                                    <div class="card card-sm">
                                          <div class="card-body">
                                                <div class="row align-items-center">
                                                      <div class="col-auto">
                                                            <span class="bg-yellow text-white avatar">
                                                                  <a href="{{url('admin-products')}}"
                                                                        class="text-white">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                              class="icon icon-tabler icon-tabler-brand-producthunt"
                                                                              width="24" height="24" viewBox="0 0 24 24"
                                                                              stroke-width="1.5" stroke="currentColor"
                                                                              fill="none" stroke-linecap="round"
                                                                              stroke-linejoin="round">
                                                                              <path stroke="none" d="M0 0h24v24H0z"
                                                                                    fill="none" />
                                                                              <path
                                                                                    d="M10 16v-8h2.5a2.5 2.5 0 1 1 0 5h-2.5" />
                                                                              <path
                                                                                    d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                                        </svg></a>

                                                            </span>
                                                      </div>
                                                      <div class="col">
                                                            <div class="font-weight-medium">
                                                                  {{ $count_product->count() }} Products
                                                            </div>
                                                            <div class="text-secondary">
                                                                  {{ $countApprovedProduct->count() }} approved
                                                            </div>
                                                      </div>
                                                      <div class="col-auto">
                                                            <div class="dropdown">
                                                                  <a class="text-secondary" href="#"
                                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                                        aria-expanded="false">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                              class="icon icon-tabler icon-tabler-dots-vertical"
                                                                              width="24" height="24" viewBox="0 0 24 24"
                                                                              stroke-width="1.5" stroke="currentColor"
                                                                              fill="none" stroke-linecap="round"
                                                                              stroke-linejoin="round">
                                                                              <path stroke="none" d="M0 0h24v24H0z"
                                                                                    fill="none" />
                                                                              <path
                                                                                    d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                              <path
                                                                                    d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                              <path
                                                                                    d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                        </svg>
                                                                  </a>
                                                                  <div class="dropdown-menu dropdown-menu-end">
                                                                        <a class="dropdown-item active"
                                                                              href="{{ url('admin-products') }}">Last 7
                                                                              days</a>
                                                                        <a class="dropdown-item"
                                                                              href="{{ url('admin-products') }}">Last 30
                                                                              days</a>
                                                                        <a class="dropdown-item"
                                                                              href="{{ url('admin-products') }}">Last 3
                                                                              months</a>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                              </div>

                              <div class="col-sm-6 col-lg-3">
                                    <div class="card card-sm">
                                          <div class="card-body">
                                                <div class="row align-items-center">
                                                      <div class="col-auto">
                                                            <span class="bg-azure text-white avatar">
                                                                  <a href="{{ url('cooperative-loan') }}" class="text-white">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                              class="icon icon-tabler icon-tabler-coins"
                                                                              width="24" height="24" viewBox="0 0 24 24"
                                                                              stroke-width="1.5" stroke="currentColor"
                                                                              fill="none" stroke-linecap="round"
                                                                              stroke-linejoin="round">
                                                                              <path stroke="none" d="M0 0h24v24H0z"
                                                                                    fill="none" />
                                                                              <path
                                                                                    d="M9 14c0 1.657 2.686 3 6 3s6 -1.343 6 -3s-2.686 -3 -6 -3s-6 1.343 -6 3z" />
                                                                              <path
                                                                                    d="M9 14v4c0 1.656 2.686 3 6 3s6 -1.344 6 -3v-4" />
                                                                              <path
                                                                                    d="M3 6c0 1.072 1.144 2.062 3 2.598s4.144 .536 6 0c1.856 -.536 3 -1.526 3 -2.598c0 -1.072 -1.144 -2.062 -3 -2.598s-4.144 -.536 -6 0c-1.856 .536 -3 1.526 -3 2.598z" />
                                                                              <path d="M3 6v10c0 .888 .772 1.45 2 2" />
                                                                              <path d="M3 11c0 .888 .772 1.45 2 2" />
                                                                        </svg>
                                                                  </a>
                                                            </span>
                                                      </div>
                                                      <div class="col">
                                                            <div class="font-weight-medium">
                                                                  {{$loan->count()}} Loans
                                                            </div>
                                                            <div class="text-secondary">
                                                                  {{$payOutLoan->count()}} payout
                                                            </div>
                                                      </div>
                                                      <div class="col-auto">
                                                            <div class="dropdown">
                                                                  <a class="text-secondary" href="#"
                                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                                        aria-expanded="false">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                              class="icon icon-tabler icon-tabler-dots-vertical"
                                                                              width="24" height="24" viewBox="0 0 24 24"
                                                                              stroke-width="1.5" stroke="currentColor"
                                                                              fill="none" stroke-linecap="round"
                                                                              stroke-linejoin="round">
                                                                              <path stroke="none" d="M0 0h24v24H0z"
                                                                                    fill="none" />
                                                                              <path
                                                                                    d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                              <path
                                                                                    d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                              <path
                                                                                    d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                        </svg>
                                                                  </a>
                                                                  <div class="dropdown-menu dropdown-menu-end">
                                                                        <a class="dropdown-item active"
                                                                              href="{{ url('cooperative-loan') }}">Last
                                                                              7
                                                                              days</a>
                                                                        <a class="dropdown-item"
                                                                              href="{{ url('cooperative-loan') }}">Last
                                                                              30
                                                                              days</a>
                                                                        <a class="dropdown-item"
                                                                              href="{{ url('cooperative-loan') }}">Last
                                                                              3
                                                                              months</a>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                              </div>

                        </div>
                        <!---- row-cards --->
                  </div>
                  <!---col-12 --->
                  <div class="col-sm-8 col-lg-8">
                        <div class="card ">
                              <div class="card-body">
                                    <div class="d-flex align-items-center">
                                          <div class="subheader">Total Members</div>
                                          <div class="ms-auto lh-1">
                                                <div class="dropdown">
                                                      <a class="dropdown-toggle text-secondary" href="#"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">Last 7 days</a>
                                                      <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item active"
                                                                  href="{{ url('members') }}">Last 7 days</a>
                                                            <a class="dropdown-item" href="{{ url('members') }}">Last 30
                                                                  days</a>
                                                            <a class="dropdown-item" href="{{ url('members') }}">Last 3
                                                                  months</a>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                                    <div class="h1 mb-3">{{ $members->count() }}</div>
                                    <div class="d-flex mb-2">
                                          <div>active member (s)</div>
                                          <div class="ms-auto">
                                                @if($adminActiveMember->count() > 0)
                                                <span class="text-green d-inline-flex align-items-center lh-1">
                                                      {{$adminActiveMember->count()}}
                                                      <!-- Download SVG icon from http://tabler-icons.io/i/trending-up -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1"
                                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M3 17l6 -6l4 4l8 -8" />
                                                            <path d="M14 7l7 0l0 7" />
                                                      </svg>
                                                </span>
                                                @else
                                                <span class="text-danger d-inline-flex align-items-center lh-1">
                                                      {{$adminActiveMember->count()}}
                                                      <!-- Download SVG icon from http://tabler-icons.io/i/trending-down -->
                                                      <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="icon icon-tabler icon-tabler-trending-down"
                                                            width="24" height="24" viewBox="0 0 24 24"
                                                            stroke-width="1.5" stroke="currentColor" fill="none"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M3 7l6 6l4 -4l8 8" />
                                                            <path d="M21 10l0 7l-7 0" />
                                                      </svg>
                                                </span>
                                                @endif
                                          </div>
                                    </div>
                                    <div class="progress progress-sm">
                                          <div class="progress-bar bg-primary"
                                                style="width: {{$adminActiveMember->count()}}%" role="progressbar"
                                                aria-valuenow="{{$adminActiveMember->count()}}" aria-valuemin="0"
                                                aria-valuemax="100"
                                                aria-label="{{$adminActiveMember->count()}}% Complete">
                                                <span class="visually-hidden">{{$adminActiveMember->count()}}%
                                                      Complete</span>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>


                  <div class="col-sm-4 col-lg-4">
                        <div class="card">
                              <div class="card-body">
                                    <div class="d-flex align-items-center">
                                          <div class="subheader">Approved Order (s)</div>
                                          <div class="ms-auto lh-1">
                                                <div class="dropdown">
                                                      <a class="dropdown-toggle text-secondary" href="#"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">Last 7 days</a>
                                                      <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item active"
                                                                  href="{{ url('admin-member-order') }}">Last 7
                                                                  days</a>
                                                            <a class="dropdown-item"
                                                                  href="{{ url('admin-member-order') }}">Last 30
                                                                  days</a>
                                                            <a class="dropdown-item"
                                                                  href="{{ url('admin-member-order') }}">Last 3
                                                                  months</a>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                                    <div class="h1 mb-3">
                                          ₦{{number_format($sumApproveOrder->sum('grandtotal'))}}
                                    </div>
                                    <div class="d-flex mb-2">
                                          <div> Payment for each member order you approved</div>
                                          <div class="ms-auto">
                                          </div>
                                    </div>
                                    <div class="card">
                                          <a href="{{ route('bank-payment') }}" class="btn btn-danger btn-xs">
                                                &nbsp;Pay Now</a>
                                    </div>
                              </div>

                        </div>
                  </div>
                  <!-- Alert start --->
                  <div class="container-xl">
                        <div class="row ">
                              <div class="col-12">
                                    <p></p>
                                    <!-- error aleart for request credit -->
                                    @error('amount')
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                          <div class="d-flex">
                                                <div>
                                                      <!-- Download SVG icon from http://tabler-icons.io/i/alert-circle -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon"
                                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                            <path d="M12 8v4" />
                                                            <path d="M12 16h.01" />
                                                      </svg>
                                                </div>
                                                <div>
                                                      {{ $message }}
                                                </div>
                                          </div>
                                          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                                    </div>
                                    @enderror

                                    @if(session('profile'))
                                    <div class="alert  alert-yellow alert-dismissible" role="alert">
                                          <div class="d-flex">
                                                <div>
                                                      <!-- Download SVG icon from http://tabler-icons.io/i/alert-triangle -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon"
                                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                  d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z" />
                                                            <path d="M12 9v4" />
                                                            <path d="M12 17h.01" />
                                                      </svg>


                                                </div>
                                                <div><a href="{{url('account-settins') }}" class="cursor"> {!!
                                                            session('profile') !!}</a></div>
                                          </div>
                                          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                                    </div>
                                    @endif

                                    @if(session('success'))
                                    <div class="alert alert-important alert-success alert-dismissible" role="alert">
                                          <div class="d-flex">
                                                <div>
                                                      <!-- Download SVG icon from http://tabler-icons.io/i/check -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon"
                                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M5 12l5 5l10 -10" />
                                                      </svg>

                                                </div>
                                                <div>{!! session('success') !!}</div>
                                          </div>
                                          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                                    </div>
                                    @endif

                                    @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                          <div class="d-flex">
                                                <div>
                                                      <!-- Download SVG icon from http://tabler-icons.io/i/alert-circle -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon"
                                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                            <path d="M12 8v4" />
                                                            <path d="M12 16h.01" />
                                                      </svg>


                                                </div>
                                                <div>{!! session('error') !!}</div>
                                          </div>
                                          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                                    </div>
                                    @endif
                              </div>
                        </div>
                  </div>
                  <!-- Alert stop --->
                  <!-- advert side --->
                  <div class="col-md-12 col-lg-12">
                        <div class="card">
                              <div class="card-header">
                                    <h3 class="card-title">Wallet Transaction (s) </h3>
                              </div>
                              <div class="card-body border-bottom py-3">
                                    <div class="d-flex">
                                          <div class="text-secondary">
                                                Show
                                                <div class="mx-2 d-inline-block">
                                                      <select id="pagination" class="form-control form-control-sm"
                                                            name="perPage">
                                                            <option value="5" @if($perPage==5) selected @endif>5
                                                            </option>
                                                            <option value="10" @if($perPage==10) selected @endif>10
                                                            </option>
                                                            <option value="25" @if($perPage==25) selected @endif>25
                                                            </option>
                                                            <option value="50" @if($perPage==50) selected @endif>50
                                                            </option>
                                                      </select>
                                                </div>
                                                records
                                          </div>
                                          <div class="ms-auto text-secondary">
                                                <!--search text here -->
                                                Search:
                                                <div class="ms-2 d-inline-block">
                                                      <form action="/cooperative" method="GET" role="search">
                                                            {{ csrf_field() }}
                                                            <div class="input-group mb-2">
                                                                  <input type="text" class="form-control"
                                                                        placeholder="Search for…" name="search">
                                                                  <button type="submit" class="btn"
                                                                        type="button">Go!</button>
                                                            </div>
                                                      </form>
                                                </div>
                                          </div>
                                    </div>
                              </div>

                              <div class="table-responsive" id="card">
                                    <table class="table card-table table-vcenter text-nowrap datatable" id="orders">
                                          <thead>
                                                <tr>
                                                      <th class="w-1"><input class="form-check-input m-0 align-middle"
                                                                  type="checkbox" aria-label="Select all product"></th>
                                                      <th class="w-1">Date
                                                            <!-- Download SVG icon from http://tabler-icons.io/i/chevron-up -->
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                  class="icon icon-sm icon-thick" width="24" height="24"
                                                                  viewBox="0 0 24 24" stroke-width="2"
                                                                  stroke="currentColor" fill="none"
                                                                  stroke-linecap="round" stroke-linejoin="round">
                                                                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                  <path d="M6 15l6 -6l6 6" />
                                                            </svg>
                                                      </th>


                                                      <th>Ref</th>
                                                      <th>Amount</th>
                                                      <th>Status</th>
                                                      <th>Reciever</th>
                                                      <th></th>
                                                </tr>
                                          </thead>
                                          <tbody>
                                                @foreach($wallets as $wallet)
                                                <tr>
                                                      <td><input class="form-check-input m-0 align-middle"
                                                                  type="checkbox" aria-label="Select"></td>
                                                      <td><span class="text-secondary">
                                                                  {{ date('m/d/Y', strtotime($wallet->created_at))}}</span>
                                                      </td>

                                                      <td>{{$wallet->paymemnt_ref}}</td>
                                                      <td>{{number_format($wallet->amount)}}</td>
                                                      <td>{{$wallet->payment_status}}</td>
                                                      <td>{{$wallet->reciever}}</td>
                                                      <td class="">
                                                            @if($wallet->payment_status =='debit')
                                                            <span
                                                                  class="badge bg-red-lt">{{$wallet->payment_status}}</span>

                                                            @elseif($wallet->payment_status =='credit')
                                                            <span
                                                                  class="badge bg-green-lt">{{$wallet->payment_status}}</span>
                                                            @else
                                                            @endif

                                                      </td>
                                                      <td class="text-end">
                                                            <span class="dropdown">
                                                                  <button
                                                                        class="btn dropdown-toggle align-text-top text-red"
                                                                        data-bs-boundary="viewport"
                                                                        data-bs-toggle="dropdown">Actions</button>
                                                                  <div class="dropdown-menu dropdown-menu-sm-start">
                                                                        <a class="dropdown-item"
                                                                              href="admin-reciept/{{$wallet->id}}">
                                                                              View
                                                                        </a>

                                                                  </div>
                                                            </span>
                                                      </td>


                                                </tr>
                                                @endforeach

                                          </tbody>

                                    </table>
                              </div>
                              <div class="card-footer d-flex align-items-center">
                                    <p class="m-0 text-secondary">

                                          Showing
                                          {{ ($wallets->currentPage() - 1) * $wallets->perPage() + 1; }} to
                                          {{ min($wallets->currentPage()* $wallets->perPage(), $wallets->total()) }}
                                          of
                                          {{$wallets->total()}} entries
                                    </p>

                                    <ul class="pagination m-0 ms-auto">
                                          @if(isset($wallets))
                                          @if($wallets->currentPage() > 1)
                                          <li class="page-item ">
                                                <a class="page-link text-danger"
                                                      href="{{ $wallets->previousPageUrl() }}" tabindex="-1"
                                                      aria-disabled="true">
                                                      <!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M15 6l-6 6l6 6" />
                                                      </svg>
                                                      prev
                                                </a>
                                          </li>
                                          @endif


                                          <li class="page-item">
                                                {{ $wallets->appends(compact('perPage'))->links()  }}
                                          </li>
                                          @if($wallets->hasMorePages())
                                          <li class="page-item">
                                                <a class="page-link text-danger" href="{{ $wallets->nextPageUrl() }}">
                                                      next
                                                      <!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M9 6l6 6l-6 6" />
                                                      </svg>
                                                </a>
                                          </li>
                                          @endif
                                          @endif
                                    </ul>
                              </div>
                        </div>
                        <!--- card-->

                  </div>
                  <!---- col-12 --->


            </div>
            <!--row --->
      </div>
      <!---container --->
</div>
<!---page body --->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
document.getElementById('pagination').onchange = function() {
      window.location = "{!! $wallets->url(1) !!}&perPage=" + this.value;
};
</script>
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