<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class Theme extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'theme_id'=>DataType::NUMBER,
			'created_datetime'=>DataType::DATETIME,
			'modified_datetime'=>DataType::DATETIME,
			'theme_name'=>DataType::TEXT,
			'thumb_path'=>DataType::TEXT,
			'css_file_path'=>DataType::TEXT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof Theme)?$DataObject:new Theme($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
}

class ThemeCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('Theme');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getThemeByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof Theme)?$return:new Theme($return->toArray());
	}
	
	public function getThemeByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof Theme)?$return:new Theme($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}