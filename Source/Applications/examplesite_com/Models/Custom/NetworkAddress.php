<?php 

class NetworkAddress extends DataObject{

	public function __construct($data=array()){
		$this->addAllowedData(array(
			'ip'=>DataType::TEXT, // always the ip
			'proxy'=>DataType::TEXT, // if a proxy exists
			'mac_address'=>DataType::TEXT // i think you can only get this from javascript
		),true);
		parent::__construct($data);
	}
}

class NetworkAddressCollection extends DataCollection{
	public function __construct(){
		$this->setCollectionType('NetworkAddress');
	}
}