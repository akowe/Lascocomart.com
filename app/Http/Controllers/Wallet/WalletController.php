<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\SMS;
use App\Models\Profile;
use App\Models\Voucher;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Models\Loan;
use App\Models\LoanType;
use App\Models\LoanRepayment;
use App\Models\LoanSetting;
use App\Models\DueLoans;
use App\Models\LoanPaymentTransaction;
use App\Models\Settings;
use App\Models\ChooseBank;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Credit;
use App\Models\ShippingDetail;
use App\Models\Transaction;
use App\Models\Categories;
use App\Models\Product;

use Carbon\Carbon;
use Auth;
use Validator;
use Session;
use Paystack;
use Storage;
use Mail;
use Notification;
use DateTime;


class WalletController extends Controller
{
    public function __construct(){
        $this->middleware(['auth','verified']);
    }
    public function userWallet(Request $request){
        if( Auth::user()){
            $code = Auth::user()->code; 
            $id = Auth::user()->id; 
            $checkUser =  DB::table('wallet')
            ->select(['wallet_account_number'])
            ->where('user_id', $id)
            ->where('cooperative_code', $code)
            ->pluck('wallet_account_number')->first();
            if(empty($checkUser)){
                Session::flash('no-wallet', ' You do not have a wallet. Kindly create one!'); 
            }
            return view('wallet.user-wallet', compact('checkUser'));
        }
        else { return Redirect::to('/login');}
    }

    public  function createWallet(){
        return view('wallet.create-wallet');
    }

    public function storeWallet(Request $request){
        $id = Auth::user()->id;
        $cooperativeCode = Auth::user()->code;
        $role = Auth::user()->role_name;
        $firstname      = $request->firstname;
        $surname        = $request->surname;
        $phone          = $request->phone;
        $gender         = $request->gender;
        $dob            = $request->dob;
        $bvn            = $request->bvn;

        $wallet = new Wallet;
        $wallet->user_id                = $id;
        $wallet->cooperative_code       = $cooperativeCode;
        $wallet->cooperative_role       = $role;
        $wallet->firstname              = $firstname;
        $wallet->surname                = $surname;
        $wallet->phone                  = $phone;
        $wallet->gender                 = $gender;
        $wallete->dob                   = $dob;
        $wallet->save();

        if($wallet){
            $pin = mt_rand(100000, 999999)
                . mt_rand(100000, 999999);
            // shuffle the result
            $generateOtp = str_shuffle($pin);
            $otp = new OTP;
            $otp->code = $generateOtp;
            $otp->save();
            //send SMS
            //implemented sms
            $country_code = '234';
      
            $json_url = "http://api.ebulksms.com:8080/sendsms.json";
            $username = 'lascocomart@gmail.com';
            $apikey = '';
            $sendername = 'LascocoMart';
            $messagetext = 'Kindly enter this '.$generateOtp.' code to verify your BVN';
            $gsm = array();
            $country_code = $country_code;
            $arr_recipient = explode(',', ltrim($phone, "0"));
            $generated_id = uniqid('int_', false);
            $generated_id = substr($generated_id, 0, 30);
            $gsm['gsm'][] = array('msidn' => $arr_recipient, 'msgid' => $generated_id);
            $mss = array(
            'sender' => $sendername,
            'messagetext' => $messagetext,
            );
            $request = array('SMS' => array(
            'auth' => array(
            'username' => $username,
            'apikey' => $apikey
            ),
            'message' => $mss,
            'recipients' => $gsm
            ));

            $json_data = json_encode($request);
            if($json_data) {
              $curl = curl_init();
              curl_setopt_array($curl, array(
              CURLOPT_URL => $json_url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              //CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_SSL_VERIFYPEER => false,
              //CURLOPT_CAINFO, "C:/xampp/cacert.pem",
              //CURLOPT_CAPATH, "C:/xampp/cacert.pem",
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>$json_data,
                CURLOPT_HTTPHEADER => array(
                  'Content-Type: application/json'
                )
              ));
              $response = curl_exec($curl);
              $err = curl_error($curl);
              $res = json_decode($response, true);
            }
            if($err){
              $message ="message is not sent";
              return redirect('create-wallet')->with('sms-error', $message);
            }elseif($response){
              $message ="SMS has been sent to your phone";             
              return redirect('bvn-verify-consent/'.$bvn)->with('sms', $message);
            }
            else {
                $status = false;
                $message ="bvn not verified";               
                return redirect('create-wallet')->with('sms-error', $message);
              }  
        }  
    }

