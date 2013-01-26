<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class MySQLRequest extends Request{
	// There's one request object per command
	private $_is_prepared = false;
	
	public function runAndReturnId(Array $data=array(), Array $integer_fields=array()){
		$db = RuntimeInfo::instance()->connections()->MySQL();
		$this->query($data, $integer_fields);
		return $db->insertId();
	}
	
	/*
	 * @depricated
	 */
	public function runAndReturnMappedData(Array $data=array(), Array $integer_fields=array()){
		return $this->runAndReturnMappedDataCollection($data, $integer_fields);
	}
	
	public function runAndReturnMappedDataArray(Array $data=array(), Array $integer_fields=array()){
		$this->queryAndReturnObjectArray($data, $integer_fields);
		return $this->mappedDataArray();
	}
	
	public function runAndReturnMappedDataCollection(Array $data=array(), Array $integer_fields=array()){
		$this->queryAndReturnObjectCollection($data, $integer_fields);
		return $this->mappedDataCollection();
	}
	
	public function runAndReturnRawData(Array $data=array(), Array $integer_fields=array()){
		$this->queryAndReturnData($data, $integer_fields);
		return $this->rawData();
	}
	
	public function runAndReturnOneRow(Array $data=array(), Array $integer_fields=array()){
		$this->queryOneResult($data, $integer_fields);
		return $this->rawData();
	}
	
	public function runAndReturnAffectedRows(Array $data=array(), Array $integer_fields=array()){
		$db = RuntimeInfo::instance()->connections()->MySQL();
		$this->query($data, $integer_fields);
		return $db->affectedRows();
	}
	
	// this one seems like it would yeild unexpected results (like, now that the methods dont spit out only object collections, how would anyone know what kind of data to expect)
	public function runAndReturnThis(Array $data=array(), Array $integer_fields=array()){
		$this->queryAndReturnObjectCollection($data, $integer_fields, true);
		return $this;
	}

	private function queryOneResult(Array $data, Array $integer_fields=array()){
		$db = RuntimeInfo::instance()->connections()->MySQL();
//		pr($this->_command);
		if($this->_is_prepared === false){
			$db->prepare($this->_command);
		}
		
		//$keys = array_keys($this->_map);
		$db->execute($data, $integer_fields);
		// if this is an insert, don't store the return data in a data object
		$db->readRow();
		$this->_result_data_array = $db->row_data;
	}
	
	private function query(Array $data, Array $integer_fields=array()){
		$db = RuntimeInfo::instance()->connections()->MySQL();
//		pr($this->_command);
		if($this->_is_prepared === false){
			$db->prepare($this->_command);
		}
		
		//$keys = array_keys($this->_map);
		$db->execute($data, $integer_fields);
		// if this is an insert, don't store the return data in a data object
	}
	
	private function queryAndReturnData(Array $data, Array $integer_fields=array()){
		$db = RuntimeInfo::instance()->connections()->MySQL();
//		pr($this->_command);
		if($this->_is_prepared === false){
			$db->prepare($this->_command);
		}
		
		//$keys = array_keys($this->_map);
		$db->execute($data, $integer_fields);
		// if this is an insert, don't store the return data in a data object
		
		while($db->readRow()){
			$this->_result_data_array[] = $db->row_data;
		}
	}
	
	private function queryAndReturnObjectArray(Array $data, Array $integer_fields=array()){
		$db = RuntimeInfo::instance()->connections()->MySQL();
//		pr($this->_command);
		if($this->_is_prepared === false){
			$db->prepare($this->_command);
		}
		
		//$keys = array_keys($this->_map);
		$db->execute($data,$integer_fields);
		// if this is an insert, don't store the return data in a data object
		
		while($db->readRow()){
			array_push($this->_mapped_data_array, $this->mapResult($db->row_data));
		}
	}
	
	private function queryAndReturnObjectCollection(Array $data, Array $integer_fields=array()){
		$db = RuntimeInfo::instance()->connections()->MySQL();
//		pr($this->_command);
		if($this->_is_prepared === false){
			$db->prepare($this->_command);
		}
		
		//$keys = array_keys($this->_map);
		$db->execute($data,$integer_fields);
		// if this is an insert, don't store the return data in a data object
		
		if($this->_return_object_type == 'DataObject'){
			$collection_type = 'DataCollection';
		}else if(class_exists($this->_return_object_type.'Collection')){
			$collection_type = $this->_return_object_type.'Collection';
		} else {
			echo 'Warning: '.$this->_return_object_type.'Collection not found. Using generic DataCollection.<br>';
			$collection_type = 'DataCollection';
		}
		
		$this->_mapped_data_collection = new $collection_type();
		while($db->readRow()){
			$this->_mapped_data_collection->addObject($this->mapResult($db->row_data));
		}
	}
}