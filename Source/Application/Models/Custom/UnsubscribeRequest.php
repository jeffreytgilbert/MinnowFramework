<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class UnsubscribeRequest extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'unsubscribe_request_id'=>DataType::NUMBER,
			'email'=>DataType::TEXT,
			'ip'=>DataType::TEXT,
			'created_datetime'=>DataType::DATETIME
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof UnsubscribeRequest)?$DataObject:new UnsubscribeRequest($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
}

class UnsubscribeRequestCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('UnsubscribeRequest');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getUnsubscribeRequestByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof UnsubscribeRequest)?$return:new UnsubscribeRequest($return->toArray());
	}
	
	public function getUnsubscribeRequestByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof UnsubscribeRequest)?$return:new UnsubscribeRequest($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}