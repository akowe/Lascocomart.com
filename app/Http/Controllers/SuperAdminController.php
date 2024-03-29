<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SMS;
use App\Models\Profile;
use App\Models\Voucher;
use App\Models\Wallet;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingDetail;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\About;
use App\Models\Privacy;
use App\Models\ReturnRefund; 
use App\Models\Terms;
use App\Models\FundRequest;
use Carbon\Carbon;
use App\Notifications\NewCardPayment;
use App\Notifications\ApproveFund;
use App\Notifications\CancelFundRequest;
use App\Mail\PasswordResetEmail;
use Notification;

use Auth;
use Validator;
use Session;
use Paystack;
use PDF;
use Mail;


class SuperAdminController extends Controller
{
    //
      public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware('superadmin');
    }

    public function index(Request $request){
      if( Auth::user()->role_name  == 'superadmin'){
      // select all user except the current login
        $users = User::all()->except(Auth::id());
        $id = Auth::user()->id;

        $cooperatives = User::where('role', '2')->get('*');
        $sellers = User::where('role', '3')->get('*');
        $members = User::where('role', '4')->get('*');
        // count orders 
        $count_orders = Order::all()
        ->where('status', '!=', 'awaits approval')
        ->where('status', '!=', 'cancel');
        $count_sales = Order::where('orders.pay_status', 'success');
        $sumSales = Order::where('orders.pay_status', 'success')->get('grandtotal');

        $offlinePayment = Order::where('pay_type', '=', 'Offline');
        $onlinePayment =Order::where('pay_type', '=', 'Debit Card');
        $bankPayment =Order::where('pay_type', '=', 'Bank Transfer');

        $onlinePayment = Transaction::join('users', 'users.id', '=', 'transactions.user_id')
        ->get('tran_amount');

        $products = Product::all(); 
        $funds = User::join('fund_request', 'fund_request.user_id', '=', 'users.id')
        ->where('fund_request.status', 'approve');

        $registeredUsers = User::select(
        \DB::raw("COUNT(*) as total_user"), 
        \DB::raw('YEAR(created_at) as year'),
        )
        ->where('role', '!=', '1')
        ->groupby('year')
        ->get();

        $result[] = ['Year', 'Users'];
        foreach ($registeredUsers as $key => $value) {
        $result[++$key] = [$value->year,  (int)$value->total_user ];
        }

        //dd(json_encode($result));
        
        $salesChart = Order::select(
          \DB::raw("COUNT(*) as total_sales"),
          \DB::raw('YEAR(created_at) as year')
          )
          ->where('pay_status', 'success')
          ->groupby('year')
          ->get();
  
          $sales[] = ['Sales', 'Other'];
          foreach ($salesChart as $key => $value) {
          $sales[++$key] = ["Sales", $value->year];
          $sales[++$key] = ["Other", (int)$value->total_sales];
          }
          \LogActivity::addToLog('SuperAdmin');
        return view('company.admin', compact('sales', 'funds','cooperatives', 'sellers', 'members', 
        'count_orders', 'count_sales',  'products', 'users', 'onlinePayment', 'sumSales', 'offlinePayment', 'onlinePayment', 'bankPayment'))->with('registeredUsers',json_encode($result));
    }
    else { return Redirect::to('/login');}
   
    }

    public function orderHistory(Request $request){
        // $orders = User::join('orders', 'orders.user_id', '=', 'users.id')
        // ->where('orders.status', '!=', 'awaits approval')
        // ->where('orders.status', '!=', 'cancel')
        // ->orderBy('orders.date', 'desc');
       // ->paginate( $request->get('per_page', 5));
        $orders =Order::join('users', 'users.id', '=', 'orders.user_id')
        ->where('orders.status', '!=', 'awaits approval')
        ->where('orders.status', '!=', 'cancel')  
        ->get(['users.coopname', 'orders.*']);
        \LogActivity::addToLog('SuperAdmin orderHistory');
        return view('company.order-history', compact('orders'));
    }
 
    public function allocateFund(Request $request)
    {  
        // if(null !== $_POST['submit']){
            $admin_id = Auth::user()->id;
            $user_id  = $request->input('user_id');
            $id       = $request->id;
            $status   = $request->input('status');
            $remark   = $request->input('remark');
            $amount   = $request->input('amount');
            $member   = User::where('id', $user_id)->first();

                // check if user is verified
                $verified = \DB::table('users')->where('id', $user_id)->first()->email_verified_at;
                if($verified){ 
                  \DB::table('fund_request')->where('id', $id)
                  ->update([
                    'status' => $status,
                    'remark' => $remark
                    ]);
                      //increase user credit limit
                      \DB::table('vouchers')->where('user_id', $user_id)->increment('credit',$amount);
                      Session::flash('credit', ' Fund Allocated successfully!'); 
                      Session::flash('alert-class', 'alert-success'); 

                      $getUser = User::where('id', $user_id)
                      ->get('id');
              
                      $getFundID = FundRequest::where('id', $id)->get();
                      $fundID= Arr::pluck($getFundID, 'id'); // 
                      $fund_id = $id;

                      $getCredit = \DB::table('vouchers')->where('user_id', $user_id)->get('credit');
                      $arrayCredit = Arr::pluck($getCredit, 'credit');
                      $credit = implode('', $arrayCredit);

                      $notification = new ApproveFund($fund_id, $amount, $credit);
                      Notification::send($getUser, $notification);
                      \LogActivity::addToLog('SuperAdmin addFunds');
                      return redirect()->back()->with('credit', 'Fund Allocated successfully!');
            
                }
              else{
                    Session::flash('verified', 'Credit not added. This member has not verified his/her account.'); 
                    Session::flash('alert-class', 'alert-danger'); 
                    return redirect()->back()->with('credit', 'Credit not added. This member has not verified his/her account.');
              }
          // } 
           

  }

    public function fundsAllocated(Request $request){
      $id = Auth::user()->id;
      $funds = User::join('fund_request', 'fund_request.user_id', '=', 'users.id')
      ->where('fund_request.status', 'approve')
      ->where('fund_request.admin_id', $id)// funds approve by superadmin only

      ->paginate( $request->get('per_page', 5));
      \LogActivity::addToLog('SuperAdmin fundsAllocated');
      return view('company.funds-allocated', compact('funds'));
    }

    public function editFundRequest($id)
    {
        $fund = FundRequest::find($id);
        $user = User::where('id', $fund->user_id)->get('email');
        $array = Arr::pluck($user,'email' );
        $userEmail = implode(",", $array);
        \LogActivity::addToLog('Cancel fundRequest');
        return view('cancel-fund-request', compact('fund', 'userEmail'));
    }

    public function cancelFundRequest(Request $request){
        $cancel = 'cancel';
        $remark = $request->remark;
        $fund_id = $request->id;
        $amount = $request->amount;
        FundRequest::where('id', $fund_id)
        ->update([
        'status' => $cancel,
        'remark' => $remark
        ]); 
        $getUser = User::join('fund_request', 'fund_request.user_id', '=', 'users.id')
        ->where('fund_request.id', $fund_id)
        ->get('users.id');

        $getFundRequest = FundRequest::where('id', $fund_id)->get();
        $FundRequest= Arr::pluck($getFundRequest, 'fund_id'); // 
        $order_number = implode('', $FundRequest);
     
        $notification = new CancelFundRequest($fund_id, $amount, $remark);
        Notification::send($getUser, $notification);
        \LogActivity::addToLog('Cancel fundRequest');

        return redirect('show-fundrequest')->with('success', 'Fund Request Canceled!');
    } 

    public function sales_invoice(Request $request, $order_number )
    {
     if( Auth::user()->role_name  == 'superadmin'){
     
        $item = Order::join('users', 'users.id', '=', 'orders.user_id')// count orders from members
        ->join('order_items', 'order_items.order_id', '=', 'orders.id')
        // ->join('shipping_details', 'shipping_details.shipping_id', '=', 'orders.id')
        ->join('products', 'products.id', '=', 'order_items.product_id')
        // ->join('vouchers', 'vouchers.user_id', '=', 'users.id')
        ->where('orders.order_number', $order_number)
        ->get([ 'orders.*', 'users.*', 'order_items.*', 'products.*'])->first();

        $orders = Order::join('order_items', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'products.id', '=', 'order_items.product_id')
        ->where('orders.order_number', $order_number)
        ->get(['orders.*',  'order_items.*',  'products.*']); 
        \LogActivity::addToLog('SuperAdmin userInvoice'); 
    return view('company.sales_invoice', compact('item', 'orders'));
           }

    else { return Redirect::to('/login');
    
        }             
    }


 public function order_details(Request $request, $order_number )
    {
     if( Auth::user()->role_name  == 'superadmin'){
      // 
         $orders = Product::join('order_items', 'order_items.product_id', '=', 'products.id')
         ->join('users', 'users.id', '=', 'products.seller_id')          
         ->join('orders', 'orders.id', '=', 'order_items.order_id')
          ->where('orders.order_number', $order_number)
          ->orderBy('orders.date', 'desc')
                        // ->get(['orders.*', 'users.*', 'order_items.*', 'products.*']);
          ->paginate( $request->get('per_page', 5)); 
          \LogActivity::addToLog('SuperAdmin orderDetails');
    return view('company.order_details', compact('orders'));
           }

    else { return Redirect::to('/login');
    
        }             
    }


    public function salesDetails(Request $request )
    {
     if( Auth::user()->role_name  == 'superadmin'){
        $count_sales = Order::where('orders.pay_status', 'success');
        $sales = Product::join('order_items', 'order_items.product_id', '=', 'products.id')
        ->join('users', 'users.id', '=', 'products.seller_id')          
        ->join('orders', 'orders.id', '=', 'order_items.order_id')
        ->where('orders.pay_status', 'success')
        ->orderBy('orders.date', 'desc')
        ->paginate( $request->get('per_page', 5)); 
        $grandtotal = Order::where('orders.pay_status', 'success')->get('grandtotal');
        $total = Order::where('orders.pay_status', 'success')->get('total');
        \LogActivity::addToLog('SuperAdmin salesDetails');
    return view('company.sales-details', compact('sales', 'grandtotal', 'total'));
           }

    else { return Redirect::to('/login');
    
        }             
    }
  
   public function products_list(Request $request){
  
      if( Auth::user()->role_name  == 'superadmin'){
        $products = User::join('products', 'products.seller_id', '=', 'users.id')
        ->where('products.prod_status', '!=', 'remove')
        ->orderBy('products.created_at', 'desc') 
        ->get(['products.*', 'users.fname', 'users.lname', 'users.coopname']); 
                        
                         // count products from members
        $count_product = User::join('products', 'products.seller_id', '=', 'users.id')
                          ->where('products.prod_status', 'pending')
                         ->orwhere('products.prod_status', 'approve');
                         \LogActivity::addToLog('SuperAdmin productList');
       return view('company.products_list', compact('products', 'count_product'));

       }

    else { return Redirect::to('/login');} 
   
    }

    //edit product
  public function editVendorProduct(Request $request, $id){
    if( Auth::user()->role_name  == 'superadmin'){
        $product = Product::find($id);
        return view('company.edit-vendor-product', compact('product')); 
     }
      else { return Redirect::to('/login');
    }
}

    public function mark_paid(Request $request)
    {
        if(null !== $_POST['submit']){
            $order_number  = $request->input('order_number');
             //mark order as paid
            \DB::table('orders')->where('order_number', $order_number)
              ->update([
                    'status' => 'paid',
                    'pay_status'=>'success',
                    'pay_type'=>'Offline' ,
                    'admin_settlement_msg' => 'paid'
              ]);

                //$order_number = Order::where('order_number', $order_number)->get('order_number');
                $order_id = Order::where('order_number', $order_number)->get('id');
                $ord=Arr::pluck($order_id, 'id');

                $orderItems = OrderItem::where('order_id', $ord)->get();
                foreach($orderItems as $item){
                  $seller_id=Arr::pluck($orderItems, 'seller_id');
                  $product_id=Arr::pluck($orderItems, 'product_id');
                  $getPrice = Product::where('id', $product_id)->get();
                  $getSellerPrice = Arr::pluck($getPrice, 'seller_price');
                  $sellerPrice = implode('', $getSellerPrice);

                  $seller =  User::where('id', $seller_id)
                  ->get('id');
                //for every new order decrease product quantity
                $itemQuantity = Arr::pluck($orderItems, 'order_quantity');
                $quantity = implode('', $itemQuantity);

                $stock = \DB::table('products')->where('id', $product_id)->first()->quantity;        
                if($stock > $quantity){
                    \DB::table('products')->where('id', $product_id)->decrement('quantity',$quantity);
                }
                $notification = new NewCardPayment($order_number);
                Notification::send($seller, $notification); 
                Wallet::where('user_id', $seller_id)->increment('credit',$sellerPrice);
              }
          
            Session::flash('pay', ' You have marked this orders as  "Paid !".'); 
            Session::flash('alert-class', 'alert-success'); 
        }
        \LogActivity::addToLog('SuperAdmin markPaid');
           return redirect()->back()->with('success', 'You have marked this orders as  "Paid !".');
    }

    public function confirmOrder(Request $request)
    {
        if(null !== $_POST['submit']){
            $order_number  = $request->input('order_number');
             //mark order as paid
            \DB::table('orders')
                ->where('order_number', $order_number)
                ->update([
                    'status' => 'confirmed',
                ]);

            Session::flash('confirm', ' Order Confirmed! .'); 
            Session::flash('alert-class', 'alert-success'); 
        }
           return redirect()->back()->with('success', 'Order Confirmed!.');
    }


  public function approved(Request $request)
  {
        if(null !== $_POST['submit']){
            $id  = $request->input('id');
             //mark order as paid
            \DB::table('products')
                ->where('id', $id)
                ->update(['prod_status' => 'approve']);

            Session::flash('approve', ' Product approved successful!.'); 
            Session::flash('alert-class', 'alert-success'); 
        }
            //return view('cooperative.credit_limit', compact('credit'));
            \LogActivity::addToLog('Approve product');
             return redirect()->back()->with('success', 'Product approved successful!..');

}

 public function users_list(Request $request){
  
      if( Auth::user()->role_name  == 'superadmin'){
          $coop = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
          ->where('users.role', '2')
          ->where('users.deleted_at',   NULL)
          ->orderBy('users.created_at', 'desc')
          ->get();
          

        $members =   User::where('role', '4')
        ->where('deleted_at',  NULL)
        ->orderBy('created_at', 'desc')
        ->get();
        //fmcg
        $fmcg = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
          ->where('users.role', '33')
          ->where('users.deleted_at',    NULL)
          ->orderBy('users.created_at', 'desc')
          ->get();  
        //sellers
        $merchants = User::where('role', '3')
        ->where('deleted_at',   NULL)
        ->orderBy('created_at', 'desc')
        ->get();
        \LogActivity::addToLog('SuperAdmin userList');
        return view('company.users_list', compact('coop', 'members', 'merchants', 'fmcg'));
 
       }

    else { return Redirect::to('/login');}
   
    }
  //edit 
  public function user_edit(Request $request, $id){
    if( Auth::user()->role  == '1'){
        $users = User::find($id);
        return view('company.user_edit', compact('users')); 
     }
      else { return Redirect::to('/login');
    }
}

