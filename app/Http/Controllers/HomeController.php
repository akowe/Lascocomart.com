<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Voucher;
use App\Models\LogActivity as LogActivityModel;

use Auth;
use Validator;
use Session;
use Mail;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function myTestAddToLog()
    {
        \LogActivity::addToLog('My Testing Add To Log.');
        dd('log insert successfully.');
        return redirect()->back();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function logActivity(Request $request)
    {
        // $logs = \LogActivity::logActivityLists();
        if(Auth::user()->role_name =='superadmin'){
            $logs = LogActivityModel::join('users', 'users.id', '=', 'log_activity.user_id')
            ->orderBy('log_activity.id', 'desc')
            ->get(['log_activity.*', 'users.email', 'users.fname']);
            return view('logActivity',compact('logs'));
        }
        else{
            return Redirect::to('/login');
        }
    }

    public function showChangePassword() {
        return view('auth.passwords.change-password');
    }

    public function changePassword(Request $request) {
        if (!(Hash::check($request->get('old-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your old password is incorrect.");
        }

        if(strcmp($request->get('old-password'), $request->get('new-password')) == 0){
            // Current password and new password same
            return redirect()->back()->with("error","New Password cannot be same as your old password.");
        }

        $validatedData = $request->validate([
            'old-password' => 'required',
            'new-password' => 'required|string|min:8|confirmed',
        ]);

        //Change Password   bcrypt();
        $user = Auth::user();
        $user->password = Hash::make($request->get('new-password'));
        $user->password_reset_at = '';// set to empty.
        $user->save();
        \LogActivity::addToLog('Change password'); 
        return redirect()->back()->with("success","Password successfully changed!");
    } 
    public function profile(Request $request){
        if(Auth::user()){
            $id = Auth::user()->id; //
            $users = User::all()->where('id', $id);
            \LogActivity::addToLog('Profile');
            return view('profile', compact('users'));
        }
        else{
            return Redirect::to('/login');
        } 
    } 

    public function update_profile(Request $request){
       if(Auth::user()){
            $user_id = Auth::user()->id; //
            $this->validate($request, [
            'fname'         => 'max:255',  
            'address'       => 'required|string|max:255',
            'phone'         => 'required|string|max:255',
            'bank'          => 'required|string|max:255',
            'account_name'  => 'required|string|max:255',
        
            ]);
            if(null !== $_POST['submit']){
                //update table
                User::where('id', $user_id)
                        ->update([
                        'fname' =>  $request->fname,
                        'address' => $request->address,
                        'phone' => $request->phone,
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
       else{
        return Redirect::to('/login');
       }
    } 


    public function updateProfileImage(Request $request){
        if(Auth::user()){
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
        else{
            return Redirect::to('/login');  
        }
           
   } 

   public function updateCertificate(Request $request){
        if(Auth::user()){
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
        else{
            return Redirect::to('/login');  
        }
    }
    
   
}
