<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class BlockReason extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'block_reason_id'=>DataType::NUMBER,
			'dropdown_choice'=>DataType::TEXT,
			'view_text'=>DataType::TEXT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof BlockReason)?$DataObject:new BlockReason($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
}

class BlockReasonCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('BlockReason');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getBlockReasonByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof BlockReason)?$return:new BlockReason($return->toArray());
	}
	
	public function getBlockReasonByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof BlockReason)?$return:new BlockReason($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}