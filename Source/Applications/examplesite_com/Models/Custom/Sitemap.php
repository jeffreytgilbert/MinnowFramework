<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class Sitemap extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'link_id'=>DataType::NUMBER,
			'parent'=>DataType::NUMBER,
			'title'=>DataType::TEXT,
			'url'=>DataType::TEXT,
			'ignore_in_sitemap'=>DataType::NUMBER,
			'description'=>DataType::TEXT,
			'order_id'=>DataType::NUMBER
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof Sitemap)?$DataObject:new Sitemap($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
}

class SitemapCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('Sitemap');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getSitemapByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof Sitemap)?$return:new Sitemap($return->toArray());
	}
	
	public function getSitemapByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof Sitemap)?$return:new Sitemap($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}