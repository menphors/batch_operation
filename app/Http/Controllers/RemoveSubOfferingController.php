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
class RemoveSubOfferingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //to view the data in remove sub offering
    public function index()
    {
        if(!Permission::check('activate', 'true'))
        {
            return view('permission.index');
        }
        $customer_info['customer_info'] = DB::table('customer_info_report_')
        ->join("users" , "users.id" , "=" , "customer_info_report_.Execute_By")
        ->select('customer_info_report_.*', 'users.name as user_name')
        ->orderBy("customer_info_report_.batch_number" , "desc")
        ->paginate(10);
        return view('remove_sub_offer.index',$customer_info);
    }

    protected $phoneNumber;
    protected $NGBSSService;


    public function remove_sub_offer(Request $request)
    {  
         ini_set('max_execution_time', 0);
        $cust_info = $request->file('number');
        $cust_info = Excel::toArray( new MsisdnsImport, $cust_info ); 
        $execute_date_now = Carbon::parse(Carbon::now())->format('Y-m-d');
        $resultId = DB::table("customer_info_report_")
        ->insertGetId(
         [                            
             "Remark" =>$request->remark,
             "Execute_By" =>Auth::user()->id,
             "Executed_Date" => $execute_date_now,
             "Amount" =>count($cust_info[0])-1,
         ]

     );
      
     $servername = "localhost";
     $username = "root";
     $password = "";
     $db = "batch_operation";
     
     // Create connection
     $conn = mysqli_connect($servername, $username, $password,$db)or die("Failed to 
     connect to MySQL: " . mysqli_error());
     
     // Check connection
     if (!$conn) {
     die("Connection failed: " . mysqli_connect_error());
     }
     // echo "Connected successfully";
     
     $sql = "SELECT batch_number FROM customer_info_report_";
     $result = mysqli_query($conn, $sql);
     
     if (mysqli_num_rows($result) > 0) {
     // output data of each row
     
     while($row = mysqli_fetch_assoc($result)) {
         $auto_number=$row;
     }
     } else {
     echo "0 results";
     }              
     mysqli_close($conn);


        for($i=1;$i<count($cust_info[0]) ; $i++){
            $number = $cust_info[0][$i][0];
            $offer_id = $cust_info[0][$i][1];
            $this->NGBSSService = new NGBSSService($number, "en");
            $variableMessage=$this->NGBSSService->RemoveSubOffer($number, $offer_id);

            $resultId = DB::table("customer_info_report")
           
            ->insertGetId(
                [   
                    "Executed_By" => Auth::user()->id,
                    "Executed_Date" =>Carbon::now(),
                    "remark" => $request->remark,
                    // "Amount" => count($cust_info[0]) -1,
                    "batch_number"=> intval(end($auto_number)),
                    "message" => $variableMessage,
                    "PhoneNumber"=>$number
                ]
            );
        }
        Session::flash('success', 'Operation successfully!');
        return redirect()->back();
    }
}
