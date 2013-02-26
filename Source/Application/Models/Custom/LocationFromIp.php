<?php

class LocationFromIp extends DataObject{
	
	public function __construct($data=array(), $default_filter='Parse::decode'){
		$this->addAllowedData(array(
			'country_code'=>DataType::TEXT,
			'country_name'=>DataType::TEXT,
			'region_name'=>DataType::TEXT,
			'city_name'=>DataType::TEXT,
			'latitude'=>DataType::NUMBER,
			'longitude'=>DataType::NUMBER,
			'postal_code'=>DataType::TEXT,
			'gmt_offset'=>DataType::NUMBER,
			'local_time'=>DataType::DATETIME,
			'iso_time'=>DataType::DATETIME,
			'utc_time'=>DataType::DATETIME,
			'dst_offset'=>DataType::NUMBER,
			'area_code'=>DataType::NUMBER,
			'dma_code'=>DataType::NUMBER,
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