    public function bvnConsent(Request $request, $bvn){
        return view('wallet.bvn-consent');
    }

    public function createWalletAccount(Request $request){
        $id = Auth::user()->id;
        $this->validate($request, [
            'otp'         => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:6|max:6',
            ]);
        $otp = $request->otp;
        $bvn = $request->bvn;
        $checkOtp= Otp::where('code', $otp)->exists();
        if($checkOtp){
            $firstname = DB::table('wallet')
            ->select(['firstname'])
            ->where('user_id', $id)
            ->where('deleted_at', '=', null)
            ->pluck('firstname')->first();

            $surname = DB::table('wallet')
            ->select(['surname'])
            ->where('user_id', $id)
            ->where('deleted_at', '=', null)
            ->pluck('surname')->first();

            $phone = DB::table('wallet')
            ->select(['phone'])
            ->where('user_id', $id)
            ->where('deleted_at', '=', null)
            ->pluck('phone')->first();

            $gender = DB::table('wallet')
            ->select(['gender'])
            ->where('user_id', $id)
            ->where('deleted_at', '=', null)
            ->pluck('gender')->first();

            $dob = DB::table('wallet')
            ->select(['dob'])
            ->where('user_id', $id)
            ->where('deleted_at', '=', null)
            ->pluck('dob')->first();
            //Ogaranya Wallet Account 
            //staging: https://api.staging.ogaranya.com/v1/2347033141516/wallet
            //production:  https://api.ogaranya.com/v1/2347033141516/wallet
            $json_url = "https://api.staging.ogaranya.com/v1/2347033141516/wallet";
            $data = array(
                    'firstname'     => $firstname,
                    'surname'       => $surname,
                    'phone'         => $phone,
                    'gender'        => $gender,
                    'dob'           => $dob,
                    'bvn'           => $bvn
                );
                $json_data = json_encode($data);
                if($json_data) {
                  $curl = curl_init();
                  curl_setopt_array($curl, array(
                  CURLOPT_URL => $json_url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_SSL_VERIFYPEER => false,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>$json_data,
                    CURLOPT_HTTPHEADER => array(
                      'Content-Type: application/json',
                      'token: e4f3f028-c0b4-4c9b-b8ef-8be41a7613f6' ,
                      'publickey: 62f2da03d13992642d5416b3b1977071bf3adfe99a93b8daea6194306b168b84901f49025f25a245f083b0d627c921f5642ff124047e4a143dfe4cc1dd526d1b'
                    )
                  ));
                  $response = curl_exec($curl);
                  $err = curl_error($curl);
                  $res = json_decode($response, true);
                }
                if($response->status == 'success'){
                    $account_number  =  $response->data->account_number;
                    $fullname        =  $response->data->full_name;
                    $bankName        =  $response->data->bank_name;
                     //update user wallet
                     $updateWallet = Wallet::where('user_id', $id)
                     ->update([
                         'fullname'              => $fullname,
                         'bank_name'             => $bankName,
                         'wallet_account_number' => $account_number,
                     ]);
                         $message ="Wallet successfully created";             
                         return redirect('wallet')->with('wallet', $message);
                  }
                  elseif($err){
                    $message ="Opps! something went wrong";
                    return redirect('create-wallet')->with('sms-error', $message);
                  }
                  else{ 
                    $message ="Opps! something went wrong";
                    return redirect('create-wallet')->with('sms-error', $message);
                  }
        }//end check otp
        else{
            $message ="Invalid Otp";
            return redirect()->back()->with('otp-error', $message); 
        }
    }
}//class
