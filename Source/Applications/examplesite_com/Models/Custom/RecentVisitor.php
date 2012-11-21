<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class RecentVisitor extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'user_id'=>DataType::NUMBER,
			'UserAccount'=>DataType::OBJECT,
			'created_datetime'=>DataType::DATETIME,
			'visitor_id'=>DataType::NUMBER,
			'Visitor'=>DataType::OBJECT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof RecentVisitor)?$DataObject:new RecentVisitor($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getUserAccount(){
		return ($this->getObject('UserAccount') instanceof UserAccount)
			?$this->_data['UserAccount']
			:new UserAccount();
	}
	
	public function getVisitor(){
		return ($this->getObject('Visitor') instanceof UserAccount)
			?$this->_data['Visitor']
			:new UserAccount();
	}
	
}

class RecentVisitorCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('RecentVisitor');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getRecentVisitorByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof RecentVisitor)?$return:new RecentVisitor($return->toArray());
	}
	
	public function getRecentVisitorByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof RecentVisitor)?$return:new RecentVisitor($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}