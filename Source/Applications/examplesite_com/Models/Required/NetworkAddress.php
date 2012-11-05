<?php 

class NetworkAddress extends DataObject{

	public function __construct($data=array(), $default_filter='Parse::decode'){
		$this->addAllowedData(array(
			'ip'=>DataType::TEXT,
			'proxy'=>DataType::TEXT,
			'mac_address'=>DataType::TEXT
		),true);
		parent::__construct($data, $default_filter);
	}
}

class NetworkAddressCollection extends DataCollection{
	public function __construct(){
		$this->setCollectionType('NetworkAddress');
	}
}