<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class NotificationType extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'notification_type_id'=>DataType::NUMBER,
			'created_datetime'=>DataType::DATETIME,
			'modified_datetime'=>DataType::DATETIME,
			'notification_type'=>DataType::TEXT,
			'template'=>DataType::TEXT,
			'application_id'=>DataType::NUMBER,
			'Application'=>DataType::OBJECT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof NotificationType)?$DataObject:new NotificationType($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
// 	public function getApplication(){
// 		return ($this->getObject('Application') instanceof Application)
// 			?$this->_data['Application']
// 			:new Application();
// 	}
	
}

class NotificationTypeCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('NotificationType');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getNotificationTypeByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof NotificationType)?$return:new NotificationType($return->toArray());
	}
	
	public function getNotificationTypeByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof NotificationType)?$return:new NotificationType($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}