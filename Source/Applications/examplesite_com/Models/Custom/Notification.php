<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class Notification extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'notification_id'=>DataType::NUMBER,
			'user_id'=>DataType::NUMBER,
			'UserAccount'=>DataType::OBJECT,
			'created_datetime'=>DataType::DATETIME,
			'modified_datetime'=>DataType::DATETIME,
			'is_read'=>DataType::BOOLEAN,
			'message'=>DataType::TEXT,
			'primary_link'=>DataType::TEXT,
			'notification_type_id'=>DataType::NUMBER,
			'NotificationType'=>DataType::OBJECT,
			'notification_type'=>DataType::TEXT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof Notification)?$DataObject:new Notification($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getUserAccount(){
		return ($this->getObject('UserAccount') instanceof UserAccount)
			?$this->_data['UserAccount']
			:new UserAccount();
	}
	
	public function getNotificationType(){
		return ($this->getObject('NotificationType') instanceof NotificationType)
			?$this->_data['NotificationType']
			:new NotificationType();
	}
	
}

class NotificationCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('Notification');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getNotificationByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof Notification)?$return:new Notification($return->toArray());
	}
	
	public function getNotificationByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof Notification)?$return:new Notification($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}