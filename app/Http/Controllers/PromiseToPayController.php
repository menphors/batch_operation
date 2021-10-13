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
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;


class PromiseToPayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('promise_to_pay.index');
    }
    /**
     * Get the invoice Promise To Pay Transaction.
     * @param string $request
     * @return array $resultCode,$resultDes,$paidId
     */
    public function doPromiseTopay(Request $request){
        $msisdn = $request['msisdn'];
        $invoiceNo = $request['invoiceNo'];
        $aggreed_pay_date = $request['agreedToPayDate'];
        $remark = $request['remark'];
        $userExecute = Auth::user()->name;
       
        $aggreedPayDate = str_replace("-", "", $aggreed_pay_date);

        $validator = Validator::make($request->all(), [
            'agreedToPayDate' => 'required|date|after:today',
            'msisdn' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('ptp')
                        ->withErrors($validator)
                        ->withInput();
        }
        $getMsisdn = $this->getInvoice($msisdn);
        foreach($getMsisdn as $value){
            $connector = new Connector();
            $do_action = $connector->access_content($msisdn,$value['invoice_no'],$aggreedPayDate);
            if(!empty($do_action)){
                $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $do_action);
                $xml = simplexml_load_string($xml);
                $json = json_encode($xml);

                $result = json_decode($json)
                ->{'soapenvBody'}
                ->{"bmpCreatePAResultMsg"};
                $resultCode = $result->ResultHeader->{"cbsResultCode"};
                $resultDes = $result->ResultHeader->{"cbsResultDesc"};
                $conn = $connector->mysql_conn();
                if($result->ResultHeader->{"cbsResultCode"} == '0'){
                    $paidId = $result->CreatePAResult->{"bmpPAId"};
                    
                    $sql = "INSERT INTO promise_to_pay (service_no,invoice_no,billcycle_id,invoice_date,acct_code,acct_id,paid_id,error_code,error_messages,execute_by,remark,end_date)  
                            VALUES ('$msisdn','".$value['invoice_no']."','".$value['billcycle']."','".$value['invoice_date']."','".$value['acct_code']."','".$value['acct_id']."','$paidId','$resultCode','$resultDes','$userExecute','$remark','$aggreedPayDate')";
                    $exec = mysqli_query($conn, $sql);
                    if ($exec) {
                        //echo ('New record created successfully');
                    } else {
                        die('Error:'. mysqli_error($conn));
                    } 
                    return view('promise_to_pay.success',[
                        'resultCode'=>$resultCode,
                        'resultDesc'=>$resultDes,
                        'paidId'=>$paidId
                    ]);
                }else{
                    $sql = "INSERT INTO promise_to_pay (service_no,invoice_no,billcycle_id,invoice_date,acct_code,acct_id,error_code,error_messages,execute_by,remark,end_date)  
                    VALUES ('$msisdn','".$value['invoice_no']."','".$value['billcycle']."','".$value['invoice_date']."','".$value['acct_code']."','".$value['acct_id']."','$resultCode','$resultDes','$userExecute','$remark','$aggreedPayDate')";
                    $exec = mysqli_query($conn, $sql);
                    if ($exec) {
                        //echo ('New record created successfully');
                    } else {
                        die('Error:'. mysqli_error($conn));
                    }
                    return view('promise_to_pay.errors',[
                        'resultCode'=> $resultCode,
                        'resultDesc'=>$resultDes,
                    ]);
                }
            }else{
                echo "Errors: Please contact Administrator!";
            }
        }
    }
    /**
     * Get the msisdn Promise To Pay Transaction.
     * @param string $msisdn
     * @return array $result
     */
    public function getMsisdn($msisdn){
        $query = "select a.sub_identity service_number ,b.invoice_amt/100000000 as open_amt, b.invoice_no,b.invoice_date + 7 / 24 as invoice_date,b.bill_cycle_id, a.acct_code, a.acct_id,to_char(b.due_date,'dd-mm-yyy') as due_date from (select t2.sub_identity ,t.acct_code,t.acct_id, t3.exp_date
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
        and a.exp_date > sysdate
        and a.sub_identity='$msisdn' order by b.invoice_date desc";
            $con = new Connector();
            $sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'";
            $st = oci_parse($con->conninhouse, $sql);
            oci_execute($st);
            $stid = oci_parse($con->conninhouse, $query);
            oci_execute($stid);

            $col = array();
            $result = [];
            while ($row=oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
                $col['invoice_no'] = $row['INVOICE_NO'];
                $col['open_amt'] = $row['OPEN_AMT'];
                $col['service_no'] = $row['SERVICE_NUMBER'];
                $col['invoice_date'] = $row['INVOICE_DATE'];
                $col['billcycle'] = $row['BILL_CYCLE_ID'];
                $col['due_date'] = $row['DUE_DATE'];
                $result[]= $col;
            }
        return $result;
    }

    /**
     * Get the invoice Promise To Pay Transaction.
     * @param string $msisdn
     * @return array $result
     */
    public function getInvoice($msisdn){
        set_time_limit(30000);
        $query = "Select * from (select a.sub_identity service_number ,b.Open_amt/100000000 as open_amt, b.invoice_no,b.invoice_date + 7 / 24 as invoice_date,b.bill_cycle_id, a.acct_code, a.acct_id from (select t2.sub_identity ,t.acct_code,t.acct_id, t3.exp_date
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
        and b.Open_amt <> 0
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
}