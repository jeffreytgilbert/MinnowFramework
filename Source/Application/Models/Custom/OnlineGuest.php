<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class OnlineGuest extends AccessRequest{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'php_session_id'=>DataType::TEXT,
			'PhpSession'=>DataType::OBJECT,
			'last_active'=>DataType::DATETIME
		),true);
		parent::__construct($data);
	}
	
	public function isOnline(){ return false; }

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof OnlineGuest)?$DataObject:new OnlineGuest($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getPhpSession(){
		return ($this->getObject('PhpSession') instanceof PhpSession)
			?$this->_data['PhpSession']
			:new PhpSession();
	}
	
}

class OnlineGuestCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('OnlineGuest');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getOnlineGuestByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof OnlineGuest)?$return:new OnlineGuest($return->toArray());
	}
	
	public function getOnlineGuestByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof OnlineGuest)?$return:new OnlineGuest($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}