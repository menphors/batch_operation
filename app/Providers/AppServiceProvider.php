<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Auth;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Blade::if('canview', function ($expression){
            $user_id = Auth::user()->id;
            $q = DB::table('user_functions')
                ->join('functions', 'functions.id', '=', 'user_functions.function_id')
                ->select('user_functions.*')
                ->where(['user_functions'.'.user_id' => $user_id , 'functions.path' => $expression])
                ->where('user_functions.active', 1);
                $i = $q->count() > 0;
                if($i)
                {
                    return true;
                }
                else 
                {
                    return false;
                }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
