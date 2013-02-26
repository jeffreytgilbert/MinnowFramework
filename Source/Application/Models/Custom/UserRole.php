<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class UserRole extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'user_id'=>DataType::NUMBER,
			'UserAccount'=>DataType::OBJECT,
			'role_id'=>DataType::NUMBER,
			'Role'=>DataType::OBJECT,
			'created_datetime'=>DataType::DATETIME
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof UserRole)?$DataObject:new UserRole($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getUserAccount(){
		return ($this->getObject('UserAccount') instanceof UserAccount)
			?$this->_data['UserAccount']
			:new UserAccount();
	}
	
	public function getRole(){
		return ($this->getObject('Role') instanceof Role)
			?$this->_data['Role']
			:new Role();
	}
	
}

class UserRoleCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('UserRole');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getUserRoleByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof UserRole)?$return:new UserRole($return->toArray());
	}
	
	public function getUserRoleByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof UserRole)?$return:new UserRole($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}