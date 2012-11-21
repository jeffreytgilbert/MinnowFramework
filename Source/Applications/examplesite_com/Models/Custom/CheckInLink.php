<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class CheckInLink extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'check_in_link_id'=>DataType::NUMBER,
			'check_in_link_image_url'=>DataType::TEXT,
			'url'=>DataType::TEXT,
			'created_datetime'=>DataType::DATETIME
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof CheckInLink)?$DataObject:new CheckInLink($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
}

class CheckInLinkCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('CheckInLink');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getCheckInLinkByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof CheckInLink)?$return:new CheckInLink($return->toArray());
	}
	
	public function getCheckInLinkByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof CheckInLink)?$return:new CheckInLink($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}