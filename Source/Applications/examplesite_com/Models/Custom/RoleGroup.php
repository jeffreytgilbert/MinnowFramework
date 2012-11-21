<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class RoleGroup extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'role_group_id'=>DataType::NUMBER,
			'created_datetime'=>DataType::DATETIME,
			'group_name'=>DataType::TEXT,
			'is_group_single_role_limited'=>DataType::BOOLEAN
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof RoleGroup)?$DataObject:new RoleGroup($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
}

class RoleGroupCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('RoleGroup');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getRoleGroupByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof RoleGroup)?$return:new RoleGroup($return->toArray());
	}
	
	public function getRoleGroupByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof RoleGroup)?$return:new RoleGroup($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}