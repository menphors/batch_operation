<?php
use App\Jobs\getMutipleDataJob;
use Illuminate\Support\Carbon;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', 'DashboardController@index');
Route::get('/test', 'testController@index');
// Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
// Route::get('/',   'HomeController@index');
Route::get('/home', 'DashboardController@index')->name('home');


Route::get('suboffering', 'ChangeSubOfferingController@index')->name('sub_offering');

//Change Primary Offering
Route::get('changeprimaryoffering', 'ChangeSubOfferingController@home');
Route::post('changeprimaryoffering/save', 'ChangeSubOfferingController@changeUserPrimaryOffering') ///=========================
->name('change_user_primary_offering');

//Hot Bill
Route::get('Hot_Bill', 'HotBillController@index');

// Route::get('Hot_Bill', 'HotBillController@ModifyXML');
Route::post('Hot_Bill/send', 'HotBillController@HotBill_Operatrion')        
->name('Hot_Bill');

Route::get('pdf', 'HotBillController@downloadingPDF');

// Route::post('Hot_Bill/save',function(){
//     $job=(new getMutipleDataJob())->delay(Carbon::now() -> addSeconds(5));
//     dispatch($job);
//     return view('Hot_Bill.index'); 
// })        
// ->name('Hot_Bill');

// Route::post('Hot_Bill/save', 'HotBillController@ModifyXML')
// ->name('Hot_Bill');
// Route::post('Hot_Bill/save', 'HotBillController@SelectDb_Data')
// ->name('Hot_Bill');

//Clear Report
Route::get('Clear_Report', 'ClearReportController@index');
Route::post('Clear_Report/save', 'ClearReportController@ClearReport')
->name('Clear_Report');

//Active Dub
Route::get('activate_sub', 'ActivateSubController@index');
Route::post('activate_sub/save', 'ActivateSubController@ActivateSub')
->name('activate_sub');
//Change Customer Information
Route::get('change_cust_info', 'ChangeCustInfoController@index');
Route::post('change_cust_info/save', 'ChangeCustInfoController@changeCustInfo')
->name('change_cust_info');
//Change EVC
Route::get('change_evc_info', 'ChangeEVCInfoController@index');
Route::post('change_evc_info/save', 'ChangeEVCInfoController@ChangeEVCInfo')
->name('change_evc_info');
//Change Dealer
Route::get('change_dealer_info', 'ChangeDealerInfoController@index');
Route::post('change_dealer_info/save', 'ChangeDealerInfoController@ChangeDealerInfo')
->name('change_dealer_info');
//Deactivate Sub
Route::get('deactivate_sub','DeactivateSubController@index');
Route::post('deactivate_sub/save','DeactivateSubController@DeactivateSub')
->name('deactivate_sub');

//Change PreToPost
Route::get('change_pre_to_post','ChangePreToPostController@index');
Route::post('change_pre_to_post/save','ChangePreToPostController@ChangePreToPost')
->name('change_pre_to_post');

//Change Post To Pre
Route::get('change_post_to_pre', 'ChangePostToPreController@index');
Route::post('change_post_to_pre/save', 'ChangePostToPreController@ChangePostToPre')
->name('change_post_to_pre');

//Change Account Info
// Route::get('change_acct_info', 'ChangeAcctInfoController@index');
// Route::post('change_acct_info/save', 'ChangeAcctInfoController@ChangeAcctInfo')
// ->name('change_acct_info');



//To Do
Route::get('to_do','ChangeSubOfferingController@todo');

//Completed
Route::get('completed','ChangeSubOfferingController@index' );

//User Route
Route::get('user', 'UsersController@index');
Route::get('user/create', 'UsersController@create');
Route::post('user/save', 'UsersController@save');
Route::get('user/delete', 'UsersController@delete');
Route::get('user/edit/{id}', 'UsersController@edit');
Route::post('user/update', 'UsersController@update');
Route::get('user/detail/{id}', 'UsersController@detail');

//User Access Login
Route::post('user/access', 'UsersController@access_user');
Route::get('user/access/view','UsersController@access_user_view');
//User login and Log Out
Route::post('/user/login','UsersController@agentLogin')->name('agent_login');
Route::get('/user/logout','UsersController@agentLogout')->name('agent_logout');
//User Profile login
Route::get('profile/{name}/{id}','UsersController@profile');
//Role
Route::get('role', 'RoleController@index');
Route::get('role/create', 'RoleController@create');
Route::post('role/save', 'RoleController@save');
Route::get('role/delete', 'RoleController@delete');
Route::get('role/edit/{id}', 'RoleController@edit');
Route::post('role/update', 'RoleController@update');

