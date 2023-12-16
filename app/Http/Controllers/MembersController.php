<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingDetail;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use App\Mail\MemberWelcomeEmail;

use Auth;
use Validator;
use Session;
use Mail;


class MembersController extends Controller
{
       public function __construct()
    {
          $this->middleware(['auth', 'verified']);
            $this->middleware('members');
           
    }

public function index(Request $request)
{
     if( Auth::user()->role_name  == 'member'){
        $firstTimeLoggedIn = Auth::user()->last_login;
        if (empty($firstTimeLoggedIn)) {
          $data = 
          array( 
           'name'      => Auth::user()->fname,
           'coopname'  => Auth::user()->coopname,
            'email'     => Auth::user()->email,
        );
          Mail::to(Auth::user()->email)->send(new MemberWelcomeEmail($data));  
          $user = Auth::user();
          $user->last_login = Carbon::now();
          $user->save();
        }
        elseif (!empty($firstTimeLoggedIn)) {
           $user = Auth::user();
           $user->last_login = Carbon::now();
           $user->save();
        }
        // check if user has field his/her profile
        $user=Auth::user();
        $address        = $user->address;
        $phone          = $user->phone;
          if($address == ''  && $phone =='' )
          {
            Session::flash('status', ' You are yet to complete your profile!'); 
            Session::flash('alert-class', 'alert-success'); 
            return Redirect::to('/profile');     
          }

        $id = Auth::user()->id; 
        
        // sumt credit from a member
        $credit = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
                       ->where('users.id', $id)
                       ->get('credit');

                        // count orders from a member
        $count_orders = User::join('orders', 'orders.user_id', '=', 'users.id')
                          ->where('orders.status', 'paid')
                          ->where('orders.pay_status', 'success')
                           ->where('users.id', $id)->count();
                      
                         //select all order history of a member
        $orders = User::join('orders', 'orders.user_id', '=', 'users.id')
                         ->where('orders.status', 'confirmed')
                         ->where('orders.status', 'paid')
                        ->orwhere('users.id', $id)// also see all orders of a member
                        
                        ->orderBy('orders.date', 'desc')
                         ->paginate( $request->get('per_page', 5));
                         \LogActivity::addToLog('Member dashboard');
    return view('members.dashboard', compact('credit', 'count_orders', 'orders'));
    }
     else { return Redirect::to('/login');
    
    }
}

    public function member_invoice(Request $request, $order_number )
    {  
     if( Auth::user()->role_name  == 'member'){
         $id = Auth::user()->id; //
        $item = Order::join('users', 'users.id', '=', 'orders.user_id')// count orders from members
                          ->leftjoin('order_items', 'order_items.order_id', '=', 'orders.id')
                        //    ->join('shipping_details', 'shipping_details.shipping_id', '=', 'orders.id')
                             ->join('products', 'products.id', '=', 'order_items.product_id')
                             // ->join('vouchers', 'vouchers.user_id', '=', 'users.id')
                        ->where('users.id', $id)// also see all orders of members
                        ->where('order_number', $order_number)
                        ->get([ 'orders.*', 'users.*', 'order_items.*',  'products.*'])->first();

        $orders = Order::join('order_items', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'products.id', '=', 'order_items.product_id')
        ->where('orders.order_number', $order_number)
         ->get(['orders.*',  'order_items.*',  'products.*']);  
         \LogActivity::addToLog('Invoice');
    return view('invoice', compact('item', 'orders'));
           }
    else { 
        return Redirect::to('/login');
        }
    }

    public function cancelOrder(Request $request, $id)
    {
         $userId = Auth::user()->id; 
         $status = 'cancel';
        //  Order::where('id', $id)
        //  ->update(['status' => $status]);
         $order = Order::find($id);
         $order->status = $status;
         $order->update();

        // refund credit, charge #200 when order is cancel
        //$amount  = $request->input('amount');
        //$bal = $amount - 200;
        //DB::table('vouchers')->where('user_id', $userId)->increment('credit',$bal);

        Session::flash('status', ' Your order has been canceled  successful!'); 
        Session::flash('alert-class', 'alert-success'); 
        \LogActivity::addToLog('Cancel order');
        return redirect()->back()->with('success', 'Your order has been canceled successful!');
    }


}//class