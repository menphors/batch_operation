<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer_info_report_;
class Customer_info_reportController extends Controller
{
    //
    function index(){
        return Customer_info_report_::find(1)->index;
    }

}
