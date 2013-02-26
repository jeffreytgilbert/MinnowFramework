<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class Achievement extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'achievement_id'=>DataType::NUMBER,
			'created_datetime'=>DataType::DATETIME,
			'modifed_datetime'=>DataType::DATETIME,
			'title'=>DataType::TEXT,
			'description'=>DataType::TEXT,
			'thumbnail'=>DataType::TEXT,
			'unlocked_actions'=>DataType::TEXT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof Achievement)?$DataObject:new Achievement($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
}

class AchievementCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('Achievement');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getAchievementByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof Achievement)?$return:new Achievement($return->toArray());
	}
	
	public function getAchievementByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof Achievement)?$return:new Achievement($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}