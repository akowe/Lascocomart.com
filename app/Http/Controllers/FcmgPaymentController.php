<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

use App\Models\User;
use App\Models\Voucher;
use App\Models\FcmgOrder;
use App\Models\FcmgOrderItem;
use App\Models\FcmgProduct;
use App\Models\ShippingDetail;
use App\Models\Transaction;
use App\Mail\PaymentEmail;
use App\Mail\ConfirmPaymentEmail;
use App\Mail\SalesEmail;


use Auth;
use Validator;
use Session;
use Paystack;
use Mail;


class FcmgPaymentController extends Controller
{
    //
/**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */

public function fcmgconfirm_order(){
        return view('fcmgorder');
        }


    public function fcmgorder(Request $request){
        $member= Auth::user()->id;
        
         //get cart items

         $fcmgcart = session()->get('fcmgcart');

         //get others form input
        $order_number  = $_POST['order_number'];
        $order_status  = $_POST['status'];
        $ship_address  = $_POST['ship_address'];
        $ship_city     = $_POST['ship_city'];
        $ship_phone    = $_POST['ship_phone'];
        $note          = $_POST['note'];


       if(isset($_POST) && count($_POST) > 0) {

         $totalAmount = 0;
         

        $fcmgorder = new Order();
        $fcmgorder->user_id     = Auth::user()->id;
        $fcmgorder->total       = $totalAmount;
        $fcmgorder->order_number = $order_number;
        $fcmgorder->status      = $order_status;
        $fcmgorder->save();

         $data = [];

        foreach ($fcmgcart as $item) {
            $data['items'] = [
                [
                    'prod_name' => $item['prod_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'seller_id'=> $item['seller_id'],
                    
            ]
        ];


        $fcmgorderItem = new OrderItem();
             $fcmgorderItem->order_id   = $fcmgorder->id;
             $fcmgorderItem->product_id = $item['id'];
             $fcmgorderItem->seller_id = $item['seller_id'];
             $fcmgorderItem->order_quantity   = $item['quantity'];
             $fcmgorderItem->amount     = $item['price'];
             $fcmgorderItem->save();
        }

           $shipDetails = new ShippingDetail();
            $shipDetails->shipping_id = $fcmgorderItem->order_id;
            $shipDetails->ship_address = $ship_address;
            $shipDetails->ship_city = $ship_city;
            $shipDetails->ship_phone = $ship_phone;
            $shipDetails->note = $note;
            $shipDetails->save();

       
        if($fcmgorderItem){

        //update voucher table with new credit balance
            Voucher::where('user_id', $member)
                    ->update([
                    'credit' => $request->input('bal')
                ]);

      
            //for every new order decrease product quantity
            //  $newOrders = \DB::table('fcmgorder_items')->get();
            
            // foreach ($newOrders as $order){
            //  $stock = \DB::table('products')->where('id', $order->product_id)->first()->quantity;
             
            //      if($stock > $order->order_quantity){
            //          \DB::table('products')->where('id', $order->product_id)->decrement('quantity',$order->order_quantity);
            //         }
            // }

         // $prod_name  = \DB::table('products')->where('id', $order->product_id)->get('prod_name');   
         
               
        }
       
        //remove item from cart
       
        $request->session()->forget('fcmgcart');

           $name =  \DB::table('users')->where('id', $fcmgorder->user_id)->get('fname') ; 
           $username = Arr::pluck($name, 'fname'); // 
           $get_name = implode(" ",$username);

            $email =  \DB::table('users')->where('id', $fcmgorder->user_id)->get('email') ; 
           $useremail = Arr::pluck($email, 'email'); // 
           $get_email = implode(" ",$useremail);


           
         // send email notification to member
            $data = array(
            'name'         => $get_name,
            'order_number' => $order_number,  
            'amount'       => $totalAmount,       
                );

             Mail::to($get_email)->send(new ConfirmOrderEmail($data)); 
              Mail::to('info@lascocomart.com')->send(new OrderEmail($data));  
            


           //   $seller_email =  \DB::table('users')->where('id', $orderItem->seller_id)
           //                      ->get('email') ; 
           //  $semail = Arr::pluck($seller_email, 'email'); // 
           // $get_semail = implode(" ",$semail);

             
             // send email notification to seller
            // $data = array(
            // 'name'         => $get_name,     
            //     );

            //     Mail::to($seller2)->send(new SalesEmail($data));               

    return redirect()->route('fcmgcart')->with('success', 'Your Order was successfull');
      
  }//isset  
}//function
 public function fcmgredirectToGateway()
    {
        try{
            return Paystack::getAuthorizationUrl()->redirectNow();
        }catch(\Exception $e) {
            return Redirect::back()->withMessage(['msg'=>'The paystack token has expired. Please refresh the page and try again.', 'type'=>'error']);
        }        
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function fcmghandleGatewayCallback()
    {
        //callback_url = http://localhost:8000/payment/callback/
        //$paymentDetails = Paystack::getPaymentData();

        //$payment = json_decode(json_encode($paymentDetails),true);
        dd($paymentDetails); // display all payment details

       //get individual payment data from to store in DB
        $status = Arr::pluck($paymentDetails, 'status');// get the status of the payment
        $get_status = implode(" ",$status);

        $reference = Arr::pluck($paymentDetails, 'reference');// paystack reference
        $get_ref = implode(" ",$reference);

        $amount = Arr::pluck($paymentDetails, 'amount'); 
        $get_amount = implode(" ",$amount) / 100;

        $created_at = Arr::pluck($paymentDetails, 'created_at');//
        $get_createDate = implode(" ",$created_at);

        $paid_at = Arr::pluck($paymentDetails, 'paid_at');//
        $get_paidDate = implode(" ",$paid_at);

        //get data from multidimemntion array in the payment data

        //echo implode(" ",$mm);// convert array to string
         $metaData = array_column($paymentDetails, 'metadata');
                 
         $member = Arr::pluck($metaData, 'member_id'); // id of the member that place the order
         $member_id = implode(" ",$member);

         $user = Arr::pluck($metaData, 'user_id'); //id of the cooperative that make the payment
         $user_id = implode(" ",$user);

         $order = Arr::pluck($metaData, 'order_id'); // id of the particular order
         $order_id = json_encode($order, true);
      

         $authorization = array_column($paymentDetails, 'authorization');  

         $authorization_code = Arr::pluck($authorization, 'authorization_code'); // 
         $get_authCode = implode(" ",$authorization_code);

         //print_r($get_amount);
        // if($get_status = 'success'){

        //    // insert to transaction table
        //      $orderItem = new Transaction();
        //      $orderItem->member_id          = $member_id;
        //      $orderItem->user_id            = $user_id;
        //      $orderItem->order_id           = $order_id;
        //      $orderItem->authorization_code = $get_authCode;
        //      $orderItem->paystack_ref       = $get_ref;
        //      $orderItem->tran_amount        =  $get_amount;
        //      $orderItem->pay_status         =  $get_status;
        //      $orderItem->save();
            
        // }

 if($get_status = 'success'){

           // insert to transaction table
             $orderItem = new Transaction();
           
             $orderItem->user_id            = $user_id;
           
             $orderItem->authorization_code = $get_authCode;
             $orderItem->paystack_ref       = $get_ref;
             $orderItem->tran_amount        =  $get_amount;
             $orderItem->pay_status         =  $get_status;
             $orderItem->save();
            
        }

        if($orderItem){

        //update order status to Paid in orderItem table with new credit balance
            Order::where('status', 'confirmed' )
                    ->update([
                    'status' => 'Paid',
                    'pay_status'=>'success',
                    'pay_type'=>'Paystack'
                ]);

// reduce product qunatity if paymemt is successful
                //$newOrders = \DB::table('order_items')->get();

                  $newOrders  = OrderItem::join  ('orders', 'orders.id', '=', 'order_items.order_id') 
                            ->where('orders.status', 'Paid');  

                    
                    foreach ($newOrders as $order){
                     $stock = \DB::table('products')->where('id', $order->product_id)->first()->quantity;
                     
                         if($stock > $order->order_quantity){
                             \DB::table('products')->where('id', $order->product_id)->decrement('quantity',$order->order_quantity);
                           
                            }
                              

                        }//reduce product qty


    
    //get details of coperative
$user_details = \DB::table('users')->where('id', $user_id)->get('*');
// get name of the coop that make payment         
$name = Arr::pluck($user_details, 'coopname');
        $get_name = implode(" ",$name);

$email = Arr::pluck($user_details, 'email');
        $get_email = implode(" ",$email);

$phone = Arr::pluck($user_details, 'phone');
        $get_phone = implode(" ",$phone);


$order_details = \DB::table('order_items')->where('order_id', $order_id)->get('*');

$order_number = Arr::pluck($order_details, 'order_number');
        $get_order_number = implode(" ",$order_number);

$order_quantity = Arr::pluck($order_details, 'order_quantity');
        $get_order_quantity = implode(" ",$order_quantity);


          

                // $seller2 = Product::join('users', 'users.id', '=', 'products.seller_id') 
                //             ->leftJoin('order_Items', 'order_Items.seller_id', '=', 'users.id' )
                //             ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                //             ->where('orders.status', 'Paid')
                //             ->get('users.email');   

                  
 


        }//orderitem

         Session::flash('payment', ' Your payment was successfull!'); 
            Session::flash('alert-class', 'alert-success'); 


            // send email notification to admin
                   $data = array(
                    'name'          =>  $get_name,
                    'order_number'   =>  $get_order_number,  
                     'amount'       =>  $get_amount,
                    
                    
                );

             Mail::to('info@lascocomart.com')->send(new PaymentEmail($data));

             //send  payment confirmation email to the cooperative

             Mail::to($get_email)->send(new ConfirmPaymentEmail($data));


             //send  sales email to seller

          // Mail::to($seller2)->send(new SalesEmail($data));
       
        return redirect()->route('cooperative');

       
      
    }


}//class
