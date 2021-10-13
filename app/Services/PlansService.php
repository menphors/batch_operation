<?php

namespace App\Services;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Helpers\Helper;
use App\Plan;
class PlansService extends ApiService {

    public function __construct()
	{

    }
    
    public function getAllPlans($lang, Request $request){
        
        $startTime =  round(microtime(true) * 1000);

        $filters = $request->all();
		$deviceType  = isset($filters['device_type']) && !empty($filters['device_type']) ? $filters['device_type']:null;
        $sizeImage = isset($filters['size']) && !empty($filters['size']) ? $filters['size']:null;
        
        $columns = $this->getColumnsByLang($lang);
		
		$query = DB::table('plan_categories')
				->select($columns)
                ->where('plan_categories.status', 1);
        $data = $query->orderBy('plan_categories.weight', 'DESC')->get()->jsonSerialize();
        
        $result = $this->getPlansGroupBy($data,$lang,$filters);

        $data = $this->checkDevicetype($result,$lang,$filters);
        
        $endTime =  round(microtime(true) * 1000) - $startTime;
        

        return ['plans_categories' =>  $data ];
  
    }
    public function getPlansDetail($lang, $id,Request $request){
        $startTime =  round(microtime(true) * 1000);

        $filters = $request->all();
		$deviceType  = isset($filters['device_type']) && !empty($filters['device_type']) ? $filters['device_type']:null;
        $sizeImage = isset($filters['size']) && !empty($filters['size']) ? $filters['size']:null;

        $columns = $this->getPlansColumnsByLang($lang);
      
        $query = DB::table('plans')
                ->select($columns)
                ->where('id',$id)
                ->where('plans.status',1);
        $data = $query->orderBy('plans.weight', 'DESC')->get()->jsonSerialize();
        $result = $this->checkDeviceTypePlanDetail($data,$lang,$filters);
        $result = count($result) ? $result[0]:null;
      
        $endTime =  round(microtime(true) * 1000) - $startTime;
      
        return ['plan' => $result];
        
    }
    
