<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
class SuspendController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected $phoneNumber;
    protected $NGBSSService;
    
     
    public function index(){
        return view('request_suspend.index');
        
    }
  
    public function getCompanyPhoneNumbeAmountBilling(){
        
              $conn = oci_connect('SMART', 'Smart_%002', '10.12.5.191/suseora');
        //   $conn = oci_connect('ccare', 'CRM%ccare01', '10.12.8.37/suseora');
		if (!$conn) {
            $e = oci_error();
            Log::info('Oracle Connection failed'. $e['code'] . ';' . $e['message']);
            return 0;
			//die('Connection failed; db_class.php; '. $e['code'] . '; ' . $e['message']);
		} else {
            // success 
            $sql = "select distinct count (1) as amount
            from
            ccare.inf_group_member gm,
            ccare.inf_group_subscriber gs,
            ccare.inf_acct_relation r,
            ccare.inf_acct a,
            ccare.inf_customer_corp c,
            ccare.inf_subscriber_all b1,
            ccare.inf_acct_relation b2,
            ccare.inf_acct b3
            where b1.sub_id=b2.sub_id
            and b2.acct_id=b3.acct_id
            and b1.msisdn=gm.service_number
            and b1.exp_date>sysdate
            and b2.exp_date>sysdate
            and b3.exp_date>sysdate
            and gm.group_sub_id = gs.sub_id
            and gs.sub_id = r.sub_id
            and r.acct_id = a.acct_id
            and a.cust_id = c.cust_id
            and r.exp_date > sysdate
            and gs.group_type='1'
            and gm.exp_date > sysdate
            and gs.exp_date > sysdate
            and gm.service_number is not null
            and c.cust_id = '1010053990918' ";
                
            //echo $sql;
$stid = oci_parse($conn, $sql);
oci_execute($stid);

if ($stid >= 1) {
	echo //"BillCycle: $BillCycle".
	       '<table width="50%" border="1" style="border:1px solid #000033;border-collapse:collapse;"><tbody>
			<tr align="center" bgcolor="#1eed15" class="td_head">				
				<td width="12%" style="color:#000033;border:1px solid #000033;">AMOUNT</td>
			</tr>';

    // output data of each row
    while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
        echo
			'</td style="color:#000033;border:1px solid #000033;"><td>' . $row["AMOUNT"].
			'</td></tr>';
	        }
	    echo "</tbody></table>";
} else {
	    echo "<h2>> NO Data Found :(</h2><br>";
	    echo "Contact Billing Postpaid!!!";
        }
        oci_free_statement($stid);
        oci_close($conn);
        }
   }
}
