<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Arr;
use App\Models\Product;
use App\Models\FcmgProduct;
use App\Models\Categories;
use App\Models\Voucher;
use App\Models\Wallet;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\fcmgOrder;
use App\Models\fcmgOrderItem;
use App\Models\ShippingDetail;
use App\Mail\ConfirmOrderEmail;
use App\Mail\SalesEmail;
use App\Mail\OrderEmail;
use App\Mail\AwaitsApprovalEmail;
use App\Notifications\NewOrder;
use Notification;
use App\Models\User;
use Session;
use Validator;
use Auth;
use Mail;

class OrderController extends Controller
{
    //
      public function __construct()
    {
         $this->middleware('auth');  
    }

    public function confirm_order(){
      \LogActivity::addToLog('ConfirmOrder');
        return view('order');
        }


    public function order(Request $request){
        $member= Auth::user()->id;
        $cart = session()->get('cart', []);
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        // generate a pin based on 2 * 7 digits + a random character
        $pin = mt_rand(1000000, 9999999)
            . mt_rand(1000000, 9999999)
            . $characters[rand(0, strlen($characters) - 1)];
        // shuffle pin
        $order_number = str_shuffle($pin);
        $order_status  = 'awaits approval'; 
        $pay_status  = 'pending';
        $ship_address  = $_POST['ship_address'];
        $ship_city     = $_POST['ship_city'];
        $ship_phone    = $_POST['ship_phone'];
        $note          = $_POST['note'];

       if(isset($_POST) && count($_POST) > 0) {
         $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        } 
         $grandtotal =  $totalAmount + $request->delivery;
        $order = new Order();
        $order->user_id     = Auth::user()->id;
        $order->total       = $totalAmount;
        $order->delivery_fee = $request->delivery;
        $order->grandtotal  = $grandtotal;
        $order->order_number = $order_number;
        $order->status       = $order_status;
        $order->pay_status   = $pay_status ;
        $order->save(); 

         $data = [];

        foreach ($cart as $item) {
            $data['items'] = [
                [
                    'prod_name' => $item['prod_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'seller_id'=> $item['seller_id'], 
                    $seller_id = $item['seller_id'], 
                    $price = $item['price'],
                    $product_id = $item['id'],
                    $quantity = $item['quantity'],
                ]
            ];
            $company_percentage = 0;
            $company_percentage +=  $price * 5/ 100;
            $seller_price = 0;
            $seller_price += $price - $company_percentage;

            $orderItem = new OrderItem();
            $orderItem->order_id   = $order->id;
            $orderItem->product_id = $item['id'];
            $orderItem->seller_id = $item['seller_id'];
            $orderItem->order_quantity   = $item['quantity'];
            $orderItem->amount     = $item['price'];
            $orderItem->save();

            //upade seller wallet 
            //Wallet::where('user_id', $seller_id)->increment('credit',$seller_price);
             //for every new order decrease product quantity
            // $stock = \DB::table('products')->where('id', $product_id)->first()->quantity;
            // if($stock > $quantity){
            //   \DB::table('products')->where('id', $product_id)->decrement('quantity',$quantity);
            // }
        }
           $shipDetails = new ShippingDetail();
            $shipDetails->shipping_id = $order->id;
            $shipDetails->ship_address = $ship_address;
            $shipDetails->ship_city = $ship_city;
            $shipDetails->ship_phone = $ship_phone;
            $shipDetails->note = $note;
            $shipDetails->save();
       
        $request->session()->forget('cart');
          $superadmin = User::where('role_name', '=', 'superadmin')->get();
          $get_superadmin_id =Arr::pluck($superadmin, 'id');
          $superadmin_id = implode('', $get_superadmin_id);

          $notification = new NewOrder($order_number);
          Notification::send($superadmin, $notification);
          
          $name =  \DB::table('users')->where('id', $order->user_id)->get('fname') ; 
          $username = Arr::pluck($name, 'fname'); // 
          $get_name = implode(" ",$username);

          $getCode =  \DB::table('users')->where('id', $order->user_id)->get('code') ; 
          $userCoopcode = Arr::pluck($getCode, 'code'); // 
          $code = implode(" ",$userCoopcode);

          $coopEmail = \DB::table('users')->where('code', $code)->where('role', '2')->get('email') ; 
          $getEmail= Arr::pluck($coopEmail, 'email'); // 
          $adminEmail = implode(" ",$getEmail);

          $coopName = \DB::table('users')->where('code', $code)->where('role', '2')->get('coopname') ; 
          $getCoop= Arr::pluck($coopName, 'coopname'); // 
          $cooperative = implode(" ",$getCoop);

          $coopId = User::where('code', $code)->where('role', '=', '2')->get() ; 
          $getId= Arr::pluck($coopId, 'id'); // 
          $adminId = implode('', $getId);
          
          $notification = new NewOrder($order_number);
          Notification::send($coopId, $notification);
           //send emails
            $data = array(
            'cooperative'   => $cooperative,
            'order_number' => $order_number,  
            'amount'       => $grandtotal, // delivery inclusive
            'name'       => $get_name,       
                );

             Mail::to($adminEmail)->send(new AwaitsApprovalEmail($data)); 
             Mail::to('info@lascocomart.com')->send(new OrderEmail($data));              
             \LogActivity::addToLog('New Order');
    return redirect()->route('cart')->with('success', 'Your Order was successfull');
      
  }//isset  

}

 public function invoice(Request $request )
    {

    }

}//class