//update 
public function user_update(Request $request, $id)
{
  $this->validate($request, [
    'fname'  => 'max:255',  
     'lname'  =>  'max:255',
     'coopname'    => 'max:255',
     'address' => 'max:255',
     'location' =>  'max:255',
     'bank'     =>  'max:255',
     'account_name' => 'max:255',
     'account_number' =>  'max:255',
    ]);

    $user = User::find($id);
    $user->fname = $request->fname;
    $user->lname = $request->lname;
    $user->coopname = $request->coopname;
    $user->address = $request->address;
    $user->location = $request->location;
    $user->bank = $request->bank;
    $user->account_name = $request->account_name;
    $user->account_number = $request->account_number;
    $user->update();
    $data = 'Update successful for ' .$user->fname. '';
    \LogActivity::addToLog('Update');
    return redirect()->back()->with('status',  $data);
}

public function resetUserPassword(Request $request, $id){
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $randomString = '';
  $num = 8;
  for ($a = 0; $a < $num; $a++) {
    $index = rand(0, strlen($characters) - 1);
    $randomString .= $characters[$index];
  }
  $tempoaryPassword = str_shuffle($randomString);
  $user = User::find($id);
  $user->password = Hash::make($tempoaryPassword);
  $user->password_reset_at = Carbon::now();
  $user->update();
  $msg = 'Password reset was successful!. A login code as been sent to:-> ' .$user->email ;
  
  $name =  \DB::table('users')->where('id', $id)->get('fname') ; 
  $username = Arr::pluck($name, 'fname'); // 
  $get_name = implode(" ",$username);

  $userEmail = \DB::table('users')->where('id', $id)->get('email') ; 
  $getEmail= Arr::pluck($userEmail, 'email'); // 
  $email = implode(" ",$getEmail);

  $data = array(
    'name'     => $get_name,
    'password' => $tempoaryPassword,        
    );
    //dd($data);
     Mail::to($email)->send(new PasswordResetEmail($data)); 
  \LogActivity::addToLog('Reset password'); 
  return redirect()->back()->with('status',  $msg);
}

 public function transactions(Request $request){
      if( Auth::user()->role_name  == 'superadmin'){
       //view all transactions by cooperatives
          $transactions = Transaction::join('users', 'users.id', '=', 'transactions.user_id')
                        ->leftjoin('order_items', 'order_items.order_id', '=', 'transactions.order_id')
                        ->where('users.role', '2')
                        ->orderBy('date', 'desc')
                        ->paginate( $request->get('per_page', 5));
                       //->get(['vouchers.*', 'users.*']);
        // }
        \LogActivity::addToLog('Transaction details');
       return view('company.transactions', compact('transactions'));

       }
    else { return Redirect::to('/login');
    }
   
  }


 public function about(Request $request){
  
      if( Auth::user()->role_name  == 'superadmin'){
        $about = About::all();
        return view('company.add_about', compact('about'));
      }

    else { return Redirect::to('/login');
    }
  }

  //edit 
 public function about_edit(Request $request, $id){
        if( Auth::user()->role  == '1'){
            $about = About::find($id);
            return view('company.about_edit', compact('about')); 
         }
          else { return Redirect::to('/login');
    }
    }

    //update 
