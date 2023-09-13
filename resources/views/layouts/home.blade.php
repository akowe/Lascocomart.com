<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/adminx.css') }}" media="screen" />
      <!-- CSRF Token -->
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <link rel="icon" type="image/x-icon" href="./images/favicon.ico">
      <title>{{ config('app.name', 'LascocoMart') }}</title>
      <!-- Scripts -->
      <script src="{{ asset('js/app.js') }}" defer></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
      <!-- Fonts -->
      <link rel="dns-prefetch" href="//fonts.gstatic.com">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
            integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
      <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css">
      <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
     <!-- Styles -->
      <link href="{{ asset('css/app.css') }}" rel="stylesheet">

      <style>
      button.dt-button,
      div.dt-button,
      a.dt-button {
            position: relative;
            display: inline-block;
            box-sizing: border-box;
            margin-right: 0.333em;
            padding: 0.2em 1em;
            border: 1px solid #D10024;
            border-radius: 2px;
            cursor: pointer;
            font-size: 0.88em;
            color: #000;
            white-space: nowrap;
            overflow: hidden;
            background-color: rgba(209, 0, 36, 0.1);
      }
      div.dataTables_wrapper div.dataTables_length select {
            width: 75px !important;
            display: inline-block;
      }
      div.dataTables_wrapper div.dataTables_length  {
            width: 75px !important;
            display: inline-block;
      }
      </style>
      <!--Start of Tawk.to Script-->
      <script type="text/javascript">
      var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
      (function() {
            var s1 = document.createElement("script"),
                  s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/64973fe5cc26a871b0247a7d/1h3nd36r6';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
      })();
      </script>
      <!--End of Tawk.to Script-->

</head>

