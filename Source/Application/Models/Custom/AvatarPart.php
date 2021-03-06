<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class AvatarPart extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'avatar_part_id'=>DataType::NUMBER,
			'created_datetime'=>DataType::DATETIME,
			'layer'=>DataType::NUMBER,
			'layer_order_id'=>DataType::NUMBER,
			'grouping'=>DataType::TEXT,
			'gender'=>DataType::TEXT,
			'path'=>DataType::TEXT,
			'path_to_thumbnail'=>DataType::TEXT,
			'is_inactive'=>DataType::BOOLEAN
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof AvatarPart)?$DataObject:new AvatarPart($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object

}

class AvatarPartCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('AvatarPart');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getAvatarPartByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof AvatarPart)?$return:new AvatarPart($return->toArray());
	}
	
	public function getAvatarPartByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof AvatarPart)?$return:new AvatarPart($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}