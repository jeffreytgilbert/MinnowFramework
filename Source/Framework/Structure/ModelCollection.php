<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

/**
 * Generic Data Collection type for objects that don't have a matching collection type. Having this prevents warnings for Objects which don't need data collections 
 * but from which data is queried from the db
 * @package CoreComponents
 */
class ModelCollection implements Iterator{
	private $_data_object_array = array();
	private $_collection_of_object_type = ''; // by setting this to blank, it will error if it's not set in the constructor
	
	// for pagination
	private $_PageData;
	
	public function __construct(Array $array_of_objects=null){
		if(!is_null($array_of_objects)){
			$this->addAll($array_of_objects);
		}
	}
	
	public function setPaginationData($start, $limit, $total){
		$this->_PageData = new PageData($start,$total,$limit);
	}
	
	public function getPageData(){
		return ($this->_PageData instanceof PageData)?$this->_PageData:new PageData(1,1,1);
	}
	
	public function addAll(Array $data_array){
		foreach($data_array as $DataObject){
			$this->addObject($DataObject);
		}
	}
		
	protected function setCollectionType($string){
		$this->_collection_of_object_type = $string;
	}
	
	public function getArrayByField($field_name){
		$new_array = array();
		foreach($this->_data_object_array as $DataObject){
			$new_array[] = $DataObject->get($field_name);
		}
		return $new_array;
	}
	
	public function getUniqueArrayByField($field_name){
		$new_array = array();
		foreach($this->_data_object_array as $DataObject){
			$new_array[] = $DataObject->get($field_name);
		}
		return array_unique($new_array);
	}
	
	public function putThisDataInAnotherCollection(DataCollection $ParentObjectCollection, $parent_field, $child_field, $object_type, $parent_object_name=null){
		
		if(!isset($parent_object_name)){ $parent_object_name = $object_type; }
		
		foreach($ParentObjectCollection as $ParentObject){
			$ParentObject->set($parent_object_name, new $object_type());
		}
		
		// go through childs array of results
		foreach($this->_data_object_array as $ChildObject){
			// grab the parent objects that have a match for this childs data
			$array = $ParentObjectCollection->getObjectArrayByFieldValue($parent_field, $ChildObject->get($child_field));
			
			foreach($array as $ParentObject){
				// set the parents data with this child object, for each of  the objects found matching this child data
				$ParentObject->set($parent_object_name, $ChildObject);
			}
		}
	}
	
	public function addAllAt(Array $data_array, $index){
		if(!empty($this->_collection_of_object_type)){
			if($index > 0){
				$second_half = array_slice($this->_data_object_array,$index);
				$first_half = array_slice($this->_data_object_array,0,$index--);
				foreach($data_array as $DataObject){
					if($DataObject instanceof $this->_collection_of_object_type){
					} else {
						echo 'Error: Data Object type does not match that of Collection Type "'.$this->_collection_of_object_type.'"'; exit;
					}
				}
				$this->_data_object_array = array_merge($first_half,$data_array,$second_half);
			} else {
				array_unshift($this->_data_object_array,$data_array);
			}
		} else {
			if($index > 0){
				$second_half = array_slice($this->_data_object_array,$index);
				$first_half = array_slice($this->_data_object_array,0,$index--);
				$this->_data_object_array = array_merge($first_half,$data_array,$second_half);
			} else {
				array_unshift($this->_data_object_array,$data_array);
			}
		}
	}
	
	public function addObject(Model $DataObject){
		if(!empty($this->_collection_of_object_type)){
			if($DataObject instanceof $this->_collection_of_object_type){
				array_push($this->_data_object_array, $DataObject);
			} else {
				echo 'Error: Data Object type does not match that of Collection Type "'.$this->_collection_of_object_type.'"'; exit;
			}
		} else {
			array_push($this->_data_object_array, $DataObject);
		}
	}
	
	public function addObjectAtIndex(Model $DataObject, $index){
		if(!empty($this->_collection_of_object_type)){
			if($DataObject instanceof $this->_collection_of_object_type){
				if($index > 0){
					$second_half = array_slice($this->_data_object_array,$index);
					$first_half = array_slice($this->_data_object_array,0,$index--);
					$this->_data_object_array = array_merge($first_half,array($DataObject),$second_half);
				} else {
					array_unshift($this->_data_object_array,$DataObject);
				}
			} else {
				echo 'Error: Data Object type does not match that of Collection Type "'.$this->_collection_of_object_type.'"'; exit;
			}
		} else {
			if($index > 0){
				$second_half = array_slice($this->_data_object_array,$index);
				$first_half = array_slice($this->_data_object_array,0,$index--);
				$this->_data_object_array = array_merge($first_half,array($DataObject),$second_half);
			} else {
				array_unshift($this->_data_object_array,$DataObject);
			}
		}
	}
	
