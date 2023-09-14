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
                              <li class="breadcrumb-item active" aria-current="page">Users</li>
                        </ol>
                  </nav>

                  <div class="pb-3 bg-dark text-center">
                        <h5>&nbsp;</h5>
                        <h4 class="text-white">All Users</h4>
                  </div>
                  @if(Session::has('credit')== true)
                  <!--show alert-->
                  <p class="alert {{ Session::get('alert-class', 'alert-info') }} text-center">
                        {{ Session::get('credit') }}</p>
                  @endif

                  @if(Session::has('credit')== false)
                  <!--show alert-->
                  <p style="display: none;">{{ Session::get('credit') }}</p>
                  @endif

                  <!-- alert if member is not verified-->
                  @if(Session::has('verified')== true)
                  <!--New registration alert-->
                  <p class="alert {{ Session::get('alert-class', 'alert-info') }} text-center">
                        {{ Session::get('verified') }}</p>
                  @endif

                  @if(Session::has('verified')== false)
                  <!--show alert-->
                  <p style="display: none;">{{ Session::get('verified') }}</p>
                  @endif
                  <p></p>
                  <div class="pb-3">
                  <h4>Cooperatives</h4>
                  </div>
                  <!-- row -->
                  <div class="row">
                        <div class="col-lg-12 table-responsive">
                              <table class="table table-striped " id="table">
                                    <thead>
                                          <tr>
                                                <th>Edit</th>
                                                <th>Cooperative</th>
                                                <th>Email</th>
                                                <th>Contact person</th>
                                                <th>Credit</th>
                                          </tr>
                                    </thead>
                                    <tbody>

                                          @foreach($coop as $details)
                                          <tr>

                                          <td><a href="user_edit/{{$details->id}}" class="text-danger"> <i
                                                                  class="fa fa-edit"></i></a></td>      
                                          <td> <span class="text-capitalize">{{ $details['coopname'] }} </span></td>
                                                <td><span class="text-lowercase"> {{ $details['email'] }}</span></td>
                                                <td><span class="text-capitalize">{{ $details['fname'] }} &nbsp; {{ $details['lname'] }}</span>
                                                      <br>
                                                      <span class="text-lowercase">{{ $details['address'] }}</span>
                                                      <br>
                                                      {{ $details['location'] }}
                                                      <br>
                                                      {{ $details['phone'] }}
                                                </td>
                                                <!--  <td >{{ $details['code'] }}</td> -->

                                                <td>{{number_format($details['credit'])  }}</td>

                                            
                                          </tr>

                                          @endforeach
                                    </tbody>
                              </table>

                        </div>
                        <!--col 12-->
                        {{ $coop->links() }}
                  </div>
                  <!--roww-->

            </div>
            <br>
            <hr>
            <br>
            <div class="container-fluid">
                  <!-- container -->
                  <div class="pb-3">
                        <h4>Members of cooperatives</h4>
                  </div>

                  <!-- row -->
                  <div class="row">
                        <div class="col-lg-12 table-responsive">
                              <table class="table table-striped " id="table2">
                                    <thead>
                                          <tr>
                                                <th>Edit</th>
                                                <th>Member Name</th>
                                                <th>Email</th>
                                                <th>Cooperative</th>
                                                <th>Address</th>
                                          </tr>
                                    </thead>
                                    <tbody>

                                          @foreach($members as $details)
                                          <tr>

                                          <td><a
                                                            href="user_edit/{{$details->id}}" class="text-danger"> <i
                                                                  class="fa fa-edit"></i> </a></td>      
                                          <td><span class="text-capitalize">{{ $details['fname'] }} &nbsp; {{ $details['lname'] }}</span> </td>

                                                <td> <span class="text-lowercase">{{ $details['email'] }}</span></td>

                                                <td><span class="text-capitalize">{{ $details['coopname'] }} </span></td>
                                                <td> <span class="text-lowercase">{{ $details['address'] }}</span>
                                                      <br>
                                                      {{ $details['location'] }}
                                                      <br>

                                                      {{ $details['phone'] }}
                                                </td>

                                          </tr>

                                          @endforeach



                                    </tbody>
                              </table>

                        </div>
                        <!--col 12-->

                  </div>
                  <!--roww-->
            </div>

            <br>
            <hr>
            <br>
            <div class="container-fluid">
                  <!-- container -->
                  <div class="pb-3">
                        <h4>FMCG distributors</h4>

                  </div>

                  <!-- row -->
                  <div class="row">
                        <div class="col-lg-12  table-responsive">
                              <table class="table table-striped " id="table3">
                                    <thead>
                                          <tr>
                                                <th>Edit</th>
                                                <th>FMCG </th>
                                                <th>Email</th>
                                                <th>Contact Person</th>
                                                <th>Details</th>
                                          </tr>
                                    </thead>
                                    <tbody>

                                          @foreach($fcmg as $details)
                                          <tr>
                                                <td><a href="user_edit/{{$details->id}}" class="text-danger"> 
                                                  <i class="fa fa-edit"></i> Edit</a></td>
                                                <td><span class="text-capitalize">{{ $details['coopname'] }}</span> 
                                                </td>
                                                <td> <span class="text-lowercase">{{ $details['email'] }}</span></td>
                                                <td><span class="text-capitalize">{{ $details['fname'] }} &nbsp;{{ $details['lname'] }}</span> </td>
                                                <td> <span class="text-lowercase">{{ $details['address'] }}</span>
                                                  <br>{{ $details['phone'] }}<br>{{ $details['location'] }}
                                                </td>

                                          </tr>

                                          @endforeach



                                    </tbody>
                              </table>

                        </div>
                        <!--col 12-->

                  </div>
                  <!--roww-->
            </div>
            <br>
            <hr>
            <br>
            <div class="container-fluid">
                  <!-- container -->
                  <div class="pb-3">
                        <h4>Sellers</h4>
                  </div>

                  <!-- row -->
                  <div class="row">
                        <div class="col-lg-12  table-responsive">
                              <table class="table table-striped " id="table4">
                                    <thead>
                                          <tr>
                                                <th>Edit</th>
                                                <th>Store Name</th>
                                                <th>Email</th>
                                                <th>Contact Person</th>
                                                <th>Address</th>
                                                <th>Bank Details</th>
                                          </tr>
                                    </thead>
                                    <tbody>

                                          @foreach($merchants as $details)
                                          <tr>
                                                <td><a href="user_edit/{{$details->id}}" class="text-danger"> <i
                                                                  class="fa fa-edit"></i> Edit</a></td>
                                                <td><span class="text-capitalize">{{ $details['coopname'] }}</span> </td>
                                                <td><span class="text-lowercase">{{ $details['email'] }}</span></td>
                                                <td><span class="text-capitalize">{{ $details['fname'] }} &nbsp;{{ $details['lname'] }}</span></td>
                                                <td> <span class="text-lowercase">{{ $details['address'] }}</span>
                                                      <br>
                                                      {{ $details['location'] }}
                                                      <br>
                                                      {{ $details['phone'] }}
                                                </td>
                                                <td><span class="text-capitalize">{{ $details['bank'] }}</span>
                                                      <br>
                                                      <span class="text-capitalize">{{ $details['account_name'] }}</span>
                                                      <br>
                                                      {{ $details['account_number'] }}
                                                </td>
                                          </tr>

                                          @endforeach
                                    </tbody>
                              </table>

                        </div>
                        <!--col 12-->
                  </div>
                  <!--roww-->
            </div>
      </div> <!-- section -->

      <script type="text/javascript">
      $(document).ready(function() {
            $('#myTable').DataTable();
      });
      </script>

      @endsection