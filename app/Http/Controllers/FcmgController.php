<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Order;
use App\Models\ShippingDetail;
use App\Models\Transaction;
use App\Models\Categories;
use App\Models\FcmgProduct;
use App\Mail\SendMail;

use Auth;
use Validator;
use Session;
use Paystack;
use Storage;
use Mail;


class FcmgController extends Controller
{
    //
      public function __construct()
    {
         // $this->middleware('auth');
          $this->middleware(['auth','verified']);
        $this->middleware('fcmg');
        
    }


    public function index (Request $request)
    {
    if( Auth::user()->role_name  == 'fcmg'){
        // check if user has field his/her profile
        $user=Auth::user();
        $address = $user->address;
        $phone = $user->phone;
          if($address == '' && $phone =='')
          {
             Session::flash('profile', ' You are yet to update your profile! <br> Kindly navigate to profile page.'); 
                Session::flash('alert-class', 'alert-success'); 
          }
          
     // count all members belonging to a fcmg
        $code = Auth::user()->code; // get the code for the logedin fcmg
        $id = Auth::user()->id; //
      // select all user except the current login
        $members = User::all()->except(Auth::id())
                    ->where('code', $code);
                   
// count products from members
        $count_product = User::join('fcmg_products', 'fcmg_products.seller_id', '=', 'users.id')
                          // ->where('fcmg_products.prod_status', 'pending')
                         ->where('fcmg_products.prod_status', 'approve')
                          ->where('users.id', $id);
                          
        $fcmgproduct = User::join('fcmg_products', 'fcmg_products.seller_id', '=', 'users.id')
                         ->where('fcmg_products.prod_status', 'pending')
                         ->where('fcmg_products.prod_status', 'approve')
                         ->orwhere('users.id', $id)

                        ->paginate( $request->get('per_page', 10));
                       //->get(['products.*', 'users.*']);
                        // count orders from members
        $count_orders = User::join('orders', 'orders.user_id', '=', 'users.id')
                             ->where('orders.status', 'confirmed')
                            ->where('users.code', $code);
                        
                         //select all orders from members
        $orders = User::join('orders', 'orders.user_id', '=', 'users.id')
                        ->where('orders.status', 'confirmed')
                         ->where('orders.status', 'paid')
                          ->orwhere('users.code', $code)// also see all orders of members
                        ->orderBy('date', 'desc')
                       
                         ->paginate( $request->get('per_page', 5));
                         // ->first();
                                              
                       //->get(['orders.*', 'users.*', 'order_items.*']);

        // count credit from members
        $credit = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
                        ->where('users.code', $code)
                       ->get('credit');


        // sum total order paid for by fcmg for his from members
        $sales = Transaction::join('users', 'users.id', '=', 'transactions.user_id')
                        ->where('users.code', $code)
                       ->get('tran_amount');

        //sum all order for payment
         $all_orders = User::join('orders', 'orders.user_id', '=', 'users.id')
                        ->where('orders.status', 'confirmed') 
                         ->where('users.code', $code) 

                        ->get('orders.total');  

    // for bulk payment by fcmg
         $all_orders_id = User::join('orders', 'orders.user_id', '=', 'users.id')
                        ->where('orders.status', 'confirmed') 
                         ->where('users.code', $code) 
                        ->get('orders.id');                             
        return view('fcmg.fcmg', compact('members', 'orders', 'credit', 'fcmgproduct', 'count_product', 'count_orders', 'sales', 'all_orders', 'all_orders_id'));
    }
    else { return Redirect::to('/login');}
   
    }


    public function fcmgmembers(Request $request )
    {
    if( Auth::user()->role_name  == 'fcmg'){

        $code = Auth::user()->code; //
        // Product::paginate( $request->get('per_page', 4));
       
         //view all members aslo see credit of only his members
          $credit = Voucher::join('users', 'users.id', '=', 'vouchers.user_id')
                        ->where('users.code', $code)
                        ->paginate( $request->get('per_page', 10));
                       //->get(['vouchers.*', 'users.*']);
        // }
       return view('fcmg.all_members', compact('credit'));

       }
    else { return Redirect::to('/login');
    
        }
   
    }
    //

   
    public function invoice(Request $request, $order_number )
    {
     if( Auth::user()->role_name  == 'fcmg'){
         $code = Auth::user()->code; //
           $item = Order::join('users', 'users.id', '=', 'orders.user_id')
                          ->leftjoin('order_items', 'order_items.order_id', '=', 'orders.id')
                           ->join('shipping_details', 'shipping_details.shipping_id', '=', 'orders.id')
                             ->join('fcmgproducts', 'fcmgproducts.id', '=', 'order_items.product_id')
                              ->join('vouchers', 'vouchers.user_id', '=', 'users.id')
        
                        ->where('users.code', $code)
                        ->where('order_number', $order_number)
                        ->get(['vouchers.*', 'orders.*', 'users.*', 'order_items.*', 'shipping_details.*', 'fcmgproducts.*'])->first();

         $orders = Order::join('users', 'users.id', '=', 'orders.user_id')
                          ->leftjoin('order_items', 'order_items.order_id', '=', 'orders.id')
                           ->join('shipping_details', 'shipping_details.shipping_id', '=', 'orders.id')
                           ->join('fcmgproducts', 'fcmgproducts.id', '=', 'order_items.product_id')
                           ->join('vouchers', 'vouchers.user_id', '=', 'users.id')
                           // ->where('order_items.status', 'confirmed')
                           // ->orwhere('order_items.status', 'paid')

                          ->where('users.code', $code)// also see all orders of members
                          ->where('order_number', $order_number)
                          ->get(['orders.*', 'users.*', 'order_items.*', 'shipping_details.*', 'fcmgproducts.*', 'vouchers.*']);              

    return view('invoice', compact('item', 'orders'));
           }

    else { return Redirect::to('/login');
    
        }
                     
    }

