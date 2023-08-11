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
                <li class="breadcrumb-item active" aria-current="page">Seller</li>
              </ol>
            </nav>

            <div class="pb-3">
            
              <p class="text-danger">"Note that LascocoMart percentage would be added to each product on our landing page. <br>We assume your prices are wholesale and reasonable." Image format: <span class="text-dark">.jpg, .png, .jpeg.</span> Max size: <span class="text-dark">300kb.</span> <!-- Max-dimension: <span class="text-dark">600 x 600</span> -->
              </p>
              <p> <h6>All field mark <i class="text-danger">*</i> are required</h6></p>
              <p>  @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif</p>

                    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
            </div>

                 <!-- row -->
                <div class="row">
                    <div class="col-lg-12 ">

                   <h4>Upload New Product</h4>
                      <form enctype="multipart/form-data" action="{{ url('upload-image') }}" method="POST">
                        <div class="row">
                          <div class="col-lg-3 ">
                        @csrf
                        <div class="form-group">
                          <label>Product name <i class="text-danger">*</i></label>
                          <input type="text" name="prod_name" class="form-control">
                           @error('prod_name')
                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                          <label>Quantity <i class="text-danger">*</i></label>
                          <input type="number" name="quantity" value="" class="form-control">
                           @error('quantity')
                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                          <label>Old price</label>
                          <input type="number" name="old_price" value="" class="form-control">
                        </div>

                        <div class="form-group">
                          <label>New price <i class="text-danger">*</i></label>
                          <input type="number" name="price" value="" class="form-control" >
                           @error('price')
                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                          <label>Product category <i class="text-danger">*</i></label>
                          <select name="cat_id" class="form-control" >
                            <option>Select a category</option>
                            @foreach($categories as  $details)
                            <option value="{{ $details['cat_id'] }}">{{ $details['cat_name'] }}</option>
                            @endforeach
                          </select>
                           @error('cat_id')
                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                            @enderror
                        </div>
                        

                        <div class="form-group">
                          <label>Brand name</label>
                          <input type="text" name="prod_brand" class="form-control">
                        </div>

                        

                         <div class="form-group">
                          <label>Product description</label>
                          <textarea type="text" name="description" placeholder="describe your product here" class="form-control"></textarea>
                        </div>

                         </div><!--col-3-->

                         <div class="col-lg-3">

                            <div class="form-group">
                              <input type="file" name="image"  accept=".jpg,.jpeg,.png" id="image" multiple="multiple" class="form-control">
                             <!--  <input #imageInput accept="image/*" (change)="processFile(imageInput)" name="image" type="file" id="image"  class="form-control" /> -->
                               @error('image')
                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                            @enderror
                          </div>


                          <div class="form-group">
                            <lablel>Upload more images <i class="text-danger" >(Optional)</i></label>
                              
                              <input type="file" name="img1"   accept=".jpg,.jpeg,.png"  id="img1" multiple="multiple"  class="form-control">
                                @error('img1')
                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                            @enderror
                          </div>

                           <div class="form-group">
                              <input type="file" name="img2"  accept=".jpg,.jpeg,.png"  id="img2" multiple="multiple"  class="form-control">
                                  @error('img2')
                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                            @enderror
                          </div>

                           <div class="form-group">
                              <input type="file" name="img3"  accept=".jpg,.jpeg,.png" id="img3" multiple="multiple"  class="form-control">
                                  @error('img3')
                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                            @enderror
                          </div>

                           <div class="form-group">
                              <input type="file" name="img4"  accept=".jpg,.jpeg,.png"  id="img4" multiple="multiple"  class="form-control">
                                  @error('img4')
                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                            @enderror
                          </div>

                         </div><!--col-3-->

                        <div class="col-lg-6">
                            <img id="image-preview" src="" style="max-height: 250px;">

                           <img id="img1-preview" src="" style="max-height: 250px;">

                             <img id="img2-preview" src="" style="max-height: 250px;">

                             <img id="img3-preview" src="" style="max-height: 250px;">

                             <img id="img4-preview" src="" style="max-height: 250px;">


                            <div class="form-group">
                            <p> <input type="checkbox" required>  I agree to receive payment for products within 1 to 14 days after successful delivery to the  cooperative/member</p>
                                                      </div>
                             <button type="submit" class="btn btn-outline-danger" id="submit">Add Product</button>
                            </div>
                         </div><!--col-6-->

                       </div><!--in form row--->
                      </form>

                    </div><!--col-12-->
                   
                  </div><!--main row-->
                </div><!--container-->
              </div><!--admin-main-content-->
            </div><!--admin-content-->

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
 
<script type="text/javascript">
      
$(document).ready(function (e) {
 
   
   $('#image').change(function(){
            
    let reader = new FileReader();
 
    reader.onload = (e) => { 
 
      $('#image-preview').attr('src', e.target.result); 
    }
 
    reader.readAsDataURL(this.files[0]); 
   
   });


   $('#img1').change(function(){
            
    let reader = new FileReader();
 
    reader.onload = (e) => { 
 
      $('#img1-preview').attr('src', e.target.result); 
    }
 
    reader.readAsDataURL(this.files[0]); 
   
   });

   $('#img2').change(function(){
            
    let reader = new FileReader();
 
    reader.onload = (e) => { 
 
      $('#img2-preview').attr('src', e.target.result); 
    }
 
    reader.readAsDataURL(this.files[0]); 
   
   });

   $('#img3').change(function(){
            
    let reader = new FileReader();
 
    reader.onload = (e) => { 
 
      $('#img3-preview').attr('src', e.target.result); 
    }
 
    reader.readAsDataURL(this.files[0]); 
   
   });

   $('#img4').change(function(){
            
    let reader = new FileReader();
 
    reader.onload = (e) => { 
 
      $('#img4-preview').attr('src', e.target.result); 
    }
 
    reader.readAsDataURL(this.files[0]); 
   
   });
   
});
 
</script>


            @endsection