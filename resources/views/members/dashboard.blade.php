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
                              <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                              <li class="breadcrumb-item active" aria-current="page">Member</li>
                        </ol>
                  </nav>

                  <div class="pb-3">
                        <h1>Dashboard</h1>

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
                        @foreach (auth()->user()->unreadNotifications
                                    ->where('type','App\Notifications\AdminCancelOrder') as $notification)
                                    <div class="alert alert-danger text-center" role="alert">
                           
                                          <span class="p-1 text-dark"> {{$notification->data['data']}}</span>
                                 
                                    <a href="{{ url('read-cancel-order') }}/{{ $notification->id }}"    data-id="{{$notification->id}}" class="text-primary" title="close">
                                          <i class="fa fa-times"></i></a>

                              </div>

                                    @endforeach
                              <div class="col-md-6 col-lg-3 d-flex">
                                    <div class="card mb-grid w-100">
                                          <div class="card-body d-flex flex-column">
                                                <div class="d-flex justify-content-between mb-3">
                                                      <h5 class="card-title mb-0 small">
                                                            Total Paid Orders
                                                      </h5>

                                                      <div class="card-title-sub">
                                                            {{ $count_orders }}
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



                        </div>

                        @if(Session::has('remove')== true)
                        <!--show alert-->
                        <p class="alert {{ Session::get('alert-class', 'alert-info') }} text-center">
                              {{ Session::get('remove') }}</p>
                        @endif

                        @if(Session::has('remove')== false)
                        <!--show alert-->
                        <p style="display: none;">{{ Session::get('remove') }}</p>
                        @endif


                        <div class="row">
                              <div class="col-lg-12">
                                    <div class="card">
                                          <div class="card-header d-flex justify-content-between align-items-center">
                                                <div class="card-header-title">Orders</div>
                                          </div>
                                          <div class="card-body collapse show tabel-resposive" id="card">
                                                <h4 class="card-title">My order history</h4>

                                                <table class="table-striped table" id="member">
                                                      <thead>
                                                            <tr class="small">
                                                                  <th>Date</th>
                                                                  <th>Member</th>
                                                                  <th>Amount</th>
                                                                  <th>Order Number</th>
                                                                  <th>Status</th>
                                                                  <th>Have you received this order? </th>
                                                                  <th>Cancel order</th>
                                                            </tr>
                                                      </thead>
                                                      <tbody>
                                                            @foreach($orders as $order)
                                                            <tr class="small">
                                                                  <td> {{ date('m-d-Y', strtotime($order->created_at))}}
                                                                  </td>
                                                                  <td>{{$order['fname']}} {{$order['lname']}}</td>
                                                                  <td>{{ number_format($order['grandtotal']) }}</td>
                                                                  <td><a href="member_invoice/{{ $order['order_number'] }}"
                                                                              title="Click to view">{{$order['order_number'] }}</a>
                                                                  </td>
                                                                  <td>{{$order['status']}}</td>
                                                                  <td><span class="text-success">
                                                                              {{ $order['delivery_status']}}</span>
                                                                        @if(empty($order->delivery_status) && $order->status == 'cancel')
                                                                       
                                                                        @elseif($order->delivery_status == 'delivered' )
                                                                        <form action="{{ url('order-received') }}/{{ $order->id}}"
                                                                              method="POST">
                                                                              @csrf
                                                                              <input type="hidden" name="order_id" id=""
                                                                                    value="{{ $order->id}}">
                                                                              <button class="btn btn-danger btn-xs"
                                                                                    type="submit">
                                                                                    Received</button>
                                                                        </form>
                                                                        @endif
                                                                  </td>
                                                                  <td class="text-danger">
                                                                        @if($order->delivery_status == 'received')
                                                                        @elseif($order->status == 'approved')
                                                                        @elseif($order->status == 'cancel')
                                                                        @elseif($order->status == 'paid')
                                                                        @else
                                                                              <input type="hidden"  id="id"
                                                                                    value="{{$order->id }}">
<!-- 
                                                                              <input type="hidden" name="amount"
                                                                                    value="{{$order->total }}"> -->

                                                                              <button type="button" 
                                                                                    class="btn btn-outline-danger btn-sm" onclick="cancelOrder()"><i
                                                                                          class="fa fa-trash-o"></i></button>
                                                                      
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
<script>
function cancelOrder() {

      var answer = window.confirm("Are you sure you want to cancel this order?");

      if (answer) {
            var id = document.getElementById('id').value;
            var showRoute = "{{ route('cancel-order', ':id') }}";
            url = showRoute.replace(':id', id);
            
            window.location = url;

      } else {
            // window.location.reload();
      }
}
</script>
<script type="text/javascript">
      $(document).ready(function() {
            $('#member').DataTable({
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
                        [5, 10, 20, -1],
                        [5, 10, 20, "All"]
                  ],
                  iDisplayLength: 5,
                  "order": [
                        [0, "desc"]
                  ],

                  "language": {
                        "lengthMenu": "_MENU_ Records per page",
                  }


            });
      });


</script>
@endsection 