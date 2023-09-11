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
                              <li class="breadcrumb-item active" aria-current="page">Superadmin</li>
                        </ol>
                  </nav>

                  <div class="pb-3">
                        <h1>Users Activity History</h1>
                        </h5>

                  </div>
            </div>
          

            <div class="container-fluid">
                  <div class="row">
                        <div class="col-lg-12">
                  
                              <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
  
                                    </div>
                                    <div class="card-body collapse show tabel-resposive" id="card">
                                          <p class="card-text"></p>

                                          <table class="table-striped table" id="log">
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
                                                @endforeach
                                                @endif
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


<script type="text/javascript">
      $(document).ready(function() {
            $('#log').DataTable({
                  responsive: true,

                  dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                  // dom: 'Bfrtip',
                  button: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5',
                        'pdfHtml5',
                  ],

                  aLengthMenu: [
                        [5, 10, 20,50,100,250,500, -1],
                        [5, 10, 20,50,100,250,500, "All"]
                  ],
                  iDisplayLength: 5,
                  "order": [
                        [0, "asc"]
                  ],

                  "language": {
                        "lengthMenu": "_MENU_ Records per page",
                  }


            });
      });
</script>
@endsection