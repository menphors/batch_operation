<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Connector extends Controller
{
    public $conn = null;
	private $conn_str = "(DESCRIPTION =
													(SDU=8192)
													(TDU=8192)
														(ADDRESS_LIST =
															(ADDRESS = (PROTOCOL = TCP)(HOST = 10.12.1.5)(PORT = 1521))
														)
													(CONNECT_DATA =
													(SERVICE_NAME = dbar)
													)
												)";
  
	public function __construct()
	{
        // $this->conn = oci_connect('smart', 'Smart_%002', '10.12.5.191:1526/suseora');
        // $this->connbmp = oci_connect('MEN_PHORS', 'PhDbsmart$#12345', '10.12.36.133:1536/ora12c');
        $this->conninhouse = oci_connect('CRM_INHOUSE', 'Crmsupport@20210224', '10.12.5.191:1526/suseora');
        
        if(!$this->conninhouse){
            $e = oci_error();
                    //var_dump($e);
                    die('Connection failed; DbConn.php; '. $e['code'] . '; ' . $e['message']);
        }
        // if (!$this->conn) {
        //             $e = oci_error();
        //             //var_dump($e);
        //             die('Connection failed; DbConn.php; '. $e['code'] . '; ' . $e['message']);
        // }

        // if(!$this->connbmp){
        //     $e = oci_error();
        //             //var_dump($e);
        //             die('Connection failed; DbConn.php; '. $e['code'] . '; ' . $e['message']);
        // }
        
	}

    public function exec_non_qry() {
        $sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'DD.MM.YY HH24:MI:SS'";
		$stid = oci_parse($this->conn, $sql);
		return oci_execute($stid);
	}

    public function mysql_conn(){
        $conn = mysqli_connect("localhost", "root", "","batch_operation");
        return $conn;
    }
    /*
    * param string $url
    * return mixed
    */
    public function access_content($msisdn,$invoiceNo,$aggreedPayDate){
        $curl = curl_init();
        $url='http://10.12.36.130:8080/services/DcPaServices';
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                'SOAPAction: Adjustment',
                'Content-Type: text/xml;charset=UTF-8'),
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
            xmlns:bmp="http://cbs.huawei.com/dc/wsservice/bmpinterface"
            xmlns:cbs="http://www.huawei.com/bme/cbsinterface/cbscommon">
<soapenv:Header/>
                <soapenv:Body>
                    <bmp:CreatePARequestMsg>
                        <RequestHeader>
                            <cbs:Version>1</cbs:Version>
                            <!--Optional:-->
                            <cbs:BusinessCode>CreatePA</cbs:BusinessCode>
                            <cbs:MessageSeq>'.time().'</cbs:MessageSeq>
                            <!--Optional:-->
                            <cbs:OwnershipInfo>
                                <cbs:BEID>101</cbs:BEID>
                                <!--Optional:-->
                            </cbs:OwnershipInfo>
                            <cbs:AccessSecurity>
                                <cbs:LoginSystemCode>hissam</cbs:LoginSystemCode>
                                <cbs:Password>Q9nmbqOehHWjysXzmQiabeI0Saa2V3WHEsw+xF7PnxM=</cbs:Password>
                            </cbs:AccessSecurity>
                        </RequestHeader>
                        <CreatePARequest>
                            <bmp:AcctAccessCode>
                                <!--You have a CHOICE of the next 3 items at this level-->
                                <bmp:PrimaryIdentity>'.$msisdn.'</bmp:PrimaryIdentity>
                            </bmp:AcctAccessCode>
                            <!--Optional:-->
                            <bmp:PAExternalID>?</bmp:PAExternalID>
                            <bmp:PAType>1</bmp:PAType>
                            <bmp:PARequestDate>'.date("Ymd").'000000</bmp:PARequestDate>
                            <!--1 or more repetitions:-->
                            <bmp:PADetailInfo>
                                <bmp:AgreedPaidDate>'.$aggreedPayDate.'000000</bmp:AgreedPaidDate>
                                <!--Optional:-->
                                <bmp:CurrencyId>1153</bmp:CurrencyId>
                            </bmp:PADetailInfo>
                            <!--Zero or more repetitions:-->
                            <bmp:PAInvoiceInfo>
                                <bmp:InvoiceNo>'.$invoiceNo.'</bmp:InvoiceNo>
                            </bmp:PAInvoiceInfo>
                            <bmp:PAReminderFlag>N</bmp:PAReminderFlag>
                        </CreatePARequest>
                    </bmp:CreatePARequestMsg>
                </soapenv:Body>
            </soapenv:Envelope>',));
            //Convert XML to JSON
            $response = curl_exec($curl);
        
          return $response;
            //return $response;
    }
}