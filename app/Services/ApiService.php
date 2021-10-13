<?php

namespace App\Services;
use App;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Traits\ApiResponser;
 
 


class ApiService
{
	use ValidatesRequests, ApiResponser;
	const IMAGE_BASE_URL = "https://app-assets.smart.com.kh/smartnas";
	protected $lang = "en";
	
	protected function generateFullPath($imageName, $directory, $deviceType, $size){
		if($deviceType == null || $size == null || $imageName == null) return null;
		return self::IMAGE_BASE_URL."/".$directory."/".$deviceType.'/'.$size.'/'.$imageName;
	}

	//return only a given props of object
	public function collectPropsObject($obj,$columns){
        $collection = collect($obj);
		$filtered = $collection->only($columns);

        return $filtered;
    }

	//return only a given props of list of object
    public function collectPropsArrayObjects($objs,$columns){
		$results = array();
		foreach($objs as $obj){
			array_push($results,$this->collectPropsObject($obj,$columns));
		} 

        return $results;
	}
	
 	//transform given props and return new object back
	public function transformPropsObject($obj,$properties){
		 
		 
		foreach($properties as $property){
			$oldProp = $property[0];
			$newProp = $property[1];
		 	$obj[$newProp] =$obj[$oldProp];
		    unset($obj[$oldProp]);
		}
	 
        return $obj;
	}
	//transform given props and return a list of new objects back
	public function transformPropsArrayObjects($objs,$properties){
		 
		$results = array();
		 
		foreach($objs as $obj){
			array_push($results,$this->transformPropsObject($obj,$properties));
	
		}
	
        return $results;
	}

	//transform and collect given props and return new object back
	public function transformAndCollectPropsObject($obj,$properties,$columns){
		 
		 
		foreach($properties as $property){
			$oldProp = $property[0];
			$newProp = $property[1];
		 	$obj[$newProp] =$obj[$oldProp];
		}
	 
        return $this->collectPropsObject($obj,$columns);
	}

	//transform and collect given props and return list of new objects back
	public function transformAndCollectPropsArrayObjects($objs,$properties,$columns){
		 
		$results = array();
		 
		foreach($objs as $obj){
			array_push($results,$this->transformAndCollectPropsObject($obj,$properties,$columns));
		}
	
        return $results;
	}
	

	//transform given columns to query parameter by language
	public function transformSelectedColumnsByLanguage($lang,$columnsWillTransform,$additionalColumnsAfterTransform){
		 $columnsResult = [];
		 foreach($columnsWillTransform as $column){
				array_push( $columnsResult , $column .'_'. $lang .' as '.  $column);
		 }

		 if($additionalColumnsAfterTransform != null){
				foreach($additionalColumnsAfterTransform as $column){
					array_push( $columnsResult , $column);
				}
		 }
	
        return $columnsResult;
	}

	//transform a given columns to UpdateInfo format by lang
	public function getUpdateInfoByLanguage($lang,$columns1,$columns2){
		$updateInfoResult = [];

		if($columns2 == null){
			foreach($columns1 as $column){
				$updateInfoItem = [];
				array_push($updateInfoItem,$column .'_'. $lang );
				array_push($updateInfoItem,$column);

				array_push($updateInfoResult,$updateInfoItem);
			  }
		}else{
			foreach($columns1 as $key => $column){
				$updateInfoItem = [];
				array_push($updateInfoItem,$column .'_'. $lang );
				array_push($updateInfoItem,$columns2[$key]);

				array_push($updateInfoResult,$updateInfoItem);
			  }
		}
	 
   
	   return $updateInfoResult;
   }

	public function configImageBaseUrl($obj,$columns,$directories,$deviceType, $size){
		 
		foreach($columns as $key => $column){
		 	$obj[$column] = $this->generateFullPath($obj[$column], $directories[$key], $deviceType, $size);	
		} 
			 
        return $obj;
	}


	
	public function configImageBaseUrls($objs,$columns,$directories,$deviceType,$size){
	 
		$results = array();
		 
		foreach($objs as $obj){
			array_push($results,$this->configImageBaseUrl($obj,$columns,$directories,$deviceType,$size));
		}
	
        return $results;
	}
	
 

}