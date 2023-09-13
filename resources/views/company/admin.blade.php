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
                              <li class="breadcrumb-item active" aria-current="page">SuperAdmin</li>
                        </ol>
                  </nav>

                  <div class="pb-3">
                        <h1>Dashboard</h1>
                        <div class="card-body">

                              @if (session('pay'))
                              <div class="alert alert-success" role="alert">
                                    {{ session('pay') }}
                              </div>
                              @endif
                        </div>



                        <!-- count product, order and sales-->

                        <div class="row">
                              <div class="col-md-3 col-lg-3 d-flex">
                                    <div class="card mb-grid w-100">
                                    <a href="{{ url('products_list') }}" class="text-md-left text-decoration-none"> 
                                          <div class="card-body d-flex flex-column bg-info text-dark">
                                                <div class="d-flex justify-content-between mb-3">
                                                      <h5 class="card-title mb-0 small">
                                                            Total Products
                                                      </h5>

                                                      <div class="card-title-sub">
                                                            {{ $products->count() }}
                                                      </div>
                                                </div>

                                                <div class="progress mt-auto">
                                                    &nbsp;
                                                            View All Products
                                                </div>
                                          </div>
                                          </a>
                                    </div>
                              </div>

                              <div class="col-md-3 col-lg-3 d-flex">
                                    <div class="card mb-grid w-100">
                                    <a href="{{ url('order-history') }}" class="text-md-left text-decoration-none"> 
                                          <div class="card-body d-flex flex-column bg-danger text-white">
                                                <div class="d-flex justify-content-between mb-3">
                                                      <h5 class="card-title mb-0 small">
                                                            Total Orders
                                                      </h5>

                                                      <div class="card-title-sub">
                                                            {{ $count_orders->count() }}
                                                      </div>
                                                </div>

                                                <div class="progress mt-auto text-dark">
                                                     &nbsp;
                                                            View Order History
                                                </div>
                                          </div>
                                          </a>
                                    </div>
                              </div>


                             

                              <div class="col-md-3 col-lg-3 d-flex">
                                    <div class="card mb-grid w-100">
                                    <a href="{{ url('funds-allocated') }}" class="text-md-left text-decoration-none">
                                          <div class="card-body d-flex flex-column bg-warning text-dark">
                                                <div class="d-flex justify-content-between mb-3">

                                                      <h5 class="card-title mb-0 small">
                                                            Funds allocated out
                                                      </h5>

                                                      <div class="card-title-sub">
                                                            <i class="fa fa-coins"></i>
                                                            ₦{{ number_format($funds->sum('amount'))}}
                                                      </div>
                                                </div>

                                                <div class="progress mt-auto">&nbsp;View funds allocated
                                                </div>
                                              
                                          </div>
                                          </a>
                                    </div>
                              </div>


                     
                              <div class="col-md-3 col-lg-3 d-flex">
                                    <div class="card border-0 bg-primary text-white text-center mb-grid w-100">
                                          <div class="d-flex flex-row align-items-center h-100">
                                                <div
                                                      class="card-icon d-flex align-items-center h-100 justify-content-center">
                                                      <i class="fa fa-coins"></i>
                                                </div>
                                                <a href="{{url('sales-details') }}" class="card-body text-white">
                                                      <div class="card-info-title">View  Payments</div>
                                                      <h3 class="card-title mb-0">

                                                            ₦{{ number_format($sumSales->sum('grandtotal')) }}
                                                      </h3>
                                                </a>
                                          </div>
                                    </div>
                              </div>

                        </div>
                  </div>
            </div>

            <div class="container-fluid">
                  <div class="row">
                        <div class="col-md-8 card ">
                              
                        <div id="sales"></div>
                        </div>
                        <div class="col-md-4 card">
                       
                              <div class="card border-0 bg-success text-white text-center mb-grid w-100">
                                    <div class="d-flex flex-row align-items-center h-100">
                                          <div class="card-icon d-flex align-items-center h-100 justify-content-center">
                                                <i class="fa fa-shopping-cart"></i>
                                          </div>
                                          <a href="{{url('sales-details')}}" class="card-body text-white">
                                                <div class="card-info-title">View all Sales</div>
                                                <h3 class="card-title mb-0">
                                                      {{ $count_sales->count() }}
                                                </h3>
                                          </a>
                                    </div>


                                    
                              </div>
                              <div class="card mb-grid w-100">
                                    <div class="card-body d-flex flex-column">
                                          <div class="d-flex justify-content-between mb-3">
                                                <h5 class="card-title mb-0 small">
                                                       Offline
                                                </h5>

                                                <div class="card-title-sub">
                                                      {{ $offlinePayment->count() }}
                                                </div>
                                          </div>

                                          <div class="d-flex justify-content-between mb-3">
                                                <h5 class="card-title mb-0 small">
                                                       Online
                                                </h5>

                                                <div class="card-title-sub">
                                                      {{ $onlinePayment->count() }}
                                                </div>
                                          </div>

                                          <div class="d-flex justify-content-between mb-3">
                                                <h5 class="card-title mb-0 small">
                                                       Bank Transfer
                                                </h5>

                                                <div class="card-title-sub">
                                                      {{ $bankPayment->count() }}
                                                </div>
                                          </div>

                                        
                                    </div>
                              </div>

                        </div>
                  </div>
            </div>
