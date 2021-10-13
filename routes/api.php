<?php

use Illuminate\Http\Request;
use App\Jobs\getMutipleDataJob;
use Illuminate\Support\Carbon;
use App\Services\HotBill;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('Hot_Bill/save',function(Request $request){
    // $job=(new getMutipleDataJob($request->all()))->delay(Carbon::now()-> addSeconds(10));
    // // $job=(new getMutipleDataJob($request->all()));
    // dispatch($job);
    $msisdn = Input::get('msisdn');
    $correlation_id = Input::get('correlation_id');
    $HotBill = new HotBill();
    $HotBill -> HotBill_Operatrion($msisdn, $correlation_id);
    return  response()
    ->json(['Status' => 'Success',
    'File Sent' => $request->all()
    ]);
});
Route::post('Hot_Bill/getStatus', 'HotBillController@GetBackResultToClinet')->name('getStatus');
Route::post('Hot_Bill/getStatus', function(Request $request){
    (new HotBillController())->GetBackResultToClinet($request->all());
});