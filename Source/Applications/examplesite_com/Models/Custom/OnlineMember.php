<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class OnlineMember extends AccessRequest{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'user_id'=>DataType::NUMBER,
			'UserAccount'=>DataType::OBJECT,
			'last_active'=>DataType::DATETIME
		),true);
		parent::__construct($data);
	}

	public function isOnline(){ return $this->getObject('UserAccount')->getBoolean('is_online'); }
	
	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof OnlineMember)?$DataObject:new OnlineMember($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getUserAccount(){
		return ($this->getObject('UserAccount') instanceof UserAccount)
			?$this->_data['UserAccount']
			:new UserAccount();
	}
	
}

class OnlineMemberCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('OnlineMember');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getOnlineMemberByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof OnlineMember)?$return:new OnlineMember($return->toArray());
	}
	
	public function getOnlineMemberByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof OnlineMember)?$return:new OnlineMember($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}