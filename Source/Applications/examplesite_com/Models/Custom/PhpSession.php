<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class PhpSession extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'id'=>DataType::TEXT,
			'access'=>DataType::NUMBER,
			'data'=>DataType::TEXT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof PhpSession)?$DataObject:new PhpSession($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
}

class PhpSessionCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('PhpSession');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getPhpSessionByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof PhpSession)?$return:new PhpSession($return->toArray());
	}
	
	public function getPhpSessionByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof PhpSession)?$return:new PhpSession($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}