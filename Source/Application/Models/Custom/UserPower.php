<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class UserPower extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'user_id'=>DataType::NUMBER,
			'UserAccount'=>DataType::OBJECT,
			'power_id'=>DataType::NUMBER,
			'Power'=>DataType::OBJECT,
			'created_datetime'=>DataType::DATETIME
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof UserPower)?$DataObject:new UserPower($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getUserAccount(){
		return ($this->getObject('UserAccount') instanceof UserAccount)
			?$this->_data['UserAccount']
			:new UserAccount();
	}
	
	public function getPower(){
		return ($this->getObject('Power') instanceof Power)
			?$this->_data['Power']
			:new Power();
	}
	
}

class UserPowerCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('UserPower');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getUserPowerByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof UserPower)?$return:new UserPower($return->toArray());
	}
	
	public function getUserPowerByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof UserPower)?$return:new UserPower($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}