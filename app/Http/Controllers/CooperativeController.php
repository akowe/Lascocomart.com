<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;
use App\Models\CooperativeMemberRole;
use App\Models\SMS;
use App\Models\Profile;
use App\Models\Voucher;
use App\Models\Wallet;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Loan;
use App\Models\LoanType;
use App\Models\LoanRepayment;
use App\Models\LoanSetting;
use App\Models\DueLoans;
use App\Models\LoanPaymentTransaction;
use App\Models\Credit;
use App\Models\ShippingDetail;
use App\Models\Transaction;
use App\Models\Categories;
use App\Models\Product;
use App\Models\FcmgProduct;
use App\Mail\SendMail;
use App\Mail\OrderApprovedEmail;
use App\Mail\SalesEmail;
use App\Mail\OrderEmail;
use App\Mail\CooperativeWelcomeEmail;
use App\Notifications\AdminCancelOrder;
use App\Notifications\NewProduct;
use Carbon\Carbon;
use Auth;
use Validator;
use Session;
use Paystack;
use Storage;
use Mail;
use Notification;
use DateTime;

class CooperativeController extends Controller
{
    //
      public function __construct()
    {
         // $this->middleware('auth');
        $this->middleware(['auth','verified']);
        $this->middleware('cooperative');
    }

