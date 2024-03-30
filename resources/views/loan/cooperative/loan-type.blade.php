@extends('layouts.home')
@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
      <div class="container-xl">
            <div class="row g-2 align-items-center">
                  <div class="col">
                        <!-- Page pre-title -->
                        <div class="page-pretitle">
                              All loan type (s)
                        </div>
                        <h2 class="page-title">
                              <span class=" d-none  d-md-block">Loan Type</span>
                        </h2>
                  </div>
                  <!-- Page title actions -->
                  <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                              <span class="d-block ">
                                    <a href="#" class="btn d-none ">
                                    </a>
                              </span>
                              <a href="" class="btn btn-danger d-none d-sm-inline-block" data-bs-toggle="modal"
                                    data-bs-target="#modal-loantype" aria-label="Create new report">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                          viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                          stroke-linecap="round" stroke-linejoin="round">
                                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                          <path d="M12 5l0 14" />
                                          <path d="M5 12l14 0" />
                                    </svg>

                                    New Loan Type
                              </a>
                              <a href="" class="btn btn-danger d-sm-none btn-icon" data-bs-toggle="modal"
                                    data-bs-target="#modal-loantype" aria-label="">
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
                    <!-- Alert start --->
                    <div class="container-xl">
                        <div class="row ">
                              <div class="col-12">
                                    <p></p>
                                    @if(session('loanType-status'))
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
                                                <div> {{ session('loanType-status') }}</div>
                                          </div>
                                          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                                    </div>
                                    @endif

                                    @if(session('success'))
                                    <div class="alert alert-important  alert-success alert-dismissible" role="alert">
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
                                                <div> {{ session('success') }}</div>
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
                                    <h3 class="card-title">Loan Type (s)</h3>
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

                                                      <form action="/cooperative-loan-type" method="GET" role="search">
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
                                                      <th class="w-1">S/N
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


                                                      <th>Name</th>
                                                      <th>Rate Type</th>
                                                      <th>Percentage (%)</th>
                                                      <th>Guarantor</th>
                                                      <th>Min-Duration</th>
                                                      <th>Max-Duration</th>
                                                      <th></th>
                                                </tr>
                                          </thead>
                                          <tbody>
                                                @foreach($loantypes as $loantype)
                                                <tr>
                                                      <td><input class="form-check-input m-0 align-middle"
                                                                  type="checkbox" aria-label="Select"></td>
                                                      <td><span class="text-secondary">
                                                                  {{ $loop->iteration}}</span>
                                                      </td>

                                                      <td>
                                                      <h4 class="strong text-capitalize">
                                                      {{$loantype->name}}
                                                            </h4>
                                                            <div class="text-secondary">
                                                            @if(!empty($loantype->description))
                                                      <span class="text-lowwercase">{{$loantype->description}}</span>
                                                            @else 
                                                            @endif
                                                            </div>   
                                                      </td>
                                                      <td class="text-capitalize">{{$loantype->rate_type}}</td>
                                                      <td>{{$loantype->percentage_rate}}</td>
                                                      <td class="text-capitalize">{{$loantype->guarantor}}</td>
                                                      <td class="">{{$loantype->min_duration}} </td>
                                                      <td class="">{{$loantype->max_duration}} </td>
                                                      <td class="text-end">
                                                            <span class="dropdown">
                                                                  <button
                                                                        class="btn dropdown-toggle align-text-top text-red"
                                                                        data-bs-boundary="viewport"
                                                                        data-bs-toggle="dropdown">Actions</button>
                                                                  <div class="dropdown-menu dropdown-menu-end">
                                                                        <a class="dropdown-item"
                                                                              href="admin-edit-loan-type/{{$loantype->id}}">
                                                                              Edit
                                                                        </a>

                                                                        <a class="dropdown-item"
                                                                              href="admin-remove-loan-type/{{$loantype->id}}">
                                                                              Remove
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

                                          Showing {{ ($loantypes->currentPage() - 1) * $loantypes->perPage() + 1; }} to
                                          {{ min($loantypes->currentPage()* $loantypes->perPage(), $loantypes->total()) }}
                                          of
                                          {{$loantypes->total()}} entries
                                    </p>

                                    <ul class="pagination m-0 ms-auto">
                                          @if(isset($loantypes))
                                          @if($loantypes->currentPage() > 1)
                                          <li class="page-item ">
                                                <a class="page-link text-danger"
                                                      href="{{ $loantypes->previousPageUrl() }}" tabindex="-1"
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


                                          <li class="page-item"> {{ $loantypes->appends(compact('perPage'))->links()  }}
                                          </li>
                                          @if($loantypes->hasMorePages())
                                          <li class="page-item">
                                                <a class="page-link text-danger" href="{{ $loantypes->nextPageUrl() }}">
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
            </div><!-- row -->
      </div>
</div>
@endsection