<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class Role extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'role_id'=>DataType::NUMBER,
			'role_group_id'=>DataType::NUMBER,
			'RoleGroup'=>DataType::OBJECT,
			'is_user_selectable'=>DataType::BOOLEAN,
			'title'=>DataType::TEXT,
			'hint'=>DataType::TEXT,
			'created_datetime'=>DataType::DATETIME
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof Role)?$DataObject:new Role($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getRoleGroup(){
		return ($this->getObject('RoleGroup') instanceof RoleGroup)
			?$this->_data['RoleGroup']
			:new RoleGroup();
	}
	
}

class RoleCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('Role');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getRoleByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof Role)?$return:new Role($return->toArray());
	}
	
	public function getRoleByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof Role)?$return:new Role($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}