<p></p><p></p>
            <div class="container-fluid">
                  <div class="row">
                        <div class="col-md-8 card ">
                              
                              <div id="linechart"></div>
                        </div>
                        <div class="col-md-4 card">
                              <div class="card border-0 bg-dark text-white text-center mb-grid w-100">
                                    <div class="d-flex flex-row align-items-center h-100">
                                          <div class="card-icon d-flex align-items-center h-100 justify-content-center">
                                                <i data-feather="users"></i>
                                          </div>
                                          <a href="{{url('users_list')}}" class="card-body text-white">
                                                <div class="card-info-title">View all Users</div>
                                                <h3 class="card-title mb-0">
                                                      {{ $users->count() }}
                                                </h3>
                                          </a>
                                    </div>
                              </div>
                              <div class="card mb-grid w-100">
                                    <div class="card-body d-flex flex-column">
                                          <div class="d-flex justify-content-between mb-3">
                                                <h5 class="card-title mb-0 small">
                                                       Sellers
                                                </h5>

                                                <div class="card-title-sub">
                                                      {{ $sellers->count() }}
                                                </div>
                                          </div>

                                          <div class="d-flex justify-content-between mb-3">
                                                <h5 class="card-title mb-0 small">
                                                       Coperatives
                                                </h5>

                                                <div class="card-title-sub">
                                                      {{ $cooperatives->count() }}
                                                </div>
                                          </div>

                                          <div class="d-flex justify-content-between mb-3">
                                                <h5 class="card-title mb-0 small">
                                                       Members
                                                </h5>

                                                <div class="card-title-sub">
                                                      {{ $members->count() }}
                                                </div>
                                          </div>

                                        
                                    </div>
                              </div>
                             
                        </div>
                  </div>
            </div>


         
      </div>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
var registeredUsers = <?php echo $registeredUsers; ?>;
console.log(registeredUsers);
google.charts.load('current', {
      'packages': ['corechart']
});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
      var data = google.visualization.arrayToDataTable(registeredUsers);
      var options = {
            title: 'Yearly Users Line Chart',
            curveType: 'function',
            legend: {
                   position: 'bottom'
            }
      };
      var chart = new google.visualization.LineChart(document.getElementById('linechart'));
      chart.draw(data, options);
}
</script>

<script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
  
      function drawChart() {
            var data = google.visualization.arrayToDataTable({{ Js::from($sales) }});
            
   
        var options = {
            title: 'Yearly Sales PieChart',
   
        pieStartAngle: 100,
        pieSliceText: 'label',
          slices: {  4: {offset: 0.2},
                    12: {offset: 0.3},
                    14: {offset: 0.4},
                    15: {offset: 0.5},
          },
            is3D: true
      };
      
  
       
        var chart = new google.visualization.PieChart(document.getElementById('sales'));
   
        chart.draw(data, options);
      }
    </script>





@endsection