//User Function(permission)
Route::get('user/function', 'UserFunctionController@index');
Route::get('user/function/permission/{id}', 'UserFunctionController@permission');
Route::get('user/function/permission/create/{id}', 'UserFunctionController@create');
Route::post('user/function/permission/save','UserFunctionController@save');

Route::post('user/function/access', 'UserFunctionController@access');


//Permission
Route::get('permission', 'PermissionController@view');
Route::get('permission/create', 'PermissionController@create');
Route::post('permission/save', 'PermissionController@save');
Route::get('permission/delete', 'PermissionController@delete');
Route::get('permission/edit/{id}', 'PermissionController@edit');
Route::post('permission/update', 'PermissionController@update');

//Add Subscriber Offering
Route::get('add_sub_offer', 'AddSubOfferingController@index');
Route::post('add_sub_offer/save', 'AddSubOfferingController@add_sub_offer');


Route::get('customer info report', 'ReportController@index');

//Remove Subcriber Offering
Route::get('remove_sub_offer', 'RemoveSubOfferingController@index');
Route::post('remove_sub_offer/save', 'RemoveSubOfferingController@remove_sub_offer');

//Batch 
// Route::post('batch_payment/query', 'BatchPaymentController@query');
// Route::get('batch_payment/query/testing', 'BatchPaymentController@query_test');
Route::get('batch_payment', 'BatchPaymentController@index');
// Route::get('batch_payment/save', 'BatchPaymentController@save');

//Request Temporary Suspend 
Route::get('request_suspend', 'RequestSuspendController@index');

//Cancel Suspend
Route::get('cancel_suspend', 'CancelSuspendController@index');

//Dashboard
Route::get('/dashboard', 'DashboardController@index');

//Bill Medium
Route::get('change_bill_medium', 'ChangeBillMediumController@index')->name('bill_medium');
Route::post('/suboffering/change_bill_medium', 'ChangeBillMediumController@ChangeBillMedium')
->name('change_bill_medium');
Route::get('/getDataGUI', 'ChangeBillMediumController@getDataGUI');
Auth::routes();


//Route Batch Payment 
Route::get('payment', 'PaymentController@index');
Route::post('payment/request', 'PaymentController@request');

//Route Query Dunning Format 
Route::get('dunning_format', 'DunningFormatController@index');
Route::post('dunning_format/request', 'DunningFormatController@request');


Route::get('suspend/testing', 'SuspendController@getCompanyPhoneNumbeAmountBilling');

//Deactivate Sub
// Route::get('batch_collection', 'ChangeAcctInfoController@index');
// Route::post('batch_collection/save', 'ChangeAcctInfoController@ChangeAcctInfo')
// ->name('batch_collection');


Route::get('change_acct_info', 'ChangeAcctInfoController@index');
Route::post('change_acct_info/save', 'ChangeAcctInfoController@ChangeAcctInfo')
->name('change_acct_info');

//Batch_Collection

Route::get('batch_collection', 'BatchCollectionController@index');
Route::get('excel', 'BatchCollectionController@excel');
Route::post('batch_collection/request', 'BatchCollectionController@save');


//Download

Route::get('/Download', 'ChangeSubOfferingController@download');
Route::get('/testing', 'Customer_info_reportController@index');
//Promise To Pay
Route::get('/ptp', 'PromiseToPayController@index');
Route::get('/get-ptp', 'PromiseToPayController@doPromiseTopay');
Route::post('/do-ptp', 'PromiseToPayController@getInvoice');
Route::get('/get-msisdn/{msisdn}', 'PromiseToPayController@getMsisdn');
Route::get('/batch-ptp', 'BatchPromiseToPayController@index');
Route::post('/do-batch-ptp', 'BatchPromiseToPayController@doPromiseTopay');
Route::get('/batch-get-msisdn', 'BatchPromiseToPayController@getMsisdn');
Route::get('/view-history', 'BatchPromiseToPayController@selectPromiseToPayHistory');
Route::get('/export-Excel', 'BatchPromiseToPayController@excel');
