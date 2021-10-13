<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Jobs\getMutipleDataJob;
use Illuminate\Support\Carbon;

class testController extends Controller
{
    //
  
    public function index(){
        $this->writeFile();
        
    }
    public function writeFile(Request $request){
        dd($request['correlation_id']);
        $job=((new getMutipleDataJob()))->delay(Carbon::now() -> addSeconds(5));
        dispatch($job,$request);
        dd("Success");
    }
}
