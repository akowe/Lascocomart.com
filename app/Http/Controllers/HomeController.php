<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    public function logActivity()
    {
        // $logs = \LogActivity::logActivityLists();
        $logs = LogActivityModel::join('users', 'users.id', '=', 'log_activity.user_id')
        ->get(['log_activity.*', 'users.email', 'users.fname']);
        return view('logActivity',compact('logs'));
    }
}
