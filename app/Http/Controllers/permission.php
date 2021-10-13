<?php
namespace App\Http\Controllers;
use DB;
use Auth;
class Permission{
    public static function check($pname, $aname) // Activate and true
    {
        $user_id = Auth::user()->id;
        $query = DB::table('user_functions')
        ->join('functions', 'functions.id', '=', 'user_functions.function_id')
        ->select('user_functions.*')
        ->where('user_functions.user_id', $user_id);
        // ->where('functions.path', $pname); // This is the issuss
        if($aname == 'true')
        {
            $query = $query->where('user_functions.active', 1);
        }
        $query = $query->get();
        return (count($query)>0);
    }
}