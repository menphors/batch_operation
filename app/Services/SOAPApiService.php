<?php
namespace App\Services;

use App\Services\ApiService;

class SOAPApiService extends ApiService{
	
	const SOAP_PRODUCTION = 1;
	const SOAP_TESTBED = 2;
	
	protected $type = 2;
	protected $SOAPAction;
	protected $serviceUrl;
	protected $user;
	protected $password;
	
	
	public function __construct($type, $SOAPAction)
	{
		$this->type = $type;
		$this->SOAPAction = $SOAPAction;
		$this->setSOAPApiInfo($type, $SOAPAction);
	}
	
	public function getServiceUrl(){
		return $this->serviceUrl;
	}
	
	public function getUser(){
		return $this->user;
	}
	
	public function getPassword(){
		return $this->password;
	}
	
	public function getSOAPAction(){
		return $this->SOAPAction;
	}
	
	protected function setSOAPApiInfo($type, $SOAPAction){
		$apis = [
				1 => [
					'QueryBalance' => [
							'service_url' => 'http://10.12.4.156:8080/services/ArServices',
							'user' => 'esb',
							'password' => 'jw8cn+D9LsKz2b3xz/TQmw00=='
					],
					'QuerySubLifeCycle' => [
							'service_url' => 'http://10.12.4.156:8080/services/BcServices',
							'user' => 'SOEUNG.SOVATH',
							'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
					],
					'QueryPurchasedPrimaryOffering' => [
							'service_url' => 'http://10.12.5.205:7080/custom/SELFCARE/HWBSS_Offering',
							'user' => '5061',
							'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
					],
					'QueryCPENumber' => [
							'service_url' => 'http://10.12.5.205:7080/custom/SELFCARE/HWBSS_WTTX',
							'user' => '5061',
							'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
					],
					'EVCTopup' => [
							'service_url' => 'http://10.12.5.205:7080/custom/SELFCARE/CommonDataModelProcess',
							'user' => '',
							'password' => '',
					],
					'getSubscriberAllQuota' => [
							'service_url' => 'http://10.12.5.205:7080/custom/SELFCARE/HWBSS_Subscriber',
							'user' => '5061',
							'password' => 'r8q0a5WwGNboj9I35XzNcQ==',
					],

					'ChangePrimaryOffering' => [
							'service_url' => 'http://10.12.5.205:7080/SELFCARE/HWBSS_Offering',
							'user' => 'SmartAppDev',
							'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
					],
						
					'ChangeSupplementaryOffering' => [
							'service_url' => 'http://10.12.5.205:7080/custom/SELFCARE/HWBSS_Offering',
							'user' => 'SmartAppDev',
							'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
					],
						
					'QueryFreeUnit' => [
							'service_url' => 'http://10.12.5.205:7080/SELFCARE/HWBSS_Offering',
							'user' => '5061',
							'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
					],
					
					'QueryPurchasedSupplementaryOffering' => [
							'service_url' => 'http://10.12.5.205:7080/custom/SELFCARE/HWBSS_Offering',
							'user' => '5061',
							'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
					],
					
					'SendSMS' => [
							'service_url' => 'http://10.12.5.205:7080/SELFCARE/HWBSS_Utility',
							'user' => '5061',
							'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
					],	
					'Adjustment' => [
							'service_url' => 'http://10.12.4.156:8080/services/ArServices',
							'user' => '5061',
							'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
					],
					'QueryDealerInfo' => [
							'service_url' => 'http://10.12.4.209:7782/services/EVCInterfaceBusinessMgrService?wsdl',
							'user' => 'EVC.EPOS',
							'password' => 'E62883E3FCFEDAE4DA7F7C91C7F4E4F82B28744CCA7CB53EE32A22747F04901D'
					],
// new EVC change basic info
                    'ChangeDealerBasicInfo' => [
	                        'service_url' => 'http://10.12.4.209:7782/services/EVCInterfaceBusinessMgrService?wsdl',
	                        'user' => 'sysadmin',
	                        'password' => '2CBAEF3E79F98F7CDE740787B8697D7E8F01C3642D5CB9FBA116584301CA42C2'
					],

					'EVCTopupSlave' => [
							'service_url' => 'http://10.12.4.206:12121/EVCDealerInterfaceService',
							'user' => '',
							'password' => '',
					],

					'ChangeCustProfile' => [

						'service_url' => 'http://10.12.5.205:7080/SELFCARE/HWBSS_Customer',
						
						'user' => 'CRM.ENTERPRISE',
						
						'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
						
					 ],
					 
					 'DeactivateSubscriber' => [

						'service_url' => 'http://10.12.5.205:7080/SELFCARE/HWBSS_Subscriber',
						
						'user' => 'CRM.ENTERPRISE',
						
						'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
						
					 ],
					 
					 'ChangePreToPost' => [

						'service_url' => 'https://mife.smart.com.kh:8243/api/hwbss/pretopost/v1.0',
						
						'user' => 'CRM.ENTERPRISE',
						
						'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
						
					 ],
					 
					 'ChangePostpaidToPrepaid' => [

						'service_url' => 'http://10.12.5.205:7080/custom/SELFCARE/HWBSS_Subscriber',
						
						'user' => 'CRM.ENTERPRISE',
						
						'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
						
					 ],
					 
					
					 
							
					'ActivateSubscriber' => [

						'service_url' => 'http://10.12.5.205:7080/custom/SELFCARE/HWBSS_Subscriber',
						
						'user' => 'CRM.ENTERPRISE',
						
						'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
						
					 ],
    /***
	* 
	================================= API Link             ==================================================	
	================================= 15/07/2020           ==================================================	
	================================= Suy Kosal            ==================================================	

	*/
					 'QueryAccountInformation' => [
						'service_url' => 'http://10.12.5.205:7080/SELFCARE/HWBSS_Account',
						'user' => 'CRM.ENTERPRISE',
						'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
				],

					 'ChangePrepaidToPostpaid' => [
						'service_url' => 'http://10.12.5.205:7080/SELFCARE/HWBSS_Subscriber',
						
						'user' => 'CRM.ENTERPRISE',
						
						'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
					 ],
					 'ChangeAcctCreditLimit' => [
						'service_url' => 'http://10.12.5.205:7080/CRM/CBSInterface_BC_Services',
						
						'user' => 'CRM.ENTERPRISE',
						
						'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
					 ],
					 'ChangeAccountInformation' => [

						'service_url' => 'http://10.12.5.205:7080/SELFCARE/HWBSS_Account',
						
						'user' => 'CRM.ENTERPRISE',
						
						'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
						
					  ],
					  'HotBillFunction' => [

						'service_url' => 'http://10.12.4.156:8080/services/BbServices',
						
						'user' => 'CRM.ENTERPRISE',
						
						'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
						
					  ],
 
 

				],
				
				2 => [
					
						'QueryBalance' => [
								'service_url' => 'http://10.12.8.14:7080/SELFCARE/CBSInterface_AR_Services',
								'user' => 'SmartNas',
								'password' => 'jw8cn+D9LsKz2b3xz/TQmw=='
						],
						'QuerySubLifeCycle' => [
								'service_url' => 'http://10.12.8.14:7080/SELFCARE/CBSInterface_BC_Services',
								'user' => '5061',
								'password' => 'r8q0a5WwGNboj9I35XzNcQ==',
						],
						'QueryPurchasedPrimaryOffering' => [
								'service_url' => 'http://10.12.8.14:7080/custom/SELFCARE/HWBSS_Offering',
								'user' => '5061',
								'password' => 'r8q0a5WwGNboj9I35XzNcQ==',
						],
						'QueryCPENumber' => [
								'service_url' => 'http://10.12.8.14:7080/custom/SELFCARE/HWBSS_WTTX',
								'user' => '5061',
								'password' => 'r8q0a5WwGNboj9I35XzNcQ==',
						],
						'EVCTopup' => [
								'service_url' => 'http://10.12.8.14:7080/custom/SELFCARE/CommonDataModelProcess',
								'user' => '',
								'password' => '',
						],
						'getSubscriberAllQuota' => [
								'service_url' => 'http://10.12.8.14:7080/custom/SELFCARE/HWBSS_Subscriber',
								'user' => '5061',
								'password' => 'r8q0a5WwGNboj9I35XzNcQ==',
						],
						'ChangePrimaryOffering' => [
								'service_url' => 'http://10.12.8.14:7080/SELFCARE/HWBSS_Offering',
								'user' => 'SmartNas',
								'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
						],
						'ChangeSupplementaryOffering' => [
								'service_url' => 'http://10.12.8.14:7080/SELFCARE/HWBSS_Offering',
								'user' => 'SmartNas',
								'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
						],
						
						'QueryFreeUnit' => [
								'service_url' => 'http://10.12.8.14:7080/SELFCARE/HWBSS_Offering',
								'user' => '5061',
								'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
						],
						
						'QueryPurchasedSupplementaryOffering' => [
								'service_url' => 'http://10.12.8.14:7080/SELFCARE/HWBSS_Offering',
								'user' => '5061',
								'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
						],
						
						'SendSMS' => [
								'service_url' => 'http://10.12.8.14:7080/SELFCARE/HWBSS_Utility',
								'user' => '5061',
								'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
						],
						'Adjustment' => [
								'service_url' => 'http://10.12.8.13:8080/services/ArServices',
								'user' => '5061',
								'password' => 'r8q0a5WwGNboj9I35XzNcQ==',
						],
						'QueryDealerInfo' => [
								'service_url' => 'http://10.12.8.13:7782/services/EVCInterfaceBusinessMgrService?wsdl',
								'user' => 'mk_test',
								'password' => '6295B6E70878449CE516B6A123A801D8B164E2C3264C18DC7BFF4D07E2C6850D',
						],
						'EVCTopupSlave' => [
								'service_url' => 'http://10.12.8.11:12121/EVCDealerInterfaceService',
								'user' => '',
								'password' => '',
						],
						'ChangeCustProfile' => [

							'service_url' => 'http://10.12.8.14:7080/SELFCARE/HWBSS_Customer',
							
							'user' => 'CRM.ENTERPRISE',
							
							'password' => 'r8q0a5WwGNboj9I35XzNcQ==',
							
						 ],	
    /*
	* 
	================================= API Link             ==================================================	
	================================= 15/07/2020           ==================================================	
	================================= Suy Kosal            ==================================================	

	*/

						 'QueryAccountInformation' => [
							'service_url' => 'http://10.12.8.14:7080/SELFCARE/HWBSS_Account',
							'user' => 'CRM.ENTERPRISE',
							'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
					],
	
						 'ChangePrepaidToPostpaid' => [
							'service_url' => 'http://10.12.8.14:7080/SELFCARE/HWBSS_Subscriber',
							
							'user' => 'CRM.ENTERPRISE',
							
							'password' => 'r8q0a5WwGNboj9I35XzNcQ==',
						 ],
						 'ChangeAcctCreditLimit' => [
							'service_url' => 'http://10.12.8.14:7080/CRM/CBSInterface_BC_Services',
							
							'user' => 'CRM.ENTERPRISE',
							
							'password' => 'r8q0a5WwGNboj9I35XzNcQ==',
						 ],
						 'ChangeAccountInformation' => [
	
							'service_url' => 'http://10.12.8.14:7080/SELFCARE/HWBSS_Account',
							
							'user' => 'CRM.ENTERPRISE',
							
							'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
							
						  ],
						  'HotBillFunction' => [

							'service_url' => 'http://10.12.4.156:8080/services/BbServices',
							
							'user' => 'CRM.ENTERPRISE',
							
							'password' => 'jw8cn+D9LsKz2b3xz/TQmw==',
							
						  ],
						
					],

		];
		
		if(array_key_exists($SOAPAction, $apis[$type])){
			$api = $apis[$type][$SOAPAction];
			$this->serviceUrl = $api['service_url'];
			$this->user = $api['user'];
			$this->password = $api['password'];
		}else{
			$this->errorResponse('There is no '.$SOAPAction.' available!', 500, true);
			exit;
		}
	}
	
}