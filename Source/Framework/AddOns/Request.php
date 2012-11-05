<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

abstract class Request{
	protected $_command;
	protected $_return_object_type;
	protected $_map;
	protected $_mapped_data_array=array();
	protected $_mapped_data_collection;
	//	protected $_resource; // no need to store this
	protected $_result_data_array=array();
	private $_size_of_map = 0;
	
	public function __construct($command, $return_object_type='Model', $map=array()){
		$this->_mapped_data_collection = new ModelCollection();
		$this->_command = $command;
		$this->_return_object_type = $return_object_type;
		$this->_map = $map;
		$this->_size_of_map = count($map);
	}
	
//	// should define the kind of resource that's used 
//	abstract function runAndReturnId(Array $data); // returns id
//	abstract function runAndReturnMappedData(Array $data); // returns remapped data records for creating statically typed objects
//	abstract function runAndReturnRawData(Array $data); // returns raw records
//	abstract function runAndReturnAffectedRows(Array $data); // returns affected rows
//	abstract function runAndReturnThis(Array $data); // returns affected rows
	
	public function rawData(){
		return $this->_result_data_array;
	}
	
	public function mappedDataCollection(){
		return $this->_mapped_data_collection;
	}
	
	public function mappedDataArray(){
		return $this->_mapped_data_array;
	}
	
	protected function mapResult($raw_data){
//		pr($raw_data);
		// not particularly happy about duplicating data for the sake of returning it in two formats. must find a better way to do this
		$this->_result_data_array[] = $raw_data;
		// this is where the mapping needs to happen
		if($this->_size_of_map > 0){
			$mapped_data = array();
			foreach($raw_data as $old_key => $data){
				if(array_key_exists($old_key,$this->_map)){
					// create the keyed data in a clean array
					$mapped_data[$this->_map[$old_key]] = $data;
					// remove the original data from the results so when merged into the remapped result, it wont have duplicate data in 2 keys
					unset($raw_data[$old_key]);
				}
			}
			$mapped_data = array_merge($mapped_data,$raw_data);
		} else {
			$mapped_data = $raw_data;
		}
//		pr($mapped_data);
		return new $this->_return_object_type($mapped_data);
	}
	
	// resets the data in this request handler to null / default, so new data doesn't add to old data for the next query
	public function reconstruct($return_object_type='Model', $map=array()){
		$this->_return_object_type = $return_object_type;
		$this->_map = $map;
		$this->_size_of_map = count($map);
		$this->_mapped_data_array = array();
		$this->_result_data_array = array();
		$this->_mapped_data_collection = new ModelCollection();
	}
}
