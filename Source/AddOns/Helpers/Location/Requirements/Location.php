<?php

class Location extends DataObject{
	
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
			'localtime'=>DataType::DATETIME,
			'isotime'=>DataType::DATETIME,
			'utctime'=>DataType::DATETIME,
			'dst_offset'=>DataType::NUMBER,
			'area_code'=>DataType::NUMBER,
			'dma_code'=>DataType::NUMBER
		),true);
		parent::__construct($data, $default_filter);
	}
	
	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof Location)?$DataObject:new Location($DataObject->toArray());
	}
	
}

class LocationCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('Location');
		parent::__construct($array_of_objects);
	}
}