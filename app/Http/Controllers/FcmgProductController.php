<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ReviewController;

use Illuminate\Http\Request;
use App\Models\FcmgProduct;
use App\Models\Categories;
use App\Models\Voucher;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Mail\LowStockEmail;
use App\Models\About;
use App\Models\Privacy;
use App\Models\ReturnRefund;
use App\Models\Terms;
use App\Models\Review;

use Session;
use Validator;
use Auth;
use Mail;

use Carbon\Carbon;

class FcmgProductController extends Controller
{

      public function __construct()
    {
         // $this->middleware('auth');
       
    }
    //
      public function index( Request $request)
    {

        $products = Product::where('prod_status', 'approve')
                    ->orderBy('created_at', 'desc')
                    ->paginate($request->get('per_page', 16));

        $seller = Arr::pluck($fcmgproducts, 'seller_id');
        $get_seller_id = implode(" ",$seller);

        //get sellers details
        $email          = User::where('id', $get_seller_id)->get('email');
        $seller_details = User::where('id', $get_seller_id)->get();

        $seller_name    = Arr::pluck($seller_details, 'fname');
        $name           = implode(" ",$seller_name);
       

          //send email notification of low stock
        
        foreach($products   as $key => $prod){
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
              
        return view('products', compact('products'));
    }
  
    /**
     * my code on Method
     *
     * @return response()
     */
    public function fcmgcart()
    {
        return view('cart');
    }
  

    //Search by product, join product and category table
    public function fcmgcategory(Request $request){

        //$products = Product::paginate( $request->get('per_page', 4));
        // ->paginate( $request->get('per_page', 1));
       
        // search  from select option tag
        if ( $search = $request->input('category')) {

             $products =Product::join('categories', 'categories.cat_id', '=', 'products.cat_id')
                   
                    ->where('categories.cat_name', 'LIKE', "%{$search}%") 
                   ->get(['products.*', 'categories.cat_name']);
                return view('products', compact('products'));
            }
        }

   
    public function fcmgaddToCart($id)
    {
        $product = Product::findOrFail($id);
          
        $cart = session()->get('cart', []);
  
        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "prod_name" => $product->prod_name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image,
                "id" => $product->id,
                "seller_id" => $product->seller_id,

            ];
        }
          
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }
  
   

    public function fcmgupdate(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Cart updated successfully');
        }
    }
  
   

    public function fcmgremove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Product removed successfully');

        }
    }

    
    public function fcmgcheckout(Request $request){

         if( Auth::user()){
     
        //get voucher from input
        $id = Auth::user()->id;// get user id for the login member

          $cart = session()->get('cart');
          $cart[$request->id]["quantity"] = $request->quantity;
          $cart[$request->id]["price"] = $request->price;
           $cart[$request->id]["seller_id"] = $request->seller_id;

          $totalAmount = 0;

        foreach ($cart as $item) {
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

        return view('checkout', compact('voucher'));
    }

        else { return Redirect::to('/login');}

        }

      

  public function fcmgaddToCartPreview($id)
    {
        $product = Product::findOrFail($id);
          
        $cart = session()->get('cart', []);
  
        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "prod_name" => $product->prod_name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image,
                "id" => $product->id

            ];
        }
          
        session()->put('cart', $cart);
        return redirect()->route('cart')->with('success', 'Product added to cart successfully!');
    }


public function fcmgpreview(Request $request, $prod_name)
{
   $products = Product::where('prod_name', $prod_name)->get('*');
     return view('preview', compact('products'));
     
}

 public function about_us(Request $request){
        $about = About::all();
        return view('about', compact('about'));
  }

public function privacy_policy(Request $request){
        $about = Privacy::all();
        return view('privacy_policy', compact('about'));
  }

public function return_policy(Request $request){
        $about = ReturnRefund::all();
        return view('refund_and_return_policy', compact('about'));
  }

  public function terms(Request $request){
        $about = Terms::all();
        return view('terms_and_condition', compact('about'));
  }

 
}//class
