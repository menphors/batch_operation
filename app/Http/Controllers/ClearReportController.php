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

class ClearReportController extends Controller
{
    //
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
     return view("Clear_Report.index",$customer_info);
    }
    public function ClearReport(Request $request){
                
                $servername = "localhost";
                $username = "root";
                $password = "";
                $db = "batch_operation";

                $conn = mysqli_connect($servername, $username, $password,$db);
                if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
                }
                if(isset($_POST['del_option'])){
            
                    $sql ="DELETE FROM customer_info_report_ WHERE Executed_Date < SUBDATE(CURDATE(), 30)";
                    if(mysqli_query($conn, $sql)){
                        echo "Records were deleted successfully.";
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
                    }
                    // dd(GETDATE());
                    $sql ="DELETE FROM customer_info_report WHERE Executed_Date < SUBDATE(CURDATE(), 30)";
                    if(mysqli_query($conn, $sql)){
                        echo "Records were deleted successfully.";
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
                    }
                    
                    mysqli_close($conn);

                }else{
                    
                $sql ="DELETE FROM customer_info_report_ ;";
                if(mysqli_query($conn, $sql)){
                    echo "Records were deleted successfully.";
                } else{
                    echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
                }
                $sql ="ALTER TABLE customer_info_report_ AUTO_INCREMENT = 1 ;";
                if(mysqli_query($conn, $sql)){
                    echo "Records were deleted successfully.";
                } else{
                    echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
                }
                $sql ="DELETE FROM customer_info_report ;";
                if(mysqli_query($conn, $sql)){
                    echo "Records were deleted successfully.";
                } else{
                    echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
                }
                $sql ="ALTER TABLE customer_info_report AUTO_INCREMENT = 1 ;";
                if(mysqli_query($conn, $sql)){
                    echo "Records were deleted successfully.";
                } else{
                    echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
                }
                   
                mysqli_close($conn);
                }
             
             return redirect()->back();
        
    }
}
