<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

/**
 * Basic session instantiated into $ID / $Me var
 * @package Authentication
 */
abstract class AccessRequest extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'NetworkAddress'=>DataType::OBJECT,
			'LocationFromIp'=>DataType::OBJECT,
			'user_agent'=>DataType::TEXT,
			'created_datetime'=>DataType::TEXT
		),true);
		parent::__construct($data);
	}
	
	public function setIp($ip) { $this->getObject('NetworkAddress')->set('ip',$ip); }
	
	public function getIp() { return $this->getObject('NetworkAddress')->get('ip'); }
	
	public function setProxy($proxy) { $this->getObject('NetworkAddress')->set('proxy', $proxy); }
	
	public function getProxy() { return $this->getObject('NetworkAddress')->get('proxy'); }	

	// Read only properties
	protected $_is_api_call = false;
	
	public function isApiCall(){ return $this->_is_api_call; }
	abstract function isOnline();
	
	public function setNetworkAddress(NetworkAddress $NetworkAddress){
		$this->set('NetworkAddress',$NetworkAddress);
	}
	
	public function getNetworkAddress(){
		return ($this->getObject('NetworkAddress') instanceof NetworkAddress)
			?$this->_data['NetworkAddress']
			:new NetworkAddress();
	}
	
	public function setLocationFromIp(LocationFromIp $LocationFromIp){
		$this->set('LocationFromIp',$LocationFromIp);
	}
	
	public function getLocationFromIp(){
		return ($this->getObject('LocationFromIp') instanceof LocationFromIp)
			?$this->_data['LocationFromIp']
			:new LocationFromIp();
	}
	
}

