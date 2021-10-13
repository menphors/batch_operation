<?php

namespace App\Services;
use Unirest;
use App\Plan;
use App\Service; 
use App\PlanFreeUnit;
use App\Libraries\AES;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Services\SOAPApiService;
use App\Services\HttpRequest;

class NGBSSService {
	const TYPE_PRODUCTION = 1;
	const TYPE_TESTBED = 2;
	
	protected $phoneNumber;
	protected $lang;
	protected $type = self::TYPE_PRODUCTION;
	
    public function __construct($phoneNumber, $lang = 'en')
	{
		
		$this->phoneNumber = $phoneNumber;
		$this->lang = $lang;
		/*
		if(\App::environment() == 'PRODUCTION'){
			$this->type = self::TYPE_PRODUCTION;
		}else{
			$this->type = self::TYPE_PRODUCTION;
		}*/

    }
    
    public function setType($type){
    	$this->type = $type;
    }
    
    public function getBalance(){
    	$result  = [
    			'main_balance' => [],
    			'extra_balance' => []
    	];
    	$SOAPApiService = new SOAPApiService($this->type, 'QueryBalance');
    	$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ars="http://www.huawei.com/bme/cbsinterface/arservices" xmlns:cbs="http://www.huawei.com/bme/cbsinterface/cbscommon" xmlns:arc="http://cbs.huawei.com/ar/wsservice/arcommon">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <ars:QueryBalanceRequestMsg>
				         <RequestHeader>
				            <cbs:Version>1</cbs:Version>
				            <cbs:BusinessCode>1</cbs:BusinessCode>
				            <cbs:MessageSeq>11121</cbs:MessageSeq>
				            <cbs:OwnershipInfo>
				               <cbs:BEID>101</cbs:BEID>
				            </cbs:OwnershipInfo>
				            <cbs:AccessSecurity>
				               <cbs:LoginSystemCode>'.$SOAPApiService->getUser().'</cbs:LoginSystemCode>
				               <cbs:Password>'.$SOAPApiService->getPassword().'</cbs:Password>
				            </cbs:AccessSecurity>
				            <cbs:OperatorInfo>
				               <cbs:OperatorID>1100057</cbs:OperatorID>
				            </cbs:OperatorInfo>
				         </RequestHeader>
				         <QueryBalanceRequest>
				            <ars:QueryObj>
				             <ars:AcctAccessCode>
				                  <arc:PrimaryIdentity>'.$this->phoneNumber.'</arc:PrimaryIdentity>
				               </ars:AcctAccessCode>
				            </ars:QueryObj>
				            <ars:BalanceType/>
				         </QueryBalanceRequest>
				      </ars:QueryBalanceRequestMsg>
				   </soapenv:Body>
				</soapenv:Envelope>';
    	$headers = array('SOAPAction' => 'QueryBalance', 'Content-Type' => 'text/xml');
		$body = $data;
		
    	
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	
    	# Full parsing, array have root element
    	$infoArray = Helper::xmlToArray($response->body, true);
    	if($infoArray['soapenv:Envelope']['soapenv:Body']['ars:QueryBalanceResultMsg']['ResultHeader']['cbs:ResultCode'] == '0'){
    		$allBalances = $infoArray['soapenv:Envelope']['soapenv:Body']['ars:QueryBalanceResultMsg']['QueryBalanceResult']['ars:AcctList']['ars:BalanceResult'];
    		$balances = [];
    		// check if there is only one balance item
    		if(array_key_exists('arc:BalanceType', $allBalances)) $balances[0] = $allBalances;
    		else $balances = $allBalances;
    		
    		$balanceTypeService = new NGBSSBalanceTypeService($this->lang);
    		foreach($balances as $balance){
    			$balanceInfo = [
    					'balance_type' => $balance['arc:BalanceType'],
    					'balance_type_name' => $balance['arc:BalanceTypeName'],
    					'display_name' => $balanceTypeService->getDisplayNameByName($balance['arc:BalanceTypeName']),
    					'currency_id' => $balance['arc:CurrencyID'],
    					'is_appear' => $balanceTypeService->getIsAppearByName($balance['arc:BalanceTypeName']),
    					'amount' => floatval($balance['arc:TotalAmount'])
    					
    			];
    			
    			$balanceInfo['effective_time'] = null;
    			$balanceInfo['expire_time'] = null;
    			if(array_key_exists('arc:BalanceDetail', $balance)){
    				
    				if(!array_key_exists('arc:EffectiveTime', $balance['arc:BalanceDetail'])){
    					$maxDetailBalance = null;
    					foreach($balance['arc:BalanceDetail'] as $detailBalance){
    						if($maxDetailBalance == null){
    							$maxDetailBalance = $detailBalance;
    						}else if(strtotime($detailBalance['arc:ExpireTime']) > strtotime($maxDetailBalance['arc:ExpireTime'])){
    							$maxDetailBalance = $detailBalance;
    						}
    					}
    					
    					if($maxDetailBalance != null){
    						$balanceInfo['effective_time'] = date('Y-m-d H:i:s', strtotime($maxDetailBalance['arc:EffectiveTime']));
    						$balanceInfo['expire_time'] = date('Y-m-d H:i:s', strtotime($maxDetailBalance['arc:ExpireTime']));
    					}
    					
    				}else{
    					$balanceInfo['effective_time'] = date('Y-m-d H:i:s', strtotime($balance['arc:BalanceDetail']['arc:EffectiveTime']));
    					$balanceInfo['expire_time'] = date('Y-m-d H:i:s', strtotime($balance['arc:BalanceDetail']['arc:ExpireTime']));
    				}
    			}
    			
    			if($balance['arc:BalanceTypeName'] == 'PPS_MainAccount'){
    				$result['main_balance'] = $balanceInfo;
    				$result['main_balance']['life_cycle'] = $this->getSubscriberLifeCycle(); 
    				$result['main_balance']['current_life_cycle'] = $this->getLifeCycleCurrentStatus($result['main_balance']['life_cycle']);
    			}else{
    				$result['extra_balance'][] = $balanceInfo;
    			}
    		}
    	}
    	
    	return $result;
    }
    
    public function getDetailBalance(){
    	$result  = [
    			'main_balance' => [],
    			'extra_balance' => []
    	];
    	$SOAPApiService = new SOAPApiService($this->type, 'QueryBalance');
    	
    	$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ars="http://www.huawei.com/bme/cbsinterface/arservices" xmlns:cbs="http://www.huawei.com/bme/cbsinterface/cbscommon" xmlns:arc="http://cbs.huawei.com/ar/wsservice/arcommon">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <ars:QueryBalanceRequestMsg>
				         <RequestHeader>
				            <cbs:Version>1</cbs:Version>
				            <cbs:BusinessCode>1</cbs:BusinessCode>
				            <cbs:MessageSeq>11121</cbs:MessageSeq>
				            <cbs:OwnershipInfo>
				               <cbs:BEID>101</cbs:BEID>
				            </cbs:OwnershipInfo>
				            <cbs:AccessSecurity>
				               <cbs:LoginSystemCode>'.$SOAPApiService->getUser().'</cbs:LoginSystemCode>
				               <cbs:Password>'.$SOAPApiService->getPassword().'</cbs:Password>
				            </cbs:AccessSecurity>
				            <cbs:OperatorInfo>
				               <cbs:OperatorID>1100057</cbs:OperatorID>
				            </cbs:OperatorInfo>
				         </RequestHeader>
				         <QueryBalanceRequest>
				            <ars:QueryObj>
				             <ars:AcctAccessCode>
				                  <arc:PrimaryIdentity>'.$this->phoneNumber.'</arc:PrimaryIdentity>
				               </ars:AcctAccessCode>
				            </ars:QueryObj>
				            <ars:BalanceType/>
				         </QueryBalanceRequest>
				      </ars:QueryBalanceRequestMsg>
				   </soapenv:Body>
				</soapenv:Envelope>';
    	$headers = array('SOAPAction' => 'QueryBalance', 'Content-Type' => 'text/xml');
    	$body = $data;
    	
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	
    	# Full parsing, array have root element
    	$infoArray = Helper::xmlToArray($response->body, true);
    	if($infoArray['soapenv:Envelope']['soapenv:Body']['ars:QueryBalanceResultMsg']['ResultHeader']['cbs:ResultCode'] == '0'){
    		$allBalances = $infoArray['soapenv:Envelope']['soapenv:Body']['ars:QueryBalanceResultMsg']['QueryBalanceResult']['ars:AcctList']['ars:BalanceResult'];
    		$balances = [];
    		// check if there is only one balance item
    		if(array_key_exists('arc:BalanceType', $allBalances)) $balances[0] = $allBalances;
    		else $balances = $allBalances;
    		
    		$balanceTypeService = new NGBSSBalanceTypeService($this->lang);
    		foreach($balances as $balance){
    			$balanceInfo = [
    					'balance_type' => $balance['arc:BalanceType'],
    					'balance_type_name' => $balance['arc:BalanceTypeName'],
    					'display_name' => $balanceTypeService->getDisplayNameByName($balance['arc:BalanceTypeName']),
    					'currency_id' => $balance['arc:CurrencyID'],
    					'is_appear' => $balanceTypeService->getIsAppearByNameV2($balance['arc:BalanceTypeName']),
    					'amount' => floatval($balance['arc:TotalAmount']),
    					'is_recurring' => $balanceTypeService->getIsRecurringByName($balance['arc:BalanceTypeName']),
    					'detail' => []
    					
    			];
    			
    			$balanceInfo['effective_time'] = null;
    			$balanceInfo['expire_time'] = null;
    			if(array_key_exists('arc:BalanceDetail', $balance)){
    				$detailBalances = [];
    				if(array_key_exists('arc:BalanceInstanceID', $balance['arc:BalanceDetail'])){
    					$detailBalances[0] = $balance['arc:BalanceDetail'];
    				}else{
    					$detailBalances = $balance['arc:BalanceDetail'];
    				}
    				
    				foreach($detailBalances as $detailBalance){
    					$balanceInfo['detail'][] = [
    							'amount' => (int)$detailBalance['arc:Amount'],
    							'initial_amount' => (int)$detailBalance['arc:InitialAmount'],
    							'effective_time' => date('Y-m-d H:i:s', strtotime($detailBalance['arc:EffectiveTime'])),
    							'expire_time' 	=> date('Y-m-d H:i:s', strtotime($detailBalance['arc:ExpireTime']))
    					];
    				}
    			}
    			
    			if($balance['arc:BalanceTypeName'] == 'PPS_MainAccount'){
    				$result['main_balance'] = $balanceInfo;
    				$result['main_balance']['life_cycle'] = $this->getSubscriberLifeCycle();
    				$result['main_balance']['current_life_cycle'] = $this->getLifeCycleCurrentStatus($result['main_balance']['life_cycle']);
    			}else{
    				$result['extra_balance'][] = $balanceInfo;
    			}
    		}
    	}
    	
    	return $result;
    }
    
    protected function getLifeCycleCurrentStatus($subscriberLifeCycle){
    	$currentStatus = 'pool';
    	if(strtotime($subscriberLifeCycle['active']['expire_time']) > time()){
    		$currentStatus = 'active';
    	}else if(strtotime($subscriberLifeCycle['call_barring']['expire_time']) > time()){
    		$currentStatus = 'call_barring';
    	}else if(strtotime($subscriberLifeCycle['suspend']['expire_time']) > time()){
    		$currentStatus = 'suspend';
    	}else if(strtotime($subscriberLifeCycle['pool']['expire_time']) > time()){
    		$currentStatus = 'pool';
    	}
    	return $currentStatus;
    }
    
    public function getSubscriberLifeCycle(){
    	$result = null;
    	$SOAPApiService = new SOAPApiService($this->type, 'QuerySubLifeCycle');
    	
    	$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:bcs="http://www.huawei.com/bme/cbsinterface/bcservices" xmlns:cbs="http://www.huawei.com/bme/cbsinterface/cbscommon" xmlns:bcc="http://www.huawei.com/bme/cbsinterface/bccommon">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <bcs:QuerySubLifeCycleRequestMsg>
				         <RequestHeader>
				            <cbs:Version>1</cbs:Version>
				            <cbs:BusinessCode>1</cbs:BusinessCode>
				            <cbs:MessageSeq>1</cbs:MessageSeq>
				            <cbs:OwnershipInfo>
				               <cbs:BEID>101</cbs:BEID>
				            </cbs:OwnershipInfo>
				            <cbs:AccessSecurity>
				               <cbs:LoginSystemCode>'.$SOAPApiService->getUser().'</cbs:LoginSystemCode>
				               <cbs:Password>'.$SOAPApiService->getPassword().'</cbs:Password>
				            </cbs:AccessSecurity>           
				         </RequestHeader>
				         <QuerySubLifeCycleRequest>
				            <bcs:SubAccessCode>
				               <!--You have a CHOICE of the next 2 items at this level-->
				               <bcc:PrimaryIdentity>'.$this->phoneNumber.'</bcc:PrimaryIdentity>
				               <!--<bcc:SubscriberKey>?</bcc:SubscriberKey> -->
				            </bcs:SubAccessCode>
				         </QuerySubLifeCycleRequest>
				      </bcs:QuerySubLifeCycleRequestMsg>
				   </soapenv:Body>
				</soapenv:Envelope>';
    	$headers = array('SOAPAction' => 'QuerySubLifeCycle', 'Content-Type' => 'text/xml');
    	$body = $data;
    	
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	# Full parsing, array have root element
    	$infoArray = Helper::xmlToArray($response->body, true);
    	if($infoArray['soapenv:Envelope']['soapenv:Body']['bcs:QuerySubLifeCycleResultMsg']['ResultHeader']['cbs:ResultCode'] == "0"){
    		$subscriberLifeCycles = $infoArray['soapenv:Envelope']['soapenv:Body']['bcs:QuerySubLifeCycleResultMsg']['QuerySubLifeCycleResult']['bcs:LifeCycleStatus'];
    		foreach($subscriberLifeCycles as $subscriberLifeCycle){
    			$result[str_replace(' ', '_', strtolower($subscriberLifeCycle['bcs:StatusName']))] = [
    					'name' => $subscriberLifeCycle['bcs:StatusName'],
    					'expire_time' => date('Y-m-d H:i:s', strtotime($subscriberLifeCycle['bcs:StatusExpireTime']))
    			];
    		}
    	}
    	return $result;
    }
    
