<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class Follow extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'created_datetime'=>DataType::DATETIME,
			'modified_datetime'=>DataType::DATETIME,
			'my_user_id'=>DataType::NUMBER,
			'MyUser'=>DataType::OBJECT,
			'target_user_id'=>DataType::NUMBER,
			'TargetUser'=>DataType::OBJECT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof Follow)?$DataObject:new Follow($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getMyUser(){
		return ($this->getObject('MyUser') instanceof UserAccount)
			?$this->_data['MyUser']
			:new UserAccount();
	}
	
	public function getTargetUser(){
		return ($this->getObject('TargetUser') instanceof UserAccount)
			?$this->_data['TargetUser']
			:new UserAccount();
	}
	
}

class FollowCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('Follow');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getFollowByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof Follow)?$return:new Follow($return->toArray());
	}
	
	public function getFollowByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof Follow)?$return:new Follow($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}