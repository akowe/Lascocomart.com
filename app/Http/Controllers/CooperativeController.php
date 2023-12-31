<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Wallet;
use App\Models\Order;
use App\Models\OrderItem;
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
        $address        = $user->address;
        $phone          = $user->phone;
        $account_number = $user->account_number;
        $account_name   = $user->account_name;
          if($account_number =='' && $account_name =='')
          {
            Session::flash('status', ' You are yet to complete your profile!'); 
            Session::flash('alert-class', 'alert-success'); 
            return Redirect::to('/profile');     
          }
        $code = Auth::user()->code; 
        $id = Auth::user()->id; //
      // select all user except the current login
        $members = User::all()->except(Auth::id())->where('code', $code);        
        // count products 
        $count_product = User::join('products', 'products.seller_id', '=', 'users.id')
        ->where('products.prod_status', 'approve')
        ->where('users.id', $id);
        // count approved orders 
        $count_orders = User::join('orders', 'orders.user_id', '=', 'users.id')
        ->where('orders.status','!=', 'cancel')
        ->where('users.code', $code);

        $orders = User::join('orders', 'orders.user_id', '=', 'users.id')
         ->where('users.code', $code)
         ->where('orders.status', '!=', 'cancel')
         ->orderBy('orders.date', 'desc')
         ->get();
         
                        
        $allocated_funds = User::join('credit_limits', 'credit_limits.user_id', '=', 'users.id')
        ->where('users.id', $id)
        ->paginate( $request->get('per_page', 5));
        // count credit from members
        $credit = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
        ->where('users.id', $id)
        ->get('credit');
        // sum total order paid for by cooperative for his from members
        $sales = Transaction::Join('orders', 'orders.id', '=', 'transactions.order_id')
        ->where('transactions.user_id', $id)
        ->get('grandtotal');
        //sum all order for payment
        $sumApproveOrder = User::join('orders', 'orders.user_id', '=', 'users.id')
        ->where('orders.status', 'approved') 
        ->where('users.code', $code) 
        ->get('orders.*');  
        
        // for bulk payment by cooperative
        $all_orders_id = User::join('orders', 'orders.user_id', '=', 'users.id')
        ->where('orders.status', 'approve') 
        ->where('users.code', $code) 
        ->get('orders.id');   
        \LogActivity::addToLog('Admin dashboard');                  
        return view('cooperative.cooperative', compact('members', 'orders',  'credit', 'count_product', 'count_orders', 'sales', 'allocated_funds', 'sumApproveOrder', 'all_orders_id'));
    
    }
    else { return Redirect::to('/login');}
    }

    public function adminOrderHistory(Request $request){
        $id = Auth::user()->id;
        $code = Auth::user()->code;
        $count_orders = User::join('orders', 'orders.user_id', '=', 'users.id')
        ->where('orders.status','!=', 'cancel')
        ->where('users.code', $code);

        $orders = User::join('orders', 'orders.user_id', '=', 'users.id')
         ->where('users.code', $code)
         ->where('orders.status', '!=', 'cancel')
         ->orderBy('orders.date', 'desc')
         ->get(); 
        // for bulk payment by cooperative
        $sumApproveOrder = User::join('orders', 'orders.user_id', '=', 'users.id')
        ->where('orders.status', 'approved') 
        ->where('users.code', $code) 
        ->get('orders.*'); 
        $credit = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
        ->where('users.id', $id)
        ->get('credit');  
        \LogActivity::addToLog('Admin order history');
        return view('cooperative.order-history', compact('credit', 'orders', 'sumApproveOrder'));
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
        $credit = $request->credit;
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
        $orders = User::join('orders', 'orders.user_id', '=', 'users.id')
        ->where('users.code', $code)
        ->where('orders.status', '=', 'cancel')
        ->orderBy('orders.date', 'desc')
        ->paginate( $request->get('per_page', 5));
        \LogActivity::addToLog('Admin view cancel order');
        return view('cooperative.canceled-orders', compact('orders'));
    }
    

    public function adminProducts(Request $request){
        $id = Auth::user()->id;
        $products = User::join('products', 'products.seller_id', '=', 'users.id')
        ->where('products.prod_status', '!=', 'remove')
        ->where('products.seller_id', $id)
        ->paginate( $request->get('per_page', 10));
        \LogActivity::addToLog('Admin products');
        return view('cooperative.products', compact('products'));

    } 

    public function approveOrder(Request $request, $order_id){
        $id = Auth::user()->id;
        $cooperative = Auth::user()->coopname;
        //$order_id = $request->order_id;
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

        if($getCredit > $grandtotal ){
            $input = 'approved'; 
            $approve = Order::where('id', $order_id)
            ->update([
            'status' => $input,
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

            foreach ($getSellerEmail as $key => $user) {
           
                Mail::to($user->email)->send(new SalesEmail($sellerData)); 
            }

            Mail::to($memberEmail)->send(new OrderApprovedEmail($memberData)); 
         
            Mail::to('info@lascocomart.com')->send(new OrderEmail($data));    
            return redirect()->back()->with('success', 'Approved successful!'); 
           }
        else{
            return redirect()->back()->with('error', 'Your credit is low kindly contact LascocoMart to get funds');   
        }
       
    }

    public function members(Request $request ){
        if( Auth::user()->role_name  == 'cooperative'){
            $id = Auth::user()->id;
            $code = Auth::user()->code;
            $owncredit = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
            ->where('users.id', $id)
        ->get('credit'); 
        
            $credit = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
            ->where('users.code', $code) 
            ->where('users.email_verified_at', '!=','null')
            ->paginate( $request->get('per_page', 10));

            $members = User::all()->except(Auth::id())->where('code', $code);  
            \LogActivity::addToLog('Admin members');
        return view('cooperative.all_members', compact('credit', 'owncredit', 'members'));

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
            // ->join('shipping_details', 'shipping_details.shipping_id', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            // ->join('vouchers', 'vouchers.user_id', '=', 'users.id')
            ->where('users.code', $code)
            ->where('order_number', $order_number)
            ->get(['orders.*', 'users.*', 'order_items.*', 'products.*'])->first();
        
            $orders = Order::join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.order_number', $order_number)
            ->get(['orders.*',  'order_items.*',  'products.*']);              
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

        // fields that are required 
        //|dimensions:max_width=600,max_height=600
         $this->validate($request, [
         'image' => 'required|image|mimes:jpg,png,jpeg|max:300',// maximum is 300kb , 600 x 600 pixel
         'img1' => 'image|mimes:jpg,png,jpeg|max:300',
         'img2' => 'image|mimes:jpg,png,jpeg|max:300',
         'img3' => 'image|mimes:jpg,png,jpeg|max:300',
         'img4' => 'image|mimes:jpg,png,jpeg|max:300',
         'prod_name' => 'required|string|max:100',
         'quantity' => 'required|string|max:100',
         'price' => 'required|string|max:100',
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
            if(isset($img1))
            {
            $img1Name =  rand(1000000000, 9999999999).'.jpeg';
             $img1->move(public_path('assets/products'),$img1Name);
             $img1_path = "/assets/products/" . $img1Name; 

             }

            else {
            $img1_path = "";
             }


              $img2= $request->file('img2');
            if(isset($img2))
            {
            $img2Name = rand(1000000000, 9999999999).'.jpeg';
             $img2->move(public_path('assets/products'),$img2Name);
             $img2_path = "/assets/products/" . $img2Name; 

             }

            else {
            $img2_path = "";
             }



            $img3= $request->file('img3');
            if(isset($img3))
            {
            $img3Name =  rand(1000000000, 9999999999).'.jpeg';
             $img3->move(public_path('assets/products'),$img3Name);
             $img3_path = "/assets/products/" . $img3Name; 

             }

            else {
            $img3_path = "";
             }


            $img4= $request->file('img4');
            if(isset($img4))
            {
            $img4Name = rand(1000000000, 9999999999).'.jpeg';
             $img4->move(public_path('assets/products'),$img4Name);
             $img4_path = "/assets/products/" . $img4Name; 

             }

            else {
            $img4_path = "";
             }

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
           $product->img4       = $img4_path;
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
            return redirect('cooperative')->with('status', 'New product added successfully');   
               
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


    public function coopsales_preview(Request $request)
    {
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

    public function fmcgproductsview(Request $request)
    {
 
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