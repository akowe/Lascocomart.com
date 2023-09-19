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
                              <li class="breadcrumb-item active" aria-current="page">Members</li>
                        </ol>
                  </nav>
                  <div class="pb-3">
                        <h2>Members</h2>


                        <h5 class="navbar bg-dark text-white" style="padding-left: 10px;">
                              Your current credit balance :₦{{ number_format($owncredit->sum('credit')) }}. <br>
                        </h5>
                  </div>
            </div>
            @if(Session::has('credit')== true)
            <!--show alert-->
            <p class="alert {{ Session::get('alert-class', 'alert-info') }} text-center">{{ Session::get('credit') }}
            </p>
            @endif

            @if(Session::has('credit')== false)
            <!--show alert-->
            <p style="display: none;">{{ Session::get('credit') }}</p>
            @endif

            <!-- alert if member is not verified-->
            @if(Session::has('verified')== true)
            <!--New registration alert-->
            <p class="alert {{ Session::get('alert-class', 'alert-info') }} text-center">{{ Session::get('verified') }}
            </p>
            @endif

            @if(Session::has('verified')== false)
            <!--show alert-->
            <p style="display: none;">{{ Session::get('verified') }}</p>
            @endif
            @if (session('success'))
                <h6 class="alert alert-success">{{ session('success') }}</h6>
            @endif


            <!-- row -->
            <div class="row">
                  <div class="col-lg-12 table-responsive">
                        <table class="table table-striped " id="table">
                              <thead>
                                    <tr>
                                          <th>First name</th>
                                          <th>Last name</th>
                                          <th>Email</th>
                                          <th>Phone</th>
                                          <!--  <th>Lascoco ID</th> -->
                                          <th>Remove member</th>
                                    </tr>
                              </thead>
                              <tbody>

                                    @foreach($members as $details)
                                    <tr>

                                          <td>{{ $details['fname'] }}</td>
                                          <td>{{ $details['lname'] }}</td>
                                          <td>{{ $details['email'] }}</td>
                                          <td>{{ $details['phone'] }}</td>
                                          <!--  <td >{{ $details['code'] }}</td> -->
                                          <td>
                                                <input type="hidden" id="user_id" value="{{$details->id}}">
                                                <button type="button" class="btn btn-outline-danger " onclick="removeMember()">
                                                      <i class="fa fa-trash"></i> 
                                                </button>

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


<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>

<script>
function removeMember() {

      var answer = window.confirm("Are you sure you want to remove this member?");

      if (answer) {
            var id = document.getElementById('user_id').value;
            var showRoute = "{{ route('delete-member', ':id') }}";
            url = showRoute.replace(':id', id);
            
            window.location = url;

      } else {
            // window.location.reload();
      }
}

</script>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
            <div class="modal-content">
                  <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Are you sure you want to remove this member?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                        </button>
                  </div>
                  <div class="modal-body">
                        <p>If you delete this member, he/she will be removed.</p>
                  </div>
                  <div class="modal-footer">

                     
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
            </div>
      </div>
</div>

@endsection