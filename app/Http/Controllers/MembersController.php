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

use Auth;
use Validator;
use Session;


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
        // check if user has field his/her profile
        $user=Auth::user();
        $address = $user->address;
        $phone = $user->phone;
          if($address == '' && $phone =='')
          {
             Session::flash('profile', ' You are yet to update your profile! <br> Kindly navigate to profile page.'); 
                Session::flash('alert-class', 'alert-success'); 
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

    else { return Redirect::to('/login');
    
        }
    }
// cancel order where status is confirmed
    public function cancel_order(Request $request)
    {
         $id = Auth::user()->id; //
    
        if(null !== $_POST['submit'])
        {
            $order_number  = $request->input('order_number');
             $input  = $request->input('status');

             //update table
             Order::where('order_number', $order_number)
                    ->update([
                    'status' => $input
                ]);

            // refund credit, charge #200
            $amount  = $request->input('amount');

            $bal = $amount - 200;
            DB::table('vouchers')->where('user_id', $id)->increment('credit',$bal);

            Session::flash('status', ' Your order has been canceled  successful!'); 
            Session::flash('alert-class', 'alert-success'); 
        }
        \LogActivity::addToLog('Cancel order');
        return redirect()->back()->with('success', 'Your order has been canceled successful!');
    }

public function profile(Request $request)
    {

         $id = Auth::user()->id; //
        $users = User::all()->where('id', $id);
        \LogActivity::addToLog('Profile');
        return view('profile', compact('users'));
    } 


    public function update_profile(Request $request){
         $user_id = Auth::user()->id; //
            $this->validate($request, [
            'fname'  => 'max:255',  
             'address'  =>  'max:255',
             'phone'    =>  'max:255',
             'location' =>  'max:255',
             'bank'     => 'max:255',
             'account_name' =>  'max:255',
         
            ]);
        if(null !== $_POST['submit']){
            //update table
            User::where('id', $user_id)
                    ->update([
                    'fname' =>  $request->fname,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'location' =>$request->location,
                    'bank' =>$request->bank,
                    'account_name' =>$request->account_name,
                    'account_number' =>$request->account_number,
                ]);

            Session::flash('profile', ' Profile Update Successful!'); 
            Session::flash('alert-class', 'alert-success'); 
        }
        \LogActivity::addToLog('Update');
        return redirect()->back()->with('status', 'Profile Update Successful!');
    } 


    public function updateProfileImage(Request $request){
        $user_id = Auth::user()->id; //
           $this->validate($request, [
            'image' => 'required|image|mimes:jpg,jpeg,png|max:300',
        
           ]);
       if(null !== $_POST['submit']){
           $image= $request->file('image');
           if(isset($image))
           {
           $imageName =  rand(1000000000, 9999999999).'.jpeg';
            $image->move(public_path('assets/usersProfileImages'),$imageName);
            $profile_image_path = "/assets/usersProfileImages/" . $imageName; 

            }

           else {
           $profile_image_path = "";
            }
           //update table
           
           User::where('id', $user_id)
                   ->update([
                   'profile_img'   =>$profile_image_path
               ]);

           Session::flash('profile', ' Profile Update Successful!'); 
           Session::flash('alert-class', 'alert-success'); 
       }
       \LogActivity::addToLog('Update');
       return redirect()->back()->with('status', 'Profile Update Successful!');
   } 

   public function updateCertificate(Request $request){
    $user_id = Auth::user()->id; //
    $this->validate($request, [
     'cert' => 'required|mimes:jpg,jpeg,png|max:300',
 
    ]);
    if(null !== $_POST['submit']){
    $image= $request->file('cert');
        if(isset($image))
        {
            
            $extension = $request->file('cert')->getClientOriginalExtension(); 
            $fileName= $request->file('cert')->getClientOriginalName(); 
            $imageName =  rand(1000000000, 9999999999).'.'.$extension;
            $image->move(public_path('assets/cooperativeCert'),$imageName);
            $profile_image_path = "/assets/cooperativeCert/" . $imageName; 

        }

    else {
        $path = "";
     }
    //update table
    User::where('id', $user_id)
            ->update([
            'cooperative_cert'   =>$profile_image_path
        ]);

    Session::flash('profile', ' Upload Successful!'); 
    Session::flash('alert-class', 'alert-success'); 
}
\LogActivity::addToLog('Update');
return redirect()->back()->with('status', ' Upload Successful!');
} 
}//class