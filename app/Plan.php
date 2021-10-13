<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Service;

class Plan extends Model
{
	protected $hidden = ['pivot'];
	
	public function services()
	{
		return $this->belongsToMany('App\Service', 'plans_services', 'plan_id', 'service_id');
	}
	
	public function planCategory()
	{
		return $this->belongsTo('App\PlanCategory');
	}
	

	
	
}
