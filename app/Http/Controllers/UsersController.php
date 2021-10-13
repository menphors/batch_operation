<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Exception;
use Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash; 
use Unirest; 
use Helper;

class UsersController extends Controller
{

    //For Redirect Login for User who didn't login yet
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    //For Log out
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    //to show the view list of user
    public function index()
    {
        if(!Permission::check('user', 'true'))
        {
            return view('permission.index');
        }
        $data['users'] = DB::table('users')
            ->orderBy('id', 'desc')
            ->paginate(config('app.row'));
        return view('users.index', $data);
    }

    //To show view Create for User
    public function create()
    {
        if(!Permission::check('user', 'true'))
        {
            return view('permission.index');
        }
        $data['rs'] = DB::table('roles')
            ->get();
        return view('users.create', $data);
    }

    //To Save the data for user in action
    public function save(Request $r)
    {
        $data = array(
            'name' => $r->name,
            'email' => $r->email,
            'phone_number' => $r->phone_number,
            'position' => $r->position,
            'location' => $r->location,
            'created_by' => Auth::user()->name,
            'role_id' => $r->role_id
        );
        $data2 = array(
            'name' => $r->name
        );
        DB::table('access_users')->insert($data2);
        $i = DB::table('users')->insert($data);
        if($i)
        {
            $r->session()->flash('success', 'Data has been saved!');
            return redirect('user/create');
        }
        else{
            $r->session()->flash('error', 'Fail to save data!');
            return redirect('user/create')->withInput();
        }
    }

    //To delete user in view
    public function delete(Request $r){
        DB::table('users')
            ->where('id', $r->id)
            ->delete();
        $r->session()->flash('success', 'Data has been removed!');
        return redirect('user');
    } 
    
    //To Edit user 
    public function edit($id)
    {
        if(!Permission::check('user', 'true'))
        {
            return view('permission.index');
        }
        $data['rs'] = DB::table('roles')
        ->get();
        $data['user'] = DB::table('users')
            ->where('id', $id)
            ->first();
        return view('users.edit', $data);
    }

    //To update the data in user
    public function update(Request $r)
    {
        $data = array(
            'name' => $r->name,
            'email' => $r->email,
            'phone_number' => $r->phone_number,
            'position' => $r->position,
            'location' => $r->location,
            'role_id' => $r->role_id
        );
        $data2 = array (
            'name' => $r->name
        );
        DB::table('access_users')->where('id', $r->id)->update($data2);
        $i = DB::table('users')
            ->where('id', $r->id)
            ->update($data);
        if($i)
        {
            $r->session()->flash('success', 'Data has been saved!');
            return redirect('user/edit/'. $r->id);
        }
        else{
            $r->session()->flash('error', 'Fail to save data!');
            return redirect('user/edit/'. $r->id)->withInput();
        }
    }

    //to show about all information for user it called detail 
    public function detail($id)
    {
        if(!Permission::check('user', 'true'))
        {
            return view('permission.index');
        }
        $data['user_detail'] = DB::table('users')
        ->join('roles', 'users.role_id', 'roles.id')
        ->select('users.*', 'roles.name as rname')
        ->where('users.id', $id)
        ->first();
    return view('users.detail', $data);
    }

    //Read data from table Access user 
    public function access_user_view()
    {   
        if(!Permission::check('userlogin', 'true'))
        {
            return view('permission.index');
        }
        $data['users'] = DB::table('access_users')
        ->orderBy('id', 'desc')
        ->get();
        return view('users.userlogin', $data);
    }

    //to save Access user from view(Check Box )
    public function access_user(Request $r)
    {
        $i = 0;
        if($r->id>0)
        {
            //update access user
            $data = array(
                'id' => $r->id,
                'status' => $r->status
            );
            DB::table('access_users')
                ->where('id', $r->id)
                ->update($data);
            $i = $r->id;
        }
        else{
            //insert into access user
            $data = array(
                'id' => $r->id,
                'status' => $r->status
            );
            $i = DB::table('access_users')
                ->insertGetId($data);
        }
        return $i;
    }