    public function index (Request $request){
    if( Auth::user()->role_name  == 'cooperative'){
        $code = Auth::user()->code; 
        $id = Auth::user()->id; //
        
        $firstTimeLoggedIn = Auth::user()->last_login;
         if (empty($firstTimeLoggedIn)) {
           $data = 
           array( 
            'user_id'   => Auth::user()->code,
            'coopname'  => Auth::user()->coopname,
            'email'     => Auth::user()->email,
         );
           Mail::to(Auth::user()->email)->send(new CooperativeWelcomeEmail($data));  
           $user = Auth::user();
           $user->last_login = Carbon::now();
           $user->save();
         }
         elseif (!empty($firstTimeLoggedIn)) {
            $user = Auth::user();
            $user->last_login = Carbon::now();
            $user->save();
         }      
        // check if user has filled his/her profile
        $user=Auth::user();
        $phone          = $user->phone;
        $bank           = $user->bank;
          if(empty($phone && $bank )){
            Session::flash('profile', ' You are yet to complete your profile!'); 
            Session::flash('alert-class', 'alert-success'); 
            return Redirect::to('/account-settings');     
          }
        //Get admin existing member   
        $getAdminMeberID = User::where('code', $code)->where('users.id', '!=', Auth::user()->id)->get('id');
        foreach($getAdminMeberID as $coopMID){  
            $getAdminMeberRoleName = User::whereIn('id', $coopMID)->get();
            $getRoleName = Arr::pluck($getAdminMeberRoleName, 'role_name');
            $roleName = implode(" ",$getRoleName);

            $getAdminMeberRole = User::whereIn('id', $coopMID)->get();
            $getRole = Arr::pluck($getAdminMeberRole, 'role');
            $role = implode(" ",$getRole);  
            
            $getMemberIDs = User::whereIn('id', $coopMID)->get();
            $getIDs = Arr::pluck($getMemberIDs, 'id');
            $memberIDs = implode(" ",$getIDs);     
        
            if (CooperativeMemberRole::where('member_id', $memberIDs)->exists()) {
                // The record exists
            } else {
                  $item =  CooperativeMemberRole::firstOrNew([
                    'cooperative_code' => $code,
                    'member_id' => $memberIDs,
                    'member_role' => $role, 
                    'member_role_name' => $roleName,
                ]);   
            $item->save();
            }
        }
       
        $credit = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
        ->where('users.id', $id)
        ->get('credit');
    
        $members = DB::table('users')
        ->select(['users.*'])
            ->where('users.code', $code)
            ->where('users.deleted_at',  NULL)
            ->where('users.id', '!=', Auth::user()->id)
            ->orderBy('users.created_at', 'desc');

         //sum all member order that is approve for payment
         $sumApproveOrder = User::join('orders', 'orders.user_id', '=', 'users.id')
         ->where('orders.status', 'approved') 
         ->where('users.code', $code) 
         ->where('orders.user_id', '!=', Auth::user()->id)
         ->get('orders.*');  
         
         // for bulk payment by cooperative
         $all_orders_id = User::join('orders', 'orders.user_id', '=', 'users.id')
         ->where('orders.status', 'approve') 
         ->where('users.code', $code) 
         ->where('orders.user_id', '!=', Auth::user()->id)
         ->get('orders.id');  

        //users logged during a period of month ago from now
       // $adminActiveMember =  User::where('last_login_at', '>=', new DateTime('-1 months'))->get(); 
        //users logged from the beggining of current callendar month
        $adminActiveMember =  User::where('code', $code)
        ->where('id', '!=', Auth::user()->id)
        ->where('last_login', '>=',Carbon::now()->startOfMonth())
        ->get(); 
        
        $countApprovedProduct = User::join('products', 'products.seller_id', '=', 'users.id')
        ->where('products.prod_status', 'approve')
        ->where('products.seller_id', $id);

        $count_product = User::join('products', 'products.seller_id', '=', 'users.id')
        ->where('products.seller_id', $id);

        $countSoldProducts = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
        ->join('users', 'users.id', '=', 'orders.user_id')// get the buyer
         ->join('products', 'products.id', '=', 'order_items.product_id')
         ->where('orders.status', 'paid')
         ->where('orders.user_id', '!=', Auth::user()->id)
        ->where('order_items.seller_id', $id);

       $allocated_funds = User::join('credit_limits', 'credit_limits.user_id', '=', 'users.id')
       ->where('users.id', $id)
       ->paginate( $request->get('per_page', 5));
       
       $memberOrders = DB::table('users')->join('orders', 'orders.user_id', '=', 'users.id')
       ->select(['orders.*', 'users.fname', 'users.lname'])
       ->where('users.code', $code)
       ->where('orders.status', '!=', 'cancel')
       ->where('orders.user_id', '!=', Auth::user()->id);
   
        $countMyCustomerOrder = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
         ->join('users', 'users.id', '=', 'orders.user_id')// get the buyer
          ->join('products', 'products.id', '=', 'order_items.product_id')
          ->where('orders.status', 'paid')
          ->where('orders.user_id', '!=', Auth::user()->id)
         ->where('order_items.seller_id', $id);

         $countShippedItem= OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
         ->join('users', 'users.id', '=', 'orders.user_id')// get the buyer
         ->join('products', 'products.id', '=', 'order_items.product_id')
         ->where('order_items.delivery_status', 'delivered')
         ->where('orders.user_id', '!=', Auth::user()->id)
         ->where('order_items.seller_id', $id);

         $sales =  DB::table('order_items')
         ->join('orders', 'orders.id', '=', 'order_items.order_id')
         ->join('users', 'users.id', '=', 'orders.user_id')// get the buyer
          ->join('products', 'products.id', '=', 'order_items.product_id')
         ->select(['orders.*','order_items.*','users.fname', 'users.phone',
          'products.prod_name','products.image','products.seller_price'])
          ->where('orders.status', 'paid')
          ->where('orders.user_id', '!=', Auth::user()->id)
         ->where('order_items.seller_id', $id);

         $loan = DB::table('loan')->join('users', 'users.id', '=', 'loan.member_id')
         ->join('loan_type', 'loan_type.id', '=', 'loan.loan_type_id')
          ->select(['loan.*', 'loan_type.name', 'users.fname'])
          ->where('loan.cooperative_code', $code);

          $payOutLoan = DB::table('loan')
           ->select(['loan.*'])
           ->where('loan.loan_status', 'paid')
           ->where('loan.cooperative_code', $code);

           $WalletAccountNumber =  DB::table('wallet')
           ->select(['wallet_account_number'])
           ->where('user_id', $id)
           ->pluck('wallet_account_number')->first();
          
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
           ->pluck('phone')->first();
 
        $perPage = $request->perPage ?? 10;
        $search = $request->input('search');
        
        $wallets = DB::table('wallet_transaction')
        ->join('wallet', 'wallet.id', '=','wallet_transaction.wallet_id' )
        ->join('users', 'users.id', '=', 'wallet.user_id')
        ->select(['wallet_transaction.*', 'users.fname'])
        ->where('wallet.user_id', $id)
        ->orderBy('wallet_transaction.created_at', 'desc')
        ->where(function ($query) use ($search) {  // <<<
        $query->where('users.fname', 'LIKE', '%'.$search.'%')
            ->orWhere('wallet_transaction.transaction_type', 'LIKE', '%'.$search.'%')
            ->orWhere('wallet_transaction.credit', 'LIKE', '%'.$search.'%')
            ->orWhere('wallet_transaction.debit', 'LIKE', '%'.$search.'%')
            ->orWhere('wallet_transaction.reciever', 'LIKE', '%'.$search.'%')
            ->orWhere('wallet_transaction.sender', 'LIKE', '%'.$search.'%')
            ->orderBy('wallet_transaction.created_at', 'desc');
        })->paginate($perPage, $columns = ['*'], $pageName = 'wallets')
            ->appends(['per_page'   => $perPage]);
        
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
                   return view('cooperative.cooperative', compact(
                        'perPage', 'wallets', 'members', 'memberOrders',  'credit', 
                        'count_product', 'countMyCustomerOrder', 'sales', 
                        'allocated_funds', 'sumApproveOrder', 'all_orders_id',
                        'countSoldProducts', 'countApprovedProduct', 'adminActiveMember',
                        'countShippedItem', 'loan', 'payOutLoan', 'WalletAccountNumber',
                        'WalletAccountName', 'WalletBankName'));
                   }
              
                $pagination = $wallets->appends ( array ('search' => $search) );
                if (count ( $pagination ) > 0){
                    return view ('cooperative.cooperativ' ,  compact(
                    'perPage', 'wallets', 'members', 'memberOrders',  'credit', 
                    'count_product', 'countMyCustomerOrder', 'sales', 
                    'allocated_funds', 'sumApproveOrder', 'all_orders_id',
                    'countSoldProducts', 'countApprovedProduct', 'adminActiveMember',
                    'countShippedItem', 'loan', 'payOutLoan', 'WalletAccountNumber',
                    'WalletAccountName', 'WalletBankName', 'accountBalance'))->withDetails( $pagination );     
                } 
                else{redirect()->back()->with('status', 'No record order found'); } 
            
            \LogActivity::addToLog('Admin dashboard'); 
            //search
            return view('cooperative.cooperative', compact(
                'perPage', 'wallets', 'members', 'memberOrders',  'credit', 
                'count_product', 'countMyCustomerOrder', 'sales', 
                'allocated_funds', 'sumApproveOrder', 'all_orders_id',
                'countSoldProducts', 'countApprovedProduct', 'adminActiveMember',
                'countShippedItem', 'loan', 'payOutLoan', 'WalletAccountNumber',
                'WalletAccountName', 'WalletBankName', 'accountBalance'));
        }
        else { return Redirect::to('/login');}
    }
    

    public function adminOrderHistory(Request $request){
        $id = Auth::user()->id;
        $code = Auth::user()->code;
        $countMyOrders = User::join('orders', 'orders.user_id', '=', 'users.id')
        ->where('orders.status','!=', 'cancel')
        ->where('orders.user_id', $id);
        
        $credit = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
        ->where('users.id', $id)
        ->get('credit');  

        $perPage = $request->perPage ?? 10;
        $search = $request->input('search');
        $orders = User::join('orders', 'orders.user_id', '=', 'users.id')
         ->where('orders.user_id', $id)
         ->orderBy('orders.date', 'desc')
         ->where(function ($query) use ($search) {  // <<<
        $query->where('users.coopname', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.order_number', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.grandtotal', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.date', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.status', 'LIKE', '%'.$search.'%')
            ->orderBy('orders.created_at', 'desc');
         })->paginate($perPage, $columns = ['*'], $pageName = 'orders'
         )->appends([
        'per_page'   => $perPage
         ]);
         $pagination = $orders->appends ( array ('search' => $search) );
        if (count ( $pagination ) > 0){
            return view('cooperative.order-history', compact(
            'perPage',
            'countMyOrders', 
            'credit', 
            'orders'))->withDetails ( $pagination );     
             } 
             else{
                 redirect()->back()->with('status', 'No order record found'); 
             }
        \LogActivity::addToLog('Admin order history');
        return view('cooperative.order-history', compact(
        'perPage',
        'countMyOrders', 
        'credit', 
        'orders'));
    }

    public function cooperativeCustomerOrder(Request $request){
        $id = Auth::user()->id;
       // count customer  orders for admin/seller product that has been paid
       $countMyCustomerOrder = Order::join('order_items', 'order_items.order_id', '=', 'orders.id')
       ->join('users', 'users.id', '=', 'orders.user_id')
       ->where('orders.status','!=', 'cancel')
       ->where('orders.pay_status',  'paid')
       ->where('order_items.seller_id', $id);

        $perPage = $request->perPage ?? 10;
        $search = $request->input('search');
        $orders =Order::join('order_items', 'order_items.order_id', '=', 'orders.id')
        ->join('users', 'users.id', '=', 'orders.user_id')//get the customer details
        ->where('orders.status', '!=', 'cancel')
        ->where('orders.pay_status',  'paid')
        ->where('order_items.seller_id', $id)
         ->orderBy('orders.date', 'desc')
         ->where(function ($query) use ($search) {  // <<<
        $query->where('users.fname', 'LIKE', '%'.$search.'%')
            ->orWhere('users.lname', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.order_number', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.grandtotal', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.date', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.status', 'LIKE', '%'.$search.'%')
            ->orderBy('orders.created_at', 'desc');
         })->paginate($perPage, $columns = ['*'], $pageName = 'orders'
         )->appends([
        'per_page'   => $perPage
         ]);
         $pagination = $orders->appends ( array ('search' => $search) );
        if (count ( $pagination ) > 0){
            return view('cooperative.customer-order', compact(
            'perPage',
            'countMyCustomerOrder', 
            'orders', ))->withDetails ( $pagination );     
             } 
             else{
                 redirect()->back()->with('status', 'No customer order found'); 
             }
        \LogActivity::addToLog('Admin member order');
        return view('cooperative.customer-order', compact(
        'perPage',
        'countMyCustomerOrder', 
        'orders', ));
    }


    public function cooperativeMemberOrder(Request $request){
        $id = Auth::user()->id;
        $code = Auth::user()->code;
        $countMemberOrders = User::join('orders', 'orders.user_id', '=', 'users.id')
        ->where('orders.status','!=', 'cancel')
        ->where('users.code', $code)
        ->where('orders.user_id', '!=', Auth::user()->id);
        // for bulk payment by cooperative
        $sumApproveOrder = User::join('orders', 'orders.user_id', '=', 'users.id')
        ->where('orders.status', 'approved') 
        ->where('users.code', $code) 
        ->where('users.id', '!=', Auth::user()->id)
        ->get('orders.*'); 
        
        $credit = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
        ->where('users.id', $id)
        ->get('credit');  

        $perPage = $request->perPage ?? 10;
        $search = $request->input('search');
        $orders = User::join('orders', 'orders.user_id', '=', 'users.id')
         ->where('users.code', $code)
         ->where('orders.user_id', '!=', Auth::user()->id)
          ->where('orders.status', '!=', 'cancel')
        // ->where('orders.status', '=', 'awaits approval')
         ->where('orders.cooperative_code', '=', $code)
         ->orderBy('orders.date', 'desc')
         ->where(function ($query) use ($search) {  // <<<
        $query->where('users.fname', 'LIKE', '%'.$search.'%')
            ->orWhere('users.lname', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.order_number', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.grandtotal', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.date', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.status', 'LIKE', '%'.$search.'%')
            ->orderBy('orders.created_at', 'desc');
         })->paginate($perPage, $columns = ['*'], $pageName = 'orders'
         )->appends([
        'per_page'   => $perPage
         ]);
         $pagination = $orders->appends ( array ('search' => $search) );
        if (count ( $pagination ) > 0){
            return view('cooperative.member-order', compact(
            'perPage',
            'countMemberOrders', 
            'credit', 
            'orders', 
            'sumApproveOrder'))->withDetails ( $pagination );     
             } 
             else{
                 redirect()->back()->with('status', 'No record found'); 
             }
        \LogActivity::addToLog('Admin member order');
        return view('cooperative.member-order', compact(
        'perPage',
        'countMemberOrders', 
        'credit', 
        'orders', 
        'sumApproveOrder'));
    }

    public function cancelMemberNewOrder($id)
    {
        $order = Order::find($id);
        $user = User::where('id', $order->user_id)->get('fname');
        $array = Arr::pluck($user,'fname' );
        $userName = implode(",", $array);
        \LogActivity::addToLog('Admin cancel'.$userName.'order');
        return view('cooperative.cancel-new-order', compact('order', 'userName'));
    }

    public function cancelOrder(Request $request){
        $this->validate($request, [
            'amount'         => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:2|max:9',
            ]);
        $credit = $request->amount;
        $input = 'cancel';
        $order_id = $request->order_id;
        Order::where('id', $order_id)
        ->update([
        'status' => $input
        ]); 
        $getMember = User::join('orders', 'orders.user_id', '=', 'users.id')
        ->where('orders.id', $order_id)
        ->get('users.id');

        $getOrderNumber = Order::where('id', $order_id)->get();
        $order= Arr::pluck($getOrderNumber, 'order_number'); // 
        $order_number = implode('', $order);
     
        $notification = new AdminCancelOrder($order_number, $credit);
        Notification::send($getMember, $notification);
        \LogActivity::addToLog('Order cancel');

        return redirect('cooperative')->with('success', 'Canceled successful!');
    }


    public function viewCanceledOrders(Request $request){
        $code = Auth::user()->code;
        $perPage = $request->perPage ?? 10;
        $search = $request->input('search');
        $orders = User::join('orders', 'orders.user_id', '=', 'users.id')
         ->where('users.code', $code)
         ->where('orders.user_id', '!=', Auth::user()->id)
         ->where('orders.status', 'cancel')
         ->orderBy('orders.date', 'desc')
         ->where(function ($query) use ($search) {  // <<<
        $query->where('users.fname', 'LIKE', '%'.$search.'%')
            ->orWhere('users.lname', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.order_number', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.grandtotal', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.date', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.status', 'LIKE', '%'.$search.'%')
            ->orderBy('orders.created_at', 'desc');
         })->paginate($perPage, $columns = ['*'], $pageName = 'orders'
         )->appends([
        'per_page'   => $perPage
         ]);
         $pagination = $orders->appends ( array ('search' => $search) );
        if (count ( $pagination ) > 0){
            return view('cooperative.canceled-orders', compact(
            'perPage',
            'orders'))->withDetails ( $pagination );     
             } 
             else{
                 redirect()->back()->with('status', 'No record found'); 
             }
        \LogActivity::addToLog('Admin view cancel order');
        return view('cooperative.canceled-orders', compact('perPage','orders'));
    }
    
    public function cooperativeSales(Request $request){
        if( Auth::user()->role_name  == 'cooperative'){
            $id = Auth::user()->id; //

             $countSoldProducts = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
             ->join('users', 'users.id', '=', 'orders.user_id')// get the buyer
              ->join('products', 'products.id', '=', 'order_items.product_id')
              ->where('orders.status', 'paid')
              ->where('orders.user_id', '!=', Auth::user()->id)
             ->where('order_items.seller_id', $id);

             $countMyCustomerOrder = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
             ->join('users', 'users.id', '=', 'orders.user_id')// get the buyer
              ->join('products', 'products.id', '=', 'order_items.product_id')
              ->where('orders.status', 'paid')
              ->where('orders.user_id', '!=', Auth::user()->id)
             ->where('order_items.seller_id', $id);

             $countShippedItem= OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
             ->join('users', 'users.id', '=', 'orders.user_id')// get the buyer
             ->join('products', 'products.id', '=', 'order_items.product_id')
             ->where('order_items.delivery_status', 'delivered')
             ->where('orders.user_id', '!=', Auth::user()->id)
             ->where('order_items.seller_id', $id);

            $sumSales =  DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('users', 'users.id', '=', 'orders.user_id')// get the buyer
             ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select([
             'orders.*',
             'order_items.*',
             'users.fname', 
             'users.phone',
             'products.prod_name',
             'products.image',
             'products.seller_price'
             ])->where('orders.status', 'paid')
              ->where('orders.user_id', '!=', Auth::user()->id)
             ->where('order_items.seller_id', $id);

            $perPage = $request->perPage ?? 10;
            $search = $request->input('search');

           $sales =  DB::table('order_items')
           ->join('orders', 'orders.id', '=', 'order_items.order_id')
           ->join('users', 'users.id', '=', 'orders.user_id')// get the buyer
            ->join('products', 'products.id', '=', 'order_items.product_id')
           ->select([
            'orders.*',
            'order_items.*',
            'users.fname', 
            'users.phone',
            'products.prod_name',
            'products.image',
            'products.seller_price'
            ])
            ->where('orders.status', 'paid')
            ->where('products.seller_id', $id)
            ->orderBy('date', 'desc')
            ->where(function ($query) use ($search) {  // <<<
            $query->where('orders.order_number', 'LIKE', '%'.$search.'%')
            ->orWhere('orders.date', 'LIKE', '%'.$search.'%')
            ->orWhere('users.fname', 'LIKE', '%'.$search.'%')
            ->orWhere('products.prod_name', 'LIKE', '%'.$search.'%')
            ->orWhere('products.seller_price', 'LIKE', '%'.$search.'%')
            ->orderBy('orders.created_at', 'desc');
            })->paginate($perPage, $columns = ['*'], $pageName = 'sales')
            ->appends(['per_page'   => $perPage]);
            $pagination = $sales->appends ( array ('search' => $search) );
            if (count ( $pagination ) > 0){
                    return view('cooperative.sales', compact(
                    'perPage',
                    'countSoldProducts',
                    'sales',
                    'sumSales',
                    'countMyCustomerOrder',
                    'countShippedItem'))->withDetails ( $pagination );     
            } 
            else{ redirect()->back()->with('status', 'No record found'); }

            \LogActivity::addToLog('Admin view sales');
            return view('cooperative.sales', compact(
            'perPage',
            'countSoldProducts',
            'sales',
            'sumSales',
            'countMyCustomerOrder',
            'countShippedItem'));
         }
         else{ return Redirect::to('/login');} 
    }

    public function adminProducts(Request $request){
        $id = Auth::user()->id;
        // count seller/cooperative products 
        $count_product = User::join('products', 'products.seller_id', '=', 'users.id')
        ->where('users.id', $id);
        // count seller/cooperative approved products 
        $countApprovedProduct = User::join('products', 'products.seller_id', '=', 'users.id')
        ->where('products.prod_status', 'approve')
        ->where('users.id', $id);
        // sum total sales for seller/cooperative products that was paid for
        $sales = Transaction::join('order_items', 'order_items.order_id', '=', 'transactions.order_id')
        ->join('products', 'products.seller_id', '=', 'order_items.seller_id')
        ->join('orders', 'orders.id', '=', 'order_items.order_id')
        ->where('orders.pay_status', 'paid')
        ->where('transactions.pay_status', 'success')
        ->where('products.seller_id', $id)
        ->get('products.seller_price');
        // count seller/cooperative products that was sold 
        $countSoldProducts = Transaction::join('order_items', 'order_items.order_id', '=', 'transactions.order_id')
        ->join('products', 'products.seller_id', '=', 'order_items.seller_id')
        ->join('orders', 'orders.id', '=', 'order_items.order_id')
        ->where('orders.pay_status', 'paid')
        ->where('transactions.pay_status', 'success')
        ->where('products.seller_id', $id);

        $perPage = $request->perPage ?? 10;
        $search = $request->input('search');
        $products = DB::table('products')->select(['*'])
        ->where('products.deleted_at',  null)
        ->where('seller_id', $id)
        ->orderBy('created_at', 'desc')
        ->where(function ($query) use ($search) {  // <<<
       $query->where('prod_status', 'LIKE', '%'.$search.'%')
           ->orWhere('prod_name', 'LIKE', '%'.$search.'%')
           ->orWhere('created_at', 'LIKE', '%'.$search.'%')
           ->orderBy('created_at', 'desc');
        })->paginate($perPage, $columns = ['*'], $pageName = 'products'
        )->appends([
       'per_page'   => $perPage
        ]);
        $pagination = $products->appends ( array ('search' => $search) );
            if (count ( $pagination ) > 0){
                  \LogActivity::addToLog('Admin products');
                return view ('cooperative.products',  compact(
                'perPage', 
                'products',
                'countSoldProducts',
                'sales',
                'count_product',
                'countApprovedProduct'))->withDetails ( $pagination );    
            }  
            else{
                redirect()->back()->with('status', 'No record found'); 
            }
            return view ('cooperative.products',  compact(
                'perPage', 
                'products',
                'countSoldProducts',
                'sales',
                'count_product',
                'countApprovedProduct'));     
    } 
    //edit product
    public function editProduct(Request $request, $id){
        if( Auth::user()){
            $product = Product::find($id);
            //dd($product->id);
            return view('cooperative.edit-product', compact('product')); 
        }
          else { return Redirect::to('/login');
        }
  }
  
      //update product
      public function updateProduct(Request $request, $id){
        $this->validate($request, [
          'quantity'      => 'required|max:255',  
          'old_price'    => 'max:255',
          'price'        => 'required|max:255',
          'productname'  => 'required|max:255',
          'brand'        => 'max:255',
          'description'  => 'max:255',
          ]);
          // add company and coperative percentage
          $company_percentage = $request->price *  5 / 100;
          $price = $request->price  + $company_percentage;
  
          $product = Product::find($id);
          $product->prod_name     = $request->productname;
          $product->quantity      = $request->quantity;
          $product->old_price     = $request->old_price;
          $product->seller_price  = $request->price;
          $product->price         = $price;
          $product->prod_brand     = $request->brand;
          $product->description     = $request->description;
          $product->update();
  
          $data = 'Edit successful for ' .$request->productname. '';
          \LogActivity::addToLog('ProductUpdate');
          return redirect('admin-products')->with('success',  $data);
      }


      public function removeProductPage(Request $request, $id){
        if( Auth::user()){
          $product = Product::find($id);
          return view('cooperative.remove-product', compact('product')); 
       }
        else { return Redirect::to('/login');}   
      }
  
      public function removeProduct(Request $request){
        $seller_id = Auth::user()->id;
        $id = $request->product_id;
        //soft delete
        Product::where('id', $id)->where('seller_id', $seller_id)->delete(); 
        Product::where('id', $id)->update([
          'prod_status' =>  'deleted',
          ]);
        \LogActivity::addToLog('Remove product');
        return redirect('admin-products')->with('success', 'Product Removed Successful!');
    }
  
    public function approveMemberOrderPage(Request $request, $id){
        if( Auth::user()){
          $order = Order::find($id);
          return view('cooperative.approve-member-order', compact('order')); 
       }
        else { return Redirect::to('/login');}   
      }
  

    public function approveOrder(Request $request){
        $id = Auth::user()->id;
        $cooperative = Auth::user()->coopname;
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        $order_number = Order::where('id', $order_id)->get('order_number') ;
        
        $grandtotal = \DB::table('orders')->where('id', $order_id)->first()->grandtotal;
        $credit = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
        ->where('users.id', $id)
        ->get('credit'); 
        $plugCredit = Arr::pluck($credit, 'credit');
        $getCredit = implode('', $plugCredit);

        $paymentDays = User::where('id', $id)->get('payment_days');
        $pluckPaymentDays = Arr::pluck($paymentDays, 'payment_days');
        $payment = implode('', $pluckPaymentDays);
        
        //if admin has credit approve order
        if($getCredit > $grandtotal ){
            //check if member has loan before approving order
            $memberID = Order::where('id',  $order_id)->get('user_id');
            $checkExistingLoan = Loan::whereIn('member_id', $memberID)
            ->where('loan_balance', '!=', null)
             ->where('loan_balance', '!=', '0')
             ->where('loan_status', '=', 'payout')
             ->get('*')->pluck('loan_balance');
             $getmMembers = User::join('loan', 'loan.member_id', '=', 'users.id')
             ->whereIn('loan.member_id', $memberID)->get('*')->pluck('fname');
             $members = substr($getmMembers, 1, -1);
 
             $checkLoanrequest = Loan::whereIn('member_id', $memberID)
              ->where('loan_status', '=', 'request')
              ->where('loan_status', '=', 'approved')
              ->get('*')->pluck('principal');
 
             if(!$checkExistingLoan->isEmpty()){
             return redirect('cooperative-loan')->with('loanExist',  ''.$members.' has unfinished loan');
             }

            $status = 'approved'; 
            $approve = Order::where('id', $order_id)
            ->update([
            'status' => $status,
            'admin_settlement_msg' => 'payment is ' .$payment
            ]);
            \DB::table('vouchers')->where('user_id', Auth::user()->id)->decrement('credit',$grandtotal);
            $orderItem_quantity= OrderItem::Join('products', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.order_id', $order_id)
            ->get('order_quantity');

           $seller_id = Order::join('order_items', 'order_items.order_id', '=', 'orders.id')
           ->where('order_items.order_id', $order_id)
           //->distinct()
           ->pluck('order_items.seller_id')->toArray();

           $myArray = Arr::pluck($seller_id,['seller_id']);
           $ss =json_encode($myArray);

           $seller_price = Product::join('order_items', 'order_items.product_id', '=', 'products.id')
           ->where('order_items.order_id', $order_id)
           ->distinct()
           ->pluck('products.seller_price')
           ->toArray();

           $product_id = Product::join('order_items', 'order_items.product_id', '=', 'products.id')
           ->where('order_items.order_id', $order_id)
           ->distinct()
           ->pluck('products.id')
           ->toArray();

        
           $orderItem_quantity= Order::join('order_items', 'order_items.order_id', '=', 'orders.id')
           ->where('order_items.order_id', $order_id)
           ->distinct()
           ->pluck('order_items.order_quantity')
           ->toArray();
    
            //Wallet::whereIn('user_id', $seller_id)->increment('credit', $seller_price); 

                //for every approve order decrease product quantity
                //  $stock = \DB::table('products')->where('id', $case)->first()->quantity;
                //  dd($stock);
                //  if($stock > $orderItem_quantity){
                //    \DB::table('products')->where('id', $product_id)->decrement('quantity',$orderItem_quantity);
                //  }
          
             \LogActivity::addToLog('Admin approve order');
             $memberID = Order::where('id',  $order_id)->get('user_id');
             $memberEmail = User::whereIn('id', $memberID)->get('email');
    
             $memberName = User::where('id', $memberID)->get('fname');

             $getSellerName = OrderItem::join('users', 'users.id', '=', 'order_items.seller_id')
             ->join('orders', 'orders.id', '=', 'order_items.order_id')
             ->whereIn('users.id', $seller_id)
             ->where('order_items.order_id', $order_id)
             ->get('fname');
             $getName =Arr::pluck($getSellerName, 'fname');
             $sellerName = implode('', $getName);

             $product = OrderItem::Join('products', 'products.id', '=', 'order_items.product_id')
             ->join('orders', 'orders.id', '=', 'order_items.order_id')
             ->where('order_items.order_id', $order_id)
             ->get('products.prod_name');

             $image = OrderItem::Join('products', 'products.id', '=', 'order_items.product_id')
             ->join('orders', 'orders.id', '=', 'order_items.order_id')
             ->where('order_items.order_id', $order_id)
             ->get('products.image');

             $quantity = OrderItem::Join('orders', 'orders.id', '=', 'order_items.order_id')
             ->where('order_items.order_id', $order_id)
             ->get('order_items.order_quantity');

             $amount = OrderItem::Join('orders', 'orders.id', '=', 'order_items.order_id')
             ->where('order_items.order_id', $order_id)
             ->get('order_items.amount');

             $sellerProductImage = OrderItem::Join('products', 'products.id', '=', 'order_items.product_id')
             ->join('orders', 'orders.id', '=', 'order_items.order_id')
             ->whereIn('order_items.seller_id', $seller_id)
             ->where('order_items.order_id', $order_id)
             ->get('products.image');

             $sellerProduct = OrderItem::Join('products', 'products.id', '=', 'order_items.product_id')
             ->join('orders', 'orders.id', '=', 'order_items.order_id')
             ->whereIn('order_items.seller_id', $seller_id)
             ->where('order_items.order_id', $order_id)
             ->get('products.prod_name');

             $sellerOrderQuantity= OrderItem::Join('orders', 'orders.id', '=', 'order_items.order_id')
             ->whereIn('order_items.seller_id', $seller_id)
             ->where('order_items.order_id', $order_id)
             ->get('order_items.order_quantity');

             $sellerProductAmount = OrderItem::Join('products', 'products.id', '=', 'order_items.product_id')
             ->join('orders', 'orders.id', '=', 'order_items.order_id')
             ->whereIn('order_items.seller_id', $seller_id)
             ->where('order_items.order_id', $order_id)
             ->get('products.seller_price');

             $status = Order::where('id', $order_id)->get('status');

             //send emails
            $memberData = 
            array(
                'cooperative'   => $cooperative,
                'order_number'  => $order_number,  
                'name'          => $memberName, 
                'product'       => $product, 
                'image'         => $image,
                'quantity'      => $quantity, 
                'amount'        => $amount,
                'total'         => $grandtotal, // delivery inclusive
                'status'        => $status,
            );
            $sellerData = 
            array(
                'cooperative'   => $cooperative,
                'order_number'  => $order_number,  
                'member'        => $memberName, 
                'product'       => $sellerProduct, 
                'image'         => $sellerProductImage,
                'quantity'      => $sellerOrderQuantity, 
                'amount'        => $sellerProductAmount,  
                'name'          => $getSellerName,
                'status'        => $status,
            );

            $data = 
            array(
                'cooperative'   => $cooperative,
                'order_number'  => $order_number,  
                'amount'        => $grandtotal, // delivery inclusive
                'name'          => $memberName, 
                'status'        => $status,      
            );
            $getSellerEmail = OrderItem::join('users', 'users.id', '=', 'order_items.seller_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('users.id', $seller_id)
            ->where('order_items.order_id', $order_id)
            ->get('email'); 

            // foreach ($getSellerEmail as $key => $user) {
           
            //     Mail::to($user->email)->send(new SalesEmail($sellerData)); 
            // }

            Mail::to($memberEmail)->send(new OrderApprovedEmail($memberData)); 
         
            Mail::to('info@lascocomart.com')->send(new OrderEmail($data));    
            return redirect('admin-member-order')->with('success', 'Approved successful!'); 
        }
        else{
            return redirect('admin-member-order')->with('error', 'Your credit is low kindly contact LascocoMart to get funds');   
        }
       
    }

    public function members(Request $request ){
        if( Auth::user()->role_name  == 'cooperative'){
            $id = Auth::user()->id;
            $code = Auth::user()->code;
            $selectRole = Role::all();
            $owncredit = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
            ->where('users.id', $id)
            ->get('credit'); 
            $credit = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
            ->where('users.code', $code) 
            ->where('users.email_verified_at', '!=','null')
            ->paginate( $request->get('per_page', 10));
             //users logged from the beggining of current callendar month
            $adminActiveMember =  User::where('code', $code)
            ->where('id', '!=', Auth::user()->id)
            ->where('last_login', '>', new DateTime('last day of previous month'))
            ->get();
            //$members = User::all()->except(Auth::id())->where('code', $code); 
            $perPage = $request->perPage ?? 12;
            $search = $request->input('search');

             $members = DB::table('users')->join('cooperative_role', 'cooperative_role.member_id', 'users.id')
            ->select(['users.*', 'cooperative_role.member_role_name'])
            ->where('users.code', $code)
            ->where('users.deleted_at',  NULL)
            ->where('users.id', '!=', Auth::user()->id)
            ->orderBy('users.created_at', 'desc')
            ->where(function ($query) use ($search) {  // <<<
            $query->where('users.fname', 'LIKE', '%'.$search.'%')
            ->orWhere('users.lname', 'LIKE', '%'.$search.'%')
            ->orWhere('users.email', 'LIKE', '%'.$search.'%')
            ->orWhere('users.phone', 'LIKE', '%'.$search.'%')
            ->orderBy('users.created_at', 'desc');
            })->paginate($perPage, $columns = ['*'], $pageName = 'members'
            )->appends(['per_page'   => $perPage]);

            $pagination = $members->appends ( array ('search' => $search) );
            if (count ( $pagination ) > 0){
                return view ('cooperative.all_members', compact(
                'perPage',
                'credit', 
                'owncredit', 
                'members',
                'adminActiveMember','selectRole'))->withDetails($pagination );    
            }
            else{
                redirect()->back()->with('member-status', 'No record found'); 
            }   
            \LogActivity::addToLog('Admin members');
            return view('cooperative.all_members', compact(
            'perPage',
            'credit', 
            'owncredit', 
            'members',
            'adminActiveMember','selectRole'));
        }
        else { return Redirect::to('/login');}
    
    }
    //softdelete
    public function deleteMember(Request $request, $id )
    {
        $code = Auth::user()->code; //
        $user = User::where('code', $code)->where('id', $id)->delete();
        \LogActivity::addToLog('Admin remove member');
        return redirect()->back()->with('success', 'Member Removed Successfully!');
    }

    public function invoice(Request $request, $order_number )
    {
        if( Auth::user()->role_name  == 'cooperative'){
            $code = Auth::user()->code; //
            $item = Order::join('users', 'users.id', '=', 'orders.user_id')
            ->leftjoin('order_items', 'order_items.order_id', '=', 'orders.id')
             ->join('shipping_details', 'shipping_details.shipping_id', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            // ->join('vouchers', 'vouchers.user_id', '=', 'users.id')
            ->where('users.code', $code)
            ->where('orders.order_number', $order_number)
            ->get(['orders.*', 
            'users.*',
            'order_items.*',  
            'products.prod_name', 
            'products.image', 
            'products.description',
            'shipping_details.*'])->first();
        
            $orders = Order::join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.order_number', $order_number)
            ->get(['orders.*', 
            'order_items.*',  
            'products.prod_name', 
            'products.image', 
            'products.description']);              
            \LogActivity::addToLog('Admin invoice');
        return view('invoice', compact('item', 'orders'));
        }

    else { return Redirect::to('/login');}             
    }

        //add new products
     public function addProduct(Request $request)
    {
    if( Auth::user()->role_name  == 'cooperative'){
        $categories = Categories::all(); 
        return view('cooperative.add_new_product', compact('categories'));
        }
      else { return Redirect::to('/login');}
    }
    
    
      public function coopstore(Request $request)
        {   
        $user_id = Auth::user()->id; // get the seller id
        $user_role = Auth::user()->role;
        //|dimensions:max_width=600,max_height=600
         $this->validate($request, [
         'image' => 'required|image|mimes:jpg,png,jpeg|max:300',// maximum is 300kb , 600 x 600 pixel
         'img1' => 'image|mimes:jpg,png,jpeg|max:300',
         'img2' => 'image|mimes:jpg,png,jpeg|max:300',
         'img3' => 'image|mimes:jpg,png,jpeg|max:300',
         'prod_name' => 'required|string|max:100',
         'quantity' => 'required|numeric|max:1000',
         'price' => 'required|numeric|min:100',
         'cat_id' => 'required|string|max:100',
        ]);
    
            //$image = $request->file('image')->getClientOriginalName();// get image original name
            
            //$image = time().'.'.$request->image->extension();
           
            //this works on local host and linux
           //$path = $request->file('image')->store('/images/resource', ['disk' =>   'my_files']);
           
            $image= $request->file('image');
            if(isset($image))
            {
            $imageName =  rand(1000000000, 9999999999).'.jpeg';
             $image->move(public_path('assets/products'),$imageName);
             $image_path = "/assets/products/" . $imageName; 
             }

            else {
            $image_path = "";
             }

           $img1= $request->file('img1');
            if(isset($img1)){
            $img1Name =  rand(1000000000, 9999999999).'.jpeg';
             $img1->move(public_path('assets/products'),$img1Name);
             $img1_path = "/assets/products/" . $img1Name; 
             }
            else {$img1_path = "";}

            $img2= $request->file('img2');
            if(isset($img2)){
            $img2Name = rand(1000000000, 9999999999).'.jpeg';
             $img2->move(public_path('assets/products'),$img2Name);
             $img2_path = "/assets/products/" . $img2Name; 
             }
            else {$img2_path = "";}

            $img3= $request->file('img3');
            if(isset($img3)){
            $img3Name =  rand(1000000000, 9999999999).'.jpeg';
             $img3->move(public_path('assets/products'),$img3Name);
             $img3_path = "/assets/products/" . $img3Name; 
             }
            else {$img3_path = "";}

            $img4= $request->file('img4');
            if(isset($img4)){
            $img4Name = rand(1000000000, 9999999999).'.jpeg';
             $img4->move(public_path('assets/products'),$img4Name);
             $img4_path = "/assets/products/" . $img4Name; 
             }
            else {$img4_path = "";}

              //    $img2= $request->file('img2');
           //  if(isset($img2))
           //  {
           //  $img2Name = time().'_'.$img2->getClientOriginalName();
           //   $img2->move(public_path('assets/products'),$img2Name);
           //   $img2_path = "/assets/products/" . $img2Name; 
           //   }

            // add company and coperative percentage
            //$cop = $request->price * 5 / 100; //cooperative percentage
            $company_percentage = $request->price *  5 / 100;// coopmart percentage
            $price = $request->price  + $company_percentage;

           $product = new Product;
           $product->cat_id    = $request->cat_id;
           $product->prod_name  = $request->prod_name;
           $product->quantity   = $request->quantity;
           $product->prod_brand = $request->prod_brand;
           $product->old_price  = $request->old_price;
           $product->seller_price = $request->price;
           $product->price      = $price;
           $product->description= $request->description;
           $product->image      = $image_path;
           $product->img1       = $img1_path;
           $product->img2       = $img2_path;
           $product->img3       = $img3_path;
        //    $product->img4       = $img4_path;
           $product->seller_id  = $user_id;
           $product->seller_role  = $user_role;
           $product->prod_status = 'pending';
           $product->save();

           $superadmin = User::where('role_name', '=', 'superadmin')->get();
           $get_superadmin_id =Arr::pluck($superadmin, 'id');
           $superadmin_id = implode('', $get_superadmin_id);
           
          $product_id =$product->id;
          $product_name= $product->prod_name; 
          $notification = new NewProduct($product_id, $product_name);
          Notification::send($superadmin, $notification);
           // send email notification to coopmart for approval
            $data = array(
                'name'      =>  'coopmart',
                'message'   =>   'approve'
                );

             Mail::to('info@lascocomart.com')->send(new SendMail($data));
             \LogActivity::addToLog('Admin new product');
            return redirect('admin-products')->with('status', 'New product added successfully');   
               
    }   

    public function coopremove_product(Request $request, $id)
    {
        $code = Auth::user()->code;
        $seller_id = Auth::user()->id; 
        $status = 'remove';
        //update table
        Product::where('id', $id)->update(['prod_status' => $status]);
        Session::flash('remove', ' Product Removed Successful!'); 
        Session::flash('alert-class', 'alert-success'); 
        \LogActivity::addToLog('Admin remove product');
        return redirect()->back()->with('success', 'Product Removed Successful!');
    }


    public function coopsales_preview(Request $request){
        if( Auth::user()->role_name  == 'cooperative'){
            $id = Auth::user()->id; //
            $sales = Product::join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'Paid')
            ->where('products.seller_id', $id) 
            ->orderBy('date', 'desc')  
            ->paginate( $request->get('per_page', 5));  
            \LogActivity::addToLog('Admin view sales');
            return view('cooperatives.sales_preview', compact('sales'));
         }
         else{
            return Redirect::to('/login');
         } 
    }

    public function fmcgproductsview(Request $request){
        $fmcgproductsview = FcmgProduct::where('prod_status', 'approve')
        ->orderBy('created_at', 'desc')
        ->paginate($request->get('per_page', 16));
        
        $seller = Arr::pluck($fmcgproductsview, 'seller_id');
        $get_seller_id = implode(" ",$seller);

        //get sellers details
        $email          = User::where('id', $get_seller_id)->get('email');
        $seller_details = User::where('id', $get_seller_id)->get();

        $seller_name    = Arr::pluck($seller_details, 'fname');
        $name           = implode(" ",$seller_name);
       

          //send email notification of low stock
        
        foreach($fmcgproductsview   as $key => $prod){
             $date = Carbon::tomorrow();
              if($prod->quantity < 1 & $prod->created_at <= $date){
           
            $data = array(
                'name'      => $name,
                'prod_name' => $prod->prod_name,
                'quantity'  => $prod->quantity,  
                'message'   => 'Your product'  
                                            
               );
             Mail::to($email)->send(new LowStockEmail($data));
              //soft delete product from landing page - update status 
             Product::where('id', $prod->id)
                    ->update(['prod_status' => 'remove']);
          }

        }
              
        \LogActivity::addToLog('Admin view FMCG product');
        return view('cooperative.fmcgproductsview', compact('fmcgproductsview'));
    }

  
   

    public function fcmgupdate(Request $request)
    {
        if($request->id && $request->quantity){
            $fcmgcart = session()->get('fcmgcart');
            $fcmgcart[$request->id]["quantity"] = $request->quantity;
            session()->put('fcmgcart', $fcmgcart);
            session()->flash('success', 'Cart updated successfully');
        }
    }
  
   

    public function fcmgremove(Request $request)
    {
        if($request->id) {
            $fcmgcart = session()->get('fcmgcart');
            if(isset($fcmgcart[$request->id])) {
                unset($fcmgcart[$request->id]);
                session()->put('fcmgcart', $fcmgcart);
            }
            session()->flash('success', 'Product removed successfully');

        }
    }

    
    public function fmcgcheckout(Request $request){

         if( Auth::user()){
     
        //get voucher from input
        $id = Auth::user()->id;// get user id for the login member

          $fcmgcart = session()->get('fcmgcart');
          $fcmgcart[$request->id]["quantity"] = $request->quantity;
          $fcmgcart[$request->id]["price"] = $request->price;
           $fcmgcart[$request->id]["seller_id"] = $request->seller_id;
 
          $totalAmount = 0;

        foreach ($fcmgcart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];

            // check if sufficient credit limit
             //$getcredit = \DB::table('vouchers')->where('user_id', $id)->first()->credit;


    //$getcredit = \DB::table('vouchers')->where('user_id', $id)->get('credit')->first();

           //   $credit = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
           //          ->where('vouchers.user_id', $id)
           //          ->get(['vouchers.credit'])->first(); 

           // if($credit < $totalAmount){

           //  Session::flash('credit', ' Your balance is low. Reduce your  items or contact your cooperative admin!'); 
           //  Session::flash('alert-class', 'alert-danger'); 

           // }

            }//foreach

           $voucher = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
                    ->where('vouchers.user_id', $id)
                    ->get(['vouchers.*', 'users.*']); 
                    \LogActivity::addToLog('Admin checkout FCMG');
        return view('cooperative.fmcgcheckout', compact('voucher'));
    }

        else { return Redirect::to('/login');}

        }

          
    public function fmcgcart()
    {
        return view('cooperative.fmcgcart');
    }
  
    public function fmcgaddToCart($id)
    {
        $fcmgproducts = FcmgProduct::findOrFail($id);
          
        $fcmgcart = session()->get('fmcgcart', []);
  
        if(isset($fcmgcart[$id])) {
            $fcmgcart[$id]['quantity']++;
        } else {
            $fcmgcart[$id] = [
                "prod_name" => $fcmgproducts->prod_name,
                "quantity" => 1,
                "price" => $fcmgproducts->price,
                "image" => $fcmgproducts->image,
                "id" => $fcmgproducts->id,
                "seller_id" => $fcmgproducts->seller_id,

            ];
        }
          
        session()->put('fmcgcart', $fcmgcart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

  public function fmcgaddToCartPreview($id)
    {
        $fcmgproducts = FcmgProduct::findOrFail($id);
          
        $fcmgcart = session()->get('fmcgcart', []);
  
        if(isset($fcmgcart[$id])) {
            $fcmgcart[$id]['quantity']++;
        } else {
            $fcmgcart[$id] = [
                "prod_name" => $fcmgproducts->prod_name,
                "quantity" => 1,
                "price" => $fcmgproducts->price,
                "image" => $fcmgproducts->image,
                "id" => $fcmgproducts->id

            ];
        }
          
        session()->put('fmcgcart', $fcmgcart);
        \LogActivity::addToLog('Admin cview cart FMCG');
        return redirect()->route('cooperative.fmcgcart')->with('success', 'Product added to cart successfully!');
    }
    
    
   
}// class