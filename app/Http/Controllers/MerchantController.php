<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Arr;
use App\Notifications\NewProduct;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Wallet;
use App\Models\Categories;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
// use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

use Auth;
use Validator;
use Session;
use Storage;
use Mail;
use Notification;

class MerchantController extends Controller
{
    //
     public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware('merchant');
    }


    public function index(Request $request)
    {
   if( Auth::user()->role_name  == 'merchant'){

        // check if user has field his/her profile
        $user=Auth::user();
        $address = $user->address;
        $phone = $user->phone;
          if($address == '' && $phone =='')
          {
             Session::flash('profile', ' You are yet to update your profile! <br> Kindly navigate to profile page.'); 
                Session::flash('alert-class', 'alert-success'); 
          }
           
        $code = Auth::user()->code; 
         $id = Auth::user()->id; 
         //view all products by a merchant / seller
          $products = User::join('products', 'products.seller_id', '=', 'users.id')
          ->where('products.prod_status', '!=', 'remove')
          // ->where('products.prod_status', 'pending')
          // ->where('products.prod_status', 'approve')
          ->where('users.id', $id)->get();
    
        $count_product = User::join('products', 'products.seller_id', '=', 'users.id')
        // ->where('products.prod_status', 'pending')
        ->where('products.prod_status', 'approve')
          ->where('users.id', $id);

         //count number of order that has been paid
         $count_orders = Product::join('order_items', 'order_items.product_id', '=', 'products.id')
         ->join('orders', 'orders.id', '=', 'order_items.order_id')
         ->where('orders.status', 'paid')
          ->where('products.seller_id', $id);

          $approveOrders = Product::join('order_items', 'order_items.product_id', '=', 'products.id')
          ->join('orders', 'orders.id', '=', 'order_items.order_id')
          ->where('orders.status', 'approved')
           ->where('products.seller_id', $id);

          $sales = Product::join('order_items', 'order_items.product_id', '=', 'products.id')
          ->join('orders', 'orders.id', '=', 'order_items.order_id')
           ->where('orders.status', 'paid')
          ->where('products.seller_id', $id);  

          $orders = Product::join('order_items', 'order_items.product_id', '=', 'products.id')
          ->join('orders', 'orders.id', '=', 'order_items.order_id')
          ->join('users', 'users.id', '=', 'orders.user_id')
           ->where('orders.status', 'approved')
          //  ->where('orders.status', '!=', 'cancel')
          //  ->where('orders.status', '!=', 'awaits approval')
          //  ->where('orders.status', 'paid')
           ->where('products.seller_id', $id)
         
          ->get(['users.fname','orders.*', 'order_items.*', 'products.prod_name','products.seller_price']);  
            
          $credit = Wallet::join('users', 'users.id', '=', 'wallets.user_id')
           ->where('users.id', $id)->get('credit');
           \LogActivity::addToLog('Merchant dashboard');
       return view('merchants.merchant', compact('approveOrders', 'products', 'count_product', 'count_orders', 'sales', 'credit', 'orders'));

       }
    else { return Redirect::to('/login');
    
        }
    }

    public function allProducts(Request $request)
    {
    if( Auth::user()->role_name  == 'merchant'){
      $id = Auth::user()->id; 

      $products = User::join('products', 'products.seller_id', '=', 'users.id')
       ->where('products.prod_status', '!=', 'remove')
       ->where('users.id', $id)->get();

      $count_product = User::join('products', 'products.seller_id', '=', 'users.id')
      ->where('products.prod_status', 'approve')
      ->where('users.id', $id);
      \LogActivity::addToLog('Products');
      return view('merchants.all-products', compact('products', 'count_product'));
        }
      else { return Redirect::to('/login');
        }
    }
    
    //add new products
    public function product(Request $request)
    {
    if( Auth::user()->role_name  == 'merchant'){
        $categories = Categories::all(); 
        \LogActivity::addToLog('Product');
        return view('merchants.add_new_product', compact('categories'));
        }
      else { return Redirect::to('/login');
        }
    }

      public function store(Request $request)
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

            // add company and coperative percentage
            $company_percentage = $request->price *  5 / 100;
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
            \LogActivity::addToLog('Add product');
            return redirect('merchant')->with('status', 'New product added successfully');   
    }   
 
    public function remove_product(Request $request){
        $code = Auth::user()->code; 
         $seller_id = Auth::user()->id;
            $id  = $request->id;
            $input = 'remove';
             Product::where('id', $id)->update(['prod_status' => $input]);

            Session::flash('remove', ' Product Removed Successful!'); 
            Session::flash('alert-class', 'alert-success'); 
        \LogActivity::addToLog('Remove product');
        return redirect()->back()->with('success', 'Product Removed Successful!');
    }


    public function sales_preview(Request $request)
    {
         if( Auth::user()->role_name  == 'merchant'){
               $id = Auth::user()->id; //  

          $sales = Product::join('order_items', 'order_items.product_id', '=', 'products.id')
          ->join('orders', 'orders.id', '=', 'order_items.order_id')
          ->where('orders.status', 'paid')
          ->where('products.seller_id', $id) 
          ->orderBy('orders.date', 'desc')  
          ->paginate( $request->get('per_page', 5));  
          \LogActivity::addToLog('Sales');
       return view('merchants.sales_preview', compact('sales'));

         }
         else{
            return Redirect::to('/login');
         } 
    }
 
    public function invoice(Request $request, $order_number )
    { 
        if( Auth::user()->role_name  == 'merchant'){
            $code = Auth::user()->code; //
            $id = Auth::user()->id;
            $item = Order::join('users', 'users.id', '=', 'orders.user_id')// count orders from members
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            // ->join('shipping_details', 'shipping_details.shipping_id', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            // ->join('vouchers', 'vouchers.user_id', '=', 'users.id')
            ->where('products.seller_id', $id) 
            ->where('orders.order_number', $order_number)
            ->get([ 'orders.*', 'users.*', 'order_items.*', 'products.*'])->first();
         
          $orders = Order::join('order_items', 'order_items.order_id', '=', 'orders.id')
          ->join('products', 'products.id', '=', 'order_items.product_id')
          ->where('products.seller_id', $id) 
         ->where('orders.order_number', $order_number)
         ->get(['orders.*',  'order_items.*',  'products.*']); 
         \LogActivity::addToLog('Invoice');
          return view('invoice', compact('item', 'orders'));
                }

          else { return Redirect::to('/login');
    
          }
      }
    
    
    
   

}//class