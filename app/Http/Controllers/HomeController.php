<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $todos = DB::table('change_offering_schedules')
        ->join("users" , "users.id" , "=" , "change_offering_schedules.user_id")
        ->select('change_offering_schedules.*', 'users.name as user_name')
        ->where("completed" , false)
        ->orderBy("id" , "desc")
        ->paginate(10);

        $completedTrns = DB::table('change_offering_schedules')
        ->join("users" , "users.id" , "=" , "change_offering_schedules.user_id")
        ->select('change_offering_schedules.*', 'users.name as user_name')
        ->where("completed" , true)
        ->orderBy("id" , "desc")
        ->paginate(10); 
        
        $customerInfo = DB::table('customer_info_report')
        ->join("users" , "users.id" , "=" , "customer_info_report.Executed_By")
        ->select('customer_info_report.*', 'users.name as user_name')
        ->orderBy("Executed_Date" , "desc")
        ->paginate(10); 
        $user = Auth::id();
        // dd($user);
        $functions = DB::table('functions')
                ->select('functions.id', 'path', 'function_name')
                ->join('user_functions', 'functions.id', '=','user_functions.function_id')
                ->where('user_id', $user)
                // ->where('functions.id', 'user_fuctions.function_id')
                ->get();

        return view('change_sub_offering.index')->with(['functions'=>$functions,'todos' => $todos,  'completed_trns' => $completedTrns, 'customer_info' => $customerInfo]);
    }


    
    

}