    /**
     * 
     * @param number $planType 1: normal plan, 2: smart@home plan
     * @return NULL|string
     */
    public function getSubscribedPlan($planType = 1, $lang = 'en'){
    	$plan = null;
    	$SOAPApiService = new SOAPApiService($this->type, 'QueryPurchasedPrimaryOffering');
    	$data = "<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:off='http://www.huawei.com/bss/soaif/interface/OfferingService/' xmlns:com='http://www.huawei.com/bss/soaif/interface/common/'>
		            <soapenv:Header/>
		            <soapenv:Body>
		               <off:QueryPurchasedPrimaryOfferingReqMsg>
		                  <com:ReqHeader>
		                     <com:TransactionId>".time()."</com:TransactionId>
		                     <com:Channel>28</com:Channel>
		                     <com:PartnerId>101</com:PartnerId>
		                     <com:ReqTime>".date('YmdHis')."</com:ReqTime>
		                     <com:AccessUser>".$SOAPApiService->getUser()."</com:AccessUser>
		                     <com:AccessPassword>".$SOAPApiService->getPassword()."</com:AccessPassword>
		                  </com:ReqHeader>
		                  <off:AccessInfo>
		                     <com:ObjectIdType>4</com:ObjectIdType>
		                     <com:ObjectId>".$this->phoneNumber."</com:ObjectId>
		                  </off:AccessInfo>
		               </off:QueryPurchasedPrimaryOfferingReqMsg>
		            </soapenv:Body>
		         </soapenv:Envelope>";
    	$headers = array('SOAPAction' => 'QueryPurchasedPrimaryOffering', 'Content-Type' => 'text/xml');
    	$body = $data;
    	
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	# Full parsing, array have root element
		 $infoArray = Helper::xmlToArray($response->body, true);
		  

		 $offeringId = 0;

    	if($infoArray['soapenv:Envelope']['soapenv:Body']['off:QueryPurchasedPrimaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'] == "0000"){
    		$detailPlan = $infoArray['soapenv:Envelope']['soapenv:Body']['off:QueryPurchasedPrimaryOfferingRspMsg']['off:PrimaryOffering'];
    		$offeringId = $detailPlan['off:OfferingId']['com:OfferingId'];
		   
			//  if($planType == 1){
			// 	 $plan = collect(PlansService::getPlanByOfferingId($offeringId));
    		// }else if($planType == 2){ 
    		// 	$plan = $smartAtHomePlanService->getPlanByOfferingId($offeringId);
    		// }
    		// $plan['effective_date'] = date('Y-m-d', strtotime($detailPlan['off:EffectiveDate']));
		 }
		   

    	return $offeringId;
    }
    
    public function getSubscribedSmartAtHomePlan(){
    	$plan = null;
    	$SOAPApiService = new SOAPApiService($this->type, 'QueryPurchasedPrimaryOffering');
    	$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:off="http://www.huawei.com/bss/soaif/interface/OfferingService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <off:QueryPurchasedPrimaryOfferingReqMsg>
				         <com:ReqHeader>
				            <!--Optional:-->
				            <com:Version>1</com:Version>
				            <!--Optional:-->
				            <com:BusinessCode>ADWTTX0001</com:BusinessCode>
				            <com:TransactionId>'.time().'</com:TransactionId>
				            <!--Optional:-->
				            <com:Channel>28</com:Channel>
				            <!--Optional:-->
				            <com:PartnerId>101</com:PartnerId>
				            
				            <com:ReqTime>'.date('YmdHis').'</com:ReqTime>           
				            <com:AccessUser>'.$SOAPApiService->getUser().'</com:AccessUser>
				            <com:AccessPassword>'.$SOAPApiService->getPassword().'</com:AccessPassword>            
				         </com:ReqHeader>
				         <off:AccessInfo>
				            <com:ObjectIdType>4</com:ObjectIdType>
				            <com:ObjectId>'.$this->phoneNumber.'</com:ObjectId>
				         </off:AccessInfo>
				      </off:QueryPurchasedPrimaryOfferingReqMsg>
				   </soapenv:Body>
				</soapenv:Envelope>';
    	$headers = array('SOAPAction' => 'QueryPurchasedPrimaryOffering', 'Content-Type' => 'text/xml');
    	$body = $data;
    	
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	# Full parsing, array have root element
    	$infoArray = Helper::xmlToArray($response->body, true);
    	if($infoArray['soapenv:Envelope']['soapenv:Body']['off:QueryPurchasedPrimaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'] == "0000"){
    		$detailPlan = $infoArray['soapenv:Envelope']['soapenv:Body']['off:QueryPurchasedPrimaryOfferingRspMsg']['off:PrimaryOffering'];
    		$offeringId = $detailPlan['off:OfferingId']['com:OfferingId'];
    		$smartAtHomePlanService = new SmartAtHomePlanService($this->lang);
    		$plan = $smartAtHomePlanService->getPlanByOfferingId($offeringId);
    		$plan['effective_date'] = date('Y-m-d', strtotime($detailPlan['off:EffectiveDate']));
    	}
    	return $plan;
    }
       
    public function getFreeUnits(){
    	$result = [];
    	$SOAPApiService = new SOAPApiService($this->type, 'QueryFreeUnit');
    	$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:off="http://www.huawei.com/bss/soaif/interface/OfferingService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <off:QueryFreeUnitReqMsg>
				         <com:ReqHeader>
				            <com:Version>1</com:Version>
				            <com:BusinessCode>101</com:BusinessCode>
				            <com:TransactionId>'.time().'</com:TransactionId>
				            <com:Channel>28</com:Channel>
				            <com:AccessUser>'.$SOAPApiService->getUser().'</com:AccessUser>
				            <com:AccessPassword>'.$SOAPApiService->getPassword().'</com:AccessPassword>
				         </com:ReqHeader>
				         <off:AccessInfo>
				            <com:ObjectIdType>4</com:ObjectIdType>
				            <com:ObjectId>'.$this->phoneNumber.'</com:ObjectId>
				         </off:AccessInfo>
				      </off:QueryFreeUnitReqMsg>
				   </soapenv:Body>
				</soapenv:Envelope>';
    	$headers = array('SOAPAction' => 'QueryFreeUnit', 'Content-Type' => 'text/xml');
    	$body = $data;
    	
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	# Full parsing, array have root element
    	$infoArray = Helper::xmlToArray($response->body, true);
    	if($infoArray['soapenv:Envelope']['soapenv:Body']['off:QueryFreeUnitRspMsg']['com:RspHeader']['com:ReturnCode'] == "0000"
    		&& array_key_exists('off:FreeUnit', $infoArray['soapenv:Envelope']['soapenv:Body']['off:QueryFreeUnitRspMsg'])){
    	
    		$freeUnits = $infoArray['soapenv:Envelope']['soapenv:Body']['off:QueryFreeUnitRspMsg']['off:FreeUnit'];
    		$allFreeUnits = [];
    		if(array_key_exists('off:TypeId', $freeUnits)){
    			$allFreeUnits[0] = $freeUnits;
    		}else{
    			$allFreeUnits = $freeUnits;
    		}
    		foreach($allFreeUnits as $freeUnit){
    			if(array_key_exists('off:UnitId', $freeUnit['off:Detail'])){
    				$offeringId = null;
    				$purchaseSeq = null;
    				if(array_key_exists('off:Origin', $freeUnit['off:Detail'])){
    					$offeringId = $freeUnit['off:Detail']['off:Origin']['off:OfferingId']['com:OfferingId'];
    					$purchaseSeq= $freeUnit['off:Detail']['off:Origin']['off:OfferingId']['com:PurchaseSeq'];
    				}
    				
    				$result[] = [
    						'offering_id' => $offeringId,
    						'type_id' 	=> $freeUnit['off:TypeId'],
    						'type_name' => $freeUnit['off:TypeName'],
    						'title' => $this->getFreeUnitTitleByMeasurementUnit($freeUnit['off:MeasureUnit']),
    						'measurement_unit' => $freeUnit['off:MeasureUnit'],
    						'unit_id' => $freeUnit['off:Detail']['off:UnitId'],
    						'initial_amount' => floatval($freeUnit['off:Detail']['off:InitialAmt']),
    						'unused_amount' => floatval($freeUnit['off:Detail']['off:UnusedAmt']),
    						'effective_date' => date('Y-m-d', strtotime($freeUnit['off:Detail']['off:EffectiveDate'])),
    						'expire_date' => date('Y-m-d', strtotime($freeUnit['off:Detail']['off:ExpireDate'])),
    						'effective_time' => date('Y-m-d H:i:s', strtotime($freeUnit['off:Detail']['off:EffectiveDate'])),
    						'expire_time' => date('Y-m-d H:i:s', strtotime($freeUnit['off:Detail']['off:ExpireDate'])),
    						'purchase_seq' => $purchaseSeq
    				];
    			}else{
    				foreach($freeUnit['off:Detail'] as $detail){
    					$offeringId = null;
    					$purchaseSeq = null;
    					
    					if(array_key_exists('off:Origin', $detail)){
    						if(array_key_exists('off:OfferingId', $detail['off:Origin'])){
    							$offeringId = $detail['off:Origin']['off:OfferingId']['com:OfferingId'];
    							$purchaseSeq= $detail['off:Origin']['off:OfferingId']['com:PurchaseSeq'];
    						}
    					}
    					$result[] = [
    							'offering_id' => $offeringId,
    							'type_id' 	=> $freeUnit['off:TypeId'],
    							'type_name' => $freeUnit['off:TypeName'],
    							'title' => $this->getFreeUnitTitleByMeasurementUnit($freeUnit['off:MeasureUnit']),
    							'measurement_unit' => $freeUnit['off:MeasureUnit'],
    							'unit_id' => $detail['off:UnitId'],
    							'initial_amount' => floatval($detail['off:InitialAmt']),
    							'unused_amount' => floatval($detail['off:UnusedAmt']),
    							'effective_date' => date('Y-m-d', strtotime($detail['off:EffectiveDate'])),
    							'expire_date' => date('Y-m-d', strtotime($detail['off:ExpireDate'])),
    							'effective_time' => date('Y-m-d H:i:s', strtotime($detail['off:EffectiveDate'])),
    							'expire_time' => date('Y-m-d H:i:s', strtotime($detail['off:ExpireDate'])),
    							'purchase_seq' => $purchaseSeq
    					];
    				}
    			}    			
    		}
    	} 
    	return $result;
    }
    
    protected function getFreeUnitTitleByMeasurementUnit($measurementUnit){
    	if(in_array($measurementUnit, ['1003', '1004'])) return __('other.minute');
    	else if(in_array($measurementUnit, ['1106', '1107', '1108', '1109'])) return __('other.data');
    	else return null;
    }
	
