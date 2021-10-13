<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Illuminate\Support\Facades\Session;
class RoleController extends Controller
{

    //To redirect if you didn't login and access by route
    public function __construct()
    {
        $this->middleware('auth');
    }

    //To view the role in view
    public function index()
    {
        if(!Permission::check('role', 'true'))
        {
            return view('permission.index');
        }
        $data['roles'] = DB::table('roles')
            ->orderBy('id', 'desc')
            ->paginate(config('app.row'));
        return view('roles.index', $data);

    }
    //To create the view for create role
    public function create()
    {
        if(!Permission::check('role', 'true'))
        {
            return view('permission.index');
        }
        return view('roles.create');
    }
    //To save the data from create view
    public function save(Request $r)
    {
        $data = array(
            'name' => $r->name
        );
        $i = DB::table('roles')->insert($data);
        if($i)
        {
            $r->session()->flash('success', 'Data has been saved!');
            return redirect('role/create');
        }
        else
        {
            $r->session()->flash('error', 'Failed to save data!');
            return redirect('role/create')->withInput();
        }
    }
    //To edit the role
    public function edit($id)
    {
        if(!Permission::check('role', 'true'))
        {
            return view('permission.index');
        }
        $data['role'] = DB::table('roles')
            ->where('id', $id)
            ->first();
        return view('roles.edit', $data);
    }
    //To update the data in edit
    public function update(Request $r)
    {
        $data = array (
            'name' => $r->name
        );
        $i = DB::table('roles')
            ->where('id', $r->id)
            ->update($data);
        if($i)
        {
            $r->session()->flash('success', 'Data has been updated!');
            return redirect('role/edit/'. $r->id);
        }
        else 
        {
            $r->session()->flash('error', 'Failed to update data!');
            return redirect('role/edit/'. $r->id)->withInput();
        }
    }
    //To delete the data in role 
    public function delete(Request $r)
    {
        DB::table('roles')
            ->where('id', $r->id)
            ->delete();
        $r->session()->flash('success', 'Data has been deleted');
        return redirect('role');
    }
}
