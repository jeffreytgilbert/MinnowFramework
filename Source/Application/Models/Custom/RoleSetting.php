<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class RoleSetting extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'role_id'=>DataType::NUMBER,
			'Role'=>DataType::OBJECT,
			'setting_id'=>DataType::NUMBER,
			'Setting'=>DataType::OBJECT,
			'created_datetime'=>DataType::DATETIME
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof RoleSetting)?$DataObject:new RoleSetting($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getRole(){
		return ($this->getObject('Role') instanceof Role)
			?$this->_data['Role']
			:new Role();
	}
	
	public function getSetting(){
		return ($this->getObject('Setting') instanceof Setting)
			?$this->_data['Setting']
			:new Setting();
	}
	
}

class RoleSettingCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('RoleSetting');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getRoleSettingByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof RoleSetting)?$return:new RoleSetting($return->toArray());
	}
	
	public function getRoleSettingByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof RoleSetting)?$return:new RoleSetting($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}