        //add new fcmgproducts
     public function fcmgproduct(Request $request)
    {
    if( Auth::user()->role_name  == 'fcmg'){
        $categories = Categories::all(); 

        return view('fcmg.add_new_product', compact('categories'));
        }
      else { return Redirect::to('/login');
        }
   
    
    }
    
    
    public function fcmgstore(Request $request)
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
             $image->move(public_path('images'),$imageName);
             $image_path = "/images/" . $imageName; 

             }

            else {
            $image_path = "";
             }


           $img1= $request->file('img1');
            if(isset($img1))
            {
            $img1Name =  rand(1000000000, 9999999999).'.jpeg';
             $img1->move(public_path('images'),$img1Name);
             $img1_path = "/images/" . $img1Name; 

             }

            else {
            $img1_path = "";
             }


              $img2= $request->file('img2');
            if(isset($img2))
            {
            $img2Name = rand(1000000000, 9999999999).'.jpeg';
             $img2->move(public_path('images'),$img2Name);
             $img2_path = "/images/" . $img2Name; 

             }

            else {
            $img2_path = "";
             }



            $img3= $request->file('img3');
            if(isset($img3))
            {
            $img3Name =  rand(1000000000, 9999999999).'.jpeg';
             $img3->move(public_path('images'),$img3Name);
             $img3_path = "/images/" . $img3Name; 

             }

            else {
            $img3_path = "";
             }


            $img4= $request->file('img4');
            if(isset($img4))
            {
            $img4Name = rand(1000000000, 9999999999).'.jpeg';
             $img4->move(public_path('images'),$img4Name);
             $img4_path = "/images/" . $img4Name; 

             }

            else {
            $img4_path = "";
             }

              //    $img2= $request->file('img2');
           //  if(isset($img2))
           //  {
           //  $img2Name = time().'_'.$img2->getClientOriginalName();
           //   $img2->move(public_path('coopmart/images'),$img2Name);
           //   $img2_path = "/images/" . $img2Name; 

           //   }


            // add company and coperative percentage

            //$cop = $request->price * 5 / 100; //cooperative percentage

            $company_percentage = $request->price *  7 / 100;// coopmart percentage
        
            $price = $request->price  + $company_percentage;

           $fcmgproduct = new FcmgProduct;
           $fcmgproduct->cat_id    = $request->cat_id;
           $fcmgproduct->prod_name  = $request->prod_name;
           $fcmgproduct->quantity   = $request->quantity;
           $fcmgproduct->prod_brand = $request->prod_brand;
           $fcmgproduct->old_price  = $request->old_price;
           $fcmgproduct->seller_price = $request->price;
           $fcmgproduct->price      = $price;
           $fcmgproduct->description= $request->description;
           $fcmgproduct->image      = $image_path;
           $fcmgproduct->img1       = $img1_path;
           $fcmgproduct->img2       = $img2_path;
           $fcmgproduct->img3       = $img3_path;
           $fcmgproduct->img4       = $img4_path;
           $fcmgproduct->seller_id  = $user_id;
           $fcmgproduct->seller_role  = $user_role;
           $fcmgproduct->prod_status = 'approve';
           $fcmgproduct->save();
         
           // send email notification to coopmart for approval
                   $data = array(
                    'name'      =>  'coopmart',
                    'message'   =>   'approve'
                );

             Mail::to('info@lascocomart.com')->send(new SendMail($data));

            
            return redirect('fcmg')->with('status', 'New product added successfully');   
               
    }   

    public function fcmgremove_product(Request $request)
    {

        $code = Auth::user()->code; //
         $seller_id = Auth::user()->id; //
    

        if(null !== $_POST['submit'])
        {
            $id  = $request->input('id');
             $input  = $request->input('prod_status');

             //update table
             FcmgProduct::where('id', $id)
                    ->update([
                    'prod_status' => $input
                ]);

            Session::flash('remove', ' Product Removed Successful!'); 
            Session::flash('alert-class', 'alert-success'); 
          
           
        }

        return redirect()->back()->with('success', 'Product Removed Successful!');
    }


    public function fcmgsales_preview(Request $request)
    {
         if( Auth::user()->role_name  == 'merchant'){
               $id = Auth::user()->id; //

          $sales = Product::join('order_items', 'order_items.product_id', '=', 'fcmgproducts.id')
                           ->join('orders', 'orders.id', '=', 'order_items.order_id')
                          ->where('orders.status', 'Paid')
                           ->where('fcmgproducts.seller_id', $id) 
                           ->orderBy('date', 'desc')  
                            ->paginate( $request->get('per_page', 5));  

       return view('fcmg.sales_preview', compact('sales'));

         }
         else{
            return Redirect::to('/login');
         } 
    }



   
}// class
