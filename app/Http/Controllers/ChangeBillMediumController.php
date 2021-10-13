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
use App\Helpers\Helper;

class ChangeBillMediumController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected $phoneNumber;
    protected $NGBSSService;
    
     
    public function index(){

        if(!Permission::check('changebillmedium', 'true'))
        {
            return view('permission.index');
        }
        $customer_info['customer_info'] = DB::table('customer_info_report_')
        ->join("users" , "users.id" , "=" , "customer_info_report_.Execute_By")
        ->select('customer_info_report_.*', 'users.name as user_name')
        ->orderBy("customer_info_report_.batch_number" , "desc")
        ->paginate(10);
        return view('change_bill_medium.index',$customer_info);


    }
  
   

    public function ChangeBillMedium(Request $request){
        // dd($request->medium_code, $request->medium_drop);
        $account_excel = $request->file('acct_id');
        $cust_info = Excel::toArray( new MsisdnsImport, $account_excel );
        // $request->medium_id;
        // $request->medium_type;
        // $request->remark;
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
       
        // Teacher Dara
        // $successItems = array();
        // $failedItems = array();
        // Loop 
        // foreach($cust_info[0] as $item) { 
        //     try { 
        //         $this->NGBSSService = new NGBSSService('69204963', "en");     
        //         $acct_id = $item[0];      
        //         $new_bill_id = $item[0][1];
        //         $new_bill_code = $item[0][2]; 
        //         $new_bill_id = $request->medium_id;
        //         $new_bill_code = $request->medium_type;
        //         // $oldinfo = $this->NGBSSService->QueryAcctInfo($acct_id);

        //         $success = $this->NGBSSService->ChangeBillMedium($acct_id, $old_bill_id, $old_bill_code, 
        //         $new_bill_id, $new_bill_code);
        //         // get queryaccinfo response
        //         // $billMedium = $oldinfo['soapenv:Envelope']['soapenv:Body']['acc:QueryAcctInfoRspMsg']['acc:Account']['com:BillMedium'];
                
        //         // assign parameters
        //         // $old_bill_id = $billMedium['com:BillMediumId'];
        //         // $old_bill_code = $billMedium['com:BillMediumCode'];
        //         $resultId = DB::table("customer_info_report")
        //     ->insertGetId(
        //     [   
        //         "Executed_By" => Auth::user()->id,
        //         "Executed_Date" =>Carbon::now(),
        //         "remark" => $request->remark,
        //         "Amount" => count($cust_info[0]) -1,
        //         "batch_number"=> intval(end($auto_number)),
        //         "message" => $success,
        //         "PhoneNumber"=>'69204963'
        //     ]
        // );

        //         Log::info($success);
        //         $successItems[] = $item[0];
        //     } catch (\Throwable $th) {
        //         //throw $th;
        //         $failedItems[] = $item[0];
        //     }
            
        // }
        for($i=1;$i<count($cust_info[0]) ; $i++){
            $acct_id = $cust_info[0][$i][0];      
            $old_bill_id = $cust_info[0][$i][1];
            $old_bill_code = $cust_info[0][$i][2]; 
            $new_bill_id = $cust_info[0][$i][3]; 
            $new_bill_code = $cust_info[0][$i][4]; 
    
            $this->NGBSSService = new NGBSSService('69204963', "en");  
            // $this->NGBSSService = new NGBSSService($number, "en");
            $variableMessage = $this->NGBSSService->ChangeBillMedium($acct_id, $old_bill_id, $old_bill_code, 
                    $new_bill_id, $new_bill_code);
            $resultId = DB::table("customer_info_report")
           
        ->insertGetId(
            [   
                "Executed_By" => Auth::user()->id,
                "Executed_Date" =>Carbon::now(),
                "remark" => $request->remark,
                // "Amount" => count($cust_info[0]) -1,
                "batch_number"=> intval(end($auto_number)),
                "message" => $variableMessage,
                "PhoneNumber"=>'69204963'
            ]
        );
        }
        Session::flash('success', 'Operation is submited!');
        return redirect()->back();
    }


    

    public function getDataGUI(Request $request) //-> return request from API
    {
        # code...
        $this->NGBSSService = new NGBSSService('en');
        $data = $this->NGBSSService->QueryAcctInfo($acct_id);
        dd($request);

    }
        

}
