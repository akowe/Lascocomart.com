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

class CooperativeLoan extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware('cooperative');
    }

    public function cooperativeLoan(Request $request){
        if(Auth::user()->role_name == 'cooperative'){
            $code = Auth::user()->code; 
            $id = Auth::user()->id; //
            $totalLoan = DB::table('loan')
            ->where('loan_status', 'disbursed')
            ->where('cooperative_code', $code); 

            $totalLoanRemaining ='';
            $countMemberLoan = Loan::join('users', 'users.id', '=', 'loan.member_id')
            ->where('users.code', $code) 
            ->where('loan.loan_status', 'disbursed')
            ->get('loan.*');  

            $chartLoanInterestDate = Loan::select('updated_at')
            ->where('interest', '!=', null)
            ->whereYear('updated_at', Carbon::now()->year)
            ->where('cooperative_code', $code)
            ->get();
            $getLoanDate = Arr::pluck($chartLoanInterestDate, 'updated_at');
            $date = $getLoanDate; 

            $chartTotalInterest =Loan::select('interest')
            ->where('cooperative_code', $code)
            ->get();
            $getInterest = Arr::pluck($chartTotalInterest, 'interest');
           // $interest = json_encode($getInterest); 
            $interest = $getInterest; 

            $chartLoanPaid = LoanRepayment::join('loan', 'loan.id', '=', 'loan_repayment.loan_id')
            ->select('loan_repayment.amount_paid')
            ->where('loan.cooperative_code', $code)
            ->get();
            $getPaidLoan =Arr::pluck($chartLoanPaid, 'amount_paid');
            $paidLoan  = $getPaidLoan; 

            $chartLoanBalance = Loan::select('loan_balance')
            ->where('cooperative_code', $code)
            ->get();
            $getLoanBalance =Arr::pluck($chartLoanBalance, 'loan_balance');
            $loanBalance  = $getLoanBalance; 

            $chartMonthlyDue = LoanRepayment::select('*')
            ->where('cooperative_code', $code)
            ->whereMonth('updated_at', Carbon::now()->month)
            ->get();
            $getLoanMonthly =Arr::pluck($chartMonthlyDue, 'monthly_due');
            $loanMonthly  = $getLoanMonthly; 

            $totalMonthlyDueLoan =  LoanRepayment::join('loan', 'loan.id', '=', 'loan_repayment.loan_id')
            ->select('monthly_due') 
            ->where('loan.loan_status', 'disbursed')
            ->whereYear('loan.updated_at', Carbon::now()->year)
            ->whereMonth('loan.updated_at', Carbon::now()->startOfMonth())
            ->where('loan.cooperative_code', $code); 

            $perPage = $request->perPage ?? 10;
            $search = $request->input('search');
            $loan = DB::table('loan')->join('users', 'users.id', '=', 'loan.member_id')
           ->join('loan_type', 'loan_type.id', '=', 'loan.loan_type_id')
            ->select(['loan.*', 'loan_type.name', 'users.fname'])
            ->where('loan.cooperative_code', $code)
            ->orderBy('loan.created_at', 'desc')
            ->where(function ($query) use ($search) {  // <<<
            $query->where('users.fname', 'LIKE', '%'.$search.'%')
                   ->orWhere('loan.principal', 'LIKE', '%'.$search.'%')
                   ->orWhere('loan.interest', 'LIKE', '%'.$search.'%')
                   ->orWhere('loan.total', 'LIKE', '%'.$search.'%')
                   ->orWhere('loan.duration', 'LIKE', '%'.$search.'%')
                     ->orWhere('loan.loan_status', 'LIKE', '%'.$search.'%')
                       ->orWhere('loan.start_date', 'LIKE', '%'.$search.'%')
                         ->orWhere('loan.end_date', 'LIKE', '%'.$search.'%')
                    ->orWhere('loan_type.name', 'LIKE', '%'.$search.'%')
                   ->orderBy('loan.created_at', 'desc');
            })->paginate($perPage, $columns = ['*'], $pageName = 'loan'
            )->appends(['per_page'   => $perPage]);
        
            $pagination = $loan->appends ( array ('search' => $search) );
                if (count ( $pagination ) > 0){
                    return view ('loan.cooperative.cooperative-loan' ,  compact(
                    'perPage', 'loan', 'interest',  'date', 'totalLoan',
                    'countMemberLoan', 'paidLoan', 'loanBalance', 'loanMonthly', 'totalMonthlyDueLoan'))->withDetails( $pagination );     
                } 
                else{redirect()->back()->with('loan-status', 'No record order found'); }   
            \LogActivity::addToLog('Admin loanDashboard'); 

            return view('loan.cooperative.cooperative-loan', compact('perPage',
            'loan', 'interest',  'date',  'totalLoan', 'countMemberLoan',
            'paidLoan', 'loanBalance', 'loanMonthly', 'totalMonthlyDueLoan'));
        }
        else{
            return Redirect::to('/login');
        }
    }


    public function loanType(Request $request){
        if(Auth::user()->role_name == 'cooperative'){
            $id = Auth::user()->id;
            $perPage = $request->perPage ?? 10;
            $search = $request->input('search');

            $loantypes = DB::table('loan_type')->select(['loan_type.*'])
            ->where('admin_id', $id)
            ->orderBy('loan_type.created_at', 'desc')
            ->where(function ($query) use ($search) {  // <<<
            $query->where('loan_type.name', 'LIKE', '%'.$search.'%')
                   ->orWhere('loan_type.percentage_rate', 'LIKE', '%'.$search.'%')
                   ->orWhere('loan_type.rate_type', 'LIKE', '%'.$search.'%')
                   ->orWhere('loan_type.max_duration', 'LIKE', '%'.$search.'%')
                   ->orderBy('loan_type.created_at', 'desc');
            })->paginate($perPage, $columns = ['*'], $pageName = 'loantypes'
            )->appends(['per_page'   => $perPage]);
        
            $pagination = $loantypes->appends ( array ('search' => $search) );
                if (count ( $pagination ) > 0){
                    return view ('loan.cooperative.loan-type' ,  compact(
                    'perPage', 'loantypes'))->withDetails( $pagination );     
                } 
                else{redirect()->back()->with('status', 'No record order found'); }   
            \LogActivity::addToLog('Admin loanType'); 
            return view('loan.cooperative.loan-type', compact(
                'perPage', 'loantypes'));
        }
        else{
            return Redirect::to('/login');
        }
    }

    public function updateLoanSetting(Request $request){
        if(Auth::user()){
            $code = Auth::user()->code; 
            $id = Auth::user()->id; //
            $this->validate($request, [ 
                'approval_level'    => 'required|string|max:255',
                'repayment'         => 'required|string|max:255',
                 ]);
            $repayment = $request->repayment;
            if($repayment == 0){
                return redirect()->back()->with('loan-repayment', 'Loan repayment date can not be "0" ');
            }
    
            $checkLoanSetting = LoanSetting::where('cooperative_code', $code)->first();
             if (empty($checkLoanSetting)) {
                $newSetting  = new LoanSetting;
                $newSetting->cooperative_code  = $code;
                $newSetting->user_id           = $id;
                $newSetting->processing_fee    = $request->processing_fee;
                $newSetting->max_loan          = $request->maximum_loan;
                $newSetting->approval_level    = $request->approval_level;
                $newSetting->start_repayment   = $repayment; 
                $newSetting->save();   
                return redirect()->back()->with('success', 'Loan settings successfully added');
            }
            else{
                $loanSeeting = LoanSetting::where('cooperative_code', $code)
                ->update([
                'user_id'           => $id,
                'processing_fee'    => $request->processing_fee,
                'max_loan'          => $request->maximum_loan,
                'approval_level'    => $request->approval_level,
                'start_repayment'   => $repayment,
                ]);
                return redirect()->back()->with('success', 'Loan settings successfully added');
            } 
        }
        else{ return Redirect::to('/login');}

    }

     public function addLoanType(Request $request){
        if(Auth::user()->role_name == 'cooperative'){
            $user_id = Auth::user()->id; 
            $cooperativeCode = Auth::user()->code;
            $this->validate($request, [ 
            'name'              => 'required|string|max:255',
            'rate'              => 'required|string|max:255',
            'rate_type'         => 'required|string|max:255',
            'guarantor'         => 'string|max:255',
            'minimum_duration'  => 'string|max:255',
            'maximum_duration'  => 'required|string|max:255',
            'description'       => 'string|max:255',
             ]);

            $addLoan  = new LoanType;
            $addLoan->admin_id          = $user_id;
            $addLoan->cooperative_code  = $cooperativeCode;
            $addLoan->name              = $request->name;
            $addLoan->percentage_rate   = $request->rate;
            $addLoan->rate_type         = $request->rate_type;
            $addLoan->guarantor         = $request->guarantor;
            $addLoan->min_duration      = $request->minimum_duration;
            $addLoan->max_duration      = $request->maximum_duration;
            $addLoan->description       = $request->description;
            $addLoan->save();
            if($addLoan){
                return redirect('cooperative-loan-type')->with('success', 'Loan type successfully added');
            }
            else{
                return redirect('cooperative-loan-type')->with('status', 'Opps! something went wrong');
            }

        }
        else{ return Redirect::to('/login');}
     }

     public function  cooperativeApproveLoan(Request $request, $loan_id){
        if(Auth::user()->role_name == 'cooperative'){
            $code = Auth::user()->code;
            $loanPrincipal = DB::table('loan')
            ->where('id', $loan_id)
            ->select('*')
            ->pluck('principal')->first();

            $loanMemberID = DB::table('loan')
            ->where('id', $loan_id)
            ->select('*')
            ->pluck('member_id')->first();

            $loanMember = DB::table('users')
            ->where('id', $loanMemberID)
            ->select('*')
            ->pluck('fname')->first();

            $checkApprovalLevel = DB::table('loan_settings')
            ->where('cooperative_code', $code)
            ->select('*')
            ->pluck('approval_level')->first();
            if($checkApprovalLevel > 1){
                Session::flash('approval', ' Required ' .$checkApprovalLevel.' approval'); 
                Session::flash('alert-class', 'alert-warning'); 
            }
            else{
                Session::flash('approval', ' Required ' .$checkApprovalLevel.' approval'); 
                Session::flash('alert-class', 'alert-warning');  
            }
            return view('loan.cooperative.approve-loan', compact('checkApprovalLevel', 
            'loanPrincipal', 'loanMember', 'loan_id'));
            
        }
        else{ return Redirect::to('/login');}
     }

     public function approveLoan(Request $request){
        if(Auth::user()){
            $id = Auth::user()->id;
            $code = Auth::user()->code;
            $loanID = $request->loan_id;
            $loanApprovalLevel = DB::table('loan')
            ->where('cooperative_code', $code)
            ->where('id', $loanID )
            ->select('*')
            ->pluck('loan_approval_level')
            ->first();

            $approvalLevel = $loanApprovalLevel + 1;
            $approveLoan = Loan::where('id', $loanID)
            ->update([
                'loan_status'           => 'approved',
                'loan_approval_level'   =>  $approvalLevel,
                'approval_agent'        => $id,
            ]);
        return redirect('cooperative-loan')->with('success', 'Loan approval successful');

        }
        else{ return Redirect::to('/login');}
     }

     public function cooperativeLoanPayOut(Request $request, $loan_id){
        if(Auth::user()){
            $code = Auth::user()->code;
            $id = Auth::user()->id;

            $loanRepaymentStart = DB::table('loan_settings')
            ->where('cooperative_code', $code)
            ->select('*')
            ->pluck('start_repayment')->first();

            $checkApprovalLevel = DB::table('loan_settings')
            ->where('cooperative_code', $code)
            ->select('*')
            ->pluck('approval_level')->first();

            $loanApproval = DB::table('loan')
            ->where('id', $loan_id)
            ->select('*')
            ->pluck('loan_approval_level')->first();
            if( $checkApprovalLevel >  $loanApproval){
                $RemainApproval = $checkApprovalLevel - $loanApproval;
                Session::flash('approval', ' This loan has ' .$loanApproval. ' approval, remiain '.$RemainApproval . ' more'); 
            }
            elseif ($checkApprovalLevel = $loanApproval) {
                # code...
                Session::flash('approval', ' Loan approval completed!'); 
            }
            $loanPrincipal = DB::table('loan')
            ->where('id', $loan_id)
            ->where('cooperative_code', $code)
            ->select('*')->pluck('principal')->first();

            $loanBeneficiary = DB::table('loan')->join('users', 'users.id', '=', 'loan.member_id')
            ->where('loan.id', $loan_id)
            ->where('loan.cooperative_code', $code)
            ->select('loan.*', 'users.fname')->pluck('fname')->first();

            $loanDuration = DB::table('loan')
            ->where('id', $loan_id)
            ->where('cooperative_code', $code)
            ->select('*')->pluck('duration')->first();

            return view('loan.cooperative.loan-payout', compact('checkApprovalLevel', 
            'loanPrincipal', 'loanBeneficiary', 'loanDuration', 'loanRepaymentStart'));
        }
        else{ return Redirect::to('/login');} 
     }

     public function calLoanRepayment(Request $request, $loan_id){
        if(Auth::user()){
            $code = Auth::user()->code;
            $loanRepaymentStart = DB::table('loan_settings')
            ->where('cooperative_code', $code)
            ->select('*')
            ->pluck('start_repayment')->first();
            //Carbon::now()->addDays(5);
            $repaymentPeriod = $loanRepaymentStart * 30;//30 days
            $payOutDate = $request->payOutDate;
            $repaymentStartDate = Carbon::now($payOutDate)->addDays($repaymentPeriod);

        }
        else{ return Redirect::to('/login');} 
     }
}//class