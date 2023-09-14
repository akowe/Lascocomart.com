<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use App\Models\LogActivity as LogActivityModel;
use Auth;

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
        $user->save();

        return redirect()->back()->with("success","Password successfully changed!");
    }
}