    private function checkDeviceTypePlanDetail($result,$lang='en',$filters){
        $deviceType  = isset($filters['device_type']) && !empty($filters['device_type']) ? $filters['device_type']:null;
        $sizeImage = isset($filters['size']) && !empty($filters['size']) ? $filters['size']:null;
        $phone_number = isset($filters['phone_number']) && !empty($filters['phone_number']) ? $filters['phone_number']:null;
        $data = [];
        $i=0;
        $ns = new NGBSSService($phone_number);
        $plan = $ns->getSubscribedPlan();
        $isActivePlan = $plan['offering_id'];   
        foreach($result as $item){
            $data[$i]['id'] = $item->id;
            $data[$i]['view_type'] = $item->view_type;
            $data[$i]['url'] = $item->url;
            $data[$i]['name'] = $item->name;
            $data[$i]['description'] = $item->description;
          //  $data[$i]['amount']= $item->amount;
            $data[$i]['offering_id'] = $item->offering_id;
            $data[$i]['confirmation_message'] = $item->confirmation_message;
            if($item->offering_id == $isActivePlan){
                $data[$i]['status'] = 1;
            }else{
                $data[$i]['status'] = 0;
            }
            
            $data[$i]['logo'] = $this->generateFullPath($item->logo, 'plans', $deviceType, $sizeImage);
            $data[$i]['image_body'] = $this->generateFullPath($item->image_body, 'plan-bodies', $deviceType, $sizeImage);
           
            $i++;
        }
        return $data;
    }
    private function checkDevicetype($result,$lang='en',$filters){
        $deviceType  = isset($filters['device_type']) && !empty($filters['device_type']) ? $filters['device_type']:null;
        $sizeImage = isset($filters['size']) && !empty($filters['size']) ? $filters['size']:null;
        $phone_number = isset($filters['phone_number']) && !empty($filters['phone_number']) ? $filters['phone_number']:null;
        $data = [];
        $j = 0;  
        $i = 0;
        $ns = new NGBSSService($phone_number);
        $plan = $ns->getSubscribedPlan();
        $isActivePlan = $plan['offering_id'];   
        foreach($result as $item){
            $plansData = [];
            $data[$i]['id'] = $item['id'];
            $data[$i]['name'] = $item['plans_name'];
            $data[$i]['description'] = $item['description'];
            $data[$i]['status'] = 0;
            $data[$i]['logo'] = $item['logo'];
          
            // plans packages
            foreach($item['packages'] as $values){
                $plansData[$j]['id'] = $values->id;
                $plansData[$j]['name'] = $values->name;
                $plansData[$j]['confirmation_message'] = $values->confirmation_message;
                $plansData[$j]['description'] = $values->description;
                $plansData[$j]['url'] = $values->url;
                $plansData[$j]['view_type'] = $values->view_type;
                $plansData[$j]['offering_id'] = $values->offering_id;
                if($values->offering_id == $isActivePlan){
                    // set plan package active
                    $plansData[$j]['status'] = 1;
                    $data[$i]['status'] = 1;
                } else {
                    // set plan package active
                    $plansData[$j]['status'] = 0;
                }
                $plansData[$j]['logo'] = $this->generateFullPath($values->logo, 'plans', $deviceType, $sizeImage);
                $plansData[$j]['image_body'] = $this->generateFullPath($values->image_body, 'plan-bodies', $deviceType, $sizeImage);
                $j++;
            }
            $data[$i]['plans'] = array_values($plansData);
            $i++;
        }

        return $data;
    }
    private function getPlansGroupBy($data,$lang,$filters){
        $deviceType  = isset($filters['device_type']) && !empty($filters['device_type']) ? $filters['device_type']:null;
		$sizeImage = isset($filters['size']) && !empty($filters['size']) ? $filters['size']:null;
	
        $result = [];
        $i = 0;
        $plansResult = [];
        $plansColumns = $this->getPlansColumnsByLang($lang);
        foreach($data as $item){
            
            $result[$i]['id'] = $item->id;
            $result[$i]['plans_name'] = $item->plans_name;
            $result[$i]['description'] = $item->description;
            $result[$i]['logo'] = $this->generateFullPath($item->logo, 'plans', $deviceType, $sizeImage); 
            $query = DB::table('plans')
            	->select($plansColumns)
            	->where('plans.plan_category_id',$item->id)
            	->where('plans.show', 1)
            	->where('plans.status', 1)
            	->orderBy('plans.weight', 'DESC');
            $data = $query->get();
            $result[$i]['packages'] = $data;
            $i++;
        }
        return $result;
    }

    private function getPlansColumnsByLang($lang = 'en'){
        return [
            'id' => 'plans.id',
            'view_type' => 'plans.view_type',
            'offering_id' => 'plans.offering_id as offering_id',
            'logo' => 'plans.logo',
        	'name' => Helper::getLangColumn('plans.name', $lang).' AS name',
        	'description' => Helper::getLangColumn('plans.description', $lang).' AS description',
        	'confirmation_message' => DB::raw("REPLACE(REPLACE('".__('confirmation.change_plan').
        							"','[plan]',".Helper::getLangColumn('plans.name', $lang)."),'[amount]',".
        							Helper::getLangColumn('plans.amount', $lang).") AS confirmation_message"),
        	'image_body' => Helper::getLangColumn('plans.body_image', $lang).' AS image_body',
        	'url' => Helper::getLangColumn('plans.link', $lang).' AS url',
        	'amount' => Helper::getLangColumn('plans.amount', $lang).' AS amount'

        ];
    }

    public function getColumnsByLang($lang = 'en'){
        return [
            'id' => 'plan_categories.id',
            'logo' => 'plan_categories.logo',
        	'plans_name' =>  Helper::getLangColumn('plan_categories.name', $lang).' AS plans_name',
        	'description' => Helper::getLangColumn('plan_categories.description', $lang).' AS description'
        ];
    }
    
    public static function getPlanByOfferingId($offeringId){
    	$plan = Plan::where('offering_id', $offeringId)
    				->where('status', 1)
    				->firstOrFail();
    	return $plan;
    }
    
}

