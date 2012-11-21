<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class PageHits extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'page_date'=>DataType::DATE,
			'page_name'=>DataType::TEXT,
			'guest_hits'=>DataType::NUMBER,
			'member_hits'=>DataType::NUMBER
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof PageHits)?$DataObject:new PageHits($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
}

class PageHitsCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('PageHits');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getPageHitsByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof PageHits)?$return:new PageHits($return->toArray());
	}
	
	public function getPageHitsByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof PageHits)?$return:new PageHits($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}