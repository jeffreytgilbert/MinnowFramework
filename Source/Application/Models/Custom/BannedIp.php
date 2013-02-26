<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class BannedIp extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'banned_ip'=>DataType::TEXT,
			'user_id'=>DataType::NUMBER,
			'UserAccount'=>DataType::OBJECT,
			'user_name'=>DataType::TEXT,
			'date_issued'=>DataType::DATETIME,
			'expiry_date'=>DataType::DATETIME
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof BannedIp)?$DataObject:new BannedIp($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getUserAccount(){
		return ($this->getObject('UserAccount') instanceof UserAccount)
			?$this->_data['UserAccount']
			:new UserAccount();
	}
	
}

class BannedIpCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('BannedIp');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getBannedIpByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof BannedIp)?$return:new BannedIp($return->toArray());
	}
	
	public function getBannedIpByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof BannedIp)?$return:new BannedIp($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}