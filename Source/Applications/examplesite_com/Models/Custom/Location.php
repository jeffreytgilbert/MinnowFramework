<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class Location extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'location_id'=>DataType::NUMBER,
			'created_datetime'=>DataType::DATETIME,
			'modified_datetime'=>DataType::DATETIME,
			'name'=>DataType::TEXT,
			'address'=>DataType::TEXT,
			'latitude'=>DataType::NUMBER,
			'longitude'=>DataType::NUMBER,
			'phone_number'=>DataType::TEXT,
			'website_url'=>DataType::TEXT,
			'email_address'=>DataType::TEXT,
			'location_thumbnail_url'=>DataType::TEXT,
			'user_id'=>DataType::NUMBER,
			'UserAccount'=>DataType::OBJECT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof Location)?$DataObject:new Location($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getUserAccount(){
		return ($this->getObject('UserAccount') instanceof UserAccount)
			?$this->_data['UserAccount']
			:new UserAccount();
	}
	
}

class LocationCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('Location');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getLocationByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof Location)?$return:new Location($return->toArray());
	}
	
	public function getLocationByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof Location)?$return:new Location($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}