    public function getSubscribedServices(){
    	$result = [];
    	$SOAPApiService = new SOAPApiService($this->type, 'QueryPurchasedSupplementaryOffering');
    	$data = "<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:off='http://www.huawei.com/bss/soaif/interface/OfferingService/' xmlns:com='http://www.huawei.com/bss/soaif/interface/common/'>
					   <soapenv:Header/>
					   <soapenv:Body>
					      <off:QueryPurchasedSupplementaryOfferingReqMsg>
					         <com:ReqHeader>
					            <com:TransactionId>".time()."</com:TransactionId>
					            <com:Channel>28</com:Channel>
					            <com:PartnerId>101</com:PartnerId>
					            <com:ReqTime>".date('YmdHis')."</com:ReqTime>
					            <com:AccessUser>".$SOAPApiService->getUser()."</com:AccessUser>
					            <com:AccessPassword>".$SOAPApiService->getPassword()."</com:AccessPassword>
					         </com:ReqHeader>
					         <off:AccessInfo>
					            <com:ObjectIdType>4</com:ObjectIdType>
					            <com:ObjectId>".$this->phoneNumber."</com:ObjectId>
					         </off:AccessInfo>
					      </off:QueryPurchasedSupplementaryOfferingReqMsg>
					   </soapenv:Body>
					</soapenv:Envelope>";
    	$headers = array('SOAPAction' => 'QueryPurchasedSupplementaryOffering', 'Content-Type' => 'text/xml');
    	$body = $data;
    	
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	# Full parsing, array have root element
    	$infoArray = Helper::xmlToArray($response->body, true);
    	
    	if($infoArray['soapenv:Envelope']['soapenv:Body']['off:QueryPurchasedSupplementaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
    		$allOfferings = [];
    		$offerings = $infoArray['soapenv:Envelope']['soapenv:Body']['off:QueryPurchasedSupplementaryOfferingRspMsg']['off:SupplementaryOffering'];	
    		if(array_key_exists('off:OfferingId', $offerings)){
    			$allOfferings[0] = $offerings;
    		}else{
    			$allOfferings = $offerings;
    		}
    		foreach($allOfferings as $offering){
    			$offeringId = $offering['off:OfferingId']['com:OfferingId'];
    			$service = ServiceService::getServiceByOfferingId($offeringId);
    			if($service == null || $offering['off:Status'] != 'C01') continue;
    			$service = collect($service);
    			$service['offering_id'] = $offering['off:OfferingId']['com:OfferingId'];
    			$service['purchase_seq'] = $offering['off:OfferingId']['com:PurchaseSeq'];
    			$service['effective_date'] = date('Y-m-d' , strtotime($offering['off:EffectiveDate']));
    			$service['effective_time'] = date('Y-m-d H:i:s' , strtotime($offering['off:EffectiveDate']));
    			$service['expire_date'] = date('Y-m-d', strtotime($offering['off:ExpireDate']));
    			$service['expire_time'] = date('Y-m-d H:i:s', strtotime($offering['off:ExpireDate']));
    			$service['next_renewal_time'] = array_key_exists('off:NextRenewalDate', $offering) ? date('Y-m-d H:i:s', strtotime($offering['off:NextRenewalDate'])):null;
    			
    			$result[] = $service;
    		}
    	}
    	$result = collect($result)->unique('id')->toArray();
    	return $result;
    	
    }
    
    public function getActiveServices(){
    	if($this->phoneNumber == null) return array();
    	$offeringIds = [];
    	$SOAPApiService = new SOAPApiService($this->type, 'QueryPurchasedSupplementaryOffering');
    	
    	$data = "<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:off='http://www.huawei.com/bss/soaif/interface/OfferingService/' xmlns:com='http://www.huawei.com/bss/soaif/interface/common/'>
					   <soapenv:Header/>
					   <soapenv:Body>
					      <off:QueryPurchasedSupplementaryOfferingReqMsg>
					         <com:ReqHeader>
					            <com:TransactionId>iChat_iFun_0423101</com:TransactionId>
					            <com:Channel>28</com:Channel>
					            <com:PartnerId>101</com:PartnerId>
					            <com:ReqTime>".date('YmdHis')."</com:ReqTime>
					            <com:AccessUser>".$SOAPApiService->getUser()."</com:AccessUser>
					            <com:AccessPassword>".$SOAPApiService->getPassword()."</com:AccessPassword>
					         </com:ReqHeader>
					         <off:AccessInfo>
					            <com:ObjectIdType>4</com:ObjectIdType>
					            <com:ObjectId>".$this->phoneNumber."</com:ObjectId>
					         </off:AccessInfo>
					      </off:QueryPurchasedSupplementaryOfferingReqMsg>
					   </soapenv:Body>
					</soapenv:Envelope>";
    	$headers = array('SOAPAction' => 'QueryPurchasedSupplementaryOffering', 'Content-Type' => 'text/xml');
    	$body = $data;
    	
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	# Full parsing, array have root element
    	$infoArray = Helper::xmlToArray($response->body, true);
    	
    	if($infoArray['soapenv:Envelope']['soapenv:Body']['off:QueryPurchasedSupplementaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
    		$allOfferings = [];
    		$offerings = $infoArray['soapenv:Envelope']['soapenv:Body']['off:QueryPurchasedSupplementaryOfferingRspMsg']['off:SupplementaryOffering'];
    		if(array_key_exists('off:OfferingId', $offerings)){
    			$allOfferings[0] = $offerings;
    		}else{
    			$allOfferings = $offerings;
    		}
    		foreach($allOfferings as $offering){
    			if($offering['off:Status'] != 'C01') continue;
    			$offeringIds[] = [
    					'offering_id' => $offering['off:OfferingId']['com:OfferingId'],
    					'purchase_seq' => $offering['off:OfferingId']['com:PurchaseSeq'],
    					'next_renewal_date' => array_key_exists('off:NextRenewalDate', $offering) ? date('Y-m-d', strtotime($offering['off:NextRenewalDate'])):null
    			];
    		}
    	}
    	return $offeringIds;
    }
    
    public function getFreeUnitsByOfferingId($freeUnits, $offeringId){
    	$result = [];
    	foreach($freeUnits as $freeUnit){
    		if($offeringId == $freeUnit['offering_id']){
    			$result[] = $freeUnit;
    		}
    	}
    	if(count($result)) return $result;
    	return null;
    }
   
    public function getCPENumbers(){
    	$result = [];
    	$SOAPApiService = new SOAPApiService($this->type, 'QueryCPENumber');
    	$body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wtt="http://www.huawei.com/bss/soaif/interface/WTTXService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <wtt:QueryCPENumberReqMsg>
				          <com:ReqHeader>
				            <com:Version>1</com:Version>
				            <com:PartnerId>101</com:PartnerId>
				            <com:BusinessCode>AD0004</com:BusinessCode>
				            <com:TransactionId>'.time().'</com:TransactionId>
				            <com:ReqTime>'.date('YmdHis').'</com:ReqTime>
				            <com:Channel>39</com:Channel>
				           	<com:AccessUser>'.$SOAPApiService->getUser().'</com:AccessUser>
				            <com:AccessPassword>'.$SOAPApiService->getPassword().'</com:AccessPassword>          
				         </com:ReqHeader>
				         <wtt:CPEMAINNUM>
				            <wtt:MainNum>'.$this->phoneNumber.'</wtt:MainNum>
				         </wtt:CPEMAINNUM>
				      </wtt:QueryCPENumberReqMsg>
				   </soapenv:Body>
				</soapenv:Envelope>';
    	$headers = array('SOAPAction' => 'QueryCPENumber', 'Content-Type' => 'text/xml');
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	# Full parsing, array have root element
    	$infoArray = Helper::xmlToArray($response->body, true);
    	if($infoArray['soapenv:Envelope']['soapenv:Body']['wtt:QueryCPENumberRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
    		$CPENums = $infoArray['soapenv:Envelope']['soapenv:Body']['wtt:QueryCPENumberRspMsg']['wtt:Results']['wtt:CPENum'];
    		if(!is_array($CPENums)){
    			$result[0] = $CPENums;
    		}else{
    			$result = $CPENums;
    		}
    	}
    	
    	return $result;
    }
       
    public function subscribeSupplementaryOffering($offeringId){
    	$message = [
    			'status' => false,
    			'message' => 'No phone number provided',
    			'code' => ''
    	];
    	if($this->phoneNumber == null) return $message;
    	$SOAPApiService = new SOAPApiService($this->type, 'ChangeSupplementaryOffering');
    	
    	$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:off="http://www.huawei.com/bss/soaif/interface/OfferingService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <off:ChangeSupplementaryOfferingReqMsg>
				         <com:ReqHeader>
				            <com:TransactionId>'.time().'</com:TransactionId>
				            <com:Channel>39</com:Channel>
				            <com:PartnerId>101</com:PartnerId>
				            <com:ReqTime>'.date('YmdHis').'</com:ReqTime>
				            <com:AccessUser>'.$SOAPApiService->getUser().'</com:AccessUser>
				            <com:AccessPassword>'.$SOAPApiService->getPassword().'</com:AccessPassword>  
				         </com:ReqHeader>
				         <off:AccessInfo>
				            <com:ObjectIdType>4</com:ObjectIdType>
				            <com:ObjectId>'.$this->phoneNumber.'</com:ObjectId>
				         </off:AccessInfo>
				         <!--Zero or more repetitions:-->
				         <off:AddOffering>
				            <com:OfferingId>
				               <com:OfferingId>'.$offeringId.'</com:OfferingId>
				            </com:OfferingId>       
				         </off:AddOffering>
				      </off:ChangeSupplementaryOfferingReqMsg>
				   </soapenv:Body>
				</soapenv:Envelope>';
    	$headers = array('SOAPAction' => 'ChangeSupplementaryOffering', 'Content-Type' => 'text/xml');
    	$body = $data;
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	# Full parsing, array have root element
    	$infoArray = Helper::xmlToArray($response->body, true);
    	$code = $infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangeSupplementaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'];
    	$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangeSupplementaryOfferingRspMsg']['com:RspHeader']['com:ReturnMsg'];
    	if($infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangeSupplementaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
    		$message['status'] = true;
    		$message['message'] = $returnMessage;
    		$message['code'] = $code;
    	}else{
    		$message['status'] = false;
    		$message['message'] = $returnMessage;
    		$message['code'] = $code;
    	}
    	return $message;
    }
       
    public function EVCTopup($data){
    	$SOAPApiService = new SOAPApiService($this->type, 'EVCTopup');
    	$result = [];
    	$externalReference = uniqid().time();
    	$body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:com="http://sdf.cellc.net/commonDataModel">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <com:SDF_Data>
				         <com:header>
				            <com:processTypeID>881232222</com:processTypeID>
				            <com:externalReference>'.$externalReference.'</com:externalReference>
				            <com:sourceID>'.$data['evc_username'].'</com:sourceID>
				            <com:username>'.$data['evc_username'].'</com:username>
				            <com:password>'.$data['evc_password'].'</com:password>
				            <com:processFlag>1</com:processFlag>
				         </com:header>
				         <com:parameters name="">
				            <com:parameter name="RechargeType">001</com:parameter>
				            <com:parameter name="MSISDN">'.$this->phoneNumber.'</com:parameter>
				            <com:parameter name="Amount">'.($data['top_up_amount']*100).'</com:parameter>
				            <com:parameter name="Channel_ID">35</com:parameter>
				         </com:parameters>
				         <com:result>
				            <com:statusCode/>
				            <com:errorCode/>
				            <com:errorDescription/>
				         </com:result>
				      </com:SDF_Data>
				   </soapenv:Body>
				</soapenv:Envelope>';
    	
    	
    	$headers = array('SOAPAction' => 'http://sdf.cellc.net/process', 'Content-Type' => 'text/xml');
    	
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	
    	# Full parsing, array have root element
    	$infoArray = Helper::xmlToArray($response->body, true);
		
    
    	
    	if($infoArray['soapenv:Envelope']['soapenv:Body']['com:SDF_Data']['com:result']['com:errorCode']== '0'){
    		return $externalReference;
    	}else{
    		return [
    				'error' => 1,
    				'message' => $infoArray['soapenv:Envelope']['soapenv:Body']['com:SDF_Data']['com:result']['com:errorDescription']
    		];
    	}
    	
    }
    
    
    
    public function EVCTopupSlave($data){
    	$SOAPApiService = new SOAPApiService($this->type, 'EVCTopupSlave');
    	$result = [];
    	$externalReference = 'epos'.uniqid().time();
    	
    	// key tb: eTjiFFZT5Pd3fsIe
    	// iv tb: W3hTskSF78I4j9tZ
    	$key = '0OyCLPi0Kw75AxcL';
    	$iv = 'W3hTskSF78I4j9tZ';
    	if($this->type == self::TYPE_TESTBED){
    		$key = 'eTjiFFZT5Pd3fsIe';
    		$iv = 'W3hTskSF78I4j9tZ';
    	}
    	$aes = new AES($key, $iv);
    	$pin = $aes->encrypt($data['evc_password']);
    	$body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:val="http://oss.huawei.com/business/intf/webservice/selfcare/values">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <val:NormalRechargeInterface>
				         <RequestHeader>
				            <val:CommandId>NormalRecharge</val:CommandId>
				            <val:Version>1</val:Version>
				            <val:TransactionId>'.$externalReference.'</val:TransactionId>
				            <val:SequenceId>'.$externalReference.'</val:SequenceId>
				            <val:RequestType>Event</val:RequestType>
				            <val:SerialNo>'.$externalReference.'</val:SerialNo>
				         </RequestHeader>
				         <NormalRechargeRequest>
				            <val:RetailerMSISDN>'.$data['evc_username'].'</val:RetailerMSISDN>
				            <val:RetailerPIN>'.$pin.'</val:RetailerPIN>
				            <val:RechargeAmount>'.$data['top_up_amount'].'</val:RechargeAmount>
				            <val:RechargepartyMSISDN>'.$this->phoneNumber.'</val:RechargepartyMSISDN>
				            <val:Channel_ID>35</val:Channel_ID>
				         </NormalRechargeRequest>
				      </val:NormalRechargeInterface>
				   </soapenv:Body>
				</soapenv:Envelope>';
    	$headers = array('SOAPAction' => 'http://sdf.cellc.net/process', 'Content-Type' => 'text/xml');
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	# Full parsing, array have root element
    	$infoArray = Helper::xmlToArray($response->body, true);	
    	$resultCode = $infoArray['soapenv:Envelope']['soapenv:Body']['val:NormalRechargeInterfaceResponse']['ResultHeader']['val:ResultCode'];
    	$resultDesc = $infoArray['soapenv:Envelope']['soapenv:Body']['val:NormalRechargeInterfaceResponse']['ResultHeader']['val:ResultDesc'];
    	
    
    	
    	
    	
    	if($resultCode== '405000000'){
    		return $infoArray['soapenv:Envelope']['soapenv:Body']['val:NormalRechargeInterfaceResponse']['NormalRechargeResult']['val:EVCTransactionID'];
    	}else{
    		return [
    				'error' => 1,
    				'message' => $resultDesc
    		];
    	}
    }
    
    
    /*
    public function EVCTopupSlave($data){
    	$SOAPApiService = new SOAPApiService($this->type, 'EVCTopupSlave');
    	$result = [];
    	$externalReference = 'epos'.uniqid().time();
		$key = 'GzoLkHkg/Ppn9+Hk';
    	$iv = 'W3hTskSF78I4j9tZ';
    	if($this->type == self::TYPE_TESTBED){
    		$key = 'eTjiFFZT5Pd3fsIe';
    		$iv = 'W3hTskSF78I4j9tZ';
    	}
    	$aes = new AES($key, $iv);
    	$pin = $aes->encrypt($data['evc_password']);
    	$data = [
    			'RequestHeader' => [
    					'CommandId' => "NormalRecharge",
    					'Version' => "1",
    					"TransactionId" => "1",
    					"SequenceId" => "1",
    					"RequestType" => "Event",
    					"SerialNo" => $externalReference
    			],
    			'NormalRechargeRequest' => [
    					"RetailerMSISDN" => $data['evc_username'],
    					"RetailerPIN" => $pin,
    					"RechargeAmount" => (string)$data['top_up_amount'],
    					"RechargepartyMSISDN" => $this->phoneNumber,
    					"Channel_ID" => "35"		
    			]
    	];
    	
    	// key tb: eTjiFFZT5Pd3fsIe
    	// iv tb: W3hTskSF78I4j9tZ
    	
    	$body = Unirest\Request\Body::json($data);
    	$url = '';
    	
    	
    	if(\App::environment() == 'production'){
    	 	$url = 'http://10.0.13.205:8280/hwevc/normalRecharge';
    	}else{
    	 	$url = 'http://10.0.13.205:8280/tb/hwevc/normalRecharge';
    	}    	
    	 
    	Unirest\Request::verifyPeer(false);
    	
    	$headers = array(
    			'Content-Type' => 'application/json',
    			'Accept' => 'application/json');
    	
    	$response = Unirest\Request::post($url, $headers, $body);
    	# Full parsing, array have root element
    	$result = $response->body;
    
    	
    	if($result->NormalRechargeInterfaceResponse->ResultHeader->ResultCode == '405000000'){
    		return $result->NormalRechargeInterfaceResponse->ResultHeader->ResultDesc;
    	}else{
    		return [
    				'error' => 1,
    				'message' => $result->NormalRechargeInterfaceResponse->ResultHeader->ResultDesc
    		];
    	}
    }
    
    */
    
    
    
    public function QueryDealerInfo(){
    	$SOAPApiService = new SOAPApiService($this->type, 'QueryDealerInfo');
    	$result = [];
    	$body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:bus="http://www.huawei.com/bme/evcinterface/evc/businessmgrmsg" xmlns:com="http://www.huawei.com/bme/evcinterface/common" xmlns:bus1="http://www.huawei.com/bme/evcinterface/evc/businessmgr"> 
				   <soapenv:Header/> 
				   <soapenv:Body> 
				      <bus:QueryDealerInfoRequestMsg> 
				         <RequestHeader> 
				            <com:CommandId>Business.QueryDealerInfo</com:CommandId> 
				            <com:Version>1</com:Version> 
				            <com:TransactionId>epos'.time().'</com:TransactionId> 
				            <com:SequenceId>epos'.time().'</com:SequenceId> 
				            <com:RequestType>Event</com:RequestType> 
				            <com:SessionEntity>
				               <com:Name>'.$SOAPApiService->getUser().'</com:Name>               
				               <com:Password>'.$SOAPApiService->getPassword().'</com:Password>               
				               <com:RemoteAddress>10.10.10.1</com:RemoteAddress> 
				            </com:SessionEntity> 
				            <com:InterFrom>4050007</com:InterFrom> 
				            <com:InterMode>4050001</com:InterMode> 
				            <com:SerialNo>T2012981010100023457</com:SerialNo> 
				            <com:Remark>QueryDealerInfo</com:Remark> 
				         </RequestHeader> 
				         <QueryDealerInfoRequest> 
				            <bus1:MSISDN>'.$this->phoneNumber.'</bus1:MSISDN> 
				         </QueryDealerInfoRequest> 
				      </bus:QueryDealerInfoRequestMsg> 
				   </soapenv:Body> 
				</soapenv:Envelope>';

    	$headers = array('SOAPAction' => $SOAPApiService->getSOAPAction(), 'Content-Type' => 'text/xml');
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	
    	# Full parsing, array have root element
    	$infoArray = Helper::xmlToArray($response->body, true);
    	$resultCode =  $infoArray['soapenv:Envelope']['soapenv:Body']['QueryDealerInfoResultMsg']['ResultHeader']['ResultCode'];
    	$resultDesc =  $infoArray['soapenv:Envelope']['soapenv:Body']['QueryDealerInfoResultMsg']['ResultHeader']['ResultDesc'];
   
    	
    	if($resultCode== '405000000'){
    		return [
    				'result_code' => $resultCode,
    				'status' => true,
    				'data' => $infoArray['soapenv:Envelope']['soapenv:Body']['QueryDealerInfoResultMsg']
    		];
    	}else{
    		return [
    				'result_code' => $resultCode,
    				'status' => false,
    				'data' => $resultDesc
    		];
    	}
    	
    }
    
    public function getSubscriberAllQuota(){
    	$result = null;
    	$SOAPApiService = new SOAPApiService($this->type, 'getSubscriberAllQuota');
    	$body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sub="http://www.huawei.com/bss/soaif/interface/SubscriberService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <sub:getSubscriberAllQuotaReqMsg>
				         <com:ReqHeader>
				            <!--Optional:-->
				            <com:Version>1</com:Version>
				            <!--Optional:-->
				            <com:BusinessCode>ADWTTX001</com:BusinessCode>
				            <com:TransactionId>ADWTTX0009</com:TransactionId>
				            <!--Optional:-->
				            <com:Channel>39</com:Channel>
				            <!--Optional:-->
				            <com:PartnerId>101</com:PartnerId>
				            <com:ReqTime>'.date('YmdHis').'</com:ReqTime>
				            <com:AccessUser>'.$SOAPApiService->getUser().'</com:AccessUser>
				            <com:AccessPassword>'.$SOAPApiService->getPassword().'</com:AccessPassword>
				         </com:ReqHeader>
				         <sub:ServiceNumber>'.$this->phoneNumber.'</sub:ServiceNumber>
				      </sub:getSubscriberAllQuotaReqMsg>
				   </soapenv:Body>
				</soapenv:Envelope>';
    	$headers = array('SOAPAction' => 'getSubscriberAllQuota', 'Content-Type' => 'text/xml');
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	# Full parsing, array have root element
    	$infoArray = Helper::xmlToArray($response->body, true);
    	
    	if($infoArray['soapenv:Envelope']['soapenv:Body']['sub:getSubscriberAllQuotaRspMsg']['com:RspHeader']['com:ReturnCode'] == '0'){
    		$subscriberQuota = $infoArray['soapenv:Envelope']['soapenv:Body']['sub:getSubscriberAllQuotaRspMsg'];
    		if(array_key_exists('sub:SubscriberQuotaList', $subscriberQuota)){
    			$result = [
    				'quota_name' => $subscriberQuota['sub:SubscriberQuotaList']['sub:QTANAME'],
    				'server_name' => $subscriberQuota['sub:SubscriberQuotaList']['sub:SRVNAME'],
    				'quota_value' => $subscriberQuota['sub:SubscriberQuotaList']['sub:QTAVALUE'],
    				'quota_balance' => $subscriberQuota['sub:SubscriberQuotaList']['sub:QTABALANCE'],
    				'quota_consumption' => $subscriberQuota['sub:SubscriberQuotaList']['sub:QTACONSUMPTION'],
    			];
    			
    		}
    	}
    	
    	return $result;
    }
    
    public function changePrimaryOffering($oldOfferingId, $newOfferingId, $effectiveDate){
    	$message = [
    			'status' => false,
    			'message' => 'No phone number provided',
    			'code' => ''
    	];
    	//return $message;
		 $SOAPApiService = new SOAPApiService($this->type, 'ChangePrimaryOffering');
	 
		//  dd(date('YmdHis', strtotime($effectiveDate)));
 
    	if($this->phoneNumber == null) return $message;
    	$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:off="http://www.huawei.com/bss/soaif/interface/OfferingService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <off:ChangePrimaryOfferingReqMsg>
				         <com:ReqHeader>
				            <com:Version>1</com:Version>
				            <com:TransactionId>'.time().'</com:TransactionId>
				            <com:Channel>39</com:Channel>
					        <com:PartnerId>101</com:PartnerId>
				            <com:ReqTime>'.date('Ymd000000').'</com:ReqTime>
				            <com:AccessUser>'.$SOAPApiService->getUser().'</com:AccessUser>
				            <com:AccessPassword>'.$SOAPApiService->getPassword().'</com:AccessPassword>
				         </com:ReqHeader>
				         <off:AccessInfo>
				            <com:ObjectIdType>4</com:ObjectIdType>
				            <com:ObjectId>'.$this->phoneNumber.'</com:ObjectId>
				         </off:AccessInfo>
				         <off:OldPrimaryOffering>
				            <com:OfferingId>'.$oldOfferingId.'</com:OfferingId>
				         </off:OldPrimaryOffering>
				         <off:NewPrimaryOffering>
				            <com:OfferingId>
				               <com:OfferingId>'.$newOfferingId.'</com:OfferingId>
				            </com:OfferingId>
				            <!--Optional:-->
				            <off:EffectiveMode>
				               <com:Mode>2</com:Mode>
				               <!--Optional:-->
				               <com:EffectiveDate>'.date('YmdHis', strtotime($effectiveDate)).'</com:EffectiveDate>
				            </off:EffectiveMode>
				         </off:NewPrimaryOffering>
				      </off:ChangePrimaryOfferingReqMsg>
				   </soapenv:Body>
				</soapenv:Envelope>';
    	$headers = array('SOAPAction' => 'ChangePrimaryOffering', 'Content-Type' => 'text/xml');
    	$body = $data;
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	# Full parsing, array have root element
		 $infoArray = Helper::xmlToArray($response->body, true);
		  

    	$code = $infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangePrimaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'];
    	$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangePrimaryOfferingRspMsg']['com:RspHeader']['com:ReturnMsg'];
    	 
    	
    	if($infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangePrimaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
    		$message['status'] = true;
    		$message['message'] = $returnMessage;
    		$message['code'] = $code;
    	}else{
    		$message['status'] = false;
    		$message['message'] = $returnMessage;
    		$message['code'] = $code;
		}

    	return $message;
	 }
	 
	 public function changePrimaryOfferingImmediately($oldOfferingId, $newOfferingId){
		$message = [
				'status' => false,
				'message' => 'No phone number provided',
				'code' => ''
		];
		//return $message;
		$SOAPApiService = new SOAPApiService($this->type, 'ChangePrimaryOffering');
	
	  //  dd(date('YmdHis', strtotime($effectiveDate)));


		if($this->phoneNumber == null) return $message;
		$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:off="http://www.huawei.com/bss/soaif/interface/OfferingService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
				  <soapenv:Header/>
				  <soapenv:Body>
					  <off:ChangePrimaryOfferingReqMsg>
						  <com:ReqHeader>
							  <com:Version>1</com:Version>
							  <com:TransactionId>'.time().'</com:TransactionId>
							  <com:Channel>39</com:Channel>
							 <com:PartnerId>101</com:PartnerId>
							  <com:ReqTime>'.date('Ymd000000').'</com:ReqTime>
							  <com:AccessUser>'.$SOAPApiService->getUser().'</com:AccessUser>
							  <com:AccessPassword>'.$SOAPApiService->getPassword().'</com:AccessPassword>
						  </com:ReqHeader>
						  <off:AccessInfo>
							  <com:ObjectIdType>4</com:ObjectIdType>
							  <com:ObjectId>'.$this->phoneNumber.'</com:ObjectId>
						  </off:AccessInfo>
						  <off:OldPrimaryOffering>
							  <com:OfferingId>'.$oldOfferingId.'</com:OfferingId>
						  </off:OldPrimaryOffering>
						  <off:NewPrimaryOffering>
							  <com:OfferingId>
								  <com:OfferingId>'.$newOfferingId.'</com:OfferingId>
							  </com:OfferingId>
							  <!--Optional:-->
							  <off:EffectiveMode>
								  <com:Mode>0</com:Mode>
							  </off:EffectiveMode>
						  </off:NewPrimaryOffering>
					  </off:ChangePrimaryOfferingReqMsg>
				  </soapenv:Body>
			  </soapenv:Envelope>';
		$headers = array('SOAPAction' => 'ChangePrimaryOffering', 'Content-Type' => 'text/xml');
		$body = $data;
		$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
		# Full parsing, array have root element
		$infoArray = Helper::xmlToArray($response->body, true);
		 

		$code = $infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangePrimaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'];
		$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangePrimaryOfferingRspMsg']['com:RspHeader']['com:ReturnMsg'];
		 
		
		if($infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangePrimaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
			$message['status'] = true;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}else{
			$message['status'] = false;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}
		return $message;
	}
	
	public function changePrimaryOfferingNextBill($oldOfferingId, $newOfferingId){
		$message = [
				'status' => false,
				'message' => 'No phone number provided',
				'code' => ''
		];
		//return $message;
		$SOAPApiService = new SOAPApiService($this->type, 'ChangePrimaryOffering');
	
	  //  dd(date('YmdHis', strtotime($effectiveDate)));


		if($this->phoneNumber == null) return $message;
		$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:off="http://www.huawei.com/bss/soaif/interface/OfferingService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
				  <soapenv:Header/>
				  <soapenv:Body>
					  <off:ChangePrimaryOfferingReqMsg>
						  <com:ReqHeader>
							  <com:Version>1</com:Version>
							  <com:TransactionId>'.time().'</com:TransactionId>
							  <com:Channel>39</com:Channel>
							 <com:PartnerId>101</com:PartnerId>
							  <com:ReqTime>'.date('Ymd000000').'</com:ReqTime>
							  <com:AccessUser>'.$SOAPApiService->getUser().'</com:AccessUser>
							  <com:AccessPassword>'.$SOAPApiService->getPassword().'</com:AccessPassword>
						  </com:ReqHeader>
						  <off:AccessInfo>
							  <com:ObjectIdType>4</com:ObjectIdType>
							  <com:ObjectId>'.$this->phoneNumber.'</com:ObjectId>
						  </off:AccessInfo>
						  <off:OldPrimaryOffering>
							  <com:OfferingId>'.$oldOfferingId.'</com:OfferingId>
						  </off:OldPrimaryOffering>
						  <off:NewPrimaryOffering>
							  <com:OfferingId>
								  <com:OfferingId>'.$newOfferingId.'</com:OfferingId>
							  </com:OfferingId>
							  <!--Optional:-->
							  <off:EffectiveMode>
								  <com:Mode>1</com:Mode>
							  </off:EffectiveMode>
						  </off:NewPrimaryOffering>
					  </off:ChangePrimaryOfferingReqMsg>
				  </soapenv:Body>
			  </soapenv:Envelope>';
		$headers = array('SOAPAction' => 'ChangePrimaryOffering', 'Content-Type' => 'text/xml');
		$body = $data;
		$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
		$infoArray = Helper::xmlToArray($response->body, true);
		 

		$code = $infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangePrimaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'];
		$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangePrimaryOfferingRspMsg']['com:RspHeader']['com:ReturnMsg'];
		 
		
		if($infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangePrimaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
			$message['status'] = true;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}else{
			$message['status'] = false;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}
		return $message;
	}

    
    public function getAccountInfo(){
    	$SOAPApiService = new SOAPApiService($this->type, 'QueryAccountInformation');
    	$data = "<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:acc='http://www.huawei.com/bss/soaif/interface/AccountService/' xmlns:com='http://www.huawei.com/bss/soaif/interface/common/'>
				   <soapenv:Header/>
				   <soapenv:Body>
				      <acc:QueryAcctInfoReqMsg>
				         <com:ReqHeader>
				            <com:TransactionId>ChangeAcctInfoa001101</com:TransactionId>
				            <com:Channel>28</com:Channel>
				            <com:PartnerId>101</com:PartnerId>
				            <com:BrandId>101</com:BrandId>
				            <com:ReqTime>".date('YmdHis')."</com:ReqTime>
				            <com:AccessUser>".$SOAPApiService->getUser()."</com:AccessUser>
				            <com:AccessPassword>".$SOAPApiService->getPassword()."</com:AccessPassword>
				         </com:ReqHeader>
				         <acc:AccessInfo>
				            <com:ObjectIdType>4</com:ObjectIdType>
				            <com:ObjectId>".$this->phoneNumber."</com:ObjectId>
				         </acc:AccessInfo>
				      </acc:QueryAcctInfoReqMsg>
				   </soapenv:Body>
				</soapenv:Envelope>";
    	$headers = array('SOAPAction' => 'QueryAccountInformation', 'Content-Type' => 'text/xml');
    	$body = $data;
    	
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	# Full parsing, array have root element
    	$infoArray = Helper::xmlToArray($response->body, true);
    	if ((int)$infoArray["soapenv:Envelope"]["soapenv:Body"]["acc:QueryAcctInfoRspMsg"]["com:RspHeader"]["com:ReturnCode"] == 0000){
    		$name = ['com:FirstName' => null, 'com:LastName' => null];
    		if(array_key_exists('com:Name', $infoArray["soapenv:Envelope"]["soapenv:Body"]["acc:QueryAcctInfoRspMsg"]["acc:Account"])){
    			$name = $infoArray["soapenv:Envelope"]["soapenv:Body"]["acc:QueryAcctInfoRspMsg"]["acc:Account"]["com:Name"];
    		}
    		return [
    			'account_id' => $infoArray["soapenv:Envelope"]["soapenv:Body"]["acc:QueryAcctInfoRspMsg"]["acc:Account"]["com:AcctId"],
    			'payment_type' => $infoArray["soapenv:Envelope"]["soapenv:Body"]["acc:QueryAcctInfoRspMsg"]["acc:Account"]["com:PaymentType"],
    			'name' => $name
    		];
    	} else { // return non smart type
    		return false;
    	}
    	
    }
    
    public function sendSMS($sourceNumber, $content){
    	$SOAPApiService = new SOAPApiService($this->type, 'SendSMS');
    	$message = [
    			'status' => false,
    			'message' => 'No phone number provided',
    			'code' => ''
    	];
    	if($this->phoneNumber == null) return $message;
    	
    	$data = "<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:util='http://www.huawei.com/bss/soaif/interface/UtilityService/' xmlns:com='http://www.huawei.com/bss/soaif/interface/common/'>
			<soapenv:Header/>
			<soapenv:Body>
			   <util:SendSMSReqMsg>
				  <com:ReqHeader>
					 <com:Version>1</com:Version>
					 <com:BusinessCode>101</com:BusinessCode>
					 <com:TransactionId>".time()."</com:TransactionId>
					 <com:Channel>28</com:Channel>
					 <com:ReqTime>".date('YmdHis')."</com:ReqTime>
					 <com:AccessUser>".$SOAPApiService->getUser()."</com:AccessUser>
					 <com:AccessPassword>".$SOAPApiService->getPassword()."</com:AccessPassword>
				  </com:ReqHeader>
				  <util:SMSInfo>
					 <util:BatchSeqId>9c6428.38382855</util:BatchSeqId>
					 <util:Content>".$content."</util:Content>
					 <util:DestinationNum>".$this->phoneNumber."</util:DestinationNum>
					 <util:SourceNum>".$sourceNumber."</util:SourceNum>
				  </util:SMSInfo>
			   </util:SendSMSReqMsg>
			</soapenv:Body>
		 </soapenv:Envelope>";
    	$headers = array('SOAPAction' => 'SendSMS', 'Content-Type' => 'text/xml');
    	$body = $data;
    	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
    	$infoArray = Helper::xmlToArray($response->body, true);
    	$returnCode = $infoArray['soapenv:Envelope']['soapenv:Body']['util:SendSMSRspMsg']['com:RspHeader']['com:ReturnCode'];
    	$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['util:SendSMSRspMsg']['com:RspHeader']['com:ReturnMsg'];
    	
    	if($returnCode == '0000'){
    		$message['status'] = true;
    		$message['message'] = $returnMessage;
    		$message['code'] = $returnCode;
    	}else{
    		$message['status'] = false;
    		$message['message'] = $returnMessage;
    		$message['code'] = $returnCode;
    	}
    	
		 return $message;
	 }
	 // Change Cust Info 

	 public function changeCustInfo($cust_id, $first_name, $last_name){
		$message = [
				'status' => false,
				'message' => 'No phone number provided',
				'code' => ''
		];
		//return $message;
		$SOAPApiService = new SOAPApiService($this->type, 'ChangeCustProfile');
	
	  //  dd(date('YmdHis', strtotime($effectiveDate)));

		if($this->phoneNumber == null) return $message;
		$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cus="http://www.huawei.com/bss/soaif/interface/CustomerService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
			<soapenv:Header/>
			<soapenv:Body>
				<cus:ChangeCustProfileReqMsg>
					<com:ReqHeader>            
						<com:Version>1</com:Version>
						<com:TransactionId>QueryAcctInfo0002</com:TransactionId>
						<com:Channel>28</com:Channel>
						<com:PartnerId>101</com:PartnerId>
						<com:ReqTime>20180608173000</com:ReqTime>
						<com:AccessUser>CRM.ENTERPRISE</com:AccessUser>
						<com:AccessPassword>jw8cn+D9LsKz2b3xz/TQmw==</com:AccessPassword>
					</com:ReqHeader>
					<cus:Customer>	
						<cus:CustId>'.$cust_id.'</cus:CustId>
						<cus:Name>
							<com:FirstName>'.$first_name.'</com:FirstName>                 
							<com:LastName>'.$last_name.'</com:LastName>
						</cus:Name>      
					</cus:Customer>
				</cus:ChangeCustProfileReqMsg>
			</soapenv:Body>
		</soapenv:Envelope>
		';
		$headers = array('SOAPAction' => 'ChangeCustomerProfile', 'Content-Type' => 'text/xml');
		$body = $data;
		
		$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
		//dd($response);
		# Full parsing, array have root element
		$infoArray = Helper::xmlToArray($response->body, true);
		 

		$code = $infoArray['soapenv:Envelope']['soapenv:Body']['cus:ChangeCustProfileRspMsg']['com:RspHeader']['com:ReturnCode'];
		$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['cus:ChangeCustProfileRspMsg']['com:RspHeader']['com:ReturnMsg'];

		if($infoArray['soapenv:Envelope']['soapenv:Body']['cus:ChangeCustProfileRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
			$message['status'] = true;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}else{
			$message['status'] = false;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}
		$myMessage=$message['message'];
		return $myMessage;
	} 
   
	public function ChangeEVCInfo($number, $old_name, $new_name){

		$message = [
				'status' => false,
				'message' => 'No phone number provided',
				'code' => ''
		];
		//return $message;
		$SOAPApiService = new SOAPApiService($this->type, 'ChangeDealerBasicInfo');
	
	  //  dd(date('YmdHis', strtotime($effectiveDate)));

		if($this->phoneNumber == null) return $message;
		$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:bus="http://www.huawei.com/bme/evcinterface/evc/businessmgrmsg" xmlns:com="http://www.huawei.com/bme/evcinterface/common" xmlns:bus1="http://www.huawei.com/bme/evcinterface/evc/businessmgr">
			<soapenv:Header/>
			<soapenv:Body>
				<bus:ChangeDealerBasicInfoRequestMsg>
					<RequestHeader>
						<com:CommandId>Business.ChangeDealerBasicInfo</com:CommandId> 
						<com:Version>1</com:Version> 
						<com:TransactionId>1</com:TransactionId> 
						<com:SequenceId>1</com:SequenceId> 
						<com:RequestType>Event</com:RequestType> 
						<!--Optional:-->
						<com:SessionEntity>
							<com:Name>sysadmin</com:Name>
							<com:Password>2CBAEF3E79F98F7CDE740787B8697D7E8F01C3642D5CB9FBA116584301CA42C2</com:Password>
							<com:RemoteAddress>10.10.10.1</com:RemoteAddress>
						</com:SessionEntity>
						<com:InterFrom>4050007</com:InterFrom> 
						<com:InterMode>4050001</com:InterMode> 
						<com:SerialNo>'.time().'</com:SerialNo> 
						<com:Remark>ChangeDealerBasicInfo</com:Remark>
					</RequestHeader>
					<ChangeDealerBasicInfoRequest>
						<bus1:MSISDN>'.$number.'</bus1:MSISDN>
						
						<bus1:DL_Name>'.$new_name.'</bus1:DL_Name>            
					</ChangeDealerBasicInfoRequest>
				</bus:ChangeDealerBasicInfoRequestMsg>
			</soapenv:Body>
		</soapenv:Envelope>
		';
		$headers = array('SOAPAction' => 'ChangeDealerBasicInfo', 'Content-Type' => 'text/xml');
		$body = $data;
		//dd ($SOAPApiService->getServiceUrl(), $headers, $body);
		$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
		//dd($response);
		# Full parsing, array have root element
		$infoArray = Helper::xmlToArray($response->body, true);
		 

		$code = $infoArray['soapenv:Envelope']['soapenv:Body']['ChangeDealerBasicInfoResultMsg']['ResultHeader']['ResultCode'];
		$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['ChangeDealerBasicInfoResultMsg']['ResultHeader']['ResultDesc'];
		
		if($infoArray['soapenv:Envelope']['soapenv:Body']['ChangeDealerBasicInfoResultMsg']['ResultHeader']['ResultCode'] == '0000'){
			$message['status'] = true;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}else{
			$message['status'] = false;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}
		$mymessage=$message['message'];
		return $mymessage;
	}
	// Start Change Dealer info

	public function ChangeDealerInfo($number, $old_name, $new_name){

		$message = [
				'status' => false,
				'message' => 'No phone number provided',
				'code' => ''
		];
		//return $message;
		$SOAPApiService = new SOAPApiService($this->type, 'ChangeDealerBasicInfo');
	
	  //  dd(date('YmdHis', strtotime($effectiveDate)));

		if($this->phoneNumber == null) return $message;
		$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:bus="http://www.huawei.com/bme/evcinterface/evc/businessmgrmsg" xmlns:com="http://www.huawei.com/bme/evcinterface/common" xmlns:bus1="http://www.huawei.com/bme/evcinterface/evc/businessmgr">
			<soapenv:Header/>
			<soapenv:Body>
				<bus:ChangeDealerBasicInfoRequestMsg>
					<RequestHeader>
						<com:CommandId>Business.ChangeDealerBasicInfo</com:CommandId> 
						<com:Version>1</com:Version> 
						<com:TransactionId>1</com:TransactionId> 
						<com:SequenceId>1</com:SequenceId> 
						<com:RequestType>Event</com:RequestType> 
						<!--Optional:-->
						<com:SessionEntity>
							<com:Name>sysadmin</com:Name>
							<com:Password>2CBAEF3E79F98F7CDE740787B8697D7E8F01C3642D5CB9FBA116584301CA42C2</com:Password>
							<com:RemoteAddress>10.10.10.1</com:RemoteAddress>
						</com:SessionEntity>
						<com:InterFrom>4050007</com:InterFrom> 
						<com:InterMode>4050001</com:InterMode> 
						<com:SerialNo>'.time().'</com:SerialNo> 
						<com:Remark>ChangeDealerBasicInfo</com:Remark>
					</RequestHeader>
					<ChangeDealerBasicInfoRequest>
						<bus1:MSISDN>'.$number.'</bus1:MSISDN>
						
						<bus1:DL_Name>'.$new_name.'</bus1:DL_Name>            
					</ChangeDealerBasicInfoRequest>
				</bus:ChangeDealerBasicInfoRequestMsg>
			</soapenv:Body>
		</soapenv:Envelope>
		';
		$headers = array('SOAPAction' => 'ChangeDealerBasicInfo', 'Content-Type' => 'text/xml');
		$body = $data;
		//dd ($SOAPApiService->getServiceUrl(), $headers, $body);
		$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
		//dd($response);
		# Full parsing, array have root element
		$infoArray = Helper::xmlToArray($response->body, true);
		 

		$code = $infoArray['soapenv:Envelope']['soapenv:Body']['ChangeDealerBasicInfoResultMsg']['ResultHeader']['ResultCode'];
		$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['ChangeDealerBasicInfoResultMsg']['ResultHeader']['ResultDesc'];
		// $returnMessage = 'Success9';
		
		if($infoArray['soapenv:Envelope']['soapenv:Body']['ChangeDealerBasicInfoResultMsg']['ResultHeader']['ResultCode'] == '0000'){
			$message['status'] = true;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}else{
			$message['status'] = false;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}
		$mymessage=$message['message'];
		return $mymessage;
	}
   // Start DeactivateSub
	public function DeactivateSub($number){
		$message = [
				'status' => false,
				'message' => 'No phone number provided',
				'code' => ''
		];
		//return $message;
		$SOAPApiService = new SOAPApiService($this->type,'DeactivateSubscriber');
	
	   //dd(date('YmdHis', strtotime($effectiveDate)));

		if($this->phoneNumber == null) return $message;
		$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sub="http://www.huawei.com/bss/soaif/interface/SubscriberService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
			<soapenv:Header/>
			<soapenv:Body>
				<sub:DeactivateSubReqMsg>
					<com:ReqHeader>
						<!--Optional:-->
						<com:Version>1</com:Version>          
						<com:TransactionId>'.time().'</com:TransactionId>
						<!--Optional:-->
						<com:Channel>28</com:Channel>
						<!--Optional:-->
						<com:PartnerId>101</com:PartnerId>            
						<com:ReqTime>20180608173000</com:ReqTime>
						<!--Optional:-->            
						<com:AccessUser>CRM.ENTERPRISE</com:AccessUser>
						<com:AccessPassword>jw8cn+D9LsKz2b3xz/TQmw==</com:AccessPassword>           
						<!--Zero or more repetitions:-->
						<com:AdditionalProperty>
							<com:Code>1</com:Code>
							<com:Value>1</com:Value>
						</com:AdditionalProperty>
					</com:ReqHeader>
					<sub:AccessInfo>
						<com:ObjectIdType>4</com:ObjectIdType>
						<com:ObjectId>'.$number.'</com:ObjectId>
					</sub:AccessInfo>
				</sub:DeactivateSubReqMsg>
			</soapenv:Body>
		</soapenv:Envelope>
		';
		$headers = array('SOAPAction' => 'DeactivateSubscriber', 'Content-Type' => 'text/xml');
		$body = $data;
		//dd ($SOAPApiService->getServiceUrl(), $headers, $body);
		$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
		//dd($response);
		# Full parsing, array have root element
		$infoArray = Helper::xmlToArray($response->body, true);
		 dd($infoArray1);
		 

		$code = $infoArray['soapenv:Envelope']['soapenv:Body']['sub:DeactivateSubRspMsg']['com:RspHeader']['com:ReturnCode'];
		$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['sub:DeactivateSubRspMsg']['com:RspHeader']['com:ReturnMsg'];
		$returnMessage = 'Success';
		
		if($infoArray['soapenv:Envelope']['soapenv:Body']['sub:DeactivateSubRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
			$message['status'] = true;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}else{
			$message['status'] = false;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}
		$mymessage=$message['message'];
		return $mymessage;
	}

	//Generate Token
	public function GenerateToken (){		

		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_PORT => "8243",
		CURLOPT_URL => "https://mife.smart.com.kh:8243/token",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "grant_type=client_credentials",
		CURLOPT_HTTPHEADER => array(
			"authorization: Basic YUpNcTByZTNUNlVHYUxKNkc4WVZPYlYwVXA4YTpLREQ0T1c3bkJFTkFKNk9XTEhZMTNqWVo4V01h",
			"cache-control: no-cache",
			"content-type: application/x-www-form-urlencoded",
			"postman-token: 103096e9-5f3a-2bd7-e544-d16540701a77"
		),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		echo "cURL Error #:" . $err;
		} else {
			$datajson = json_decode($response);
			echo $datajson->access_token;
		echo $response;
		//echo $response => "access_token";
		
		}
		return  $datajson->access_token;
		// $token = $response['access_token'];
		// $token = $response->access_token;

		//dd($datajson->access_token);
		//dd($response);
		//return $message;
	}
    /***
	* 
	================================= QueryAcctInfo Call Mutiple Time =======================================	
	================================= 15/07/2020           ==================================================	
	================================= Suy Kosal            ==================================================	

	*/
	public function QueryAcctInfo($number,$text,$textHead){
		$SOAPApiService = new SOAPApiService($this->type, 'QueryAccountInformation');
		if($this->phoneNumber == null) return $message;
		$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:acc="http://www.huawei.com/bss/soaif/interface/AccountService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
			<soapenv:Header/>
			<soapenv:Body>
			   <acc:QueryAcctInfoReqMsg>
				  <com:ReqHeader>
					 <com:Version>1</com:Version>
					 <com:TransactionId>'.time().'</com:TransactionId>
					 <com:Channel>28</com:Channel>
					 <com:PartnerId>101</com:PartnerId>
					 <com:ReqTime>20180608173000</com:ReqTime>
					 <com:AccessUser>'.$SOAPApiService->getUser().'</com:AccessUser>
					 <com:AccessPassword>'.$SOAPApiService->getPassword().'</com:AccessPassword>           
				  </com:ReqHeader>
				  <acc:AccessInfo>
					 <com:ObjectIdType>4</com:ObjectIdType>
					 <com:ObjectId>'.$number.'</com:ObjectId>
				  </acc:AccessInfo>
			   </acc:QueryAcctInfoReqMsg>
			</soapenv:Body>
		 </soapenv:Envelope>';
	
		$headers = array('SOAPAction' => 'QueryAccountInformation', 'Content-Type' => 'text/xml');
		$body = $data;
	
		$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
		$infoArray = Helper::xmlToArray($response->body, true);
		// $code = $infoArray['soapenv:Envelope']['soapenv:Body']['acc:QueryAcctInfoRspMsg']['acc:Account'][$text];
		$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['acc:QueryAcctInfoRspMsg'][$textHead][$text];
		// dd($infoArray);
		$CusId=$returnMessage;
		return $CusId;
	
	 }
	


    /***
	* 
	================================= ChangePreToPost      ==================================================	
	================================= 15/07/2020           ==================================================	
	================================= Suy Kosal            ==================================================	

	*/

	public function ChangePreToPost($number,$new_offering,$FirstName,$LastName,$BillMediumId,$marketingCategory,$billingGroup,$creditMode,$CreditLimitType,$LimitAmount){
		$message = [
				'status' => false,
				'message' => 'No phone number provided',
				'code' => ''
		];
		$code=$this->QueryAcctInfo($number,'com:ReturnCode','com:RspHeader');
		if($code!='1211000230'){
		$PaymentType=$this->QueryAcctInfo($number,'com:PaymentType','acc:Account');
		// dd($PaymentType);
		if(!$PaymentType){
		$CusId=$this->QueryAcctInfo($number,'acc:CustId','acc:Account');
		$SOAPApiService1 = new SOAPApiService($this->type, 'ChangePrepaidToPostpaid');
		$data1 = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sub="http://www.huawei.com/bss/soaif/interface/SubscriberService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
			<soapenv:Header/>
			<soapenv:Body>
			   <sub:ChangePrepaidToPostpaidReqMsg>
				  <com:ReqHeader>
					 <!--Optional:-->
					 <com:Version>1</com:Version>
					 <com:TransactionId>'.time().'</com:TransactionId>
					 <!--Optional:-->
					 <com:Channel>28</com:Channel>
					 <!--Optional:-->
					 <com:PartnerId>101</com:PartnerId>
					 <com:ReqTime>20180608173000</com:ReqTime>
					 <com:AccessUser>'.$SOAPApiService1->getUser().'</com:AccessUser>
					 <com:AccessPassword>'.$SOAPApiService1->getPassword().'</com:AccessPassword>
				  </com:ReqHeader>
				  <sub:AccessInfo>
					 <com:ObjectIdType>4</com:ObjectIdType>
					 <com:ObjectId>'.$number.'</com:ObjectId>
				  </sub:AccessInfo>
				  <sub:PostpaidAcct>
					 <sub:Account>
						<sub:CustId>'.$CusId.'</sub:CustId>
						<sub:PaymentType>1</sub:PaymentType>
						<sub:Contact>
						<!--Optional
						<com:ContactId>11111111</com:ContactId> :-->
						<!--Optional:-->
						<com:Name>
						   <!--Optional:-->
						   <com:FirstName>"'.$FirstName.'"</com:FirstName>
						   <!--Optional:-->
						   <com:MiddleName></com:MiddleName>
						   <!--Optional:-->
						   <com:LastName>"'.$LastName.'"</com:LastName>
						</com:Name>      
						<sub:ActionType>2</sub:ActionType>
					 </sub:Contact>              
						<sub:Address>               
						<com:AddressId>4000000020965</com:AddressId>           
						<!--Optional:-->
						<com:AddressType>4</com:AddressType>    <!-- 4=Account Address -->            
						<!--Optional:-->
						<com:Address1>1116</com:Address1>
						<!--Optional:-->
						<com:Address2>15</com:Address2>
						<!--Optional:-->
						<com:Address3>93</com:Address3>               
						<!--Optional:-->
						<com:Address11>149</com:Address11>                    
						<sub:ActionType>1</sub:ActionType>
						</sub:Address>
					   <sub:BillMedium>
						  
						   <com:BillMediumId>'.$BillMediumId.'</com:BillMediumId>
						   
						   <com:BillMediumCode>1</com:BillMediumCode>
						   
						   <com:BillContentType>1</com:BillContentType>                  
						</sub:BillMedium>             
						<sub:Currency>1153</sub:Currency>               
						<!--Zero or more repetitions:-->
						<sub:CreditLimit>
						   <com:LimitType>All</com:LimitType>
						   <com:LimitValue>000000000</com:LimitValue>
						</sub:CreditLimit>
					 </sub:Account>
				  </sub:PostpaidAcct>
				  <sub:Offering>
					 <!--Optional:-->            
					 <sub:NewPrimaryOffering>
						<com:OfferingId>
						   <com:OfferingId>'.$new_offering.'</com:OfferingId> <!--Value 5:-->                 
						</com:OfferingId>                
					 </sub:NewPrimaryOffering>
				  </sub:Offering>         
			   </sub:ChangePrepaidToPostpaidReqMsg>
			</soapenv:Body>
		 </soapenv:Envelope>
		';

		$headers1 = array('SOAPAction' => 'ChangePrepaidToPostpaid', 'Content-Type' => 'text/xml');
		$body_ = $data1;
		$response1 = Unirest\Request::post($SOAPApiService1->getServiceUrl(), $headers1, $body_);
		$infoArray1 = Helper::xmlToArray($response1->body, true);
		$code_A = $infoArray1['soapenv:Envelope']['soapenv:Body']['sub:ChangePrepaidToPostpaidRspMsg']['com:RspHeader']['com:ReturnCode'];
		$message_A = $infoArray1['soapenv:Envelope']['soapenv:Body']['sub:ChangePrepaidToPostpaidRspMsg']['com:RspHeader']['com:ReturnMsg'];
	  
	//    dd($infoArray1);
	
	if($code_A!='1211000163'){
		$number_old=$this->QueryAcctInfo($number,'com:AcctId','acc:Account');
		do{
			$number_new=$number_old;
			$number_old=$this->QueryAcctInfo($number,'com:AcctId','acc:Account');
		}while($number_old==$number_new);

		  $AccId=$this->QueryAcctInfo($number,'com:AcctId','acc:Account');


	// dd($AccId);
		$SOAPApiService1 = new SOAPApiService($this->type, 'ChangeAcctCreditLimit');
		$data3 = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
		<soapenv:Body>
		   <bcs:ChangeAcctCreditLimitRequestMsg xmlns:bcc="http://www.huawei.com/bme/cbsinterface/bccommon" xmlns:bcs="http://www.huawei.com/bme/cbsinterface/bcservices" xmlns:cbs="http://www.huawei.com/bme/cbsinterface/cbscommon">
			  <RequestHeader>
				 <cbs:Version>1</cbs:Version>
				 <cbs:MessageSeq>'.time().'</cbs:MessageSeq>
				 <cbs:OwnershipInfo>
					<cbs:BEID>101</cbs:BEID>
				 </cbs:OwnershipInfo>
				 <cbs:AccessSecurity>
				 <cbs:LoginSystemCode>'.$SOAPApiService1->getUser().'</cbs:LoginSystemCode>
				 <cbs:Password>'.$SOAPApiService1->getPassword().'</cbs:Password>
				 </cbs:AccessSecurity>
			  </RequestHeader>
			  <ChangeAcctCreditLimitRequest>
				 <bcs:AcctAccessCode> 
				 <!--Optional  
					<bcc:AccountCode>1.3154352</bcc:AccountCode>
					<bcc:PrimaryIdentity>1010020263218</bcc:PrimaryIdentity>
				  :-->
				 <bcc:AccountKey>'.$AccId.'</bcc:AccountKey> <!-- AcctId in CRM :-->
				 </bcs:AcctAccessCode>
				 <bcs:AddAccountCredit>
					<bcs:CreditLimitType>'.$CreditLimitType.'</bcs:CreditLimitType>
					<bcs:LimitAmount>'.$LimitAmount.'</bcs:LimitAmount>
					<bcs:EffectiveTime>
					   <bcc:Mode>I</bcc:Mode>
					</bcs:EffectiveTime>
				 </bcs:AddAccountCredit>
			  </ChangeAcctCreditLimitRequest>
		   </bcs:ChangeAcctCreditLimitRequestMsg>
		</soapenv:Body>
	 </soapenv:Envelope>
		';

		$headers1 = array('SOAPAction' => 'ChangeAcctCreditLimit', 'Content-Type' => 'text/xml');
		$body_ = $data3;
		$response1 = Unirest\Request::post($SOAPApiService1->getServiceUrl(), $headers1, $body_);
		$infoArray1 = Helper::xmlToArray($response1->body, true);
	
		$AccId=$this->QueryAcctInfo($number,'com:AcctId','acc:Account');

		$SOAPApiService1 = new SOAPApiService($this->type, 'ChangeAccountInformation');
		$data4 = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:acc="http://www.huawei.com/bss/soaif/interface/AccountService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
			<soapenv:Header/>
			<soapenv:Body>
			   <acc:ChangeAcctInfoReqMsg>
				  <com:ReqHeader>
					 <com:Version>1</com:Version>
					 <com:TransactionId>'.time().'</com:TransactionId>
					 <com:Channel>28</com:Channel>
					 <com:PartnerId>101</com:PartnerId>
					 <com:ReqTime>20180612221100</com:ReqTime>           
					 <com:AccessUser>'.$SOAPApiService1->getUser().'</com:AccessUser>
					 <com:AccessPassword>'.$SOAPApiService1->getPassword().'</com:AccessPassword> 
				  </com:ReqHeader>
				  <acc:Account>
					 <acc:AcctId>'.$AccId.'</acc:AcctId>
					 <acc:BillLanguage>2002</acc:BillLanguage>
					 <acc:Title>0</acc:Title>
					 <acc:Name>
						<com:FirstName>'.$FirstName.'</com:FirstName>
						<com:MiddleName></com:MiddleName>
						<com:LastName>'.$LastName.'</com:LastName>
					 </acc:Name>
					 <acc:Address>
						<com:AddressType>0</com:AddressType>
						<com:Address1>1116</com:Address1>
						<com:Address2>15</com:Address2>
						<com:Address3>29</com:Address3>
						<com:Address11>854</com:Address11>
						<com:SmsNumber>10202177</com:SmsNumber>
						<acc:ActionType>2</acc:ActionType>
					 </acc:Address>
					 <acc:BillCycleType>01</acc:BillCycleType>
					 <acc:AcctPayMethod>2</acc:AcctPayMethod>
					 <acc:AdditionalProperty>
						<com:Code>marketingCategory</com:Code><!--2=Standard Natural Person-->
						<com:Value>'.$marketingCategory.'</com:Value>
					 </acc:AdditionalProperty>
				   <acc:AdditionalProperty>
						<com:Code>billingGroup</com:Code><!--7=Postpaid, 8=Prepaid-->
						<com:Value>'.$billingGroup.'</com:Value>
					 </acc:AdditionalProperty>            
					 <acc:AdditionalProperty>
						<com:Code>creditMode</com:Code>
						<com:Value>'.$creditMode.'</com:Value>
					 </acc:AdditionalProperty>
					<acc:AdditionalProperty>
						<com:Code>dunningPlan</com:Code>
						<com:Value>28</com:Value>
					 </acc:AdditionalProperty>            
					  <acc:AdditionalProperty>
						<com:Code>zoneId</com:Code>
						<com:Value>1</com:Value>
					 </acc:AdditionalProperty>  
					  <acc:AdditionalProperty>
						<com:Code>freeBillMediumFlag</com:Code>
						<com:Value>0</com:Value>
					 </acc:AdditionalProperty>           
				  </acc:Account>
			   </acc:ChangeAcctInfoReqMsg>
			</soapenv:Body>
		 </soapenv:Envelope>
		';
		
		$headers1 = array('SOAPAction' => 'ChangeAccountInformation', 'Content-Type' => 'text/xml');
		$body_ = $data4;
		$response1 = Unirest\Request::post($SOAPApiService1->getServiceUrl(), $headers1, $body_);
		$infoArray1 = Helper::xmlToArray($response1->body, true);

		$code = $infoArray1['soapenv:Envelope']['soapenv:Body']['acc:ChangeAcctInfoRspMsg']['com:RspHeader']['com:ReturnCode'];
		$returnMessage = $infoArray1['soapenv:Envelope']['soapenv:Body']['acc:ChangeAcctInfoRspMsg']['com:RspHeader']['com:ReturnMsg'];
		
		if($infoArray1['soapenv:Envelope']['soapenv:Body']['acc:ChangeAcctInfoRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
			$message['status'] = true;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}else{
			$message['status'] = false;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}
			
		$message=$message['message'];
		return $message;
	}else{
		$CusId=$this->QueryAcctInfo($number,'acc:CustId','acc:Account');
		$SOAPApiService1 = new SOAPApiService($this->type, 'ChangePrepaidToPostpaid');
		$data1 = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sub="http://www.huawei.com/bss/soaif/interface/SubscriberService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
			<soapenv:Header/>
			<soapenv:Body>
			   <sub:ChangePrepaidToPostpaidReqMsg>
				  <com:ReqHeader>
					 <!--Optional:-->
					 <com:Version>1</com:Version>
					 <com:TransactionId>'.time().'</com:TransactionId>
					 <!--Optional:-->
					 <com:Channel>28</com:Channel>
					 <!--Optional:-->
					 <com:PartnerId>101</com:PartnerId>
					 <com:ReqTime>20180608173000</com:ReqTime>
					 <com:AccessUser>'.$SOAPApiService1->getUser().'</com:AccessUser>
					 <com:AccessPassword>'.$SOAPApiService1->getPassword().'</com:AccessPassword>
				  </com:ReqHeader>
				  <sub:AccessInfo>
					 <com:ObjectIdType>4</com:ObjectIdType>
					 <com:ObjectId>'.$number.'</com:ObjectId>
				  </sub:AccessInfo>
				  <sub:PostpaidAcct>
					 <sub:Account>
						<sub:CustId>'.$CusId.'</sub:CustId>
						<sub:PaymentType>1</sub:PaymentType>
						<sub:Contact>
						<!--Optional
						<com:ContactId>11111111</com:ContactId> :-->
						<!--Optional:-->
						<com:Name>
						   <!--Optional:-->
						   <com:FirstName>'.$FirstName.'</com:FirstName>
						   <!--Optional:-->
						   <com:MiddleName></com:MiddleName>
						   <!--Optional:-->
						   <com:LastName>'.$LastName.'</com:LastName>
						</com:Name>      
						<sub:ActionType>2</sub:ActionType>
					 </sub:Contact>              
						<sub:Address>               
						<com:AddressId>4000000020965</com:AddressId>           
						<!--Optional:-->
						<com:AddressType>4</com:AddressType>    <!-- 4=Account Address -->            
						<!--Optional:-->
						<com:Address1>1116</com:Address1>
						<!--Optional:-->
						<com:Address2>15</com:Address2>
						<!--Optional:-->
						<com:Address3>93</com:Address3>               
						<!--Optional:-->
						<com:Address11>149</com:Address11>                    
						<sub:ActionType>1</sub:ActionType>
						</sub:Address>
					   <sub:BillMedium>
						  
						   <com:BillMediumId>'.$BillMediumId.'</com:BillMediumId>
						   
						   <com:BillMediumCode>1</com:BillMediumCode>
						   
						   <com:BillContentType>1</com:BillContentType>                  
						</sub:BillMedium>             
						<sub:Currency>1153</sub:Currency>               
						<!--Zero or more repetitions:-->
						<sub:CreditLimit>
						   <com:LimitType>All</com:LimitType>
						   <com:LimitValue>000000000</com:LimitValue>
						</sub:CreditLimit>
					 </sub:Account>
				  </sub:PostpaidAcct>
				  <sub:Offering>
					 <!--Optional:-->            
					 <sub:NewPrimaryOffering>
						<com:OfferingId>
						   <com:OfferingId>'.$new_offering.'</com:OfferingId> <!--Value 5:-->                 
						</com:OfferingId>                
					 </sub:NewPrimaryOffering>
				  </sub:Offering>         
			   </sub:ChangePrepaidToPostpaidReqMsg>
			</soapenv:Body>
		 </soapenv:Envelope>
		';

		$headers1 = array('SOAPAction' => 'ChangePrepaidToPostpaid', 'Content-Type' => 'text/xml');
		$body_ = $data1;
		$response1 = Unirest\Request::post($SOAPApiService1->getServiceUrl(), $headers1, $body_);
		$infoArray1 = Helper::xmlToArray($response1->body, true);
		// $code_A = $infoArray1['soapenv:Envelope']['soapenv:Body']['sub:ChangePrepaidToPostpaidRspMsg']['com:RspHeader']['com:ReturnCode'];
		$message_A = $infoArray1['soapenv:Envelope']['soapenv:Body']['sub:ChangePrepaidToPostpaidRspMsg']['com:RspHeader']['com:ReturnMsg'];
	
		return $message_A;
	}
	

	}else{
		
		$message='This number is already postpaid!';
		return $message;
	}
		$message=$message['message'];
		return $message;

	}else{
		$message=$this->QueryAcctInfo($number,'com:ReturnMsg','com:RspHeader');
		return $message;
	}
	}


	// Start Change Post to Pre
	public function ChangePostToPre($number, $offer_name, $offer_id){

		$message = [
				'status' => false,
				'message' => 'No phone number provided',
				'code' => ''
		];
		//return $message;
		$SOAPApiService = new SOAPApiService($this->type, 'ChangePostpaidToPrepaid');
	
	  //  dd(date('YmdHis', strtotime($effectiveDate)));

		if($this->phoneNumber == null) return $message;
		$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sub="http://www.huawei.com/bss/soaif/interface/SubscriberService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
			<soapenv:Header/>
			<soapenv:Body>
				<sub:ChangePostpaidToPrepaidReqMsg>
					 <com:ReqHeader>
						<!--Optional:-->
						<com:Version>1</com:Version>
						<com:TransactionId>TreaPostpaid0232</com:TransactionId>
						<!--Optional:-->
						<com:Channel>28</com:Channel>
						<!--Optional:-->
						<com:PartnerId>101</com:PartnerId>
						<com:ReqTime>20180612221100</com:ReqTime>            
						<com:AccessUser>CRM.ENTERPRISE</com:AccessUser>
						<com:AccessPassword>jw8cn+D9LsKz2b3xz/TQmw==</com:AccessPassword>  
					</com:ReqHeader>
					<sub:AccessInfo>
						<com:ObjectIdType>4</com:ObjectIdType>
						<com:ObjectId>'.$number.'</com:ObjectId>
					</sub:AccessInfo>
					<sub:Offering>
						<sub:NewPrimaryOffering>
							<com:OfferingId>
								<com:OfferingId>'.$offer_id.'</com:OfferingId>
								<!--Optional:-->
								<!--com:PurchaseSeq>?</com:PurchaseSeq-->
							</com:OfferingId>
							<sub:EffectiveMode>
								<com:Mode>0</com:Mode>
								<!--Optional:-->
								<!--com:EffectiveDate>?</com:EffectiveDate-->
							</sub:EffectiveMode>
						</sub:NewPrimaryOffering>
					</sub:Offering>
				</sub:ChangePostpaidToPrepaidReqMsg>
			</soapenv:Body>
		</soapenv:Envelope>		
		';
		$headers = array('SOAPAction' => 'ChangePostpaidToPrepaid', 'Content-Type' => 'text/xml');
		$body = $data;
		$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
		//dd ($SOAPApiService->getServiceUrl(), $headers, $body);
		# Full parsing, array have root element
		$infoArray = Helper::xmlToArray($response->body, true);
		 

		$code = $infoArray['soapenv:Envelope']['soapenv:Body']['sub:ChangePostpaidToPrepaidRspMsg']['com:RspHeader']['com:ReturnCode'];
		$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['sub:ChangePostpaidToPrepaidRspMsg']['com:RspHeader']['com:ReturnMsg'];
		// $returnMessage = 	$response->body;
		
		if($infoArray['soapenv:Envelope']['soapenv:Body']['sub:ChangePostpaidToPrepaidRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
			$message['status'] = true;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}else{
			$message['status'] = false;
			$message['message'] = $returnMessage;
			$message['code'] = $code;
		}
		$mymessage=$message['message'];
		return $mymessage;
	}


	// Start Change Acct info

public function ChangeAcctInfo($number, $acct_id, $new_cust_language, $last_name, $first_name, $title,
$Nationality, $province, $district, $Commune, $Village, $Street, $houseNo, $block, $market_message,$billinggroup, $freeBillmediumFlag){

   $message = [
		   'status' => false,
		   'message' => 'No phone number provided',
		   'code' => ''
   ];
   //return $message;
   $SOAPApiService = new SOAPApiService($this->type, 'ChangeAccountInformation');

 //  dd(date('YmdHis', strtotime($effectiveDate)));
   
   if($this->phoneNumber == null) return $message;
   $data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:acc="http://www.huawei.com/bss/soaif/interface/AccountService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
	   <soapenv:Header/>
	   <soapenv:Body>
		  <acc:ChangeAcctInfoReqMsg>
			 <com:ReqHeader>
				<com:Version>1</com:Version>
				<com:TransactionId>PanhaPostpaid0115</com:TransactionId>
				<com:Channel>28</com:Channel>
				<com:PartnerId>101</com:PartnerId>
				<com:ReqTime>20180612221100</com:ReqTime>           
				<com:AccessUser>CRM.ENTERPRISE</com:AccessUser>
				<com:AccessPassword>jw8cn+D9LsKz2b3xz/TQmw==</com:AccessPassword> 
			 </com:ReqHeader>
			 <acc:Account>
				<acc:AcctId>'.$acct_id.'</acc:AcctId>
				<acc:BillLanguage>'.$new_cust_language.'</acc:BillLanguage>
				<acc:Title>0</acc:Title>
				<acc:Name>
				   <com:FirstName>'.$first_name.'</com:FirstName>
				   <com:MiddleName></com:MiddleName>
				   <com:LastName>'.$last_name.'</com:LastName>
				</acc:Name>
				<acc:Address>
				   <com:AddressType>0</com:AddressType>
				   <com:Address1>'.$Nationality.'</com:Address1>
				   <com:Address2>'.$province.'</com:Address2>
				   <com:Address3>'.$district.'</com:Address3>
				   <com:Address11>'.$Commune.'</com:Address11>
				   <com:Address4>'.$Village.'</com:Address4>
				   <com:Address5>'.$Street.'</com:Address5>
				   <com:Address6>'.$block.'</com:Address6>
				   <com:Address7>'.$houseNo.'</com:Address7>
				   <com:SmsNumber></com:SmsNumber>
				   <acc:ActionType>2</acc:ActionType>
				</acc:Address>
				<acc:BillCycleType>01</acc:BillCycleType>
				<acc:AcctPayMethod>2</acc:AcctPayMethod>
				<acc:AdditionalProperty>
				   <com:Code>marketingCategory</com:Code><!--2=Standard Natural Person-->
				   <com:Value>'.$market_message.'</com:Value>
				</acc:AdditionalProperty>
			  <acc:AdditionalProperty>
				   <com:Code>billingGroup</com:Code><!--7=Postpaid, 8=Prepaid-->
				   <com:Value>'.$billinggroup.'</com:Value>
				</acc:AdditionalProperty>            
				<acc:AdditionalProperty>
				   <com:Code>creditMode</com:Code>
				   <com:Value>1</com:Value>
				</acc:AdditionalProperty>
			   <acc:AdditionalProperty>
				   <com:Code>dunningPlan</com:Code>
				   <com:Value>28</com:Value>
				</acc:AdditionalProperty>            
				 <acc:AdditionalProperty>
				   <com:Code>zoneId</com:Code>
				   <com:Value>1</com:Value>
				</acc:AdditionalProperty>  
				 <acc:AdditionalProperty>
				   <com:Code>freeBillMediumFlag</com:Code>
				   <com:Value>'.$freeBillmediumFlag.'</com:Value>
				</acc:AdditionalProperty>           
			 </acc:Account>
		  </acc:ChangeAcctInfoReqMsg>
	   </soapenv:Body>
	</soapenv:Envelope>
   ';
   $headers = array('SOAPAction' => 'ChangeAccountInformation', 'Content-Type' => 'text/xml');
   $body = $data;
   $response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
   $infoArray = Helper::xmlToArray($response->body, true);
	

   $code = $infoArray['soapenv:Envelope']['soapenv:Body']['acc:ChangeAcctInfoRspMsg']['com:RspHeader']['com:ReturnCode'];
   $returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['acc:ChangeAcctInfoRspMsg']['com:RspHeader']['com:ReturnMsg'];

   
   if($infoArray['soapenv:Envelope']['soapenv:Body']['acc:ChangeAcctInfoRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
   	$message['status'] = true;
   	$message['message'] = $returnMessage;
   	$message['code'] = $code;
   }else{
   	$message['status'] = false;
   	$message['message'] = $returnMessage;
   	$message['code'] = $code;
   }
   $mymessage=$message['message'];
   return $mymessage;
}

//End Change Acct Info//


 // Start ActivateSub
 public function activateSub($number){
	$message = [
			'status' => false,
			'message' => 'No phone number provided',
			'code' => ''
	];
	//return $message;
	$SOAPApiService = new SOAPApiService($this->type, 'ActivateSubscriber');

   //dd(date('YmdHis', strtotime($effectiveDate)));

	if($this->phoneNumber == null) return $message;
	$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sub="http://www.huawei.com/bss/soaif/interface/SubscriberService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
		<soapenv:Header/>
		<soapenv:Body>
		   <sub:ActivateSubReqMsg>
			  <com:ReqHeader>
				 <com:Version>1</com:Version>
				 <com:TransactionId>CRM20171061413131313134</com:TransactionId>
				 <com:Channel>28</com:Channel>
				 <com:PartnerId>101</com:PartnerId>
				 <com:ReqTime>20151226102900</com:ReqTime>
				 <com:AccessUser>CRM.ENTERPRISE</com:AccessUser>
				 <com:AccessPassword>jw8cn+D9LsKz2b3xz/TQmw==</com:AccessPassword>
			  </com:ReqHeader>
			  <sub:AccessInfo>
				 <com:ObjectIdType>4</com:ObjectIdType>
				 <com:ObjectId>'.$number.'</com:ObjectId>
			  </sub:AccessInfo>
		   </sub:ActivateSubReqMsg>
		</soapenv:Body>
	 </soapenv:Envelope>
	 
	';
	$headers = array('SOAPAction' => 'ActivateSubscriber', 'Content-Type' => 'text/xml');
	$body = $data;
	//dd ($SOAPApiService->getServiceUrl(), $headers, $body);
	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
	//dd($response);
	# Full parsing, array have root element
	$infoArray = Helper::xmlToArray($response->body, true);
	 

	$code = $infoArray['soapenv:Envelope']['soapenv:Body']['sub:ActivateSubRspMsg']['com:RspHeader']['com:ReturnCode'];
	$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['sub:ActivateSubRspMsg']['com:RspHeader']['com:ReturnMsg'];
	// $returnMessage = 'Success9';
	
	if($infoArray['soapenv:Envelope']['soapenv:Body']['sub:ActivateSubRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
		$message['status'] = true;
		$message['message'] = $returnMessage;
		$message['code'] = $code;
	}else{
		$message['status'] = false;
		$message['message'] = $returnMessage;
		$message['code'] = $code;
	}
	$mymessage=$message['message'];
		return $mymessage;
	// return $message;
}

//End ActivateSub


public function AddSubOffer($number, $offer_id)
{
	$message = [
		'status' => false,
		'message' => 'No phone number provided',
		'code' => ''
	];
	$SOAPApiService = new SOAPApiService($this->type, 'ChangeSupplementaryOffering');
	if($this->phoneNumber == null) return $message;
	$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:off="http://www.huawei.com/bss/soaif/interface/OfferingService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
		<soapenv:Header/>
		<soapenv:Body>
		   <off:ChangeSupplementaryOfferingReqMsg>
			  <com:ReqHeader>
				 <com:TransactionId>20200125100000</com:TransactionId>
				 <com:Channel>28</com:Channel>
				 <com:PartnerId>101</com:PartnerId>
				 <com:ReqTime>20190625100000</com:ReqTime>
				 <com:AccessUser>CRM.ENTERPRISE</com:AccessUser>
				 <com:AccessPassword>jw8cn+D9LsKz2b3xz/TQmw==</com:AccessPassword>  
			  </com:ReqHeader>
			  <off:AccessInfo>
				 <com:ObjectIdType>4</com:ObjectIdType>
				 <com:ObjectId>'.$number.'</com:ObjectId>
			  </off:AccessInfo>
			  <!--Zero or more repetitions:-->
			  <off:AddOffering>
				 <com:OfferingId>
					<com:OfferingId>'.$offer_id.'</com:OfferingId>
				 </com:OfferingId>       
			  </off:AddOffering>
		   </off:ChangeSupplementaryOfferingReqMsg>
		</soapenv:Body>
	 </soapenv:Envelope>
	';
	$headers = array('SOAPAction' => 'ChangeSupplementaryOffering', 'Content-Type' => 'text/xml');
	$body = $data;
	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
	$infoArray = Helper::xmlToArray($response->body, true);

	$code = $infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangeSupplementaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'];
	$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangeSupplementaryOfferingRspMsg']['com:RspHeader']['com:ReturnMsg'];
	
	if($infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangeSupplementaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
	 $message['status'] = true;
	 $message['message'] = $returnMessage;
	 $message['code'] = $code;
 }else{
	 $message['status'] = false;
	 $message['message'] = $returnMessage;
	 $message['code'] = $code;
 }
	 $Message= $message['message'];
	return $Message;
}


public function RemoveSubOffer($number, $offer_id)
{
	$message = [
		'status' => false,
		'message' => 'No phone number provided',
		'code' => ''
	];
	$SOAPApiService = new SOAPApiService($this->type, 'ChangeSupplementaryOffering');
	if($this->phoneNumber == null) return $message;
	$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:off="http://www.huawei.com/bss/soaif/interface/OfferingService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
		<soapenv:Header/>
		<soapenv:Body>
		   <off:ChangeSupplementaryOfferingReqMsg>
			  <com:ReqHeader>
				 <com:TransactionId>20200125100011</com:TransactionId>
				 <com:Channel>28</com:Channel>
				 <com:PartnerId>101</com:PartnerId>
				 <com:ReqTime>20190625100022</com:ReqTime>
				 <com:AccessUser>CRM.ENTERPRISE</com:AccessUser>
				 <com:AccessPassword>jw8cn+D9LsKz2b3xz/TQmw==</com:AccessPassword>
			  </com:ReqHeader>
			  <off:AccessInfo>
				 <com:ObjectIdType>4</com:ObjectIdType>
				 <com:ObjectId>'.$number.'</com:ObjectId>
			  </off:AccessInfo>
			  <!--Zero or more repetitions:-->
			  <off:DeleteOffering>
				 <off:OfferingId>
					<com:OfferingId>'.$offer_id.'</com:OfferingId>
				 </off:OfferingId>
				 <off:ExpireMode>
					<off:Mode>I</off:Mode>
				 </off:ExpireMode>
			  </off:DeleteOffering>
		   </off:ChangeSupplementaryOfferingReqMsg>
		</soapenv:Body>
	 </soapenv:Envelope>
	 ';
	$headers = array('SOAPAction' => 'ChangeSupplementaryOffering', 'Content-Type' => 'text/xml');
	$body = $data;
	$response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
	$infoArray = Helper::xmlToArray($response->body, true);
	$code = $infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangeSupplementaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'];
	$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangeSupplementaryOfferingRspMsg']['com:RspHeader']['com:ReturnMsg'];
	
	if($infoArray['soapenv:Envelope']['soapenv:Body']['off:ChangeSupplementaryOfferingRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
	 $message['status'] = true;
	 $message['message'] = $returnMessage;
	 $message['code'] = $code;
 }else{
	 $message['status'] = false;
	 $message['message'] = $returnMessage;
	 $message['code'] = $code;
 }
	 $Message= $message['message'];
	return $Message;
}

// Start Change Bill Medium
public function ChangeBillMedium($acct_id, $old_bill_id, $old_bill_code,$new_bill_id, $new_bill_code)
{

   $message = [
		   'status' => false,
		   'message' => 'No account ID provided',
		   'code' => ''
   ];
   //return $message;
   $SOAPApiService = new SOAPApiService($this->type, 'ChangeAccountInformation');

 //  dd(date('YmdHis', strtotime($effectiveDate)));
//    if($this->phoneNumber == null) return $message;
   $data = '
   <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:acc="http://www.huawei.com/bss/soaif/interface/AccountService/" xmlns:com="http://www.huawei.com/bss/soaif/interface/common/">
	<soapenv:Header/>
	<soapenv:Body>
	   <acc:ChangeBillMediumReqMsg>
		  <com:ReqHeader>
			 <com:Version>1</com:Version>
			 <com:TransactionId>QueryAcctInfo0022</com:TransactionId>
			 <com:Channel>28</com:Channel>
			 <com:PartnerId>101</com:PartnerId>
			 <com:ReqTime>20180608173000</com:ReqTime>
			 <com:AccessUser>CRM.ENTERPRISE</com:AccessUser>
			 <com:AccessPassword>jw8cn+D9LsKz2b3xz/TQmw==</com:AccessPassword>           
		  </com:ReqHeader>
		  <acc:AcctId>'.$acct_id.'</acc:AcctId>         
		  <acc:BillMedium>            
			 <com:BillMediumId>'.$old_bill_id.'</com:BillMediumId> 
			 <!--1084702653 EBill_normal-->
			 <com:BillMediumCode>'.$old_bill_code.'</com:BillMediumCode>
			 <!--1:Paper 2:SMS 3:Email 4:Fax 5:EBILL-->
			 <com:BillContentType>1</com:BillContentType>
			 <!--1:summary 2:Itemized-->
			  <acc:ActionType>3</acc:ActionType>
			 <!-- 1:Add. 2:Modify. 3:Delete-->            
			 <!-- Modify email textbox
			 <com:BillMediumInfo>342342433@qq.com</com:BillMediumInfo>
			 -->                       
		  </acc:BillMedium>
		  <acc:BillMedium>            
			 <com:BillMediumId>'.$new_bill_id.'</com:BillMediumId> 
			 <!--1084702653 EBill_normal-->2:SMS 
			 <com:BillMediumCode>'.$new_bill_code.'</com:BillMediumCode>
			 <!--1:Paper 2:SMS 3:Email 4:Fax 5:EBILL-->
			 <com:BillContentType>1</com:BillContentType>
			 <!--1:summary 2:Itemized-->            
			 <acc:ActionType>1</acc:ActionType>
			 <!-- 1:Add. 2:Modify. 3:Delete-->
		  </acc:BillMedium>
	   </acc:ChangeBillMediumReqMsg>
	</soapenv:Body>
 </soapenv:Envelope> 
   ';
   $headers = array('SOAPAction' => 'ChangeBillMedium', 'Content-Type' => 'text/xml');
   $body = $data;
   //dd ($SOAPApiService->getServiceUrl(), $headers, $body);
   $response = Unirest\Request::post($SOAPApiService->getServiceUrl(), $headers, $body);
   
   //dd($data);
   # Full parsing, array have root element
   $infoArray = Helper::xmlToArray($response->body, true);
//dd($response->body);

   $code = $infoArray['soapenv:Envelope']['soapenv:Body']['acc:ChangeBillMediumRspMsg']['com:RspHeader']['com:ReturnCode'];
   $returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['acc:ChangeBillMediumRspMsg']['com:RspHeader']['com:ReturnMsg'];
//    $returnMessage = 'Success';
   
   if($infoArray['soapenv:Envelope']['soapenv:Body']['acc:ChangeBillMediumRspMsg']['com:RspHeader']['com:ReturnCode'] == '0000'){
	$message['status'] = true;
	$message['message'] = $returnMessage;
	$message['code'] = $code;
}else{
	$message['status'] = false;
	$message['message'] = $returnMessage;
	$message['code'] = $code;
}
$Message= $message['message'];
   return $Message;
   
}
public function HotBill($ACCT)
{

	// dd($ACCT_CODE);
	$message = [
		'status' => false,
		'message' => 'No phone number provided',
		'code' => ''
	];
	$SOAPApiService = new SOAPApiService($this->type, 'HotBillFunction');
	if($this->phoneNumber == null) return $message;
	$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:bbs="http://www.huawei.com/bme/cbsinterface/bbservices" xmlns:cbs="http://www.huawei.com/bme/cbsinterface/cbscommon" xmlns:bbc="http://www.huawei.com/bme/cbsinterface/bbcommon">
	<soapenv:Header/>
	<soapenv:Body>
	   <bbs:HotBillingRequestMsg>
		 <RequestHeader>
			 <cbs:Version>1</cbs:Version>
			 <cbs:MessageSeq>'.time().'</cbs:MessageSeq>
			 <cbs:OwnershipInfo>
				<cbs:BEID>101</cbs:BEID>
			 </cbs:OwnershipInfo>
			 <cbs:AccessSecurity>
				<cbs:LoginSystemCode>ussd</cbs:LoginSystemCode>
				<cbs:Password>jw8cn+D9LsKz2</cbs:Password>
			 </cbs:AccessSecurity>
		  </RequestHeader>
		  <HotBillingRequest>
			 <bbs:AcctAccessCode>
				<!--You have a CHOICE of the next 3 items at this level-->
				<!--bbc:PrimaryIdentity>?</bbc:PrimaryIdentity-->
				<!--bbc:AccountKey>?</bbc:AccountKey-->
				<bbc:AccountCode>'.$ACCT.'</bbc:AccountCode>
			 </bbs:AcctAccessCode>
		  </HotBillingRequest>
	   </bbs:HotBillingRequestMsg>
	</soapenv:Body>
 </soapenv:Envelope>
	 ';
	$headers = array('SOAPAction' => 'HotBillFunction', 'Content-Type' => 'text/xml');
	$body = $data;
	$response = Unirest\Request::post('http://10.12.4.156:8080/services/BbServices', $headers, $body);
	$infoArray = Helper::xmlToArray($response->body, true);
	$code = $infoArray['soapenv:Envelope']['soapenv:Body']['bbs:HotBillingResultMsg']['ResultHeader']['cbs:ResultDesc'];
	$returnMessage = $infoArray['soapenv:Envelope']['soapenv:Body']['bbs:HotBillingResultMsg']['HotBillingResult']['bbs:hotbillingSerialNo'];
	
	$Message= $returnMessage;
	return $Message;
}
}




