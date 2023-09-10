@extends('layouts.user-activity')

@extends('layouts.sidebar')
@section('content')
<div class="adminx-content">
      <div class="adminx-main-content">
            <div class="container-fluid">
                  <!-- BreadCrumb -->
                  <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb adminx-page-breadcrumb">
                              <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                              <li class="breadcrumb-item active" aria-current="page">Superadmin</li>
                        </ol>
                  </nav>

                  <div class="pb-3">
                  <h2>Monitoring All User Activities</h2>
                  </div>
            </div>
            <div class="container py-5">
                  <div class="row">
                      
                        <div class="col-md-12">
                              <div class="card">
                                    <div class="card-body">
                                          <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-hover"
                                                      id="userActivity">
                                                      <thead class="table-secondary">
                                                            <tr>
                                                                  <th>S.N</th>
                                                                  <th><a href="#" wire:click="sortBy('table')"
                                                                              class="sort-link text-dark">
                                                                              Table </a>
                                                                  </th>
                                                                  <th><a href="#" wire:click="sortBy('event')"
                                                                              class="sort-link text-dark">
                                                                              Log Type</a>
                                                                  </th>
                                                                  <th><a href="#" wire:click="sortBy('users.fname')"
                                                                              class="sort-link text-dark">
                                                                              Done By </a>
                                                                  </th>

                                                                  <th><a href="#" wire:click="sortBy('ip_address')"
                                                                              class="sort-link text-dark">
                                                                              IP</a>
                                                                  </th>
                                                                  <th><a href="#" wire:click="sortBy('created_at')"
                                                                              class="sort-link text-dark">
                                                                              Date </a>
                                                                  </th>
                                                                  <th>Action</th>
                                                            </tr>
                                                      </thead>

                                                      <tbody>
                                                            @foreach ($userActivities as $activity)
                                                            <tr>

                                                                  <td scope="row">
                                                                        {{ $activity->id }}
                                                                  </td>
                                                                  <td>{{ ucfirst($activity->table) }}</td>
                                                                  <td>{{ $activity->event }}</td>

                                                                  <td><span>{{ $activity->fname }}</span><br>
                                                                        {{ $activity->email }}
                                                                  </td>
                                                                  <td>{{ $activity->ip_address }}</td>
                                                                  <td>
                                                                        {{ $activity->created_at->format('Y-m-d H:i:s') }}
                                                                        ({{ $activity->created_at->diffForHumans() }})
                                                                  </td>
                                                                  <td>
                                                                        <a href="{{ url('detail-activity/'.$activity->id) }}"
                                                                              class="btn btn-outline-danger ">
                                                                              <i class="fa fa-eye"></i></a>



                                                                  </td>
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

@endsection