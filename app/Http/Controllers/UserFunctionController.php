<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
class UserFunctionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //to view user in permission
    public function index()
    {
        if(!Permission::check('user_function', 'true'))
        {
            return view('permission.index');
        }
        $data['users'] = DB::table('users')
            ->select('name', 'id')
            ->orderBy('id' , 'desc')
            ->paginate(config('app.row'));
        return view('user_functions.index', $data);
    }
    //to group data by id user permission
    public function permission($id)
    {
        if(!Permission::check('user_function', 'true'))
        {
            return view('permission.index');
        }
        $data['permission'] = DB::table('user_functions')
            ->join('users', 'user_functions.user_id', 'users.id')
            ->join('functions', 'user_functions.function_id', 'functions.id')
            ->select('user_functions.*', 'users.name as uname', 'functions.function_name as fname')
            ->where('users.id', $id)
            ->orderBy('id', 'desc')
            ->paginate(config('app.row'));
        // $data['p'] = DB::table('user_functions')
        //     ->join('users', 'user_functions.user_id', 'users.id')
        //     ->join('functions', 'user_functions.function_id', 'functions.id')
        //     ->select('user_functions.*', 'users.name as uname', 'functions.function_name as fname','users.id as uid')
        //     ->where('users.id', $id)
        //     ->first();
        $data['p'] = DB::table('users')
            ->where('id', $id)
            ->first();
        return view('user_functions.permission', $data);
    }

    //To create user permission
    public function create($id)
    {
        $data['per'] = DB::table('user_functions')
            ->where('user_id', $id)
            ->first();
        $data['fs'] = DB::table('functions')
            ->get();
        return view('user_functions.create', $data);
    }

    public function save(Request $r){
        $data = array(
            'user_id' => $r->user_id,
            'function_id' => $r->function_id
        );
        $i = DB::table('user_functions')->insert($data);
        if($i){ 
            $r->session()->flash('success', 'Data has been save!');
            return redirect()->back();
        }
        else{
                $r->session()->flash('eror', 'Fail to save data!');
                return redirect('user/function/permission/create'. $user_id)->withInput();
        }
    }

    //to access and get value from ajax in view.
    public function access(Request $r)
    {
        $i = 0;
        if($r->id>0)
        {
            //update access user
            $data = array(
                'id' => $r->id,
                'active' => $r->active,
            );
            DB::table('user_functions')
                ->where('id', $r->id)
                ->update($data);
            $i = $r->id;
        }
        else{
            //insert into access user
            $data = array(
                'id' => $r->id,
                'status' => $r->status,
            );
            $i = DB::table('user_functions')
                ->insertGetId($data);
        }
        return $i;
    }
}
