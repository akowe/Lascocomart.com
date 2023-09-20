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
use App\Models\LogActivity ;



class CoopController extends Controller
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
        // $this->middleware('guest');
    }

    public function registerCooperative(Request $request){
        return view('auth.cooperative-register');
    }

     public function coop_insert(Request $request){
        $request->validate([
            'email'     =>'required|email|max:255|unique:users|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'fname'     => 'required|string|max:255',
            'password'  => 'required|string|min:6|confirmed',
            'code'      => 'string',
            'coopname'  => 'string',
            'address'   => 'required|max:225',
            'cooptype'  => 'required|max:225',
            'payment_days' => 'required|max:225',
            'file'      => 'required|mimes:jpg,jpeg,png|max:300',
            ]);

           $role = '2';
           $role_name = 'cooperative';
           $coopID =rand(100,999);
           $code = 'Lascoco'.$coopID;

           $image= $request->file('file');

           $extension = $request->file('file')->getClientOriginalExtension(); 
            $fileName= $request->file('file')->getClientOriginalName(); 
            $imageName =  rand(1000000000, 9999999999).'.'.$extension;
            $image->move(public_path('assets/cooperativeCert'),$imageName);
            $image_path = "/assets/cooperativeCert/".$imageName; 
           //new User;

        $user = new User();
        $user->role         = $role;
        $user->role_name    = $role_name;
        $user->fname        =$request->fname;
        $user->code         = $code;
        $user->coopname     = $request->coopname;
        $user->address      = $request->address;
        $user->cooptype     = $request->cooptype; 
        $user->payment_days = $request->payment_days; 
        $user->cooperative_cert = $image_path;
        $user->email        = $request->email;
        $user->password     = Hash::make($request['password']);
        $user->save();
         if($user){
            $voucherDigit = rand(1000000000,9999999999);
              $voucher = new Voucher();
              $voucher->user_id = $user->id;
              $voucher->voucher = $voucherDigit;
              $voucher->credit = '0';
              $voucher->save();
  
              $wallet = new Wallet();
              $wallet->user_id = $user->id;
              $wallet->credit = '0';
              $wallet->save();
            
              //LOG NEW REGISTER COOPERATIVE
                $log = new LogActivity();
                $log->subject = 'Signup';
                $log->url = $request->fullUrl();
                $log->method = $request->method();
                $log->ip= $request->ip();
                $log->agent =$request->header('user-agent');
                $log->user_id = $user->id;
                $log->save();
       
         }
            Session::flash('status', ' You have successfully registered!. <br> Verification link has been sent to your email address. <br> Check your inbox or spam/junk'); 
            Session::flash('alert-class', 'alert-success'); 
          return redirect('/')->with('status', ' You have successfully registered!. <br> Verification link has been sent to your email address. <br> Check your inbox or spam/junk');   
        
    }

    public function registerMember(Request $request){
        return view('auth.member-register');
    }

    public function createMember(Request $request)
    {
        $coop = User::where('code',  $request->code)->first();  
        $coopname = $coop->coopname;
           $role = '4';
           $role_name = 'member';

           $request->validate([
            'email'=>'required|unique:users|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            ]);

          $user = new User();
          $user->role         = $role;
          $user->role_name    = $role_name;
          $user->fname        = $request->fname;
          $user->code         = $request->code;
          $user->coopname     = $coopname;
          $user->email        = $request->email;
          $user->password     = Hash::make($request['password']);
          $user->save();
           if($user){
              $voucherDigit = rand(1000000000,9999999999);
                $voucher = new Voucher();
                $voucher->user_id = $user->id;
                $voucher->voucher = $voucherDigit;
                $voucher->credit = '0';
                $voucher->save();

                $wallet = new Wallet();
                $wallet->user_id = $user->id;
                $wallet->credit = '0';
                $wallet->save();
                //LOG NEW REGISTER MEMBER
                $log = new LogActivity();
                $log->subject = 'Signup';
                $log->url = $request->fullUrl();
                $log->method = $request->method();
                $log->ip= $request->ip();
                $log->agent =$request->header('user-agent');
                $log->user_id = $user->id;
                $log->save();
       
           }
           
            Session::flash('status', ' You have successfully registered!. <br> Verification link has been sent to your email address. <br> Check your inbox or spam/junk'); 
            Session::flash('alert-class', 'alert-success'); 
         // return $user;

          return redirect('/')->with('status', ' You have successfully registered!. <br> Verification link has been sent to your email address. <br> Check your inbox or spam/junk');   
        
        
    }
}