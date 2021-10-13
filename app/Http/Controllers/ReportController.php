<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request; 
use App\Imports\MsisdnsImport;
use App\Services\NGBSSService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected $phoneNumber;
    protected $NGBSSService;
    //
    function index(){
        if(!Permission::check('activate', 'true'))
        {
            return view('permission.index');
        }
        $customer_info['customer_info'] = DB::table('customer_info_report_')
        ->join("users" , "users.id" , "=" , "customer_info_report_.Execute_By")
        ->select('customer_info_report_.*', 'users.name as user_name')
        ->orderBy("customer_info_report_.batch_number" , "desc")
        ->paginate(10);
        return view('Report.index',$customer_info);
    }
}
