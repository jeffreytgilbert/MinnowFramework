<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class AccountStatus extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'account_status_id'=>DataType::NUMBER,
			'status_type'=>DataType::TEXT,
			'hierarchical_order'=>DataType::TEXT,
			'created_datetime'=>DataType::DATETIME
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof AccountStatus)?$DataObject:new AccountStatus($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
}

class AccountStatusCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('AccountStatus');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getAccountStatusByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof AccountStatus)?$return:new AccountStatus($return->toArray());
	}
	
	public function getAccountStatusByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof AccountStatus)?$return:new AccountStatus($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}