	public function contains(Model $DataObject){
		return (bool)array_search($DataObject, $this->_data_object_array, true);
	}
	
	public function getObjectByIndex($index, $return_blank_object_if_null=false){
		return isset($this->_data_object_array[$index])
			?$this->_data_object_array[$index]
			:($return_blank_object_if_null?new DataObject():null)
		;
	}
	
	public function getObjectIndex($Object){
		return array_search($Object, $this->_data_object_array, true);
	}
	
	public function getIndexByFieldValue($field_name, $data_value, $return_blank_object_if_null=false){
		foreach($this->_data_object_array as $index => $DataObject){
			if($DataObject->get($field_name) == $data_value){
				return $index;
			}
		}
		return $return_blank_object_if_null?new DataObject():null;
	}
	
	public function getIndexArrayByFieldValue($field_name, $data_value){
		$return = array();
		foreach($this->_data_object_array as $index => $DataObject){
			if($DataObject->get($field_name) == $data_value){
				$return[] = $index;
			}
		}
		return $return;
	}

	public function getObjectByFieldValue($field_name, $data_value, $return_blank_object_if_null=false){
		foreach($this->_data_object_array as $DataObject){
			if($DataObject->get($field_name) == $data_value){
				return $DataObject;
			}
		}
		return $return_blank_object_if_null?new DataObject():null;
	}
	
	public function getObjectArrayByFieldValue($field_name, $data_value){
		$return = array();
		foreach($this->_data_object_array as $DataObject){
			if($DataObject->get($field_name) == $data_value){
				$return[] = $DataObject;
			}
		}
		return $return;
	}
	
	public function searchObjects($needle){
		$return = array();
		foreach($this->_data_object_array as $DataObject){
			if($DataObject->searchFields($needle)){
				$return[] = $DataObject;
			}
		}
		return $return;
	}
	
	public function searchObjectByField($field_name, $needle){
		$return = array();
		foreach($this->_data_object_array as $DataObject){
			if($DataObject->searchField($field_name, $needle)){
				$return[] = $DataObject;
			}
		}
		return $return;
	}
	
	public function removeAll(){
		$this->_data_object_array = array();
	}
	
	public function removeObjectAtIndex($index){
		if(isset($this->_data_object_array[$index])){
			unset($this->_data_object_array[$index]);
			return true;
		}
		return false;
	}
	
	public function setObjectAtIndex($DataObject, $index){
		if(!empty($this->_collection_of_object_type)){
			if($DataObject instanceof $this->_collection_of_object_type){
				$this->_data_object_array[$index] = $DataObject;
			} else {
				echo 'Error: Data Object type does not match that of Collection Type "'.$this->_collection_of_object_type.'"'; exit;
			}
		} else {
			$this->_data_object_array[$index] = $DataObject;
		}
	}
	
	// recursive limiting so that object chains don't get infinite and kill the process
	public function toArrayRecursive($limit=10, $blacklist=array()){
		if($limit < 0){ return array(); }
		
		$array_to_return = array();
		
		foreach($this->_data_object_array as $key => $value){
			if($value instanceof Model){
				$array_to_return[$key] = $value->toArrayRecursive($limit-1,$blacklist);
			} else if($value instanceof ModelCollection) {
				$array_to_return[$key] = $value->toArrayRecursive($limit-1,$blacklist);
			} else if(is_array($value)) {
				$array_to_return[$key] = $value;
			} else if(is_object($value)){
				$array_to_return[$key] = get_class_vars($value);
			} else {
				$array_to_return[$key] = $value;
			}
		}
		
		return $array_to_return;
	}
	
	public function length(){
		return count($this->_data_object_array);
	}
	
	public function toArray(){
		return $this->_data_object_array;
	}
	
	public function rewind(){
//		echo "rewinding\n";
		reset($this->_data_object_array);
	}
  
	public function current(){
		$var = current($this->_data_object_array);
//		echo "current: $var\n";
		return $var;
	}
  
	public function key(){
		$var = key($this->_data_object_array);
//		echo "key: $var\n";
		return $var;
	}
  
	public function next(){
		$var = next($this->_data_object_array);
//		echo "next: $var\n";
		return $var;
	}
  
	public function valid(){
		$key = key($this->_data_object_array);
		$var = ($key !== NULL && $key !== FALSE);
//		echo "valid: $var\n";
		return $var;
	}
}
