<?php

class LocationFromIp extends Location{
	
	public function __construct($data=array(), $default_filter='Parse::decode'){
		$this->addAllowedData(array(
			'ip'=>DataType::TEXT
		), true);
		
		parent::__construct($data, $default_filter);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof LocationFromIp)?$DataObject:new LocationFromIp($DataObject->toArray());
	}
	
}

class LocationFromIpCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('LocationFromIp');
		parent::__construct($array_of_objects);
	}
}