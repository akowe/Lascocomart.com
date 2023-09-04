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
                              <li class="breadcrumb-item active" aria-current="page">Cooperative</li>
                        </ol>
                  </nav>

                  <div class="pb-3">
                        <h4>Are you sure you want to Cancel this order:  {{$order->order_number}}</h4>
                        <h4><a href="{{ url('admin-order-history') }}" class="btn btn-danger">Back to order history</a>
                    </h4>
                  </div>
            </div>
            <div class="container-fluid">
                  <div class="row">
                        @if (session('success'))
                        <div class="alert alert-success" role="alert">
                              {!! session('success') !!}
                        </div>
                        @endif
                  </div>
            </div>

            <div class="container">
    <div class="row">
        <div class="col-md-12">

            @if (session('status'))
                <h6 class="alert alert-success">{{ session('status') }}</h6>
            @endif

            <div class="card">
                <div class="card-header">
                Enter current credit worth for <strong> {{$userName}}</strong> 
                </div>
                <div class="card-body">

                <form action="/order-cancel" method="post" name="submit">
                              @csrf
                            
                              <input type="hidden" name="order_id" value="{{$order->id}}">
                          
                              <div class="form-group">
                                    <label for="">
                                    </label>
                                    <input type="number" name="credit" value="" placeholder="Enter Amount" required
                                          class="form-control" id="credit" onkeyup="myFunction()">
                                    <span class="text-danger text-xs">Enter figures without comma.</span>
                                    <h4> ₦ <span id="show"></span></h4>


                              </div>
                              <button type="submit" name="submit" class="btn btn-outline-danger btn-xs" title="Cancel">
                                    Cancel Now</button>
                        </form>

                </div>
            </div>
        </div>
    </div>
</div>
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