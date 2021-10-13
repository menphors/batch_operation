<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer_info_report_ extends Model
{
    //
    function index(){
        return $this->hasMany('App\Customer_info_report','batch_number','batch_number');
    }
}
