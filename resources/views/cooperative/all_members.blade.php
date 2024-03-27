@extends('layouts.home')
@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
      <div class="container-xl">
            <div class="row g-2 align-items-center">
                  <div class="col">
                        <!-- Page pre-title -->
                        <div class="page-pretitle">
                              Members
                        </div>
                        <h2 class="page-title">
                        <span class=" d-none  d-md-block">Cooperative&nbsp;</span>ID: {{Auth::user()->code}}&nbsp;

                        <a href="" alt="Copy" title="Copy" class="text-danger"
                              onclick="copyToClipboard('{{Auth::user()->code}}')"><svg
                                    xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-copy  "
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
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

                              <a href="{{ route('add_new_product') }}" class="btn btn-danger d-none d-sm-inline-block">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                          viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                          stroke-linecap="round" stroke-linejoin="round">
                                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                          <path d="M12 5l0 14" />
                                          <path d="M5 12l14 0" />
                                    </svg>
                                    Add New Member
                              </a>
                              <a href="{{ route('add_new_product') }}" class="btn btn-danger d-sm-none btn-icon">
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
                              <div class="col-sm-6 col-lg-6">
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
                                                                              href="{{ url('members') }}">Last 7
                                                                              days</a>
                                                                        <a class="dropdown-item"
                                                                              href="{{ url('members') }}">Last 30
                                                                              days</a>
                                                                        <a class="dropdown-item"
                                                                              href="{{ url('members') }}">Last 3
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
                                                            <span
                                                                  class="text-green d-inline-flex align-items-center lh-1">
                                                                  {{$adminActiveMember->count()}}
                                                                  <!-- Download SVG icon from http://tabler-icons.io/i/trending-up -->
                                                                  <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="icon ms-1" width="24" height="24"
                                                                        viewBox="0 0 24 24" stroke-width="2"
                                                                        stroke="currentColor" fill="none"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z"
                                                                              fill="none" />
                                                                        <path d="M3 17l6 -6l4 4l8 -8" />
                                                                        <path d="M14 7l7 0l0 7" />
                                                                  </svg>
                                                            </span>
                                                            @else
                                                            <span
                                                                  class="text-danger d-inline-flex align-items-center lh-1">
                                                                  {{$adminActiveMember->count()}}
                                                                  <!-- Download SVG icon from http://tabler-icons.io/i/trending-down -->
                                                                  <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="icon icon-tabler icon-tabler-trending-down"
                                                                        width="24" height="24" viewBox="0 0 24 24"
                                                                        stroke-width="1.5" stroke="currentColor"
                                                                        fill="none" stroke-linecap="round"
                                                                        stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z"
                                                                              fill="none" />
                                                                        <path d="M3 7l6 6l4 -4l8 8" />
                                                                        <path d="M21 10l0 7l-7 0" />
                                                                  </svg>
                                                            </span>
                                                            @endif
                                                      </div>
                                                </div>
                                                <div class="progress progress-sm">
                                                      <div class="progress-bar bg-primary"
                                                            style="width: {{$adminActiveMember->count()}}%"
                                                            role="progressbar"
                                                            aria-valuenow="{{$adminActiveMember->count()}}"
                                                            aria-valuemin="0" aria-valuemax="100"
                                                            aria-label="{{$adminActiveMember->count()}}% Complete">
                                                            <span class="visually-hidden">{{$adminActiveMember->count()}}%
                                                                  Complete</span>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                              </div>




                        </div>
                        <!---- row-cards --->
                  </div>
                  <!---col-12 --->
                  <!-- Alert start --->
                  <div class="container-xl">
                        <div class="row ">
                              <div class="col-12">
                                    <p></p>
                                    @if (session('success'))
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
                                                <div>{{ session('success') }}</div>
                                          </div>
                                          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                                    </div>
                                    @endif
                                 

                                    @if(Session::has('credit')== true)
                                    <div class="alert alert-important alert-purple alert-dismissible" role="alert">
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
                                                <div> {{ Session::get('credit') }}</div>
                                          </div>
                                          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                                    </div>
                                    @endif


                                    @if(Session::has('verified')== true)
                                    <div class="alert alert-important alert-info alert-dismissible" role="alert">
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
                                                <div>{{ Session::get('verified') }}</div>
                                          </div>
                                          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                                    </div>
                                    @endif

                              </div>
                        </div>
                  </div>
                  <!-- Alert stop --->
                  <div class="col-12">
                        <div class="card">
                              <div class="card-header">
                                    <h3 class="card-title">All Members</h3>
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
                                                Search:
                                                <div class="ms-2 d-inline-block">

                                                      <form action="/members" method="GET" role="search">
                                                            {{ csrf_field() }}
                                                            <div class="input-group mb-2">
                                                                  <input type="text" class="form-control"
                                                                        placeholder="Search for…" name="search">
                                                                  <button type ="submit" class="btn" type="button">Go!</button>
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
                                                                  type="checkbox" aria-label="all member"></th>
                                                      <th class="w-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                  class="icon icon-tabler icon-tabler-number" width="24"
                                                                  height="24" viewBox="0 0 24 24" stroke-width="1.5"
                                                                  stroke="currentColor" fill="none"
                                                                  stroke-linecap="round" stroke-linejoin="round">
                                                                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                  <path d="M4 17v-10l7 10v-10" />
                                                                  <path d="M15 17h5" />
                                                                  <path
                                                                        d="M17.5 10m-2.5 0a2.5 3 0 1 0 5 0a2.5 3 0 1 0 -5 0" />
                                                            </svg>
                                                      </th>


                                                      <th>Name</th>
                                                      <th>Email</th>
                                                      <th>Phone</th>
                                                      <th>Status</th>
                                                      <th></th>
                                                </tr>
                                          </thead>
                                          <tbody>
                                                @foreach($members as $details)
                                                <tr>
                                                      <td><input class="form-check-input m-0 align-middle"
                                                                  type="checkbox" aria-label="Select">
                                                      </td>
                                                      <td><span class="text-secondary">{{$loop->iteration}}</span></td>
                                                      <td>{{ $details->fname }}</td>
                                                      <td>{{ $details->email }}</td>
                                                      <td>{{ $details->phone }}</td>
                                                      <td class="">
                                                            @if(!empty($details->email_verified_at ))
                                                            <span class="badge bg-green-lt">verified</span>

                                                            @else
                                                            <span class="badge bg-yellow-lt">not verified</span>

                                                            @endif

                                                      </td>
                                                      <td class="text-end">
                                                            <span class="dropdown">
                                                                  <button
                                                                        class="btn dropdown-toggle align-text-top text-red"
                                                                        data-bs-boundary="viewport"
                                                                        data-bs-toggle="dropdown">Actions</button>
                                                                  <div class="dropdown-menu dropdown-menu-end">
                                                                        @csrf
                                                                        <a href="delete-member/{{$details->id}}"
                                                                              onclick="return confirm('Do you want to remove? {{$details->fname}}')"
                                                                              class="dropdown-item"> Remove
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

                                          Showing {{ ($members->currentPage() - 1) * $members->perPage() + 1; }} to
                                          {{ min($members->currentPage()* $members->perPage(), $members->total()) }}
                                          of
                                          {{$members->total()}} entries
                                    </p>

                                    <ul class="pagination m-0 ms-auto">
                                          @if(isset($members))
                                          @if($members->currentPage() > 1)
                                          <li class="page-item ">
                                                <a class="page-link text-danger"
                                                      href="{{ $members->previousPageUrl() }}" tabindex="-1"
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


                                          <li class="page-item"> {{ $members->appends(compact('perPage'))->links()  }}
                                          </li>
                                          @if($members->hasMorePages())
                                          <li class="page-item">
                                                <a class="page-link text-danger" href="{{ $members->nextPageUrl() }}">
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


<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
<script>
document.getElementById('pagination').onchange = function() {
      window.location = "{!! $members->url(1) !!}&perPage=" + this.value;
};
</script>
<script>
function removeMember() {
      var name = document.getElementById('user_id').value;
      var answer = window.confirm("Are you sure you want to remove this member?" + name);

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