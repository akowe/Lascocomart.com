<?php

namespace App\Http\Controllers\Loan;

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

class LoanController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware('cooperative');
    }

    public function loan(Request $request){
        if( Auth::user()->role_name  == 'cooperative'){
        return view('loan.loan-packages');
        }
        else{ return redirect()->back()->with('success', 'Access denied!, Only cooperatives can subscribe.');}
    }

    public function addLoan(Request $request){
        if( Auth::user()){
            $id = Auth::user()->id;
            $cooperativeCode = Auth::user()->code;
            $this->validate($request, [  
                'service_fee'     => 'string|max:255',
            ]);
            $checkExistingLoan = DB::table('loan')->select('loan_balance')
            ->where('loan_balance', '!=', null)
            ->where('loan_balance', '!=', '0')
            ->where('member_id', $id)
            ->get()->first();
            if($checkExistingLoan){
            return redirect('member-request-loan')->with('loanExist', 'You have unfinished loan');
            }
            else{ 
                $loan = new Loan;
                $loan->member_id            = $id;
                $loan->cooperative_code     = $cooperativeCode;
                $loan->loan_type_id         = $request->ratetype;
                $loan->principal            = $request->principal;
                $loan->interest             = $request->annual_interest;
                $loan->total                = $request->total_due;
                $loan->duration             = $request->duration;
                $loan->loan_balance         = $request->total_due;
                $loan->loan_status          = 'request';
                $loan->save();
                if($loan){
                    $loanRepayment = new LoanRepayment;
                    $loanRepayment->loan_id             = $loan->id;
                    $loanRepayment->member_id           = $id;
                    $loanRepayment->cooperative_code    = $cooperativeCode;
                    $loanRepayment->loan_type_id        = $request->ratetype;
                    $loanRepayment->monthly_principal   = $request->monthly_principal;
                    $loanRepayment->monthly_interest     = $request->monthly_interest;
                    $loanRepayment->monthly_due         = $request->monthly_due;
                    $loanRepayment->save();
                  
                }
                else{
                    return redirect('member-request-loan')->with('loan', 'Opps! Something went wrong');
                }}
          
           return redirect('member-loan-history')->with('loan', 'Loan request successful!');

        }
        else{
            return Redirect::to('/login');
        }
    }

    public function cooperativeAddLoan(Request $request){
        if(Auth::user()->role_name == 'cooperative'){
            $code = Auth::user()->code;
            
            $this->validate($request, [  
                'service_fee'     => 'string|max:255',
            ]);
            $memberID= preg_split("/[,]/",$request->memberID);

            $checkExistingLoan = Loan::whereIn('member_id', $memberID)
           ->where('loan_balance', '!=', null)
            ->where('loan_balance', '!=', '0')
            ->get('*')->pluck('loan_balance');
            $getmMembers = User::join('loan', 'loan.member_id', '=', 'users.id')
            ->whereIn('loan.member_id', $memberID)->get('*')->pluck('fname');
            $members = substr($getmMembers, 1, -1);

            if(!$checkExistingLoan->isEmpty()){
            return redirect('cooperative-create-loan')->with('loanExist',  ''.$members.' has unfinished loan');
            }
            else{ 
                $loan = new Loan;
                $loan->member_id            = $request->memberID;
                $loan->cooperative_code     = $code;
                $loan->loan_type_id         = $request->ratetype;
                $loan->principal            = $request->principal;
                $loan->interest             = $request->annual_interest;
                $loan->total                = $request->total_due;
                $loan->duration             = $request->duration;
                $loan->loan_balance         = $request->total_due;
                $loan->loan_status          = 'request';
                $loan->save();
                if($loan){
                    $loanRepayment = new LoanRepayment;
                    $loanRepayment->loan_id             = $loan->id;
                    $loanRepayment->member_id           = $request->memberID;
                    $loanRepayment->cooperative_code    = $code;
                    $loanRepayment->loan_type_id        = $request->ratetype;
                    $loanRepayment->monthly_principal   = $request->monthly_principal;
                    $loanRepayment->monthly_interest     = $request->monthly_interest;
                    $loanRepayment->monthly_due         = $request->monthly_due;
                    $loanRepayment->save();
                  
                }
                else{
                    return redirect('cooperative-create-loan')->with('loan', 'Opps! Something went wrong');
                }}
          
           return redirect('cooperative-loan')->with('loan', 'Loan added successful!');

        }
        else{
            return Redirect::to('/login');
        }
    }
}//class
