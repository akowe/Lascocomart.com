<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = RouteServiceProvider::HOME;
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectTo()
    {
    
    switch(Auth::user()->role){
        case 1:
            $this->redirectTo = '/superadmin';
            return $this->redirectTo;
            break;

        case 2:
            $this->redirectTo = '/cooperative';
            return $this->redirectTo;
            break;

        case 3:
            $this->redirectTo = '/merchant';
            return $this->redirectTo;
            break;
        
        case 4:
            $this->redirectTo = '/checkout';
            return $this->redirectTo;
            break;
        
        case 5:
            $this->redirectTo = '/fcmg';
            return $this->redirectTo;
            break;

        default:
            $this->redirectTo = 'login';
            return $this->redirectTo;
        }

}

}//class
