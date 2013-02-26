<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class UserHistory extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'user_id'=>DataType::NUMBER,
			'UserAccount'=>DataType::OBJECT,
			'created_datetime'=>DataType::DATETIME,
			'page_title'=>DataType::TEXT,
			'location'=>DataType::TEXT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof UserHistory)?$DataObject:new UserHistory($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getUserAccount(){
		return ($this->getObject('UserAccount') instanceof UserAccount)
			?$this->_data['UserAccount']
			:new UserAccount();
	}
	
}

class UserHistoryCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('UserHistory');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getUserHistoryByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof UserHistory)?$return:new UserHistory($return->toArray());
	}
	
	public function getUserHistoryByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof UserHistory)?$return:new UserHistory($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}