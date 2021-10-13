<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view()
    {
        $data['functions'] = DB::table('functions')
            ->orderBy('id', 'desc')
            ->paginate(config('app.row'));
        return view('permission.view', $data);
    }

    //To show view Create for Permission
    public function create()
    {
        return view('permission.create');
    }

    //To Save the data for permission in action
    public function save(Request $r)
    {
        $data = array(
            'path' => $r->path,
            'function_name' => $r->function_name
        );
        $i = DB::table('functions')->insert($data);
        if($i)
        {
            $r->session()->flash('success', 'Data has been saved!');
            return redirect('permission/create');
        }
        else{
            $r->session()->flash('error', 'Fail to save data!');
            return redirect('permission/create')->withInput();
        }
    }

    //To delete permission in view
    public function delete(Request $r){
        DB::table('functions')
            ->where('id', $r->id)
            ->delete();
        $r->session()->flash('success', 'Data has been removed!');
        return redirect('permission');
    } 
    
    //To Edit permission 
    public function edit($id)
    {
        $data['permission'] = DB::table('functions')
            ->where('id', $id)
            ->first();
        return view('permission.edit', $data);
    }

    //To update the data in permission
    public function update(Request $r)
    {
        $data = array(
            'path' => $r->path,
            'function_name' => $r->function_name,
        );
        $i = DB::table('functions')
            ->where('id', $r->id)
            ->update($data);
        if($i)
        {
            $r->session()->flash('success', 'Data has been saved!');
            return redirect('permission/edit/'. $r->id);
        }
        else{
            $r->session()->flash('error', 'Fail to save data!');
            return redirect('permission/edit/'. $r->id)->withInput();
        }
    }
}
