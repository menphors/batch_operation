<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Connector;
use App\oci_connect;
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
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
session_start();

class BatchPromiseToPayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('promise_to_pay.batch_promise');
    }
    /**
     * Get the request.
     * @param string $request
     * 
     * @return string $resultDes,$resultCode
     */
    public function doPromiseTopay(Request $request){
        $aggreed_pay_date = $request['agreedToPayDate'];
        $remark = $request['remark'];
        $aggreedPayDate = str_replace("-", "", $aggreed_pay_date);
        $userExecute = Auth::user()->name;

        $cust_excel = $request->file('msisdn');
        $cust_info = Excel::toArray( new MsisdnsImport, $cust_excel);

        $validator = Validator::make($request->all(), [
            'agreedToPayDate' => 'required|date|after:today',
            'msisdn' => 'required',
        ]);
        if ($validator->fails()) {
            return  redirect('batch-ptp')
                    ->withErrors($validator)
                    ->withInput();
        }
        $connector = new Connector();
        $conn = $connector->mysql_conn();
        $resCode = [];
        $resDesc = [];
        $pdId = [];
        for($i=0;$i<count($cust_info[0]);$i++){
            $msisdn = $cust_info[0][$i][0];
            $getMsisdn= $this->getMsisdn($msisdn);
            foreach($getMsisdn as $value){
                $do_action = $connector->access_content($value['service_no'],$value['invoice_no'],$aggreedPayDate);   
                $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $do_action);
                $xml = simplexml_load_string($xml);
                $json = json_encode($xml);
                
                $result = json_decode($json)
                ->{'soapenvBody'}
                ->{"bmpCreatePAResultMsg"};
                if(!empty($do_action)){
                    if($result->ResultHeader->{"cbsResultCode"} == '0'){
                        $resultCode = $result->ResultHeader->{"cbsResultCode"};
                        $resultDes = $result->ResultHeader->{"cbsResultDesc"};
                        $paidId = $result->CreatePAResult->{"bmpPAId"};
                        $pdId = $paidId;
                        $sql = "INSERT INTO promise_to_pay (service_no,invoice_no,invoice_date,acct_code,acct_id,paid_id,error_code,error_messages,remark,execute_by,end_date,billcycle_id)  
                        VALUES ('".$value['service_no']."','".$value['invoice_no']."','".$value['invoice_date']."','".$value['acct_code']."','".$value['acct_id']."','".$paidId."','". $resultCode."','".$resultDes."','".$remark."','$userExecute','$aggreedPayDate','".$value['billcycle']."')";
                    }else{
                        $resultCode = $result->ResultHeader->{"cbsResultCode"};
                        $resultDes = $result->ResultHeader->{"cbsResultDesc"};
                        $sql = "INSERT INTO promise_to_pay (service_no,invoice_no,invoice_date,acct_code,acct_id,paid_id,error_code,error_messages,remark,execute_by,end_date,billcycle_id)  
                        VALUES ('".$value['service_no']."','".$value['invoice_no']."','".$value['invoice_date']."','".$value['acct_code']."','".$value['acct_id']."','','". $resultCode."','".$resultDes."','".$remark."','$userExecute','$aggreedPayDate','".$value['billcycle']."')";
                    }
                    $resCode = $resultCode;
                    $resDesc = $resultDes;
                    
                    $exec = mysqli_query($conn, $sql);
                    if ($exec) {
                        //echo ('New record created successfully');
                    } else {
                        die('Error:'. mysqli_error($conn));
                    } 
                }else{
                    echo "Errors: Please contact Administrator!";
                }
                
            }
        }
    
        if(!empty($pdId)){
            return view('promise_to_pay.success',[
                'resultCode'=> $resCode,
                'resultDesc'=> $resDesc,
                'paidId'=>$pdId
            ]);
        }else{
            return view('promise_to_pay.errors',[
                'resultCode'=> $resCode,
                'resultDesc'=> $resDesc
            ]);
        }
    }
    /**
     * Get the Msisdn information.
     * @param string $msisdn
     * 
     * @return array $result
     */
    public function getMsisdn($msisdn){
        set_time_limit(30000);
        $query = "Select * from (select a.sub_identity service_number ,b.invoice_amt/100000000 as open_amt, b.invoice_no,b.invoice_date + 7 / 24 as invoice_date,b.bill_cycle_id, a.acct_code, a.acct_id from (select t2.sub_identity ,t.acct_code,t.acct_id, t3.exp_date
        from usr_145.bc_acct@cn_usrdb12 t,usr_145.bc_sub_iden@cn_usrdb12 t2,usr_145.bc_payment_relation@cn_usrdb12 t3
        where t.acct_id=t3.acct_id
        and t3.pay_obj_id=t2.sub_id
        and sub_iden_type='1'
        union all
        select t2.sub_identity ,t.acct_code,t.acct_id, t3.exp_date
        from usr_142.bc_acct@cn_usrdb34 t,usr_142.bc_sub_iden@cn_usrdb34 t2,usr_142.bc_payment_relation@cn_usrdb34 t3
        where t.acct_id=t3.acct_id
        and t3.pay_obj_id=t2.sub_id
        and sub_iden_type='1'
        )a, ardb_156.ar_invoice@cn_billdb b
        where
        b.acct_id = a.acct_id
        and b.bill_cycle_id >=to_char(trunc(trunc(sysdate, 'MM') - 1, 'MM'),'YYYYMMDD')
        and a.exp_date > sysdate
        and a.sub_identity='$msisdn' order by b.invoice_date desc) where ROWNUM=1";
            $con = new Connector();
            //964108871
            $sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'";
            $st = oci_parse($con->conninhouse, $sql);
            oci_execute($st);
            $stid = oci_parse($con->conninhouse, $query);
            oci_execute($stid);
            $col = [];
            $result = [];
            while ($row=oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
                $col['invoice_no'] = $row['INVOICE_NO'];
                $col['open_amt'] = $row['OPEN_AMT'];
                $col['service_no'] = $row['SERVICE_NUMBER'];
                $col['billcycle'] = $row['BILL_CYCLE_ID'];
                $col['invoice_date'] = $row['INVOICE_DATE'];
                $col['acct_code'] = $row['ACCT_CODE'];
                $col['acct_id'] = $row['ACCT_ID'];
                $result[]= $col;
            }
        return $result;
    }
    /**
     * Get the History Promise To Pay History.
     * 
     * @return array $data
     */
    public function selectPromiseToPayHistory()
    {
        $connector = new Connector();
        $conn = $connector->mysql_conn();
        $sql = "SELECT *
        FROM promise_to_pay order by created_date desc";

        $exec = mysqli_query($conn, $sql);
        if ($exec) {
            //echo ('New record created successfully');
        } else {
            die('Error:'. mysqli_error($conn));
        }
        $resultset = array();
        while ($row = mysqli_fetch_array($exec)) {
            $resultset[] = $row;
            $_SESSION['arr_param']=$resultset;
        }
        return view('promise_to_pay.transaction_his',['data'=> $resultset]);
    }
    public function excel (){
        $headings = array("No","Service No","Invoice No","Invoice Date","Status","message","Start Date","End Date","Execute By","Remark");
        $filename = 'Batch_Promise_to_pay.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=$filename");
        $fp = fopen('php://output', 'w');
        $head = array();
        
        foreach ($headings as $heading) {
          $head[] = $heading;
        }
        fputcsv($fp, $head);
        ////////////////////////////
        // print_r($_SESSION['array1'] );
        foreach($_SESSION['arr_param'] as $data){
            // foreach ($data as $row) {
            //     $rowData[] = "" . addslashes($row) . "";
            // }
            fputcsv($fp,$data);
            unset($data);
        }
    }
}
