<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Models\Voucher;
use App\Models\Wallet;
use Session;


class FcmgsController extends Controller
{
     use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

     public function fcmgs_insert(Request $request)
    {

        $this->validate($request, [
            'fname' => 'required', 'string', 'max:255',
            'email' => 'required', 'string', 'email', 'max:255', 'unique:users',
            'password' => 'required', 'string', 'min:6', 'confirmed',
            'code' => 'string',
            // 'coopname' => 'string',
            // 'rcnumber' => 'required', 'varchar', 'max:225',
            'address' => 'required', 'varchar', 'max:225',
        ]);

           $role = '5';
           $role_name = 'fcmg';

           //$user = new User;
            $user = User::create([
            'role' => '5',
            'role_name' =>$role_name,
            'fname' => $request->fname,
            'code' => $request->code,
            // 'coopname' => $request->coopname,
            'email' => $request->email,
            'password' => Hash::make($request['password']),
            'rcnumber' => $request->rcnumber,
            'address' => $request->address,
        ]);
          event(new Registered($user));
          // $rr = rand(1000000000,9999999999);
        $rand = $request->voucher;

            $voucher = new Voucher();
            $voucher->user_id = $user->id;
            $voucher->voucher = $rand;
            $voucher->credit = '0';
            $voucher->save();
           
            $wallet = new Wallet();
            $wallet->user_id = $user->id;
            $wallet->credit = '0';
            $wallet->save();
                  //LOG NEW REGISTER FCMG
                  $log = new LogActivity();
                  $log->subject = 'Signup';
                  $log->url = $request->fullUrl();
                  $log->method = $request->method();
                  $log->ip= $request->ip();
                  $log->agent =$request->header('user-agent');
                  $log->user_id = $user->id;
                  $log->save();

            Session::flash('status', ' You have successfully registered!. <br> Verification link has been sent to your email address. <br> Check your inbox or spam/junk'); 
            Session::flash('alert-class', 'alert-success'); 
          //return $user;

          return redirect('/')->with('status', ' You have successfully registered!. <br> Verification link has been sent to your email address. <br> Check your inbox or spam/junk');   
        
         

    }
}
