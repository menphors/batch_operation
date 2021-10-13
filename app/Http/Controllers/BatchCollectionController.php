<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\MsisdnsImport;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Routing\Controller;
use App\Exports\UsersExport;
use File;
use App\Page;
use total_data;

session_start();
class BatchCollectionController extends Controller
{
    

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected $phoneNumber;
    protected $NGBSSService;
    protected $total_data;
    
     
    public function index(){

        if(!Permission::check('batch_collection', 'true'))
        {
            return view('permission.index');
        }
       
        return view('batch_collection.index');


    }
        
        public function save(Request $r)
    {
        

        $conn = oci_connect('smart', 'Smart_%002', '10.12.5.191:1526/suseora');
        ini_set('max_execution_time', 0);
        if (!$conn) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
         $master_acct = $r->master_acct;
         $remark = $r->remark;
         $date = $r->date;
         $_SESSION['remark']=$remark;

         $stids = oci_parse($conn,"select distinct 
c.cust_name,
count(m.service_number) as total
from ccare.INF_GROUP_MEMBER      m,  
 ccare.INF_ACCT_RELATION     gr,
 ccare.INF_ACCT_RELATION     gr2, 
 CCARE.INF_ACCT              ga, 
 CCARE.INF_ACCT              ga2, 
 ccare.inf_customer_corp     c,
 ccare.inf_customer_all      c1,
 ccare.inf_subscriber_all b1
where ga.acct_id = gr.acct_id
and   gr2.sub_id = b1.sub_id
and   ga2.acct_id = gr2.acct_id
and   m.service_number = b1.msisdn
and   gr.sub_id = m.group_sub_id
and   ga.cust_id = c.cust_id
and   c1.cust_id = ga2.cust_id
and   gr.exp_date > sysdate
and   ga.exp_date > sysdate
and   m.exp_date > sysdate
and   gr2.exp_date > sysdate
and   ga2.exp_date > sysdate
and   b1.exp_date > sysdate
and   m.MEMBER_CLASS = '1'
and   m.member_type = 'Default'
and   b1.sub_state in('B03','B04')
and   c.corp_no = '".$master_acct."'
group by c.cust_name");
oci_execute($stids);
$count = array();
$result = [0];
while ($row=oci_fetch_array($stids)) {   
$count['cust_name'] = $row['CUST_NAME'];
$count['total'] = $row['TOTAL'];

$result[]= $count;}

           
         $stid = oci_parse($conn,"
         select distinct 
         c1.cust_code,',',
         ga2.acct_code,',',
         m.service_number,',,remark'
         from ccare.INF_GROUP_MEMBER      m, 
              ccare.INF_ACCT_RELATION     gr,
              ccare.INF_ACCT_RELATION     gr2, 
              CCARE.INF_ACCT              ga, 
              CCARE.INF_ACCT              ga2, 
              ccare.inf_customer_corp     c,
              ccare.inf_customer_all      c1,
              ccare.inf_subscriber_all b1
        where ga.acct_id = gr.acct_id
        and   gr2.sub_id = b1.sub_id
        and   ga2.acct_id = gr2.acct_id
        and   m.service_number = b1.msisdn
        and   gr.sub_id = m.group_sub_id
        and   ga.cust_id = c.cust_id
        and   c1.cust_id = ga2.cust_id
        and   gr.exp_date > sysdate
        and   ga.exp_date > sysdate
        and   m.exp_date > sysdate
        and   gr2.exp_date > sysdate
        and   ga2.exp_date > sysdate
        and   b1.exp_date > sysdate
        and   m.MEMBER_CLASS = '1'
        and   m.member_type = 'Default'
        and   b1.sub_state in ('B03','B04')
        and   c.corp_no = '".$master_acct."'");
   
        oci_execute($stid);
        $total_data = array();
        $final_data = [0];
        //$row = oci_fetch_assoc($stid)
        while ($row=oci_fetch_array($stid)) {   
            // $x++;
        $total_data['cust_code'] = $row['CUST_CODE'];
        $total_data['acct_code'] = $row['ACCT_CODE'];
        $total_data['service_number'] = $row['SERVICE_NUMBER'];
         $final_data[]= $total_data;
         $final = $final_data;
        // print_r($total_data);
        $_SESSION['array1'][]=$total_data;  
    //    dd ($total_data);
        // array_push($_SESSION['array'],$total_data);
     
        // print_r($_SESSION['array1']['array2']);

        
$data = array();
foreach($total_data as $team)
    
$data[] = "" . addslashes($team) . "";
$data  ='\'' .implode('\',\'' , $data). '\''; 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "batch_operation";
$conn = mysqli_connect($servername, $username, $password, $dbname);

$sql = "INSERT INTO batch_collection (CUST_CODE,ACCT_CODE,SERVICE_NUMBER,REMARK,DUE_DATE )  VALUES ($data,'$remark','$date')";


if ($conn->query($sql) === TRUE) {

} else {
echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

}   
return view('batch_collection.batch_result')->with('total_display', $result);  
}       

    
 public function excel (){

$remarks = $_SESSION['remark'];
$headings = array("'Cust_code',","'acct_code',","'service_number',,","remark'");
$filename = 'Batch_Collection.csv';
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
    foreach($_SESSION['array1'] as $data){
    foreach ($data as $row) {
        if($data['service_number']==$row){
            $rowData[] = "" . addslashes($row) . ",,";
            break;
        }
            $rowData[] = "" . addslashes($row) . ",";
            implode(",", $rowData);
        }
    $rowData[]=$remarks;
    fputcsv($fp,$rowData);
    unset($rowData);
  
}
session_destroy();
}
}


    

   
  

