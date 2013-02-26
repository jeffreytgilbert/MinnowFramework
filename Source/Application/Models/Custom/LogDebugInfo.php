<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class LogDebugInfo extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'channel'=>DataType::TEXT,
			'level'=>DataType::NUMBER,
			'message'=>DataType::TEXT,
			'time'=>DataType::NUMBER
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof LogDebugInfo)?$DataObject:new LogDebugInfo($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
}

class LogDebugInfoCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('LogDebugInfo');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getLogDebugInfoByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof LogDebugInfo)?$return:new LogDebugInfo($return->toArray());
	}
	
	public function getLogDebugInfoByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof LogDebugInfo)?$return:new LogDebugInfo($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}