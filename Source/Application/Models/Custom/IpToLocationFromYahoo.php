<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class IpToLocationFromYahoo extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'ip'=>DataType::TEXT,
			'country_code'=>DataType::TEXT,
			'country_name'=>DataType::TEXT,
			'region_name'=>DataType::TEXT,
			'city_name'=>DataType::TEXT,
			'latitude'=>DataType::NUMBER,
			'longitude'=>DataType::NUMBER,
			'postal_code'=>DataType::TEXT,
			'gmt_offset'=>DataType::NUMBER,
			'dst_offset'=>DataType::NUMBER,
			'area_code'=>DataType::TEXT,
			'dma_code'=>DataType::TEXT,
			'local_time'=>DataType::DATETIME,
			'iso_time'=>DataType::DATETIME,
			'utc_time'=>DataType::DATETIME
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof IpToLocationFromYahoo)?$DataObject:new IpToLocationFromYahoo($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
}

class IpToLocationFromYahooCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('IpToLocationFromYahoo');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getIpToLocationFromYahooByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof IpToLocationFromYahoo)?$return:new IpToLocationFromYahoo($return->toArray());
	}
	
	public function getIpToLocationFromYahooByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof IpToLocationFromYahoo)?$return:new IpToLocationFromYahoo($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}