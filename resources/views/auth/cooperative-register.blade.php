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
                                    <div class="card-header text-center ">Cooperative Organization</div>

                                    <div class="card-body">
                                          <form method="POST" action="{{ route('coop_insert') }}"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <div class="row mb-3">
                                                      <label for="name"
                                                            class="col-md-4 col-form-label text-md-end">Cooperative
                                                            Name</label>
                                                      <div class="col-md-6 form-group">
                                                            <input id="coopname" type="text" name="coopname" required
                                                                  class="form-control">

                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <label for="name" class="col-md-4 col-form-label text-md-end">
                                                            Address</label>

                                                      <div class="col-md-6 form-group ">
                                                            <input id="address" type="text" name="address" value=""
                                                                  required class="form-control">

                                                            @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                  <strong>{{ $message }}</strong>
                                                            </span>
                                                            @enderror
                                                      </div>
                                                </div>


                                                <div class="row mb-3">
                                                      <label for="type" class="col-md-4 col-form-label text-md-end">
                                                            Cooperative Type</label>

                                                      <div class="col-md-6 form-group">
                                                            <input id="type" list="browsers" name="cooptype" 
                                                                  required class="form-control">
                                                            <datalist id="browsers">
                                          
                                                                  <option value="Coperate">
                                                                  <option value="Industrial">
                                                                  <option value="Community">
                                                                  <option value="NGO">
                                                                  <option value="Others">
                                                            </datalist>
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <label for="name"
                                                            class="col-md-4 col-form-label text-md-end">Admin Full
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
                                                      <label for="name"
                                                            class="col-md-4 col-form-label text-md-end">Upload Certificate
                                                      </label>

                                                      <div class="col-md-6 form-group ">
                                                      <span class="small text-danger">image type: jpeg or jpg or png</span>

                                                            <input type="file" id="file-upload" name="file"
                                                                  accept=".jpg,.jpeg,.png" class="form-control" multiple
                                                                  required />
                                                                  <span class="small text-danger">image size max: 300 kb</span>

                                                            @error('cert')
                                                            <span class="invalid-feedback" role="alert">
                                                                  <strong>{{ $message }}</strong>
                                                            </span>
                                                            @enderror
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <label for="name"
                                                            class="col-md-4 col-form-label text-md-end">Payment Days
                                                      </label>

                                                      <div class="col-md-6 form-group ">
                                                      <input id="payment" list="days" name="payment_days" 
                                                                  required class="form-control">
                                                            <datalist id="days">
                                          
                                                                  <option value="immediately after delivery">
                                                                  <option value="1 - 7 days after delivery">
                                                                  <option value="7 - 14 days after delivery">
                                                            </datalist>


                                                            @error('payment_days')
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
                                                            <input id="password" type="password"
                                                                  class="@error('password') is-invalid @enderror input-field form-control "
                                                                  name="password" required autocomplete="new-password">

                                                            <span class="small text-danger">Password minimum length:
                                                                  6</span>

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
                                                            <input id="password-confirm" type="password"
                                                                  class="form-control" name="password_confirmation"
                                                                  required autocomplete="new-password">

                                                      </div>
                                                </div>
                                                <div class="row mb-3">
                                                      <p></p>
                                                      <label for="" class="col-md-4 col-form-label text-md-end"></label>
                                                      <div class="col-md-6 " style="font-size:14px;">

                                                            <input type="checkbox" required> I have read and agree to LascocoMart <a
                                                                  href="https://lascocomart.com/terms" class="text-danger"
                                                                  title="Click here to read">terms & condition</a>
                                                      </div>
                                                </div>
                                                      <div class="form-group text-center">
                                                        <br>
                                                            <button type="submit"
                                                                  class="btn btn-danger btn-block">Create
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