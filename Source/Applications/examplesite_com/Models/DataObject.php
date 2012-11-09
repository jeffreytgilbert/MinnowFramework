<?php

/**
 * Generic Data Object for dynamically handling sql returns without writing specific classes for them.
 * Class which allows filtering, parsing, and caching of data in an easy to use fashion.
 * @package CoreComponents
 */
class DataObject extends Model{

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof DataObject)?$DataObject:new DataObject($DataObject->toArray());
	}
	
}