public function about_update(Request $request, $id)
    {
        $about = About::find($id);
        $about->about = $request->input('about');
        $about->our_story = $request->input('our_story');
        $about->update();
        \LogActivity::addToLog('Update aboutUs');
        return redirect()->back()->with('status','About page updated');
    }

public function privacy(Request $request){
  
      if( Auth::user()->role_name  == 'superadmin'){
        $about = Privacy::all();
        return view('company.add_privacy_policy', compact('about'));
      }

    else { return Redirect::to('/login');
    }
  }

  //edit 
 public function privacy_edit(Request $request, $id){
        if( Auth::user()->role  == '1'){
            $about = Privacy::find($id);
            return view('company.privacy_edit', compact('about')); 
         }
          else { return Redirect::to('/login');
    }
    }

    //update 
    public function privacy_update(Request $request, $id)
    {
        $about = Privacy::find($id);
        $about->privacy_policy = $request->input('privacy');
        $about->update();
        \LogActivity::addToLog('Update privacyPolicy');
        return redirect()->back()->with('status','Privacy page updated');
    }


    public function refund(Request $request){
  
      if( Auth::user()->role_name  == 'superadmin'){
        $about = ReturnRefund::all();
        return view('company.add_refund_and_return_policy', compact('about'));
      }

    else { return Redirect::to('/login');
    }
  }

  //edit 
 public function refund_edit(Request $request, $id){
        if( Auth::user()->role  == '1'){
            $about = ReturnRefund::find($id);
            return view('company.refund_edit', compact('about')); 
         }
          else { return Redirect::to('/login');
    }
    }

    //update 
    public function refund_update(Request $request, $id)
    {
        $about = ReturnRefund::find($id);
        $about->return_policy = $request->input('return');
        $about->update();
        \LogActivity::addToLog('Update refundPolicy');
        return redirect()->back()->with('status','Reurn & Refund page updated');
    }


