@extends('layouts.home')

@extends('layouts.sidebar')


@section('content')
<div class="adminx-content">
      <!-- <div class="adminx-aside">

        </div> -->

      <div class="adminx-main-content">
            <div class="container-fluid">
                  <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb adminx-page-breadcrumb">
                              <li class="breadcrumb-item"><a href="{{url('/') }}">Home</a></li>
                              <li class="breadcrumb-item"><a href="{{url('merchant') }}">Seller</a></li>
                              <li class="breadcrumb-item active" aria-current="page">Products</li>
                        </ol>
                  </nav>
                  <div class="row">
                        <div class="col-md-6 col-lg-3 d-flex">
                              <div class="card mb-grid w-100">
                                    <div class="card-body d-flex flex-column">
                                          <div class="d-flex justify-content-between mb-3">
                                                <h5 class="card-title mb-0 small">
                                                      Total Approved Products
                                                </h5>

                                                <div class="card-title-sub">
                                                      {{ $count_product->count() }}
                                                </div>
                                          </div>

                                          <div class="progress mt-auto">
                                                <div class="progress-bar" role="progressbar" style="width: 75%;"
                                                      aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="pb-3">
                        <h1>Products</h1>
                       <h4 class="text-end">
                        <a href="{{ route('product') }}" class="btn btn-outline-danger"><i class="fa fa-plus"></i> Add New Product</a></h4>
                  </div>
            </div>
            <div class="container-fluid">
                  <div class="row">
                        <div class="col-lg-12">
                              @if (session('status'))
                              <div class="alert alert-success" role="alert">
                                    {!! session('status') !!}

                              </div>
                              @endif

                              @if(Session::has('remove')== true)
                              <!--show alert-->
                              <p class="alert {{ Session::get('alert-class', 'alert-info') }} text-center">
                                    {{ Session::get('remove') }}</p>
                              @endif

                              @if(Session::has('remove')== false)
                              <!--show alert-->
                              <p style="display: none;">{{ Session::get('remove') }}</p>
                              @endif

                              <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                          <div class="card-header-title">All Products</div>
                                    
                                    </div>
                                    <div class="card-body collapse show tabel-resposive" id="card">
                                          <p class="card-text text-danger">Note that LascocoMart percentage would be
                                                added to the price of each product on our landing page.</p>

                                          <table class="table-striped table" id="table">
                                                <thead>
                                                      <tr class="small">
                                                            <th>Date</th>
                                                            <th>Product</th>
                                                            <th>Qty.</th>
                                                            <th>Old Price</th>
                                                            <th>New Price</th>
                                                            <th>Status</th>
                                                            <th>Remove Product</th>

                                                      </tr>
                                                </thead>
                                                <tbody>
                                                      @foreach($products as $product)
                                                      <tr class="small">
                                                            <td> {{ date('d/m/y', strtotime($product->created_at))}}
                                                            </td>
                                                            <td>{{$product->prod_name }}</td>
                                                            <td>{{$product->quantity }}</td>
                                                            <td>{{number_format($product->old_price )}}</td>
                                                            <td>{{number_format($product->seller_price)}}</td>
                                                            <td>
                                                                  @if($product->prod_status == 'approve')

                                                                  <span class="text-success">
                                                                        <i class="fa fa-check"></i></span>
                                                                  @else
                                                                  @endif
                                                                  {{$product->prod_status }}
                                                            </td>
                                                            <!-- <td class="text-danger">
                                                            <a href="" data-toggle="modal" data-target="#pModal" class="btn btn-outline-danger btn-sm"> 
                                                            Remove
                                                            </a>
                                                            </td> -->
                                                            <td>
                                                                  @if($product->prod_status == 'pending')
           

                                                                        <input type="hidden" name="id" id="id"
                                                                              value="{{$product->id }}">


                                                                        <button type="button" onclick="removeProduct()"
                                                                              class="btn btn-outline-danger btn-sm"><i
                                                                                    class="fa fa-trash-o"></i>
                                                                              Remove</button>
                                                                  </form>
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
<script>
function removeProduct() {

      var answer = window.confirm("Are you sure you want to remove this product?");

      if (answer) {
            var id = document.getElementById('id').value; 
            var showRoute = "{{ route('remove-product', ':id') }}";
            url = showRoute.replace(':id', id);
            
            window.location = url;

      } else {
            // window.location.reload();
      }
}
</script>
  <!-- remove Modal -->

  <div class="modal fade" id="pModal" tabindex="-1" role="dialog" aria-labelledby="pModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
         <h5 class="modal-title" id="pModalLabel">Are you sure want to remove this product?</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
       
        <p></p>
       
      </div>
      <form action="/remove_product" method="post" name="submit">
            @csrf
      <div class="modal-body">
       <div class="row mb-3">
          
           <input type="hidden" name="id"   value="">

            <input type="hidden" name="prod_status"  value="remove"  >
        </div>
    

      </div>
      <div class="modal-footer">
       <button type="submit" name="submit" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash-o"></i> Yes. Remove</button>
                            
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">No</button>
        
        </div>
         </form>
      </div>
    </div>
  </div>
</div>
<!--remove modal end-->
@endsection