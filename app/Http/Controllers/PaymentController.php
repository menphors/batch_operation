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
class PaymentController extends Controller
{
    // *Note
    // In request function it's catch the value from blade view and put it in script to query
    // We have set the condition follow by user selection
    // In 1 condition we have 2 scripts for running, 1 for summary data and one more for query data list
    // Script have 2 types are Bill Amount Script and Amount Due Script.
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('batch_payment.payment');
    }
    public function request(Request $r)
    {   
        $conn = oci_connect('smart', 'Smart_%002', '10.12.5.191:1526/suseora');
        ini_set('max_execution_time', 0);
        if (!$conn) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        //Get the value from mthod
        $cash = $r->method;
        if($cash == "1001")
        {
            $method['method_pay'] = "1001";
        }
        else if($cash == "3001")
        {
            $method['method_pay']  = "3001";
        }

        $master_acct = $r->master_acct;
        $remark['comment'] = $r->remark;
        if($r->file('import_file') != NULL)
        {
            $data = $r->file('import_file');
            $data = Excel::toCollection( new MsisdnsImport, $data );
            $number = '';
            // if(!empty($data)){
                for($i=0;$i<count($data[0]) ; $i++){

                    for($j=0;$j<count($data[0][$i]); $j++){
                        $number .= "'".$data[0][$i][$j]."',";
                    }
                }
                $number = substr($number, 0, -1);
                if(!empty($number)){
                    $bill_cycle = $r->bill_cycle;
                    if($bill_cycle == "specific_bill")
                    {
                        $data_date = $r->file('date');
                        $data_date = Excel::toCollection( new MsisdnsImport, $data_date );
                        $insert_date = '';
                        if(!empty($data_date)){
                            for($i=0;$i<count($data_date[0]) ; $i++){

                                for($j=0; $j<count($data_date[0][$i]); $j++){
                                    $insert_date .= "'".$data_date[0][$i][$j]."',";
                                }
                            }
                            $insert_date = substr($insert_date, 0, -1);
                            if(!empty($insert_date))
                            {
                                //Bill_Amount Script
                                $stid = oci_parse($conn, "
                                select t6.cust_name,t2.acct_code as Master_acct,count(t.service_number) numbers_line,sum(t7.open_amt/100000000) Total_Out
                                from ccare.inf_group_member      t,
                                    ccare.inf_group_subscriber  t1,
                                    ccare.inf_acct              t2,
                                    ccare.inf_acct_relation     t3,
                                    ccare.inf_acct              t4,
                                    ccare.inf_acct_relation     t5,
                                    ccare.inf_customer_Corp     t6,
                                    ardb_1601.ar_invoice@edrdb1 t7
                                where t.group_sub_id = t1.sub_id
                                    and t.member_class = '1'
                                    and t1.group_type = '1'
                                    and t2.acct_id = t3.acct_id
                                    and t1.sub_id = t3.sub_id
                                    and t4.acct_id = t5.acct_id
                                    and t.sub_id = t5.sub_id
                                    and t1.cust_id = t6.cust_id
                                    and t.service_number = t7.pri_identity
                                    and t5.exp_date > sysdate
                                    and t.service_number not in (".$number.")
                                    and t7.bill_cycle_id in (".$insert_date.")
                                    and t2.acct_code='".$master_acct."'
                                    group by t6.cust_name,
                                    t2.acct_code 
                                   ");
                                oci_execute($stid);
                                $total_data = array();
                                $final_data = [];
                                while ($row = oci_fetch_assoc($stid)) {
                                    $total_data['cust_name'] = $row['CUST_NAME'];
                                    $total_data['Master_acct'] = $row['MASTER_ACCT'];
                                    $total_data['numbers_line'] = $row['NUMBERS_LINE'];
                                    $total_data['Total_Out'] = $row['TOTAL_OUT'];
                                    $final_data[] = $total_data;
                                }
                                //Bill Amount Data Script
                                $stids = oci_parse($conn, "
                            select t6.cust_name,
                                t.service_number,
                                t4.acct_code     Member_acct,
                                t2.acct_code     Master_acct,
                                t7.invoice_amt/100000000 Bill_AMT,
                                t7.Open_amt/100000000 Open_AMT,
                                t7.bill_cycle_id
                            from ccare.inf_group_member      t,
                                ccare.inf_group_subscriber  t1,
                                ccare.inf_acct              t2,
                                ccare.inf_acct_relation     t3,
                                ccare.inf_acct              t4,
                                ccare.inf_acct_relation     t5,
                                ccare.inf_customer_Corp     t6,
                                ardb_1601.ar_invoice@edrdb1 t7
                            where t.group_sub_id = t1.sub_id
                                and t.member_class = '1'
                                and t1.group_type = '1'
                                and t2.acct_id = t3.acct_id
                                and t1.sub_id = t3.sub_id
                                and t4.acct_id = t5.acct_id
                                and t.sub_id = t5.sub_id
                                and t1.cust_id = t6.cust_id
                                and t.service_number = t7.pri_identity
                                and t5.exp_date > sysdate
                                and t.service_number not in (".$number.")
                                and t7.bill_cycle_id in (".$insert_date.")
                                and t2.acct_code='".$master_acct."'
                                ");
                                oci_execute($stids);
                                $data_file = array();
                                $final_data_file = [];
                                while ($row = oci_fetch_assoc($stids)) {
                                    $data_file['cust_name'] = $row['CUST_NAME'];
                                    $data_file['service_number'] = $row['SERVICE_NUMBER'];
                                    $data_file['Member_acct'] = $row['MEMBER_ACCT'];
                                    $data_file['Master_acct'] = $row['MASTER_ACCT'];
                                    $data_file['Bill_AMT'] = $row['BILL_AMT'];
                                    $data_file['Open_AMT'] = $row['OPEN_AMT'];
                                    $data_file['bill_cycle_id'] = $row['BILL_CYCLE_ID'];
                                    $final_data_file[] = $data_file;
                                }                                      
                                //  $final_data['total_display']
                                //Data Script
                                return view('batch_payment.payment_result', $remark, $method)->with('total_display', $final_data)->with('total_file_display', $final_data_file);
                            }else{
                                $r->session()->flash('fail', 'Bill Data is Empty');
                                return redirect('payment')->withInput();
                            }
                        }else{
                            $r->session()->flash('fail', 'Bill Date Excel is Null');
                            return redirect('payment')->withInput();
                        }
                        //Amount Due Scipt
                    }else if($bill_cycle == "all_bill"){
                        $stid = oci_parse($conn, "
                        select t6.cust_name,t2.acct_code as Master_acct,count(t.service_number) As numbers_line,sum(t8.open_amt/100000000) as Total_out
                        from ccare.inf_group_member      t,
                            ccare.inf_group_subscriber  t1,
                            ccare.inf_acct              t2,
                            ccare.inf_acct_relation     t3,
                            ccare.inf_acct              t4,
                            ccare.inf_acct_relation     t5,
                            ccare.inf_customer_Corp     t6,
                            ardb_1601.ar_account_balance@EDRDB1 t8
                        where t.group_sub_id = t1.sub_id
                            and t.member_class = '1'
                            and t1.group_type = '1'
                            and t2.acct_id = t3.acct_id
                            and t1.sub_id = t3.sub_id
                            and t4.acct_id = t5.acct_id
                            and t.sub_id = t5.sub_id
                            and t1.cust_id = t6.cust_id
                            and t4.acct_code=t8.acct_code
                            and t5.exp_date > sysdate
                            and t2.acct_code='".$master_acct."'
                            and t.service_number not in (".$number.")
                            group by t6.cust_name,
                            t2.acct_code
                           ");
                        oci_execute($stid);
                        $total_data = array();
                        $final_data = [];
                        while ($row = oci_fetch_assoc($stid)) {
                            $total_data['cust_name'] = $row['CUST_NAME'];
                            $total_data['Master_acct'] = $row['MASTER_ACCT'];
                            $total_data['numbers_line'] = $row['NUMBERS_LINE'];
                            $total_data['Total_Out'] = $row['TOTAL_OUT'];
                            $final_data[] = $total_data;
                        }
                        //Amount Due Data Script
                        $stids = oci_parse($conn, "
                    select t6.cust_name,
                        t.service_number,
                        t4.acct_code     Member_acct,
                        t2.acct_code     Master_Acct,
                        t8.Open_amt/100000000 Open_AMT
                    from ccare.inf_group_member      t,
                        ccare.inf_group_subscriber  t1,
                        ccare.inf_acct              t2,
                        ccare.inf_acct_relation     t3,
                        ccare.inf_acct              t4,
                        ccare.inf_acct_relation     t5,
                        ccare.inf_customer_Corp     t6,
                        ardb_1601.ar_account_balance@EDRDB1 t8
                    where t.group_sub_id = t1.sub_id
                        and t.member_class = '1'
                        and t1.group_type = '1'
                        and t2.acct_id = t3.acct_id
                        and t1.sub_id = t3.sub_id
                        and t4.acct_id = t5.acct_id
                        and t.sub_id = t5.sub_id
                        and t1.cust_id = t6.cust_id
                        and t4.acct_code=t8.acct_code
                        and t5.exp_date > sysdate
                        and t2.acct_code='".$master_acct."'
                        and t.service_number not in (".$number.")
                        ");
                        oci_execute($stids);
                        $data_file = array();
                        $final_data_file = [];
                        while ($row = oci_fetch_assoc($stids)) {
                            $data_file['cust_name'] = $row['CUST_NAME'];
                            $data_file['service_number'] = $row['SERVICE_NUMBER'];
                            $data_file['Member_acct'] = $row['MEMBER_ACCT'];
                            $data_file['Master_acct'] = $row['MASTER_ACCT'];
                            $data_file['Open_AMT'] = $row['OPEN_AMT'];
                            $final_data_file[] = $data_file;
                        }
                        //Data Script
                        return view('batch_payment.payment_result', $remark,$method)->with('total_display', $final_data)->with('total_file_display', $final_data_file);
                    }else{
                        $r->session()->flash('fail', 'You Must Select Bill Cycle!');
                        return redirect('payment')->withInput();
                    }
                }else{
                $r->session()->flash('fail', 'Your Exclude Number is Empty');
                return redirect('payment')->withInput();
            }
        }
        else{
            $bill_cycle = $r->bill_cycle;
            if($bill_cycle == "specific_bill")
            {
                $data_date = $r->file('date');
                $data_date = Excel::toCollection( new MsisdnsImport, $data_date );
                $insert_date = '';
                if(!empty($data_date)){
                    for($i=0;$i<count($data_date[0]) ; $i++){
                        for($j=0; $j<count($data_date[0][$i]); $j++){
                            $insert_date .= "'".$data_date[0][$i][$j]."',";
                        }
                    }
                    $insert_date = substr($insert_date, 0, -1);
                    if(!empty($insert_date))
                    {
                        //Bill Amount Script
                        $stid = oci_parse($conn, "
                        select t6.cust_name,t2.acct_code as Master_acct,count(t.service_number) numbers_line,sum(t7.open_amt/100000000) Total_Out
                        from ccare.inf_group_member      t,
                            ccare.inf_group_subscriber  t1,
                            ccare.inf_acct              t2,
                            ccare.inf_acct_relation     t3,
                            ccare.inf_acct              t4,
                            ccare.inf_acct_relation     t5,
                            ccare.inf_customer_Corp     t6,
                            ardb_1601.ar_invoice@edrdb1 t7
                        where t.group_sub_id = t1.sub_id
                            and t.member_class = '1'
                            and t1.group_type = '1'
                            and t2.acct_id = t3.acct_id
                            and t1.sub_id = t3.sub_id
                            and t4.acct_id = t5.acct_id
                            and t.sub_id = t5.sub_id
                            and t1.cust_id = t6.cust_id
                            and t.service_number = t7.pri_identity
                            and t5.exp_date > sysdate
                            and t7.bill_cycle_id in (".$insert_date.")
                            and t2.acct_code='".$master_acct."'
                            group by t6.cust_name,
                            t2.acct_code
                           ");
                        oci_execute($stid);
                        $total_data = array();
                        $final_data = [];
                        while ($row = oci_fetch_assoc($stid)) {
                            $total_data['cust_name'] = $row['CUST_NAME'];
                            $total_data['Master_acct'] = $row['MASTER_ACCT'];
                            $total_data['numbers_line'] = $row['NUMBERS_LINE'];
                            $total_data['Total_Out'] = $row['TOTAL_OUT'];
                            $final_data[] = $total_data;
                        }
                        //Bill Amount Data Script
                        $stids = oci_parse($conn, "
                        select t6.cust_name,
                            t.service_number,
                            t4.acct_code     Member_acct,
                            t2.acct_code     Master_acct,
                            t7.invoice_amt/100000000 Bill_AMT,
                            t7.Open_amt/100000000 Open_AMT,
                            t7.bill_cycle_id
                        from ccare.inf_group_member      t,
                            ccare.inf_group_subscriber  t1,
                            ccare.inf_acct              t2,
                            ccare.inf_acct_relation     t3,
                            ccare.inf_acct              t4,
                            ccare.inf_acct_relation     t5,
                            ccare.inf_customer_Corp     t6,
                            ardb_1601.ar_invoice@edrdb1 t7
                        where t.group_sub_id = t1.sub_id
                            and t.member_class = '1'
                            and t1.group_type = '1'
                            and t2.acct_id = t3.acct_id
                            and t1.sub_id = t3.sub_id
                            and t4.acct_id = t5.acct_id
                            and t.sub_id = t5.sub_id
                            and t1.cust_id = t6.cust_id
                            and t.service_number = t7.pri_identity
                            and t5.exp_date > sysdate
                            and t7.bill_cycle_id in (".$insert_date.")
                            and t2.acct_code='".$master_acct."'
                        ");
                        oci_execute($stids);
                        $data_file = array();
                        $final_data_file = [];
                        while ($row = oci_fetch_assoc($stids)) {
                            $data_file['cust_name'] = $row['CUST_NAME'];
                            $data_file['service_number'] = $row['SERVICE_NUMBER'];
                            $data_file['Member_acct'] = $row['MEMBER_ACCT'];
                            $data_file['Master_acct'] = $row['MASTER_ACCT'];
                            $data_file['Bill_AMT'] = $row['BILL_AMT'];
                            $data_file['Open_AMT'] = $row['OPEN_AMT'];
                            $data_file['bill_cycle_id'] = $row['BILL_CYCLE_ID'];
                            $final_data_file[] = $data_file;
                        }
                        //Data Script
                        return view('batch_payment.payment_result', $remark,$method)->with('total_display', $final_data)->with('total_file_display', $final_data_file);
                    }else{
                        $r->session()->flash('fail', 'Bill Data is Empty');
                        return redirect('payment')->withInput();
                    }
                }else{
                    $r->session()->flash('fail', 'Bill Date Excel is Null');
                    return redirect('payment')->withInput();
                }
            }else if($bill_cycle == "all_bill"){
                //Query Amount Due Script of Summary data to table
                $stid = oci_parse($conn, "
                select t6.cust_name,t2.acct_code as Master_acct,count(t.service_number) As numbers_line,sum(t8.open_amt/100000000) as Total_out
                    from ccare.inf_group_member      t,
                    ccare.inf_group_subscriber  t1,
                    ccare.inf_acct              t2,
                    ccare.inf_acct_relation     t3,
                    ccare.inf_acct              t4,
                    ccare.inf_acct_relation     t5,
                    ccare.inf_customer_Corp     t6,
                    ardb_1601.ar_account_balance@EDRDB1 t8
                where t.group_sub_id = t1.sub_id
                    and t.member_class = '1'
                    and t1.group_type = '1'
                    and t2.acct_id = t3.acct_id
                    and t1.sub_id = t3.sub_id
                    and t4.acct_id = t5.acct_id
                    and t.sub_id = t5.sub_id
                    and t1.cust_id = t6.cust_id
                    and t4.acct_code=t8.acct_code
                    and t5.exp_date > sysdate
                    and t2.acct_code='".$master_acct."'
                    group by t6.cust_name,
                        t2.acct_code
                   ");
                oci_execute($stid);
                $total_data = array();
                $final_data = [];
                while ($row = oci_fetch_assoc($stid)) {
                    $total_data['cust_name'] = $row['CUST_NAME'];
                    $total_data['Master_acct'] = $row['MASTER_ACCT'];
                    $total_data['numbers_line'] = $row['NUMBERS_LINE'];
                    $total_data['Total_Out'] = $row['TOTAL_OUT'];
                    $final_data[] = $total_data;
                }
                //Data Script List Format of Amount Due CSV/UNL
                $stids = oci_parse($conn, "
                select t6.cust_name,
                    t.service_number,
                    t4.acct_code     Member_acct,
                    t2.acct_code     Master_Acct,
                    t8.Open_amt/100000000 Open_AMT
                from ccare.inf_group_member      t,
                    ccare.inf_group_subscriber  t1,
                    ccare.inf_acct              t2,
                    ccare.inf_acct_relation     t3,
                    ccare.inf_acct              t4,
                    ccare.inf_acct_relation     t5,
                    ccare.inf_customer_Corp     t6,
                    ardb_1601.ar_account_balance@EDRDB1 t8
                where t.group_sub_id = t1.sub_id
                    and t.member_class = '1'
                    and t1.group_type = '1'
                    and t2.acct_id = t3.acct_id
                    and t1.sub_id = t3.sub_id
                    and t4.acct_id = t5.acct_id
                    and t.sub_id = t5.sub_id
                    and t1.cust_id = t6.cust_id
                    and t4.acct_code=t8.acct_code
                    and t5.exp_date > sysdate
                    and t2.acct_code='".$master_acct."'
                ");
                oci_execute($stids);
                $data_file = array();
                $final_data_file = [];
                while ($row = oci_fetch_assoc($stids)) {
                    $data_file['cust_name'] = $row['CUST_NAME'];
                    $data_file['service_number'] = $row['SERVICE_NUMBER'];
                    $data_file['Member_acct'] = $row['MEMBER_ACCT'];
                    $data_file['Master_acct'] = $row['MASTER_ACCT'];
                    $data_file['Open_AMT'] = $row['OPEN_AMT'];
                    $final_data_file[] = $data_file;
                }
                //Data Script
                return view('batch_payment.payment_result', $remark,$method)->with('total_display', $final_data)->with('total_file_display', $final_data_file);
            }else{
                $r->session()->flash('fail', 'You Must Input Bill Cycle');
                return redirect('payment');
            }
        }
    }
}
