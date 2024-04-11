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

class MemberLoan extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware('cooperative');
    }

    public function requestLoan(Request $request){
        if(Auth::user()->role_name == 'member'){
            $code = Auth::user()->code;
            $chooseLoanType = LoanType::select('*')
            ->where('cooperative_code', $code)->get();
            
            $principal = '';
            $annualInterest = '';
            $totalDue = '';
            $rateType = '';
            $duration ='';
            $maxTenure = '';
            $percentage = '';
            $loanType = '';
            $loanTypeID = '';
            return view('loan.member.request-loan', compact('chooseLoanType', 'loanType',
            'principal', 'maxTenure', 'percentage', 'annualInterest', 'totalDue',
            'rateType','duration', 'loanTypeID'));
        }
        else{
            return Redirect::to('/login');
        }
    }


    public function calculateInterest(Request $request, $id, $amount, $duration){
        if(Auth::user()){
            $code = Auth::user()->code;
            $chooseLoanType = LoanType::select('*')
            ->where('cooperative_code', $code)->get();
            $loanTypeID = $id;

            $getLoanTypeName = LoanType::select('name')
            ->where('id', $id)
            ->where('cooperative_code', $code)->get();
            $loanTypeName =Arr::pluck($getLoanTypeName, 'name');
            $loanType = implode(" ",$loanTypeName); 

            $getRateType = LoanType::select('rate_type')
            ->where('id', $id)
            ->where('cooperative_code', $code)->get();
            $loanRateType =Arr::pluck($getRateType, 'rate_type');
            $rateType = implode(" ",$loanRateType); 
         
            $getPercentage = LoanType::select('percentage_rate')
            ->where('id', $id)
            ->where('cooperative_code', $code)->get();
            $loanPercentage =Arr::pluck($getPercentage, 'percentage_rate');
            $percentageRate = implode(" ",$loanPercentage); 
        
            $getTenure = LoanType::select('max_duration')
            ->where('id', $id)
            ->where('cooperative_code', $code)->get();
            $loanTenure =Arr::pluck($getTenure, 'max_duration');
            $maxTenure = implode(" ",$loanTenure); 

            $principal = (int)$amount;
            $percentage = $principal / 100 * $percentageRate ;
            $annualInterest = $percentage * $maxTenure; //for flat rate interest type
            $totalDue = $principal +   $annualInterest;//for flat rate interest type
            
            return view('loan.member.request-loan', compact('chooseLoanType', 'loanType',
            'principal', 'maxTenure',  'percentage', 'annualInterest', 'totalDue',
            'rateType', 'duration', 'loanTypeID'));
        }
        else{ return Redirect::to('/login');} 
    }

    public function loanHistory(Request  $request){
        if(Auth::user()->role_name == 'member'){
            $id = Auth::user()->id;
            $loanPrincipal = DB::table('loan')
            ->where('loan_status', 'payout')
            ->where('member_id', $id)
            ->pluck('principal')->first();

            $loanInterest = DB::table('loan')
            ->where('loan_status', 'payout')
            ->where('member_id', $id)
            ->pluck('interest')->first();

            $monthlyDueLoan =  DB::table('due_loans')
            ->where('payment_status', 'pending')
            ->where('member_id', $id)
            ->get();
          
            // loan_id,
            // DB::table('loan_repayment')->select('monthly_due')
            //  ->where('next_due_date', '!=', null)
            //  ->where('member_id', $id)
            // ->pluck('monthly_due')->first();
            
             $nextDueDate =  DB::table('due_loans')->join('loan', 'loan.id', '=', 'due_loans.loan_id')
             ->where('due_loans.payment_status', 'pending')
             ->where('loan.member_id', $id)
             ->pluck('due_date')->first();

            $perPage = $request->perPage ?? 10;
            $search = $request->input('search');
            $loan = DB::table('loan')->join('users', 'users.id', '=', 'loan.member_id')
           ->join('loan_type', 'loan_type.id', '=', 'loan.loan_type_id')
           ->join('loan_repayment', 'loan_repayment.loan_id', 'loan.id')
            ->select(['loan.*', 'loan_type.name', 'users.fname', 'loan_repayment.*'])
            ->where('loan.loan_status', 'payout')
            ->where('loan.member_id', $id)
            ->orderBy('loan.created_at', 'desc')
            ->where(function ($query) use ($search) {  // <<<
            $query->where('users.fname', 'LIKE', '%'.$search.'%')
                   ->orWhere('loan.principal', 'LIKE', '%'.$search.'%')
                   ->orWhere('loan.interest', 'LIKE', '%'.$search.'%')
                   ->orWhere('loan.total', 'LIKE', '%'.$search.'%')
                   ->orWhere('loan.duration', 'LIKE', '%'.$search.'%')
                     ->orWhere('loan.loan_status', 'LIKE', '%'.$search.'%')
                    ->orWhere('loan_type.name', 'LIKE', '%'.$search.'%')
                   ->orderBy('loan.created_at', 'desc');
            })->paginate($perPage, $columns = ['*'], $pageName = 'loan'
            )->appends(['per_page'   => $perPage]);
        
            $pagination = $loan->appends ( array ('search' => $search) );
                if (count ( $pagination ) > 0){
                    return view ('loan.member.loan-history', compact(
                    'perPage', 'loan', 'monthlyDueLoan', 'nextDueDate',
                    'loanPrincipal', 'loanInterest'))->withDetails( $pagination );     
                } 
                else{redirect()->back()->with('loan-status', 'No record order found'); }   
            \LogActivity::addToLog('Member loanDashboard'); 
            return view ('loan.member.loan-history', compact('perPage', 
            'loan', 'monthlyDueLoan', 'nextDueDate', 'loanPrincipal', 'loanInterest'));
        }
        else{
            return Redirect::to('/login');
        }

    }
}