public function tandc(Request $request){
  
      if( Auth::user()->role_name  == 'superadmin'){
        $about = Terms::all();
        return view('company.add_terms_and_condition', compact('about'));
      }

    else { return Redirect::to('/login');
    }
  }

  //edit 
 public function tandc_edit(Request $request, $id){
        if( Auth::user()->role  == '1'){
            $about = Terms::find($id);
            return view('company.terms_edit', compact('about')); 
         }
          else { return Redirect::to('/login');
    }
    }

    //update 
    public function tandc_update(Request $request, $id)
    {
        $about = Terms::find($id);
        $about->terms_c = $request->input('terms_c');
        $about->update();
        \LogActivity::addToLog('Update TandC');
        return redirect()->back()->with('status','T & C page updated');
    }


     public function removed_product(Request $request){
   
      if( Auth::user()->role_name  == 'superadmin'){
        $products = User::join('products', 'products.seller_id', '=', 'users.id')
                         ->where('products.prod_status', 'remove')
                        ->paginate( $request->get('per_page', 4));
                        \LogActivity::addToLog('Remove product');
        return view('company.removed_product', compact('products'));
      }

    else { return Redirect::to('/login');
    }
  }

  public function deleteUser(Request $request, $id )
  {
      $user = User::where('id', $id)->delete();
      \LogActivity::addToLog('SuperAdmin remove user');
      return redirect()->back()->with('success', 'User Removed Successfully!');
  }
  public function addNewAdmin(){
    return view('company.add-new-admin');
  }

  
  public function showSetPassword(Request $request){
    // $email = $email;
    return view('set-password');
}

public function setPassword(Request $request) {
    $validatedData = $request->validate([
        'email'        =>'required|email',
        'new-password' => 'required|string|min:8|confirmed',
    ]); 

    //Set Password   bcrypt();
    $user = User::where('email', $request->email)
    ->update(['password' => Hash::make($request->get('new-password'))]);
    if($user){
        \LogActivity::addToLog('Set password'); 
        return redirect('login')->with("success","You have successfully set your password!");
    }else{
        return redirect()->back()->with("success","Password Not Set!");
    } 
} 
}//class