    public function updateUser(Request $request){

        $password = $request->password;

        if($password == null){
            DB::table('users')->where('id',$request->userId)->update([
                'name' => $request->username,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'position' => $request->position,
                'user_type' => $request->user_type,
                'location_id' => $request->location_id,
                ]);

        }else{
            $password = $password->trim();

            if($password == ""){
                DB::table('users')->where('id',$request->userId)->update([
                    'name' => $request->username,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'position' => $request->position,
                    'user_type' => $request->user_type,
                    'location_id' => $request->location_id,
                    ]);
            }else{
                DB::table('users')->where('id',$request->userId)->update([
                    'name' => $request->username,
                    'email' => $request->email,
                    'password' => $request->password,
                    'phone_number' => $request->phone_number,
                    'position' => $request->position,
                    'user_type' => $request->user_type,
                    'location_id' => $request->location_id,
                    ]);
            }
        }
        return redirect('/user');
    }


    public function agentLogin(Request $request){
      
        $results = DB::table('access_users')
        ->where('status', true)
        ->select('name')
        ->get();
        
        $listIfUsers = [];

        foreach($results  as $key => $value ){
            $listIfUsers[$key] = $value->name;
        } 
 
        

        if(in_array($request->username, $listIfUsers)){
            
            

            //$headers = array('Authorization'=>'Bearer '. $this->getAccessToken()->access_token ,
            $headers = array('Authorization'=>'Bearer b6f3ee19-f696-3f6f-8ff3-cc94131ea012',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json');
        
            $data = array('username' => $request->username,
                            'password'  =>  $request->password ,
            );
             
            $body = Unirest\Request\Body::json($data);
    
            $baseUrl = "https://single-signon.smart.com.kh/public/";
            $baseUrlLocal = "http://localhost:3000/";
            
    
            $response = Unirest\Request::post($baseUrl .'api/ldap/authenticate',$headers ,  $body);
           
        
    
             if($response->body->code == 200){
                $ldapUser = $response->body->user;
                $user = DB::table('users')->where('name','=',$ldapUser->username)->first();
                
                if($user){
                    DB::table('users')
                        ->where('name', $ldapUser->username)
                        ->update(['phone_number' =>  $ldapUser->phone_number,
                                  'position' =>  $ldapUser->title,
                                  'location' =>  $ldapUser->location,
                                 
                        ]);
                }else{
                    
                    DB::table('users')
                    ->insert(['name' =>  $ldapUser->username,
                              'email' =>  $ldapUser->email,
                              'phone_number' =>  $ldapUser->phone_number,
                              'position' =>  $ldapUser->title,
                              'location' =>  $ldapUser->location,
                              'status' =>  1,
                    ]);
                   
                   
                }
    
                
                   // dd($ldapUser->username);
             
                if(Auth::attempt(['name' => $ldapUser->username,'password'=>  'BelieveInYourself' ,'status' => 1])){
                    return redirect()->intended('home');
                 }else{
                    return view('home',['error'=>1]);
                 } 
                
             }else if($response->body->code == 401){
    
                return view('home',['error'=>1]);
             }    
        }else{ 
            
            return redirect()->back();
        }

       

    }


    public function getAccessToken(){
        $headers = array('Authorization'=>'Basic dkFJUU5Wd2J5alZqOGZNOU0xOHZ4UXA4YVJBYTpzZm4xTlc1SThiNU1aTnVoNWlmZnpLUHN3QXdh',
                         'Content-Type' => 'application/x-www-form-urlencoded');
        //$data = array('grant_type' => 'client_credentials', 'username' => 'smartpayways','password' => 'Penlymeng123','Scope' => 'PRODUCTION');
        $data = array('grant_type' => 'client_credentials');
        $body = Unirest\Request\Body::form($data);
    
        $response = Unirest\Request::post('https://mife.smart.com.kh:8243/token',$headers ,  $body);
     
        if($response->code === 200){
            return  $response->body; 
        }
    
       
    }

    public function welcome(Request $request){
        if(Auth::check()){
            return redirect('/agent');
        }else{
            return view('welcome',['error'=>0]);
        }
         

    }

    //Profile User login
    public function profile($id)
    {
        $id = Auth::user()->id;
        $data['profile'] = DB::table('users')
            ->where('id', $id)
            ->first();
        return view('users.profile', $data);
    }

}
