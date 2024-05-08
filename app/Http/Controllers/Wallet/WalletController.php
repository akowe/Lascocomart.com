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
use App\Models\FundWallet;
use App\Models\CashTransfer;
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
            $phoneNumber =  DB::table('wallet')
            ->select(['phone'])
            ->where('user_id', $id)
            ->where('cooperative_code', $code)
            ->pluck('phone')->first();

            $WalletAccountNumber =  DB::table('wallet')
            ->select(['wallet_account_number'])
            ->where('user_id', $id)
            ->where('cooperative_code', $code)
            ->pluck('wallet_account_number')->first();
            if(empty($WalletAccountNumber)){
                Session::flash('no-wallet', ' You do not have a wallet. Kindly create one!'); 
            }
            $WalletAccountName = DB::table('wallet')
            ->select(['fullname'])
            ->where('user_id', $id)
            ->where('cooperative_code', $code)
            ->pluck('fullname')->first(); 

            $WalletBankName = DB::table('wallet')
            ->select(['bank_name'])
            ->where('user_id', $id)
            ->where('cooperative_code', $code)
            ->pluck('bank_name')->first(); 

            $phoneNumber = DB::table('wallet')
            ->select(['phone'])
            ->where('user_id', $id)
            ->where('cooperative_code', $code)
            ->pluck('phone')->first();
  
            //Ogaranya Wallet Account 
          //staging: https://api.staging.ogaranya.com/v1/2347033141516/wallet
          //'token: e4f3f028-c0b4-4c9b-b8ef-8be41a7613f6',
          //'publickey: 62f2da03d13992642d5416b3b1977071bf3adfe99a93b8daea6194306b168b84901f49025f25a245f083b0d627c921f5642ff124047e4a143dfe4cc1dd526d1b',
          
          //production:  https://api.ogaranya.com/v1/2347033141516/wallet
          // 'token: MDY0OTgzMTkxNjIzNGViZDA3YWIxZWMwZTFjYzY2Mzk1OTAwYjYwNTc2ZjY4NzBlOTBlMGQzMjk5YzJlZmUxZA==',
          // 'publickey: 4f223ac9cff724d03833fb8fb9e1a0638dc5125696420cc33c71bcf2e35a0af08beb8cd85a0c0c2eca2670d0244ca70bb9dff6bfa081def75cdaab1034beb1fe',
          $data = array(
            "phone"            => $phoneNumber,
            "account_number"   => $WalletAccountNumber,
            );
            $jsonData = json_encode($data);
             $url = "https://api.staging.ogaranya.com/v1/2347033141516/wallet/info";
            if($jsonData) {
                     $curl = curl_init();
                     curl_setopt_array($curl, array(
                     CURLOPT_URL => $url,
                     CURLOPT_RETURNTRANSFER => true,
                     CURLOPT_CUSTOMREQUEST => 'POST',
                     CURLOPT_POSTFIELDS =>$jsonData,
                     CURLOPT_HTTPHEADER => array(
                       'Content-Type: application/json',
                       'token: e4f3f028-c0b4-4c9b-b8ef-8be41a7613f6',
                        'publickey: 62f2da03d13992642d5416b3b1977071bf3adfe99a93b8daea6194306b168b84901f49025f25a245f083b0d627c921f5642ff124047e4a143dfe4cc1dd526d1b',
          
                       )
                     ));
                  $res = curl_exec($curl);
                  $error = curl_error($curl);
                  curl_close($curl);
                  $result =  json_decode($res, true);
                  //dd($result);
                }
                 if($result['status'] == 'success'){
                  $accountBalance = $result['data']['available_balance'];
                 }
                 if($result['status'] == 'error'){
                  return view('wallet.user-wallet', compact('WalletAccountNumber',
                'WalletAccountName', 'WalletBankName', 'phoneNumber'));
                  }
                  
            return view('wallet.user-wallet', compact('WalletAccountNumber',
            'WalletAccountName', 'WalletBankName', 'phoneNumber', 'accountBalance'));
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
            $apikey = 'd34fc300d4f1466b291f54cf895d87ef51a42a46';
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
//for otp to verify bvn
    public function bvnConsent(Request $request, $bvn){
        return view('wallet.bvn-consent');
    }

    public function createWalletAccount(Request $request){
        $id = Auth::user()->id;
        $cooperativeCode = Auth::user()->code;
        $role = Auth::user()->role_name;
        $this->validate($request, [
          'firstname'         => 'required|max:255',  
          'surname'           => 'required|max:255',  
          'phone'             => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:11',
          'gender'            => 'required|max:255',
          'date_of_birth'     => 'required|max:255',  
          'bvn'               => 'required|min:11|max:11', 
          ]);
        $countryCode = '234';
        $trimPhone = explode(',', ltrim($request->phone, "0"));
        $getPhone = implode("", $trimPhone);
        $phoneNumber =  $countryCode.$getPhone; 

        $firstname      = $request->firstname;
        $surname        = $request->surname;
        $phone          = $phoneNumber;
        $gender         = $request->gender;
        $dob            = $request->date_of_birth;
        $bvn            = $request->bvn;

        // $checkOtp= Otp::where('code', $otp)->exists();
        //Ogaranya Wallet Account 
        //staging: https://api.staging.ogaranya.com/v1/2347033141516/wallet
        //'token: e4f3f028-c0b4-4c9b-b8ef-8be41a7613f6',
        //'publickey: 62f2da03d13992642d5416b3b1977071bf3adfe99a93b8daea6194306b168b84901f49025f25a245f083b0d627c921f5642ff124047e4a143dfe4cc1dd526d1b',
        
        //production:  https://api.ogaranya.com/v1/2347033141516/wallet
        // 'token: MDY0OTgzMTkxNjIzNGViZDA3YWIxZWMwZTFjYzY2Mzk1OTAwYjYwNTc2ZjY4NzBlOTBlMGQzMjk5YzJlZmUxZA==',
        // 'publickey: 4f223ac9cff724d03833fb8fb9e1a0638dc5125696420cc33c71bcf2e35a0af08beb8cd85a0c0c2eca2670d0244ca70bb9dff6bfa081def75cdaab1034beb1fe',
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
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>$json_data,
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'token: e4f3f028-c0b4-4c9b-b8ef-8be41a7613f6',
                'publickey: 62f2da03d13992642d5416b3b1977071bf3adfe99a93b8daea6194306b168b84901f49025f25a245f083b0d627c921f5642ff124047e4a143dfe4cc1dd526d1b',
                )
              ));
              $response = curl_exec($curl);
              $err = curl_error($curl);
              curl_close($curl);
              $result =  json_decode($response, true);
            }
            if($result['status'] == 'success'){
              $account_number = $result['data']['account_number'];
              $fullname = $result['data']['full_name'];
              $bank_name = $result['data']['bank_name'];

              $wallet = new Wallet;
              $wallet->user_id                = $id;
              $wallet->cooperative_code       = $cooperativeCode;
              $wallet->cooperative_role       = $role;
              $wallet->firstname              = $firstname;
              $wallet->surname                = $surname;
              $wallet->phone                  = $phone;
              $wallet->gender                 = $gender;
              $wallet->dob                    = $dob;
              $wallet->wallet_account_number  = $account_number;
              $wallet->fullname               = $fullname;
              $wallet->bank_name              =  $bank_name;
              $wallet->save();

              $message ="Wallet successfully created";             
              return redirect('wallet')->with('wallet', $message);
              exit;
            } else {
              $error = $result['message'];
              $message = $error ;               
              return redirect('create-wallet')->with('sms-error', $message);
            }
        }//end check otp
     
        public function fundWalletAccount(Request $request, $reference, $user_id, $wallet_id, $amount){
          if($reference){
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
              // generate a pin based on 2 * 7 digits + a random character
              $pin = mt_rand(1000000, 9999999)
                  . mt_rand(1000000, 9999999)
                  . $characters[rand(0, strlen($characters) - 1)];
              $transactionRef = str_shuffle($pin);

              $accountNumber = Wallet::where('id', $wallet_id)  
              ->where('user_id', $user_id)
              ->pluck('wallet_account_number')
              ->first();  

            //create Transfer reciepient code
            $url = "https://api.paystack.co/transferrecipient";
            $data = array(
              "type"            => "nuban",
              "name"            => "Account 1029",
              "description"     => "fund wallet",
              "account_number"  => $accountNumber,
              "bank_code"       => "120001",//9 payment service bank. code 120001
              "currency"        => "NGN",
              );
              $json_data = json_encode($data);
              if($json_data) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$json_data,
                CURLOPT_HTTPHEADER => array(
                  'Content-Type: application/json',
                  'Authorization: Bearer sk_live_7d6403cd59aab7c53d116aca23f1253be0b50cd2',
                  )
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                $result =  json_decode($response, true);
                //   exit;
                // dd($result);
              }
              if($result['status'] == 'true'){
              $transferURL = "https://api.paystack.co/transfer/";
              $transferData = array(
                "source"            => "balance",
                "reason"            => "fund wallet",
                "amount"            =>  $amount,
                "recipient"         =>  $result['data']['recipient_code'],
                "reference"         =>  $transactionRef,
                "authorization_code" =>$result['data']['details']['authorization_code'],
                "account_number"    => $result['data']['details']['account_number'],
                "bank_code"         => $result['data']['details']['bank_code'],
                );

              $jsonTransferData = json_encode($transferData);
              if($jsonTransferData) {
                    $cur = curl_init();
                    curl_setopt_array($cur, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>$jsonTransferData,
                    CURLOPT_HTTPHEADER => array(
                      'Content-Type: application/json',
                      'Authorization: Bearer sk_live_7d6403cd59aab7c53d116aca23f1253be0b50cd2',
                      )
                    ));
                    $paystackResponse = curl_exec($cur);
                    $err = curl_error($cur);
                    curl_close($cur);
                    $payResult =  json_decode($paystackResponse, true);
                 //   exit;
                 dd($payResult);
             
                }
              if($payResult['status'] == 'true'){
                //insert to cash transfer table. for superadmin record purpose
                $cashTransfer = new CashTransfer;
                $cashTransfer->wallet_id      = $wallet_id;
                $cashTransfer->user_id        = $user_id;
                $cashTransfer->recipient      = $payResult['data']['recipient_code'];
                $cashTransfer->currency       = $payResult['data']['currency'];
                $cashTransfer->save();
                // verifiy transfer here
                $verfyTransfer = " https://api.paystack.co/transfer/verify/".$transactionRef;
                $curlopt = curl_init();
                    curl_setopt_array($curlopt, array(
                    CURLOPT_URL => $verfyTransfer,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                      'Content-Type: application/json',
                      'Authorization: Bearer sk_live_7d6403cd59aab7c53d116aca23f1253be0b50cd2',
                      )
                    ));
                    $res = curl_exec($curlopt);
                    $error = curl_error($curlopt);
                    curl_close($curlopt);
                    $details =  json_decode($res, true);
                    dd($details);

                    if($details['status'] == 'true'){

                      $transferDetails = CashTransfer::where('reference', $details['data']['reference'])
                      ->update([
                        'integration' => $details['data']['integration'],
                        'domain'      => $details['data']['recipient']['domain'],
                        'type'        => $details['data']['recipient']['type'],
                        'name'        => $details['data']['recipient']['name'],
                        'account_number' => $details['data']['recipient']['details']['account_number'],
                        'account_name'   => $details['data']['recipient']['details']['account_name'],
                        'bank_code'   => $details['data']['recipient']['details']['bank_code'],
                        'bank_name'   => $details['data']['recipient']['details']['bank_name'],
                        'transfer_id' => $details['data']['recipient']['id'],
                        'transfer_date' => $details['data']['recipient']['createdAt'],
                        'status'      => $details['data']['status'],
                      ]);
                    }

                    $fundWallet = new FundWallet;
                    $fundWallet->reference    = $reference;
                    $fundWallet->user_id      = $user_id;
                    $fundWallet->wallet_id    = $wallet_id;
                    $fundWallet->amount       = $amount;
                    $fundWallet->payment_date = Carbon::now()->format('Y-m-d');
                    $fundWallet->payment_status = 'success';
                    $fundWallet->payment_type   = 'wallet';
                    $fundWallet->save();

                    $walletHistory = new WalletHistory;
                    $walletHistory->wallet_id         = $wallet_id;
                    $walletHistory->fund_wallet_id    = $fundWallet->id;
                    $walletHistory->transaction_type  = 'credit';
                    $walletHistory->credit            = $amount;
                    $walletHistory->sender            = 'Self';
                    $walletHistory->save();
                      //Update wallet account balance
                    $updateWalletAccount = Wallet::where('user_id', $user_id)->increment('balance',$amount);
                    exit;
              }
              else{
                $message ="Opps! something went wrong";             
                return redirect('wallet')->with('fund-wallet-error', $message);
              }
            }

            #
          }
          $message ="Wallet successfully funded";  
          return redirect('wallet')->with('wallet', $message);
        }

        public function walletAccountBalance(Request $request){
          $id = Auth::user()->id;
          $code = Auth::user()->code; 
          $phoneNumber = DB::table('wallet')
          ->select(['phone'])
          ->where('user_id', $id)
          ->where('cooperative_code', $code)
          ->pluck('phone')->first();
          $accountNumber = DB::table('wallet')
          ->select(['wallet_account_number'])
          ->where('user_id', $id)
          ->where('cooperative_code', $code)
          ->pluck('wallet_account_number')->first();

          //Ogaranya Wallet Account 
        //staging: https://api.staging.ogaranya.com/v1/2347033141516/wallet
        //'token: e4f3f028-c0b4-4c9b-b8ef-8be41a7613f6',
        //'publickey: 62f2da03d13992642d5416b3b1977071bf3adfe99a93b8daea6194306b168b84901f49025f25a245f083b0d627c921f5642ff124047e4a143dfe4cc1dd526d1b',
        
        //production:  https://api.ogaranya.com/v1/2347033141516/wallet
        // 'token: MDY0OTgzMTkxNjIzNGViZDA3YWIxZWMwZTFjYzY2Mzk1OTAwYjYwNTc2ZjY4NzBlOTBlMGQzMjk5YzJlZmUxZA==',
        // 'publickey: 4f223ac9cff724d03833fb8fb9e1a0638dc5125696420cc33c71bcf2e35a0af08beb8cd85a0c0c2eca2670d0244ca70bb9dff6bfa081def75cdaab1034beb1fe',
        $data = array(
          "phone"            => $phoneNumber,
          "account_number"   => $accountNumber,
          );
          $jsonData = json_encode($data);
           $url = "https://api.staging.ogaranya.com/v1/2347033141516/wallet/info";
          if($jsonData) {
                   $curl = curl_init();
                   curl_setopt_array($curl, array(
                   CURLOPT_URL => $url,
                   CURLOPT_RETURNTRANSFER => true,
                   CURLOPT_CUSTOMREQUEST => 'POST',
                   CURLOPT_POSTFIELDS =>$jsonData,
                   CURLOPT_HTTPHEADER => array(
                     'Content-Type: application/json',
                     'token: e4f3f028-c0b4-4c9b-b8ef-8be41a7613f6',
                      'publickey: 62f2da03d13992642d5416b3b1977071bf3adfe99a93b8daea6194306b168b84901f49025f25a245f083b0d627c921f5642ff124047e4a143dfe4cc1dd526d1b',
        
                     )
                   ));
                $res = curl_exec($curl);
                $error = curl_error($curl);
                curl_close($curl);
                $result =  json_decode($res, true);
                //dd($result);
              }
               if($result['status'] == 'success'){
                $accountBalance = $result['data']['available_balance'];
              
                //dd($accountBalance);
                              
                return redirect()->back();
               }
         
        }
}//class
