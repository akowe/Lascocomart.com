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
                              <li class="breadcrumb-item active" aria-current="page">User activity</li>
                        </ol>
                  </nav>

            </div>
          
            <div class="container-fluid">
                  <div class="row">
                  <div class="col-lg-12">
                              <h1>Users Activity Log </h1>
                              <div class="card">
                                    <div class="card-body collapse show tabel-resposive">
                                          <table class="table-striped table" id="table">
                                                <thead>
                                                <tr class="text-uppercase small">
                                                      <th>No</th>
                                                      <th>User</th>
                                                      <th>Event</th>
                                                      <th>URL</th>
                                                      <th>Ip</th>
                                                      <th >User Agent</th>
                                                      <th >TimeStamp</th>
                                                      <th>Show</th>
                                                </tr>
                                                </thead>
                                               <tbody>
                                               @if($logs->count())
                                                @foreach($logs as $key => $log)
                                                <tr class="small">
                                                      <td>{{ ++$key }}</td>
                                                      <td><strong>{{ $log->fname }}</strong><br>
                                                            <span class="text_light">{{ $log->email }}</span>
                                                      </td>
                                                      <td>{{ $log->subject }}</td>
                                                      <td class="text-success">{{ $log->url }}</td>

                                                      <td class="text-warning">{{ $log->ip }}</td>
                                                      <td class="text-danger">{{ $log->agent }}</td>
                                                      <td>{{ $log->created_at->format('d/M/Y H:i:s') }}
                                                            <br>({{ $log->created_at->diffForHumans() }})
                                                      </td>

                                                      <td class="text-center" ><a href="" class="text-danger"><i class="fa fa-eye"></i> </a></td>
                                                </tr>
                                               </tbody>
                                                @endforeach
                                                @endif
                                          </table>
                                    </div>
                              </div>
                        </div>             
                  </div>
            </div>
      </div>
</div>
@endsection