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

use App\Models\Voucher;
use App\Models\Wallet;
use Session;

class NewAdminUserController extends Controller
{
    use RegistersUsers;
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct(){
      //
    }
    
    public function newAdminUser(Request $request){

        $this->validate($request, [
           'email'     => 'required|email|max:255|unique:users|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',   'email' => 'required', 'string', 'email', 'max:255', 'unique:users',
           'fname'     => 'string|max:255',
           //'password'  => 'required|string|min:6|confirmed',
           'role_name' => 'required|string',
           'code'      => 'string',
           'coopname'  => 'string',
           'phone'     => 'number|max:13' 
           
        ]);

         if($request->role_name == 'fcmg'){
            $role = '5';
         }elseif($request->role_name == 'cooperative'){
            $role = '2';
         }
         else{
            $role ='';
         }
        
           $lascocoID =rand(100,999);
           $code = 'Lascoco'.$lascocoID;

            $user       = User::create([
            'role'      => $role,
            'role_name' =>$role_name,
            'fname'     => $request->fname,
            'code'      => $code,
            'coopname'  => $request->coopname,
            'phone'     => $request->phone,
            'email'     => $request->email,
            //'password'  => Hash::make($request['password']),
        ]);
        event(new Registered($user));
        $rand = rand(1000000000,9999999999);
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
        return redirect('/')->with('status', ' You have successfully registered!. <br> Verification link has been sent to your email address. <br> Check your inbox or spam/junk');         
    }
}
