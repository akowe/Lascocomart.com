<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

use App\Models\User;
use App\Models\Voucher;
use App\Models\Wallet;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingDetail;
use App\Models\Transaction;
use App\Mail\PaymentEmail;
use App\Mail\ConfirmPaymentEmail;
use App\Mail\ConfirmOrderEmail;
use App\Mail\SalesEmail;
use App\Mail\OrderEmail;
use App\Notifications\NewCardPayment;
use Notification;


use Auth;
use Validator;
use Session;
use Paystack;
use Mail;

class BankTransferController extends Controller
{
    //
    public function __construct()
    {
         // $this->middleware('auth');
        $this->middleware(['auth','verified']);
    }

    public function bankPayment(Request $request){
        session_start();
        $user_id= Auth::user()->id;
        $code = Auth::user()->code;

        $select_bank = \DB::table('banks')->orderBy('name')->get('*');
        $all_orders = User::join('orders', 'orders.user_id', '=', 'users.id')
        ->where('orders.status', 'approved') 
        ->where('users.code', $code) 
        ->get('orders.*');  

        $orders = User::join('orders', 'orders.user_id', '=', 'users.id')
        ->where('orders.status', 'approved') 
        ->where('users.code', $code) 
        ->get(['orders.id']);  
       // dd($all_orders);
        return view('cooperative.bank-payment', compact('select_bank', 'all_orders', 'orders'));
    }

public function banTransferPayment(Request $request, $reference, $order_id, $order_amount){
    $ids = $request->order_id;
    $asset_requst = Order::whereIn('id', explode(",", $ids))->get();
    $order_id = Arr::pluck($asset_requst, 'id');

    foreach($order_id as $item){
        \DB::table('orders')->where('id',$item )->update([
            'status'=> 'paid', 
            'pay_status' =>'success',
            'pay_type' =>'Bank Transfer',
            'admin_settlement_msg' => 'paid'
        ]);
        $tranx = new Transaction();
        $tranx->user_id     = Auth::user()->id;
        $tranx->paystack_ref= $reference;
        $tranx->order_id    =  $item;
        $tranx->tran_amount = $order_amount;
        $tranx->pay_status  =  'success';
        $tranx->save();

        $order_number = Order::where('id', $order_id)->get('order_number');
        $orderItems = OrderItem::where('order_id', $order_id)->get();
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
    $superadmin = User::where('role_name', '=', 'superadmin')->get();
    $get_superadmin_id =Arr::pluck($superadmin, 'id');
    $superadmin_id = implode('', $get_superadmin_id);
    Notification::send($superadmin, $notification);

    return redirect('cooperative')->with('status', 'Payment successful');
}


}