<body>

      <div id="app" class="adminx-container">
            <!-- COOP NAV BAR-->
            <nav class="navbar navbar-expand justify-content-between fixed-top">
                  <a class="navbar-brand mb-0 h1 d-none d-md-block text-danger" href="{{ url('/') }}">
                        <img src="{{ asset('admin/img/lascoco-logo.png') }}" class="d-inline-block align-top mr-2"  width="139" height="93" alt="Lascoco" title="Lascoco">
                           
                  </a>
                  <div class="d-flex flex-1 d-block d-md-none">
                        <a href="#" class="sidebar-toggle ml-3">
                              <i data-feather="menu"></i>
                        </a>
                  </div>
                  <ul class="navbar-nav d-flex justify-content-end mr-2">
                        <!-- Notificatoins -->
                        <li class="nav-item dropdown d-flex align-items-center mr-2">

                        </li>

                        <!-- <li class="nav-item dropdown d-flex align-items-center mr-2">

                        </li> -->
                        <li class="nav-item dropdown">
                              <a id="navbarDropdown" class="nav-link " href="#" role="button" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" v-pre title="Orders from members">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span
                                          class="badge badge-light bg-danger badge-xs">{{auth()->user()->unreadNotifications()->where('type', 'App\Notifications\NewOrder')->count()}}</span>
                              </a>
                              <ul class="dropdown-menu">
                                    @if (auth()->user()->unreadNotifications->where('type',
                                    'App\Notifications\NewOrder'))
                                    <li class="d-flex justify-content-end mx-1 my-2">
                                          <a href="{{route('read-all-order')}}"
                                                class="btn btn-danger  btn-xs btn-block text-sm">Mark All as
                                                Read</a>
                                    </li>
                                    @endif

                                    @foreach (auth()->user()->unreadNotifications->where('type',
                                    'App\Notifications\NewOrder') as $notification)
                                    <a href="{{ url('read-admin-order') }}/{{ $notification->id }}"
                                          data-id="{{$notification->id}}" class="text-success">
                                          <li class="p-1 text-primary"> {{$notification->data['data']}}</li>
                                    </a>
                                    @endforeach
                                  
                              </ul>
                        </li>

                        <li class="nav-item dropdown">
                              <a id="navbarDropdown" class="nav-link " href="#" role="button" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" v-pre title="Delivery">
                                    <i class="fa fa-truck"></i>
                                    <span
                                          class="badge badge-light bg-success badge-xs">{{auth()->user()->unreadNotifications()->where('type', 'App\Notifications\ProductDelivered')->count()}}</span>
                              </a>
                              <ul class="dropdown-menu">
                                    @if (auth()->user()->unreadNotifications->where('type',
                                    'App\Notifications\ProductDelivered'))
                                    <li class="d-flex justify-content-end mx-1 my-2">
                                          <a href="{{route('mark-as-read')}}"
                                                class="btn btn-success  btn-xs btn-block text-sm">Mark All as
                                                Read</a>
                                    </li>
                                    @endif

                                    @foreach (auth()->user()->unreadNotifications->where('type',
                                    'App\Notifications\ProductDelivered') as $notification)
                                    <a href="{{ url('read') }}/{{ $notification->id }}" data-id="{{$notification->id}}"
                                          class="text-success">
                                          <li class="p-1 text-primary"> {{$notification->data['data']}}</li>
                                    </a>
                                    @endforeach
                                   
                              </ul>
                        </li>
                           <li class="nav-item dropdown d-flex align-items-center mr-2">

                        </li>
                        <li class="nav-item dropdown">
                              <a id="navbarDropdown" class="nav-link " href="#" role="button" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" v-pre title="">
                                    <i class="fa fa-money"></i>
                                    <span
                                          class="badge badge-light bg-danger badge-xs">{{auth()->user()->unreadNotifications()->where('type', 'App\Notifications\ApproveFund')->count()}}</span>
                              </a>
                              <ul class="dropdown-menu">
                                    @if (auth()->user()->unreadNotifications->where('type',
                                    'App\Notifications\ApproveFund'))
                                    <li class="d-flex justify-content-end mx-1 my-2">
                                          <a href="{{route('read-all-approve-funds')}}"
                                                class="btn btn-danger  btn-xs btn-block text-sm">Mark All as
                                                Read</a>
                                    </li>
                                    @endif

                                    @foreach (auth()->user()->unreadNotifications->where('type',
                                    'App\Notifications\ApproveFund') as $notification)
                                    <a href="{{ url('read-approve-funds') }}/{{ $notification->id }}"
                                          data-id="{{$notification->id}}" class="text-success">
                                          <li class="p-1 text-primary"> {{$notification->data['data']}}</li>
                                    </a>
                                    @endforeach
                                  
                              </ul>
                        </li>

                        <li class="nav-item dropdown d-flex align-items-center mr-2">

                        </li>
                        <!-- Notifications -->
                        @guest
                        @if (Route::has('login'))
                        <li class="nav-item">
                              <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @endif

                        @if (Route::has('register'))
                        <li class="nav-item">
                              <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                        @endif
                        @else
                        <li class="nav-item dropdown">
                              <a class="nav-link avatar-with-name text-capitalize text-dark" id="navbarDropdownMenuLink"
                                    data-toggle="dropdown" href="#">
                                    {{ Auth::user()->coopname }} &nbsp;
                                  
                              </a>
                              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                    <a class="dropdown-item" href="{{ url('profile') }}">My Profile</a>
                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                          {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                          @csrf
                                    </form>
                              </div>
                        </li>
                        @endguest
                  </ul>
            </nav>
            <!-- END COOP NAV BAR-->



            <!-- MERCHANT NAV BAR-->
            @auth
            @if(Auth::user()->role_name == 'merchant')

            <nav class="navbar navbar-expand justify-content-between fixed-top">
                  <a class="navbar-brand mb-0 h1 d-none d-md-block text-danger" href="{{ url('/') }}">
                  <img src="{{ asset('admin/img/lascoco-logo.png') }}" class="d-inline-block align-top mr-2"  width="139" height="93" alt="Lascoco" title="Lascoco">
                           
                  </a>

                  <div class="d-flex flex-1 d-block d-md-none">
                        <a href="#" class="sidebar-toggle ml-3">
                              <i data-feather="menu"></i>
                        </a>
                  </div>

                  <ul class="navbar-nav d-flex justify-content-end mr-2">
                        <!-- Notificatoins -->
                        <li class="nav-item dropdown d-flex align-items-center mr-2">

                        </li>

                        <!-- <li class="nav-item dropdown d-flex align-items-center mr-2">

                        </li> -->

                        <li class="nav-item dropdown">
                              <a id="navbarDropdown" class="nav-link " href="#" role="button" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" v-pre title="Delivery">
                                    <i class="fa fa-coins"></i>
                                    <span
                                          class="badge badge-light bg-success badge-xs">{{auth()->user()->unreadNotifications()->where('type', 'App\Notifications\NewCardPayment')->count()}}</span>
                              </a>
                              <ul class="dropdown-menu">
                                    @if (auth()->user()->unreadNotifications->where('type',
                                    'App\Notifications\NewCardPayment'))
                                    <li class="d-flex justify-content-end mx-1 my-2">
                                          <a href="{{route('read-all-payment')}}"
                                                class="btn btn-success  btn-xs btn-block text-sm">Mark All as
                                                Read</a>
                                    </li>
                                    @endif

                                    @foreach (auth()->user()->unreadNotifications->where('type',
                                    'App\Notifications\NewCardPayment') as $notification)
                                    <a href="{{ url('read-seller-payment') }}/{{ $notification->id }}"
                                          data-id="{{$notification->id}}" class="text-success">
                                          <li class="p-1 text-primary"> {{$notification->data['data']}}</li>
                                    </a>
                                    @endforeach
                                  
                              </ul>
                        </li>
                        <li class="nav-item dropdown d-flex align-items-center mr-2">

                        </li>
                        <!-- Notifications -->
                        @guest
                        @if (Route::has('login'))
                        <li class="nav-item">
                              <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @endif

                        @if (Route::has('register'))
                        <li class="nav-item">
                              <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                        @endif
                        @else
                        <li class="nav-item dropdown">
                              <a class="nav-link avatar-with-name text-capitalize text-dark" id="navbarDropdownMenuLink"
                                    data-toggle="dropdown" href="#">
                                    {{ Auth::user()->coopname }}
                                  
                              </a>
                              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                    <a class="dropdown-item" href="{{ url('profile') }}">My Profile</a>
                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                          {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                          @csrf
                                    </form>
                              </div>
                        </li>
                        @endguest
                  </ul>
            </nav>
            @endif
            @endauth
            <!-- END MERCHANT NAV BAR-->



            <!-- SUPERADMIN NAV BAR-->
            @auth
            @if(Auth::user()->role_name == 'superadmin')
            <nav class="navbar navbar-expand justify-content-between fixed-top">
                  <a class="navbar-brand mb-0 h1 d-none d-md-block text-danger" href="{{ url('/') }}">
                  <img src="{{ asset('admin/img/lascoco-logo.png') }}" class="d-inline-block align-top mr-2"  width="139" height="93" alt="Lascoco" title="Lascoco">
                           
                  </a>
                  <div class="d-flex flex-1 d-block d-md-none">
                        <a href="#" class="sidebar-toggle ml-3">
                              <i data-feather="menu"></i>
                        </a>
                  </div>

                  <ul class="navbar-nav d-flex justify-content-end mr-2">
                        <!-- Notificatoins -->
                        <li class="nav-item dropdown d-flex align-items-center mr-2">

                        </li>
                        <li class="nav-item dropdown">
                              <a id="navbarDropdown" class="nav-link " href="#" role="button" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" v-pre title="Products">
                                    <i class="fa fa-coins" aria-hidden="true"></i>
                                    <span
                                          class="badge badge-light bg-success badge-xs">{{auth()->user()->unreadNotifications()->where('type', 'App\Notifications\NewCardPayment')->count()}}</span>
                              </a>
                              <ul class="dropdown-menu">
                                    @if (auth()->user()->unreadNotifications->where('type',
                                    'App\Notifications\NewCardPayment'))
                                    <li class="d-flex justify-content-end mx-1 my-2">
                                          <a href="{{route('read-all-payment')}}"
                                                class="btn btn-success btn-xs btn-block text-sm">Mark All as
                                                Read</a>
                                    </li>
                                    @endif

                                    @foreach (auth()->user()->unreadNotifications->where('type',
                                    'App\Notifications\NewCardPayment') as $notification)
                                    <a href="{{ url('read-company-payment') }}/{{ $notification->id }}"
                                          data-id="{{$notification->id}}" class="text-success">
                                          <li class="p-1 text-primary"> {{$notification->data['data']}}</li>
                                    </a>
                                    @endforeach
                                   
                              </ul>
                        </li>

                        <li class="nav-item dropdown">
                              <a id="navbarDropdown" class="nav-link " href="#" role="button" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" v-pre title="Products">
                                    <i class="fa fa-product-hunt" aria-hidden="true"></i>
                                    <span
                                          class="badge badge-light bg-warning badge-xs">{{auth()->user()->unreadNotifications()->where('type', 'App\Notifications\NewProduct')->count()}}</span>
                              </a>
                              <ul class="dropdown-menu">
                                    @if (auth()->user()->unreadNotifications->where('type',
                                    'App\Notifications\NewProduct'))
                                    <li class="d-flex justify-content-end mx-1 my-2">
                                          <a href="{{route('mark-as-read')}}"
                                                class="btn btn-warning  btn-xs btn-block text-sm">Mark All as
                                                Read</a>
                                    </li>
                                    @endif

                                    @foreach (auth()->user()->unreadNotifications->where('type',
                                    'App\Notifications\NewProduct') as $notification)
                                    <a href="{{ url('read-product') }}/{{ $notification->id }}"
                                          data-id="{{$notification->id}}" class="text-success">
                                          <li class="p-1 text-primary"> {{$notification->data['data']}}</li>
                                    </a>
                                    @endforeach
                                   
                              </ul>
                        </li>
                        <!-- <li class="nav-item dropdown d-flex align-items-center mr-2">

                        </li> -->

                        <li class="nav-item dropdown">

                              <a id="navbarDropdown" class="nav-link " href="#" role="button" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" v-pre title="Delivery">
                                    <i class="fa fa-truck"></i>
                                    <span class="badge badge-light bg-info badge-xs">
                                          {{auth()->user()->unreadNotifications()
                                                      ->where('type', 'App\Notifications\ProductDelivered')
                                                      ->orwhere('type', 'App\Notifications\ProductReceived')
                                                ->count()}}</span>
                              </a>
                              <ul class="dropdown-menu">
                                    @if (auth()->user()->unreadNotifications
                                    ->where('type','App\Notifications\ProductDelivered'))
                                    <li class="d-flex  mx-1 my-2">
                                          <a href="{{route('product-delivered')}}"
                                                class="btn btn-info  btn-xs text-sm">Mark
                                                All Delivered as
                                                Read</a>
                                    </li>
                                    @foreach (auth()->user()->unreadNotifications
                                    ->where('type','App\Notifications\ProductDelivered') as $notification)
                                    <a href="{{ url('read-product-delivered') }}/{{ $notification->id }}"
                                          data-id="{{$notification->id}}" class="text-success">
                                          <li class="p-1 text-primary"> {{$notification->data['data']}}</li>
                                    </a>
                                    @endforeach

                                    @endif

                                    @if (auth()->user()->unreadNotifications
                                    ->where('type', 'App\Notifications\ProductReceived'))
                                    <li class="d-flex  mx-1 my-2">
                                          <a href="{{route('product-received')}}"
                                                class="btn btn-info  btn-xs text-sm">Mark
                                                All Received as Read</a>
                                    </li>
                                    @foreach (auth()->user()->unreadNotifications
                                    ->where('type','App\Notifications\ProductReceived') as $notification)
                                    <a href="{{ url('read-product-received') }}/{{ $notification->id }}"
                                          data-id="{{$notification->id}}" class="text-success">
                                          <li class="p-1 text-primary"> {{$notification->data['data']}}</li>
                                    </a>
                                    @endforeach
                                    @endif
                              </ul>
                        </li>
                        <!-- <li class="nav-item dropdown d-flex align-items-center mr-2">

                        </li> -->
                        <li class="nav-item dropdown">
                              <a id="navbarDropdown" class="nav-link " href="#" role="button" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" v-pre title="Fund Request">
                                    <i class="fa fa-credit-card"></i>
                                    <span
                                          class="badge badge-light bg-danger badge-xs">{{auth()->user()->unreadNotifications()->where('type', 'App\Notifications\CooperativeFundRequest')->count()}}</span>
                              </a>
                              <ul class="dropdown-menu">
                                    @if (auth()->user()->unreadNotifications->where('type',
                                    'App\Notifications\CooperativeFundRequest'))
                                    <li class="d-flex justify-content-end mx-1 my-2">
                                          <a href="{{route('mark-as-read')}}"
                                                class="btn btn-danger  btn-xs btn-block text-sm">Mark All as
                                                Read</a>
                                    </li>
                                    @endif

                                    @foreach (auth()->user()->unreadNotifications->where('type',
                                    'App\Notifications\CooperativeFundRequest') as $notification)
                                    <a href="{{ url('read') }}/{{ $notification->id }}" data-id="{{$notification->id}}"
                                          class="text-success">
                                          <li class="p-1 text-primary"> {{$notification->data['data']}}</li>
                                    </a>
                                    @endforeach
                                  
                              </ul>
                        </li>
                        <!-- Notifications -->
                        <li class="nav-item dropdown d-flex align-items-center mr-2">
                        </li>
                        @guest
                        @if (Route::has('login'))
                        <li class="nav-item">
                              <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @endif

                        @if (Route::has('register'))
                        <li class="nav-item">
                              <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                        @endif
                        @else
                        <li class="nav-item dropdown">
                              <a class="nav-link avatar-with-name text-capitalize text-dark" id="navbarDropdownMenuLink"
                                    data-toggle="dropdown" href="#">
                                    {{ Auth::user()->fname }}
                              </a>
                              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                    <a class="dropdown-item" href="{{ url('profile') }}">My Profile</a>
                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                          {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                          @csrf
                                    </form>
                              </div>
                        </li>
                        @endguest
                  </ul>
            </nav>
            @endif
            @endauth
            <!-- END SUPERADMIN NAV BAR-->

            <!-- MEMBER NAV BAR-->
            @auth
            @if(Auth::user()->role_name == 'member')
            <nav class="navbar navbar-expand justify-content-between fixed-top">
                  <a class="navbar-brand mb-0 h1 d-none d-md-block text-danger" href="{{ url('/') }}">
                  <img src="{{ asset('admin/img/lascoco-logo.png') }}" class="d-inline-block align-top mr-2"  width="139" height="93" alt="Lascoco" title="Lascoco">
                           
                  </a>

                  <div class="d-flex flex-1 d-block d-md-none">
                        <a href="#" class="sidebar-toggle ml-3">
                              <i data-feather="menu"></i>
                        </a>
                  </div>

                  <ul class="navbar-nav d-flex justify-content-end mr-2">
                        <!-- Notificatoins -->
                        <li class="nav-item dropdown d-flex align-items-center mr-2">

                        </li>
                        <li class="nav-item dropdown">
                              <a id="navbarDropdown" class="nav-link " href="#" role="button" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" v-pre title="Orders">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span
                                          class="badge badge-light bg-info badge-xs">{{auth()->user()->unreadNotifications()->where('type', 'App\Notifications\AdminCancelOrder')->count()}}</span>
                              </a>
                              <ul class="dropdown-menu">
                                    @if(auth()->user()->unreadNotifications->where('type','App\Notifications\AdminCancelOrder'))
                                    <li class="d-flex">
                                          <a href="{{ url('read-all-cancel-order')}} "
                                                class="btn btn-info  btn-xs btn-block text-sm">Mark
                                                All as Read</a>
                                    </li>
                                    @endif

                                    @foreach (auth()->user()->unreadNotifications
                                    ->where('type','App\Notifications\AdminCancelOrder') as $notification)
                                    <a href="   {{ url('read-cancel-order') }}/{{ $notification->id }}"
                                          data-id="{{$notification->id}}" class="text-success">
                                          <li class="p-1 text-primary"> {{$notification->data['data']}}</li>
                                    </a> 
                                    @endforeach
                                   
                              </ul>
                        </li>

                        <li class="nav-item dropdown">
                              <a id="navbarDropdown" class="nav-link " href="#" role="button" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" v-pre title="Delivery">
                                    <i class="fa fa-truck"></i>
                                    <span
                                          class="badge badge-light bg-success badge-xs">{{auth()->user()->unreadNotifications()->where('type', 'App\Notifications\ProductDelivered')->count()}}</span>
                              </a>
                              <ul class="dropdown-menu">
                                    @if(auth()->user()->unreadNotifications->where('type','App\Notifications\ProductDelivered'))
                                    <li class="d-flex">
                                          <a href="{{route('product-delivered')}}"
                                                class="btn btn-success  btn-xs btn-block text-sm">Mark
                                                All as Read</a>
                                    </li>
                                    @endif

                                    @foreach (auth()->user()->unreadNotifications
                                    ->where('type','App\Notifications\ProductDelivered') as $notification)
                                    <a href="{{ url('read-product-delivered') }}/{{ $notification->id }}"
                                          data-id="{{$notification->id}}" class="text-success">
                                          <li class="p-1 text-primary"> {{$notification->data['data']}}</li>
                                    </a>
                                    @endforeach
                               
                              </ul>
                        </li>
                        <li class="nav-item dropdown d-flex align-items-center mr-2">

                        </li>
                        <!-- Notifications -->
                        @guest
                        @if (Route::has('login'))
                        <li class="nav-item">
                              <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @endif

                        @if (Route::has('register'))
                        <li class="nav-item">
                              <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                        @endif
                        @else
                        <li class="nav-item dropdown">
                              <a class="nav-link avatar-with-name text-capitalize text-dark" id="navbarDropdownMenuLink"
                                    data-toggle="dropdown" href="" title="My Profile">
                                    {{ Auth::user()->fname }}
                              </a>
                              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                    <a class="dropdown-item" href="{{ url('profile') }}">My Profile</a>
                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                          {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                          @csrf
                                    </form>
                              </div>
                        </li>
                        @endguest
                  </ul>
            </nav>
            @endif
            @endauth
            <!-- END MEMBER NAV BAR-->


            <main class="py-4">
                  @yield('content')
            </main>


            @yield('scripts')
      </div>
      <!--adminx-container-->


      <!-- footer-->
      <!--script-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
      <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
      <script src="admin/js/vendor.js"></script>
      <script src="admin/js/adminx.js"></script>
      <script src="admin/js/custom.js"></script>
      <!-- Footer row -->

      <script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

      <script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>

      <script src="https://cdn.datatables.net/responsive/2.3.0/js/responsive.bootstrap4.min.js"></script>
      <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
      <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
      <script type="text/javascript">
      $(document).ready(function() {
            $('#table').DataTable({
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

      $(document).ready(function() {
            $('#table2').DataTable({
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


      $(document).ready(function() {
            $('#table3').DataTable({
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


      $(document).ready(function() {
            $('#table4').DataTable({
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
</body>

</html>