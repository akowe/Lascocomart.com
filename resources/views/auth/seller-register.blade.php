<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
      <meta charset="utf-8">

      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

      <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/adminx.css') }}" media="screen" />

      <!-- CSRF Token -->
      <meta name="csrf-token" content="{{ csrf_token() }}">

      <title>{{ config('app.name', 'Lascocomart') }}</title>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>


      <!-- Scripts -->
      <script src="{{ asset('js/app.js') }}" defer></script>

      <!-- Fonts -->
      <link rel="dns-prefetch" href="//fonts.gstatic.com">
      <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">


      <!-- Styles -->
      <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>

      <div class="adminx-container">
            <!--content-->
            <div class="container">
                  <div class="row justify-content-center">
                        <div class="col-md-8">
                              <div class="header-logo text-center ">
                                    <a href="{{ url('/') }}" class="logo">
                                          <img src="./images/lascoco-logo.png" alt="LASCOCO" title="LASCOCO" width="139"
                                                height="93">
                                    </a>
                              </div>
                              <div class="card">
                                    <div class="card-header text-center ">Are you a Merchant or Seller</div>
                                    

                                    <div class="card-body">
                                    <p>Do you have products for sale in wholesale price; kindly create an account to
                                          start selling on
                                          LascocoMart</p>
                                          <form method="POST" action="{{ route('seller_insert') }}"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <div class="row mb-3">
                                                      <label for="name"
                                                            class="col-md-4 col-form-label text-md-end">Store
                                                            Name</label>

                                                      <div class="col-md-6 form-group">
                                                            <input type="text" name="coopname" value=""
                                                                  class="form-control">

                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <label for="name" class="col-md-4 col-form-label text-md-end">Full
                                                            Name</label>

                                                      <div class="col-md-6 form-group">
                                                            <input id="fname" type="text" name="fname" value="" required
                                                                  class="form-control">
                                                            @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                  <strong>{{ $message }}</strong>
                                                            </span>
                                                            @enderror
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <label for="email"
                                                            class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                                      <div class="col-md-6 form-group ">
                                                            <input id="email" type="email"
                                                                  class="form-control @error('email') is-invalid @enderror"
                                                                  name="email" value="{{ old('email') }}" required
                                                                  autocomplete="email">

                                                            @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                  <strong>{{ $message }}</strong>
                                                            </span>
                                                            @enderror
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <label for="password"
                                                            class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                                      <div class="col-md-6 ">
                                                            <span class="small text-primary">minimum length: 6</span>
                                                            <div class="form-group">
                                                            <input id="password" type="password"
                                                                  class="@error('password') is-invalid @enderror input-field form-control "
                                                                  name="password" required autocomplete="new-password">

                                                            </div>
                                                            @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                  <strong>{{ $message }}</strong>
                                                            </span>
                                                            @enderror
                                                      </div>
                                                </div>


                                                <div class="row mb-3">
                                                      <label for="password-confirm"
                                                            class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                                                      <div class="col-md-6 ">
                                                            <div class="form-group">
                                                            <input id="password-confirm" type="password"
                                                                  class="form-control" name="password_confirmation"
                                                                  required autocomplete="new-password">

                                                            </div>
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <p></p>
                                                      <label for="" class="col-md-4 col-form-label text-md-end"></label>
                                                      <div class="col-md-6 " style="font-size:14px;">
                                                      <p> <input type="checkbox" required>  I agree to receive payment for products within 1 to 14 days after successful delivery to the  cooperative/member</p>

                                                            <input type="checkbox" required> I have read and agree to LascocoMart <a
                                                                  href="https://lascocomart.com/terms" class="text-danger"
                                                                  title="Click here to read">terms & condition</a>
                                                                 
                                                      </div>
                                                </div>
                                                <div class="form-group text-center">
                                                      <br>
                                                      <button type="submit" class="btn btn-danger btn-block">Create
                                                            Account</button>
                                                </div>
                                          </form>

                                    </div>


                              </div>
                        </div>
                        <!--card-->
                        <div class="text-center">

                              @if (Route::has('login'))

                              Already have an account? <a class="" href="{{ route('login') }}">{{ __('Login') }} &nbsp;</a>

                              @endif
                              <br><br>
                        </div>

                  </div>
            </div>
      </div>


      </div>
      <!--adminx-container-->
      <div class="container">
            <p></p>
      </div>



      <!-- footer-->
      <!--script-->
      <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
      <script src="admin/js/vendor.js"></script>
      <script src="admin/js/adminx.js"></script>
      <script src="admin/js/custom.js"></script>
</body>

</html>