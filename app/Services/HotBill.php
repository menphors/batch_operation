<?php
namespace App\Services;
use SSH;
use phpseclib\Net\SSH2;
use Illuminate\Http\Request; 
use App\Imports\MsisdnsImport;
use App\Services\NGBSSService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Exports\UsersExport;
use Collective\Remote\RemoteServiceProvider;
use File;
use App\Page;
use total_data;
use DOMDocument;
use DOMXPath;
use DOMNode;
use SimpleXMLElement;
use Simplexml_load_file;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
use Exception;
use Ixudra\Curl\Facades\Curl;
use App\Http\Controllers\HotBillController;
use Zipper;
use ZipArchive;

//=============SFTP =======================

use Illuminate\Support\Facades\Storage;
    class HotBill{
      
        public function __construct()
        {

            //============ Need Schudle to run it ================
            // $this->setExchangeRate();
        }
        public function HotBill_Operatrion($msisdn, $correlation_id)
        {
            $timeStarts=Carbon::now();
            //==============================(AccountCode Line 500)=================================
            $status=null;
            $timeStart=null;
            $timeEnd=null;
            $error=$this->WrtieLogs_Group("HotBill",$correlation_id);
            $timeStart=Carbon::now();
            $cust_info=$this->getAccount_Code_by_Number($msisdn);
            $timeEnd=Carbon::now();
            if($cust_info){
                $status="Success";
            }else{
                $status="failed";
               }
            $this->WrtieLogs("GetAcct_Code",$status,$timeStart,$timeEnd,$correlation_id,"NULL");
// dd($cust_info);
          
         
            $randDeomFileName=$correlation_id;
            $fileNameServer;
            $localtionFileNameServer;
            $conn = oci_connect('smart', 'Smart_%002', '10.12.5.191:1526/suseora');
            ini_set('max_execution_time', 0);
            if (!$conn) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }           
            $execute_date_now = Carbon::parse(Carbon::now())->format('Y-m-d');
            for($i=1;$i<count($cust_info) ; $i++){
                $ACCT = $cust_info[0];
                // dd($ACCT);
                //=============================(NGBSS path: App\Services\NGBSSServeices)==================================
                $timeStart=Carbon::now();
                sleep(180);
                $this->NGBSSService = new NGBSSService($ACCT, "en");
                $Task_ID=$this->NGBSSService->HotBill($ACCT);
                $timeEnd=Carbon::now();
                if($Task_ID){
                    $status="Success";
                }else{
                    $status="failed";
                   }
                $this->WrtieLogs("Get Task_ID",$status,$timeStart,$timeEnd,$correlation_id,"NULL");
// dd($Task_ID);
                //================================(query XML by Accout_Code and Task Id)===============================
                $timeStart=Carbon::now();
                $stids = oci_parse($conn,"select t.acct_code,
                t.billtype_id,
                t.output_filename,
                t.output_dir || t.output_filename,
                t.*
               from general.bb_billfile_desc_".Carbon::now()->format('Ym')."@bmpdb t --***
                where t.flow_type = 5 --1:real; 3:test; 5:hot bill;
                --and t.bill_cycle_id = '20170401'
                and t.file_type = 4 --1:PDF;2:PCL;3HTML:4:XML
               --and t.billtype_id = 3   -- BILLTYPE_ID1: PAPER; 2: SMS; 3: EMAIL; 4: FAX; 5: EBILL
                --and t.invoice_type = 0 -- 0: Individual 1:Master
                --and t.acct_code = '".$ACCT."';
               and t.task_ID='".$Task_ID."'");
                oci_execute($stids);
               $count = array();
               $result2 = [0];
    
               while(!oci_fetch_array($stids)) {  
                    $stids = oci_parse($conn,"select t.acct_code,
                    t.billtype_id,
                    t.output_filename,
                    t.output_dir || t.output_filename,
                    t.*
                   from general.bb_billfile_desc_".Carbon::now()->format('Ym')."@bmpdb t --***
                    where t.flow_type = 5 --1:real; 3:test; 5:hot bill;
                    --and t.bill_cycle_id = '20170401'
                    and t.file_type = 4 --1:PDF;2:PCL;3HTML:4:XML
                   --and t.billtype_id = 3   -- BILLTYPE_ID1: PAPER; 2: SMS; 3: EMAIL; 4: FAX; 5: EBILL
                    --and t.invoice_type = 0 -- 0: Individual 1:Master
                    --and t.acct_code = '".$ACCT."';
                   and t.task_ID='".$Task_ID."'");
                    oci_execute($stids);
            } 
    
            $stids = oci_parse($conn,"select t.acct_code,
            t.billtype_id,
            t.output_filename,
            t.output_dir || t.output_filename,
            t.*
           from general.bb_billfile_desc_".Carbon::now()->format('Ym')."@bmpdb t --***
            where t.flow_type = 5 --1:real; 3:test; 5:hot bill;
            --and t.bill_cycle_id = '20170401'
            and t.file_type = 4 --1:PDF;2:PCL;3HTML:4:XML
           --and t.billtype_id = 3   -- BILLTYPE_ID1: PAPER; 2: SMS; 3: EMAIL; 4: FAX; 5: EBILL
            --and t.invoice_type = 0 -- 0: Individual 1:Master
            --and t.acct_code = '".$ACCT."';
           and t.task_ID='".$Task_ID."'");
            oci_execute($stids);
    
               while ($row=oci_fetch_array($stids)) {   
                $result2['XML']=$row['T.OUTPUT_DIR||T.OUTPUT_FILENAME'];
               }  
               $result = [0];
               $result1 = [0];
               $stids = oci_parse($conn,"select t.acct_code,
               t.billtype_id,
               t.output_filename,
               t.output_dir,
               t.output_filename,
               t.output_dir || t.output_filename,
               t.*
       from general.bb_billfile_desc_".Carbon::now()->format('Ym')."@bmpdb t --***
       where t.flow_type = 5 --1:real; 3:test; 5:hot bill;
       --and t.bill_cycle_id = '20200801'
       and t.file_type = 4 --1:PDF;2:PCL;3HTML:4:XML
       --and t.billtype_id = 3   -- BILLTYPE_ID1: PAPER; 2: SMS; 3: EMAIL; 4: FAX; 5: EBILL
       --and t.invoice_type = 0 -- 0: Individual 1:Master
       and t.task_ID = '".$Task_ID."'");
               oci_execute($stids);
               while ($row=oci_fetch_array($stids)) {   
                   $result['link']=$row['OUTPUT_FILENAME'];
                   $result1['link']=$row['OUTPUT_DIR'];
               }

        //===============================(Download Xml Process)================================
        $location=$result1['link'];
        $location=substr($result1['link'],11,strlen($result1['link']));
        $fileNameServer=substr($result2['XML'],12,strlen($result2['XML'])-12);              
        $testcontent = Storage::disk('sftp')->get('/onip/bmbill_mount/bill_nfs/billgen/'.$fileNameServer);      
        $fileName=substr( $result2['XML'],41,strlen( $result2['XML'])-41);
        Storage::disk('local')->put('DownloadHotBill/'.$fileName,$testcontent);
        $myfile = 'C:\wamp64\www\batch_operation_portal\storage\app\DownloadHotBill/'.$fileName;

//                dd("OK");
//================ PDF =================================

$timeStart=Carbon::now();
                $stids = oci_parse($conn,"select t.acct_code,
                t.billtype_id,
                t.output_filename,
                t.output_dir || t.output_filename,
                t.*
               from general.bb_billfile_desc_".Carbon::now()->format('Ym')."@bmpdb t --***
                where t.flow_type = 5 --1:real; 3:test; 5:hot bill;
                --and t.bill_cycle_id = '20170401'
                and t.file_type = 1 --1:PDF;2:PCL;3HTML:4:XML
               --and t.billtype_id = 3   -- BILLTYPE_ID1: PAPER; 2: SMS; 3: EMAIL; 4: FAX; 5: EBILL
                --and t.invoice_type = 0 -- 0: Individual 1:Master
                --and t.acct_code = '".$ACCT."';
               and t.task_ID='".$Task_ID."'");
                oci_execute($stids);
               $count = array();
               $result2 = [0];
    
               while(!oci_fetch_array($stids)) {  
                    $stids = oci_parse($conn,"select t.acct_code,
                    t.billtype_id,
                    t.output_filename,
                    t.output_dir || t.output_filename,
                    t.*
                   from general.bb_billfile_desc_".Carbon::now()->format('Ym')."@bmpdb t --***
                    where t.flow_type = 5 --1:real; 3:test; 5:hot bill;
                    --and t.bill_cycle_id = '20170401'
                    and t.file_type = 1 --1:PDF;2:PCL;3HTML:4:XML
                   --and t.billtype_id = 3   -- BILLTYPE_ID1: PAPER; 2: SMS; 3: EMAIL; 4: FAX; 5: EBILL
                    --and t.invoice_type = 0 -- 0: Individual 1:Master
                    --and t.acct_code = '".$ACCT."';
                   and t.task_ID='".$Task_ID."'");
                    oci_execute($stids);
            } 
    
            $stids = oci_parse($conn,"select t.acct_code,
            t.billtype_id,
            t.output_filename,
            t.output_dir || t.output_filename,
            t.*
           from general.bb_billfile_desc_".Carbon::now()->format('Ym')."@bmpdb t --***
            where t.flow_type = 5 --1:real; 3:test; 5:hot bill;
            --and t.bill_cycle_id = '20170401'
            and t.file_type = 1 --1:PDF;2:PCL;3HTML:4:XML
           --and t.billtype_id = 3   -- BILLTYPE_ID1: PAPER; 2: SMS; 3: EMAIL; 4: FAX; 5: EBILL
            --and t.invoice_type = 0 -- 0: Individual 1:Master
            --and t.acct_code = '".$ACCT."';
           and t.task_ID='".$Task_ID."'");
            oci_execute($stids);
    
               while ($row=oci_fetch_array($stids)) {   
                $result2_PDF['XML']=$row['T.OUTPUT_DIR||T.OUTPUT_FILENAME'];
               }  
               $result_PDF = [0];
               $result1_PDF = [0];
               $stids = oci_parse($conn,"select t.acct_code,
               t.billtype_id,
               t.output_filename,
               t.output_dir,
               t.output_filename,
               t.output_dir || t.output_filename,
               t.*
       from general.bb_billfile_desc_".Carbon::now()->format('Ym')."@bmpdb t --***
       where t.flow_type = 5 --1:real; 3:test; 5:hot bill;
       --and t.bill_cycle_id = '20200801'
       and t.file_type = 1 --1:PDF;2:PCL;3HTML:4:XML
       --and t.billtype_id = 3   -- BILLTYPE_ID1: PAPER; 2: SMS; 3: EMAIL; 4: FAX; 5: EBILL
       --and t.invoice_type = 0 -- 0: Individual 1:Master
       and t.task_ID = '".$Task_ID."'");
               oci_execute($stids);
               while ($row=oci_fetch_array($stids)) {   
                   $result_PDF['link']=$row['OUTPUT_FILENAME'];
                   $result1_PDF['link']=$row['OUTPUT_DIR'];
               }


//===============================(Download PDF Process)================================
$location_PDF=$result1_PDF['link'];
$location_PDF=substr($result1_PDF['link'],11,strlen($result1_PDF['link']));
$fileNameServer_PDF=substr($result2_PDF['XML'],12,strlen($result2_PDF['XML'])-12);
$fileName_PDF=substr( $result2_PDF['XML'],78,strlen( $result2_PDF['XML'])-78);      
            $timeEnd=Carbon::now();
            if($result['link']){
                   $status="Success";
            }else{
                $status="failed";
            }
            $this->WrtieLogs("Download XML by Task_ID",$status,$timeStart,$timeEnd,$correlation_id,"NULL");
    

               
               //===================================(GetExchangeRate from local DB: Line 549)============================
                $timeStart=Carbon::now();
                $exchangeRate=$this->getExchangeRate();
                $timeEnd=Carbon::now();
                if($exchangeRate){
                       $status="Success";
                }else{
                    $status="failed";
                }
                $this->WrtieLogs("Get ExchangeRate",$status,$timeStart,$timeEnd,$correlation_id,"NULL");

                //==================================(get Invoices Number line 577)=============================
                $timeStart=Carbon::now();
                $invoiceNo=$this->getInvoicesNumber();
                // $invoiceNo=$this->SelectDb_Data();
                $timeEnd=Carbon::now();
                if($invoiceNo){
                       $status="Success";
                }else{
                    $status="failed";
                }
                $this->WrtieLogs("Get InvoiceNo",$status,$timeStart,$timeEnd,$correlation_id,"NULL");

                //===================================(get VAT Info line 381)============================
                $timeStart=Carbon::now();
                $vat=$this->getVATInfo($ACCT);
                // $vat=$this->SelectVATInfo($ACCT);
                $timeEnd=Carbon::now();
                if($vat){
                       $status="Success";
                }else{
                    $status="failed";
                }
                $this->WrtieLogs("Get VAT",$status,$timeStart,$timeEnd,$correlation_id,"NULL");

                //===================================(get Company Name line 229)============================
                $timeStart=Carbon::now();
                $Company_name=$this->getCompanyName($ACCT);
                // $Company_name=$this->SelectCUSInfo($ACCT);
                $timeEnd=Carbon::now();
                if($Company_name){
                    $status="Success";
                }else{
                    $status="failed";
                }
                $this->WrtieLogs("Get Company Name",$status,$timeStart,$timeEnd,$correlation_id,"NULL");
                
                //==================================(Modify XML line 703)=============================
                $timeStart=Carbon::now();
                $generateXML=$this->ModifyXML($myfile,$exchangeRate,$invoiceNo,$Company_name,$vat);
                $timeEnd=Carbon::now();
                if($generateXML){
                    $status="Success";
                }else{
                    $status="failed";
                }
                $this->WrtieLogs("Modify XML",$status,$timeStart,$timeEnd,$correlation_id,"NULL");

                //====================================(uploading XML and generatePDF_By_XML line 534)===========================
                $timeStart=Carbon::now();
                $this->uploadXml($fileName,'/onip/bmbill_mount/bill_nfs/billgen'.$location);
                $message['message']=$this->generatePDF_By_XML($fileName_PDF,'/onip/invbill_mount/bill_nfs/fmt'.$location_PDF,$randDeomFileName);
                // $message['message']=$this->processCmd($fileName,'/onip/bmbill_mount/bill_nfs/billgen'.$location,$randDeomFileName);
                $timeEnd=Carbon::now();
                if($message['message']){
                    $status="Success";
                }else{
                    $status="failed";
                }
                $this->WrtieLogs("Generate PDF",$status,$timeStart,$timeEnd,$correlation_id,"NULL");

                //========================================(End Of HotBill Process)=======================
                $this->WrtieLogs("Generate HotBill",$status,$timeStarts,$timeEnd,$correlation_id,$location_PDF);
            }
         }


        public function GetPOST(){
            $nameFile=rand(10000,99999).Carbon::now();
            $name=Storage::disk('sftp')->makeDirectory('/onip/bmbill_mount/bill_nfs/billgen/PDF_FIle/'.$nameFile);
            return $nameFile;
        }
        public function replacePDF(){
            $conn = oci_connect('smart', 'Smart_%002', '10.12.5.191:1526/suseora');
            ini_set('max_execution_time', 0);
            if (!$conn) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
            $result = [0];
            $result1 = [0];
            $stids = oci_parse($conn,"select t.acct_code,
            t.billtype_id,
            t.output_filename,
            t.output_dir,
            t.output_filename,
            t.output_dir || t.output_filename,
            t.*
       from general.bb_billfile_desc_202009@bmpdb t --***
      where t.flow_type = 5 --1:real; 3:test; 5:hot bill;
      --and t.bill_cycle_id = '20200801'
      and t.file_type = 1 --1:PDF;2:PCL;3HTML:4:XML
     --and t.billtype_id = 3   -- BILLTYPE_ID1: PAPER; 2: SMS; 3: EMAIL; 4: FAX; 5: EBILL
      --and t.invoice_type = 0 -- 0: Individual 1:Master
     and t.acct_code = '1.56316018'");
            oci_execute($stids);
            while ($row=oci_fetch_array($stids)) {   
                $result['link']=$row['OUTPUT_FILENAME'];
                $result1['link']=$row['OUTPUT_DIR'];
            }
              return $result;
            
        }
        public function Hot_Bill(Request $request){
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
    
         $conn = mysqli_connect($servername, $username, $password,$db)or die("Failed to 
         connect to MySQL: " . mysqli_error());
         if (!$conn) {
         die("Connection failed: " . mysqli_connect_error());
         }
         
         $sql = "SELECT batch_number FROM customer_info_report_";
         $result = mysqli_query($conn, $sql);
         
         if (mysqli_num_rows($result) > 0) {
         while($row = mysqli_fetch_assoc($result)) {
             $auto_number=$row;
         }
         } else {
         echo "0 results";
         }              
         mysqli_close($conn);
            for($i=1;$i<count($cust_info[0]) ; $i++){
                $number = $cust_info[0][$i][0];
                
                $this->NGBSSService = new NGBSSService($number, "en");
                $variableMessage=$this->NGBSSService->HotBill($number);
                $resultId = DB::table("customer_info_report")
               
            ->insertGetId(
                [   
                    "Executed_By" => Auth::user()->id,
                    "Executed_Date" =>Carbon::now(),
                    "remark" => $request->remark,
                    "batch_number"=> intval(end($auto_number)),
                    "message" => $variableMessage,
                    "PhoneNumber"=>$number
                ]
            );
    
            }
            Session::flash('success', 'Operation is submited!');
            return redirect()->back();
        }
        
        public function setExchangeRate(){
            
            $ch = curl_init("https://www.nbc.org.kh/english/economic_research/exchange_rate.php");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            $content = curl_exec($ch);
            $value = substr($content, strpos($content,'Official Exchange Rate : <font color="#FF3300">')+47, 4);
            curl_close($ch);
    
            $resultId = DB::table("exchangrate")
            ->insertGetId(
                [   
                    "ExchangeRate" => $value,
                    "Date" =>Carbon::now(),
                ]
            );
            
        }
        public function getVATInfo($ACCT){
            $conn = oci_connect('smart', 'Smart_%002', '10.12.5.191:1526/suseora');
            ini_set('max_execution_time', 0);
            if (!$conn) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
           $stid = oci_parse($conn,"select 
           z.info3 VAT
       from ccare.inf_acct               t1,
       ccare.inf_acct_relation      t2,
       ccare.inf_acct               t4,                                       
       ccare.inf_acct_relation      t5,
       ccare.inf_group_member       t3,
       ccare.inf_customer_all       t6,
       ccare.INF_CREDIT_LIMIT_VALUE x,
       ccare.INF_CREDIT_LIMIT       y,
       ccare.inf_customer_corp      z,
       ccare.inf_group_subscriber   v,
       ccare.INF_OFFERS o,
       ccare.pdm_offer n
       where t1.acct_id = t2.acct_id
       and z.cust_id = t1.cust_id
       and x.CREDIT_LIMIT_SEQ = y.CREDIT_LIMIT_SEQ
       and y.OWNER_ENTITY_ID = t4.ACCT_id
       and x.credit_limit_category='I'
       and t2.sub_id = t3.group_sub_id
       and t4.acct_id = t5.acct_id
       and t5.sub_id = t3.sub_id
       and t4.cust_id = t6.cust_id
       and t3.sub_id=o.sub_id
       and o.OFFER_ID=n.OFFER_ID
       and o.PRIMARY_FLAG='1'
       and t3.exp_date > sysdate
       and t3.member_class = '1'
       and v.sub_id = t3.group_sub_id
       and t4.acct_code='".$ACCT."'");
            oci_execute($stid);
            $count = array();
            $result = [0];
            $result2 = [0];
            while ($row=oci_fetch_array($stid)) {   
                $result['link']=$row[0]; 
            }   
           
            return $result['link'];  
        }
        public function getCompanyName($ACCT){
            $conn = oci_connect('smart', 'Smart_%002', '10.12.5.191:1526/suseora','AL32UTF8');
            ini_set('max_execution_time', 0);
            if (!$conn) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
        $stid = oci_parse($conn,"select  z.cust_name
        
    from ccare.inf_acct               t1,
    ccare.inf_acct_relation      t2,
    ccare.inf_acct               t4,                                       
    ccare.inf_acct_relation      t5,
    ccare.inf_group_member       t3,
    ccare.inf_customer_all       t6,
    ccare.INF_CREDIT_LIMIT_VALUE x,
    ccare.INF_CREDIT_LIMIT       y,
    ccare.inf_customer_corp      z,
    ccare.inf_group_subscriber   v,
    ccare.INF_OFFERS o,
    ccare.pdm_offer n
    where t1.acct_id = t2.acct_id
    and z.cust_id = t1.cust_id
    and x.CREDIT_LIMIT_SEQ = y.CREDIT_LIMIT_SEQ
    and y.OWNER_ENTITY_ID = t4.ACCT_id
    and x.credit_limit_category='I'
    and t2.sub_id = t3.group_sub_id
    and t4.acct_id = t5.acct_id
    and t5.sub_id = t3.sub_id
    and t4.cust_id = t6.cust_id
    and t3.sub_id=o.sub_id
    and o.OFFER_ID=n.OFFER_ID
    and o.PRIMARY_FLAG='1'
    and t3.exp_date > sysdate
    and t3.member_class = '1'
    and v.sub_id = t3.group_sub_id
    and t4.acct_code='".$ACCT."'");
            oci_execute($stid);
            $count = array();
            $result = [0];
            $result2 = [0];
            while ($row=oci_fetch_array($stid)) {   
                $result['link']=$row[0];   
     
            }  
            return $result['link'];  
        }
        public function getFileContent(Request $request){
            ini_set('max_execution_time', 0);
            $cust_info = $request->file('number');
            $cust_info = Excel::toArray( new MsisdnsImport, $cust_info );
            return $cust_info;
        }
    
        public function uploadXml($fileName,$localtionFileName){
    
            $fileName=preg_replace('/.[^.]*$/', '', $fileName);
            $testcontent = Storage::disk('local')->get("DownloadHotBill/".$fileName."."."XML");
            Storage::disk('sftp')->put($localtionFileName.$fileName.".XML",$testcontent);
        
            $tmpfname = tempnam("C:\wamp64\www\batch_operation_portal\storage\app\Tmp", "TMP0");
            $handle = fopen($tmpfname, "a");
            fwrite($handle,$localtionFileName.$fileName.".XML");
            fclose($handle);
            rename($tmpfname,'C:\wamp64\www\batch_operation_portal\storage\app\Tmp\TMP_IN_ONE.tmp');
            $testcontent = Storage::disk('local')->get('Tmp\TMP_IN_ONE.tmp');
            Storage::disk('sftp')->put("/onip/bmbill_mount/bill_nfs/billgen/TMP_FILE/TMP_IN_ONE.tmp",$testcontent);
    
            unlink('C:\wamp64\www\batch_operation_portal\storage\app\Tmp\TMP_IN_ONE.tmp');
            Storage::delete('DownloadHotBill/'.$fileName.".XML");
        }
    
    
        public function downloadingPDF(){
            $testcontent = Storage::disk('sftp')->get('/home/billmgmt/sokheang/XML_PBX/CL/1.16074418_20200501_Master_summary.pdf');
            Storage::disk('local')->put('DownloadPDF/1.16074418_20200501_Master_summary.pdf',$testcontent);
    
        }
        public function getAccount_Code_by_Number($phone_Number){
            $ACCT_CODE=[0];
            $conn = oci_connect('smart', 'Smart_%002', '10.12.5.191:1526/suseora');
            ini_set('max_execution_time', 0);
            if (!$conn) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
    
    
            $stids = oci_parse($conn,"select c.acct_code from ccare.inf_acct_relation a,
            ccare.inf_subscriber_all b,
            ccare.inf_acct c
      where b.sub_id=a.sub_id and c.acct_id=a.acct_id
      and b.exp_date>sysdate
      and a.exp_date>sysdate
      and c.exp_date>sysdate
      and b.msisdn in ('".$phone_Number."')");
    
            oci_execute($stids);
            while ($row=oci_fetch_array($stids)) {   
                $ACCT_CODE=$row;
                return  $ACCT_CODE;
            }  
        }
        public function generatePDF_By_XML($fileName,$localtionFileName,$randDeomFileName){
            $conn = oci_connect('smart', 'Smart_%002', '10.12.5.191:1526/suseora');
            ini_set('max_execution_time', 0);
            if (!$conn) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
            $fileName=preg_replace('/.[^.]*$/', '', $fileName);
            $ssh = new SSH2('10.12.4.137', 22); 
            if (!$ssh->login('billmgmt', 'Billmgmt%001'))   exit('Login Failed'); 
            $ssh->exec('PNetTC  /home/billmgmt/sokheang/Hot_Bill_draft_v0.1.wfd -c /home/billmgmt/sokheang/Normal_PDF_Summary1.job -ItemFlagParamInput 1 -nowarnings -retiw -retadv2 -OutputPathParamInput "'.$localtionFileName.'" -SignatureParamInput "/home/billmgmt/PrintNet" -InputFileParamInput "/onip/bmbill_mount/bill_nfs/billgen/TMP_FILE/TMP_IN_ONE.tmp" -RptFilepathParamInput "/home/billmgmt/sokheang/RPT.751000000000349691" -nowarnings -retiw -retadv2 -LogoPathParamInput /home/billmgmt/PrintNet/Logo/SMART.png');
            $message= $ssh->exec('PNetTC  /home/billmgmt/sokheang/Hot_Bill_draft_v0.1.wfd -c /home/billmgmt/sokheang/Normal_PDF_Summary1.job -ItemFlagParamInput 1 -nowarnings -retiw -retadv2 -OutputPathParamInput "/onip/bmbill_mount/bill_nfs/billgen/PDF_FIle/'.$randDeomFileName.'" -SignatureParamInput "/home/billmgmt/PrintNet" -InputFileParamInput "/onip/bmbill_mount/bill_nfs/billgen/TMP_FILE/TMP_IN_ONE.tmp" -RptFilepathParamInput "/home/billmgmt/sokheang/RPT.751000000000349691" -nowarnings -retiw -retadv2 -LogoPathParamInput /home/billmgmt/PrintNet/Logo/SMART.png');
            return $message;
    
        }
        
         public function create_Group(){
            Storage::disk('sftp')->makeDirectory('/onip/bmbill_mount/bill_nfs/billgen/PDF_FIle');
         }
         public function getExchangeRate(){
            
            $servername = "localhost";
            $username = "root";
            $password = "";
            $db = "batch_operation";
       
            $conn = mysqli_connect($servername, $username, $password,$db)or die("Failed to 
            connect to MySQL: " . mysqli_error());
            if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
            }
            
            $sql = "SELECT ExchangeRate FROM exchangrate where Date='".Carbon::now()->format('Y-m-d')."'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $exchangeRate=$row;
            }
            } else {
            echo "0 results";
            }              
            mysqli_close($conn);
            return $exchangeRate['ExchangeRate'];
            
        }
    
         public function getInvoicesNumber(){
          
            $serverName = "10.12.1.6, 1433";
            $connectionInfo = array( "Database"=>"InvoiceDB", "UID"=>"sa", "PWD"=>"6<#Ks5-bcf3kU8u");
            $conn = sqlsrv_connect( $serverName, $connectionInfo);
            
            if( $conn ) {
    
            }else{
            echo"Connection could not be established.<br />";
            die(print_r( sqlsrv_errors(), true));
            }
    
            // $sql = "select TOP 1 RunningNumber from  [dbo].[v_xml_file_detail] where RunningNumber is not null
            // and RunningNumber not like '%C2020%'
            // order by RunningNumber DESC";
            // dd(Carbon::now()->format('Y-m-d'));
    
            $sql="select top 1 * from [dbo].[RunningNumber]
            where type='B'
            order by number desc";
            
            $stmt = sqlsrv_query( $conn, $sql);
            if( $stmt === false ) {
            die( print_r( sqlsrv_errors(), true));
            }
            $sey = array();
            $final_data = [0];
            while($row =sqlsrv_fetch_array($stmt)) {
            $sey['Txndate'] = $row['Txndate'];
            $sey['UserID'] = $row['UserID'];
            $sey['Type'] = $row['Type'];
            $sey['YYYY'] = $row['YYYY'];
            $sey['MM'] = $row['MM'];
            $sey['DD'] = $row['DD'];
            $sey['Number'] = $row['Number'];
            $sey['IDFile'] = $row['IDFile'];
            $final_data[]= $sey;
            }
            $execute_date_now = Carbon::now();

         
            $result = [0];
            $result1 = [0];
            $result2 = [0];

            $number=($sey['Number']+1);
            $number2= ($sey['IDFile']+1);
            // dd($number."   ".$number2);
            // $stids = oci_parse($conn,"INSERT INTO [dbo].[RunningNumber] (Txndate, UserID, Type, YYYY, MM, DD,Number, IDFile)
            // Values ('".$execute_date_now."', '".$sey['UserID']."', '".$sey['Type']."', '".$sey['YYYY']."',
            // '".$sey['MM']."', '".$sey['DD']."','".$number."', '". $sey['IDFile']."')");
            // oci_execute($stids);
           
            $sql="INSERT INTO [dbo].[RunningNumber] (Txndate, UserID, Type, YYYY, MM, DD,Number, IDFile)
            Values ('".$execute_date_now."', '".$sey['UserID']."', '".$sey['Type']."', '".$sey['YYYY']."',
            '".$sey['MM']."', '".$sey['DD']."','".$number."', '".$number2."')";
            
            $stmt = sqlsrv_query( $conn, $sql);
            if( $stmt === false ) {
            die( print_r( sqlsrv_errors(), true));
            }

            $resultId = DB::table("runningnumber")
            ->insertGetId(
                [   
                    "Txndate" =>  $execute_date_now,
                    "UserID" => $sey['UserID'],
                    "Type" =>  $sey['Type'],
                    "YYYY" => $sey['YYYY'],
                    "MM" =>  $sey['MM'],
                    "DD" => $sey['DD'],
                    "Number" => $number,
                    "IDFile" =>  $number2,
                ]
            );
                  
            $number=$sey['Number']+1;
            if ($number < 10)
            $value = "0000000".$number."";
            else if ($number< 100)
                $value = "000000".$number."";
            else if ($number< 1000)
                $value = "0000".$number."";
            else if ($number< 10000)
                $value = "000".$number."";
            else if ($number< 100000)
                $value = "00".$number."";
            else if ($number< 100000)
                $value = "0".$number."";
            else if ($number<   100000)
                $value = "".$number."";


            return $sey['Type'].Carbon::now()->format('Y').$value;  
         }
    
    // ======================== Modify XML ================================
            public function create_elements($xml,$element_name,$parents){
                $node = $xml->createElement($element_name);
                $parents->appendChild($node);
                return $node;
    
            }
            public function create_value($xml,$value,$parents){
                $value = $xml->createTextNode($value);
                $parents->appendChild($value);
                return $value;
            }
            public function replaceNode($xml,$tageName,$value,$node_old){
                
                $xp = new DOMXPath($xml);
            $fotos = $xp->query("//*[starts-with(local-name(), '".$node_old."')]");
            foreach ( $fotos as $foto ) {
                $path = $foto->nodeValue;
                $foto->parentNode->replaceChild($xml->createElement($tageName,$value), $foto);
            } 
            }
    
            public function InsertNode($xml,$tageName,$root,$number,$value){
            $id=$this->create_elements($xml,$tageName,$root->childNodes[$number]);
            $root->insertBefore($id, $root->childNodes[$number]);
            $this->create_value($xml,$value,$id);
            }
    
            public function ModifyXML($myfile,$exchangeRate,$invoiceNo,$Company_name,$vat){
                libxml_disable_entity_loader(false);
                $xml=new DOMDocument("1.0");
                $xml->load($myfile);
                $root=$xml->getElementsByTagName("BILL_PROP")->item(0);
                
                //======================== Create Good node ============================================
                
                $INVOICE_NO_POSTPAID=$this->create_elements($xml,"INVOICE_NO_POSTPAID",$root);
                $this->create_value($xml,$invoiceNo,$INVOICE_NO_POSTPAID);
    
                $INVOICE_NO_PBX=$this->create_elements($xml,"INVOICE_NO_PBX",$root);
                $this->create_value($xml,"",$INVOICE_NO_PBX);
    
                $EXCHANGE_RATE=$this->create_elements($xml,"EXCHANGE_RATE",$root);
                $this->create_value($xml,$exchangeRate,$EXCHANGE_RATE);
    
                // $root=$xml->getElementsByTagName("CUST_NAME")->item(0);
                // $EXCHANGE_RATE=$this->create_elements($xml,"COMPANY_NAME",$root);
                // $this->create_value($xml,$Company_name,$EXCHANGE_RATE);
    
                // ====================== Insert Good Node
                // $root=$xml->getElementsByTagName("CUSTOM_INFO")->item(0);
                // $this->InsertNode($xml,"New_Node",$root,5,"GOOD INSERT");
    
    
                //======================== Replace Good Node
                $this->replaceNode($xml,"ORG_NAME",$Company_name,"ORG_NAME");
                $this->replaceNode($xml,"VAT_NUMBER",$vat,"VAT_NUMBER");
    
                $xml->formartOutput=true;
                $xml->save($myfile);
                return "Success";  
            } 
            public function WrtieLogs($proName,$status,$timeStart,$timeEnd,$colrelationID,$PDF_Location){
                $resultId = DB::table("tbproccesreport")
                ->insertGetId(
                    [   
                        "ProccesName" =>$proName,
                        "Status" =>$status,
                        "Time_Start" => $timeStart,
                        "Time_end"=> $timeEnd,
                        "colrelationID"=>$colrelationID,
                        "PDF_Location"=>$PDF_Location,
                    ]
                );
        
            }
            public function WrtieLogs_Group($proName,$colrelationID){
                $resultId = DB::table("procces_group")
                ->insertGetId(
                    [   
                        "colralationID" =>$colrelationID,
                        "ProccesName"=>$proName,
                      
                    ]
                );
                return "Success";
            }
            // public function GetBackResultToClinet(){
            //     // dd($this->req['correlation_id']);
            //     $PDF= DB::table('tbproccesreport')
            //     ->select('Status', 'tbproccesreport.ProccesName')
            //     ->where('ProccesName','=','Generate HotBill')
            //     ->paginate(10);
             
            //     if($PDF){
            //         if(($PDF[0]->Status)=='Success'){
                    
            //             return  response()
            //             ->json(['Group PDF Localtion' => '/onip/bmbill_mount/bill_nfs/billgen/PDF_FIle/'.$request->correlation_id,'PDF Localtion' => '/onip/bmbill_mount/bill_nfs/billgen/out/101/0/XML/5/'.Carbon::now()->format('Y-m')."01/",'ColRelationID' => $request->correlation_id
            //             ]);
            //         }
            //     }else{
            //         return "I am querying";
            //     }
            //    return "Error";
            // }


            public function GetBackResultToClinet(Request $request){
                try{
                 $PDF= DB::table('tbproccesreport')
                 ->select('Status', 'tbproccesreport.ProccesName')
                 ->select('PDF_Location')
                 ->where('ProccesName','=','Generate HotBill')
                 ->where('colrelationID','=',$request->correlation_id)
                 ->paginate(10);
                dd($PDF);
                 if($PDF){
                     if(($PDF[0]->Status)=='Success'){
                        return  response()
                             ->json(['Group PDF Localtion' => '/onip/bmbill_mount/bill_nfs/billgen/PDF_FIle/'.$request->correlation_id,
                             'PDF Localtion' => '/onip/invbill_mount/bill_nfs/fmt/BILL/AR/temp2/101/',
                             'ColRelationID' => $request->correlation_id
                             ]);
                     }else{
                         return response()
                         ->json(['Status' => 'Error',
                         ]);
                     }
                 }else{
                     
                     return response()
                         ->json(['Status' => "I am querying",
                         ]);
                 }
                }catch(Exception $e){
            
                     return response()
                         ->json(['Status' => "Hot Bill is not completed",
                         ]);
                }
                return response()
                         ->json(['Status' => "Please, Waiting(5min)...",
                         ]);
                
             }

            public function create_zip($colrelationID)
            {
    
    $pathdir = Storage::disk('sftp')->allFiles('/onip/bmbill_mount/bill_nfs/billgen/PDF_FIle/'.$colrelationID);
    
    
    Storage::disk('local')->makeDirectory($colrelationID);
    for($i=0;$i<count($pathdir);$i++){
        $files_=Storage::disk('sftp')->get($pathdir[$i]);
        $fileName=substr( $pathdir[$i],53,strlen( $pathdir[$i])-41);
        Storage::disk('local')->put($colrelationID.'/'.$fileName,$files_);
    }
    }
    public function Zip($colrelationID){
        $zip_path=Storage::disk('local')->path($colrelationID);
    
    $zipcreated =$colrelationID.".zip"; 
    $zip = new ZipArchive;
    
        if($zip->open(Storage::disk('local')->path($zipcreated),ZipArchive::CREATE)==TRUE){
            $file=File::files($zip_path);
            foreach($file as $key=>$value){
                $relativeName=basename($value);
                $zip->addFile($value,$relativeName);
            }
            $zip->close();
        }     
    }
    public function Test($data){
        $x=1;
        sleep(180);
        $conn = oci_connect('smart', 'Smart_%002', '10.12.5.191:1526/suseora');
            ini_set('max_execution_time', 0);
            if (!$conn) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }  



            $cust_info=$this->QueryData(961169725); 
            $ACCT=$cust_info[0];
            $this->NGBSSService = new NGBSSService($ACCT, "en");
            $Task_ID=$this->NGBSSService->HotBill($ACCT);
        $x++;
    }
    
 
    }


    

?>