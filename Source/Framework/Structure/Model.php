<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class Model implements Iterator, Serializable{
	
	// Store all the data in one associatively indexed array for dynamic handling of any number of fields
	protected $_data = array();
	protected $_allowed_data = array();
	
	protected $_size_of_allowed_data = 0;
	
	protected $_size_of_data = 0;
	
	/**
	*This should set the data based on the results returned from the db and the column names returned.
	* @param array $data
	* @param string $default_filter
	*/
	public function __construct(Array $data=array()){
		$this->setDataFromArray($data);
	}
	
	public function set($key_name, $data_value){
		if($this->_size_of_allowed_data === 0 || in_array($key_name,array_keys($this->_allowed_data))){
			$this->_data[$key_name] = $data_value;
			return true;
		} else {
			if(RuntimeInfo::instance()->appSettings()->get('debug')){
				echo "\nWarning: tried to set property \"{$key_name}\" on object \"".get_class($this)."\" where not allowed by model definition.\n<br>";
			}
		}
		return false;
	}
	
	public function setDataFromArray(Array $data){
//		print_r($data);
		foreach($data as $column => $value){
//			echo "{$column} => {$value}";
			$this->set($column,$value);
		}
	}
	
	public function get($key_name){
		switch(isset($this->_allowed_data[$key_name])?$this->_allowed_data[$key_name]:''){
			case DataType::BINARY: return isset($this->_data[$key_name])?$this->_data[$key_name]:null; break;
			case DataType::BOOLEAN: return $this->getBoolean($key_name); break;
			case DataType::COLLECTION: return $this->getCollection($key_name); break;
			case DataType::DATE:
			case DataType::DATETIME: return $this->getDate($key_name); break;
			case DataType::NUMBER: return $this->getNumber($key_name); break;
			case DataType::CURRENCY: return $this->getCurrency($key_name); break;
			case DataType::OBJECT: return $this->getObject($key_name); break;
			case DataType::PHP_ARRAY: return $this->getArray($key_name); break;
			case DataType::SECRET: return '************'; break; // if you want the secret, you'll have to ask for it as a string manually
			case DataType::TEXT: return $this->getString($key_name); break;
			case DataType::TIMESTAMP: return $this->getTimestamp($key_name); break;
			default: return $this->getString($key_name); break;
		}
	}
	
	public function getAllowData(){
		return $this->_allowed_data;
	}
	
	public function getArray($key_name){
		$result = isset($this->_data[$key_name])?$this->_data[$key_name]:array();
		return (is_array($result))?$result:array();
	}
	
	// Add some sugar to sweeten the syntax
	
	public function getBoolean($key_name){
		$result = isset($this->_data[$key_name])?$this->_data[$key_name]:false;
		return (
//				!isset($result) ||
				is_null($result) ||
				empty($result) ||
				strtolower($result) === "false"
		)?false:true;
	}
	
	public function getCollection($key_name, $object_type_if_null='DataCollection'){
		$result = isset($this->_data[$key_name])?$this->_data[$key_name]:new $object_type_if_null();
		return ($result instanceof $object_type_if_null)?$result:new $object_type_if_null();
	}
		
	// format matches that of http://us1.php.net/manual/en/function.money-format.php
	public function getCurrency($key_name, $min=null, $max=null, $format='%(#10n', $region='en_US.UTF-8'){
		$result = self::getNumber($key_name, $min, $max);
		setlocale(LC_MONETARY, $region);
		$result = money_format($format, $result);
		setlocale(LC_MONETARY, '0'); // restore to php's default. annoying because php doesnt allow you to get the current locale setting it is using
		return $result;
	}
	
	public function getCurrentDataFieldsToString(){
		$keys = array_keys($this->_data);
		$dataFields = "'".implode("','",$keys)."'";
		return $dataFields;
	}
	
	/**
	 * Returns original data entered into filtered object in an unfiltered array
	 * @return array
	 */
	public function getDataArrayInSqlRequestFormat() {
		$sql_format = array();
		if($this->_size_of_allowed_data > 0){
			foreach($this->_allowed_data as $key => $data_type){ $sql_format[':'.$key] = $this->get($key); }
		}else{
			// warnings need to be tied to a logger class
			echo 'Warning: This is not a mapped array so data may be missing from return. For best results, use staticly typed data objects extended from the Model class.<br>';
		}
//		pr($sql_format);
		return $sql_format;
	}
	
	public function getDateTimeObject($key_name, $timezone='UTC'){
		$result = isset($this->_data[$key_name])?strtotime($this->_data[$key_name]):0; // same
		return new DateTimeObject($result, new DateTimeZone($timezone));
	}
	
	// meant to be shorthand for getNumber, only with the default being to have decimals set to 2
	// format matches that of http://us1.php.net/number_format
	public function getDecimal($key_name, $min=null, $max=null, $decimals=2, $dec_point = '.' , $delimiter = ','){
		$result = self::getNumber($key_name, $min, $max);
		number_format($result, $decimals, $dec_point, $delimiter);
	}
	
	public function getStringAndConvertEntitiesToHTML($key_name, $character_limit=null, $trim_output=true){
		return html_entity_decode(self::getString($key_name,$character_limit, $trim_output));
	}
	
	public function getStringAndConvertHTMLToEntities($key_name, $character_limit=null, $trim_output=true){
		return htmlentities(self::getString($key_name,$character_limit, $trim_output));
	}
	
	public function getInteger($key_name, $min=null, $max=null){
		$result = isset($this->_data[$key_name])?intval($this->_data[$key_name]):0; // sanitize
		if(is_null($min) && is_null($max)){ return $result; }
		if(!is_null($min) && $this->_data[$key_name] < $min) { return $min; }
		if($this->_data[$key_name] > $max) { return $max; } // can only be max
		return $result;
	}
	
	public function getNumber($key_name, $min=null, $max=null){
		$result = isset($this->_data[$key_name])?floatval($this->_data[$key_name]):0; // sanitize
		if(is_null($min) && is_null($max)){ return $result; }
		if(!is_null($min) && $this->_data[$key_name] < $min) { return floatval($min); }
		if($this->_data[$key_name] > $max) { return floatval($max); } // can only be max
		return $result;
	}
	
	public function getObject($key_name, $object_type_if_null='DataObject'){
		$result = isset($this->_data[$key_name])?$this->_data[$key_name]:new $object_type_if_null();
		return ($result instanceof $object_type_if_null)?$result:new $object_type_if_null();
	}
	
	public function getString($key_name, $character_limit=null, $trim_output=true){
		$result = isset($this->_data[$key_name])?$this->_data[$key_name]:'';
		$result = $trim_output?trim($result):$result;
		
		/* ---- not sure this is necessary anymore. need to check with db. works fine for text files. 
		///////////////////////////////
		// multilingual utf-8 support
		
		$str = str_replace("\x00",'',stripslashes($result)); // do i really need strip slashes here?
		$str = mb_encode_numericentity($str, array(0x0, 0x2FFFF, 0, 0xFFFF), 'UTF-8');
		$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
		$str = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $str);
		$str = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $str);
		
		// end multilingual utf-8 support
		///////////////////////////////
		*/
		
		return is_null($character_limit)?$result:substr($result, 0, $character_limit);
	}
	
	public function getTimestamp($key_name){
		return isset($this->_data[$key_name])?strtotime($this->_data[$key_name]):0; // 0 seconds from unix epoc
	}
	
	protected function setAllowedData(Array $a, $typed_array = false){
		if(!$typed_array){
			$a = array_flip($a);
			foreach($a as $key => $value){ $a[$key] = 'text'; }
		}
		
		$this->_allowed_data = $a;
		$this->_size_of_allowed_data = count($this->_allowed_data);
	}
	
	protected function addAllowedData(Array $a, $typed_array = false){
		if(!$typed_array){
			$a = array_flip($a);
			foreach($a as $key => $value){ $a[$key] = 'text'; }
		}
		if($this->_size_of_allowed_data == 0){
			$this->_allowed_data = $a;
		} else {
			$this->_allowed_data = array_merge($this->_allowed_data,$a);
		}
		$this->_size_of_allowed_data = count($this->_allowed_data);
	}
	
	public function clearObjectData(){
		$this->_data = array();
	}
		
	//////////////////////////////////////
	// Searching for collection searches
	//////////////////////////////////////
	
	public function searchFields($needle){
		$matches = array();
		foreach($this->_data as $key => $value){
			if(strstr($value, $needle)){
				$matches[$key] = $value;
			}
		}
		return count($matches)?$matches:false;
	}
	
	public function searchField($field_name, $needle){
		return (strstr($this->_data[$field_name], $needle))?$this->_data[$field_name]:false;
	}
	
	/**
	 * Returns original data entered into filtered object in an unfiltered array
	 * @return array
	 */
	public function toArray($blacklist=array()) { 
		$return_array = $this->_data;
		if(count($this->_allowed_data)>0){
			$object_fields_array = $this->_allowed_data;
			$field_list = array_keys($this->_allowed_data);
			$object_fields_array = array_combine(
				$field_list, // field names
				array_fill(0,count($object_fields_array),null) // null values
			);
			$return_array = $this->_data + $object_fields_array; // the syntax for combining arrays and overwriting keys
		} else {
			$field_list = array_keys($this->_data);
		}
		
		foreach($blacklist as $key_name){
			if(is_array($key_name)) { continue; } // if its an array, this is likely a hierarchy of blacklists so ignore this one
			if(in($key_name,$field_list)){
				unset($return_array[$key_name]);
			}
		}
		return $return_array;
	}
	
	/**
	 * Returns original data entered into filtered object in an unfiltered array
	 * @return array
	 */
	public function toArrayOfSetValues() { return $this->_data; }
	
	// recursive limiting so that object chains don't get infinite and kill the process
	public function toArrayRecursive($limit=10, $blacklist=array()){
		if($limit < 0){ return array(); }
		
		$array_to_return = $this->toArray($blacklist);
//		pr($array_to_return);
		
		foreach($array_to_return as $key => $value){
			
			if($value instanceof Model){
				$array_to_return[$key] = $value->toArrayRecursive($limit-1, isset($blacklist[$key])?$blacklist[$key]:array());
			} else if($value instanceof ModelCollection) {
				$array_to_return[$key] = $value->toArrayRecursive($limit-1, isset($blacklist[$key])?$blacklist[$key]:array());
			} else if(is_array($value)) {
				$array_to_return[$key] = $value;
			} else if(is_object($value)){
				$array_to_return[$key] = get_class_vars($value);
// 			} else { // done in initial blacklisted dump. 
// 				$array_to_return[$key] = $value;
			}
		}
	
		return $array_to_return;
	}
	
	public function toJSON(){
		return json_encode(self::toArrayRecursive());
	}
	
	public function toString(){
		$data_allowed = count($this->_allowed_data);
		return 'My name is '.get_class($this).' and I was called as '.get_called_class()
		.'. I have '.count($this->_data).' fields stored'.($data_allowed?' and '.$data_allowed.' allowed fields.':'')."\n";
	}
	
	public function length(){ return count($this->_data); }
	
	///////////////////////////
	// Iterator functions
	///////////////////////////
	
	public function rewind(){
//		echo "rewinding\n";
		if(count($this->_allowed_data)){
			reset($this->_allowed_data);
		} else {
			reset($this->_data);
		}
	}
  
	public function current(){
		if(count($this->_allowed_data)){
			$key = key($this->_allowed_data);
		} else {
			$key = key($this->_data);
		}
		
//		echo "current: $key\n";
		return isset($this->_data[$key])?$this->_data[$key]:null;
	}
  
	public function key(){
		if(count($this->_allowed_data)){
			$key = key($this->_allowed_data);
		} else {
			$key = key($this->_data);
		}
		
//		echo "key: $key\n";
		return $key;
	}
  
	public function next(){
		if(count($this->_allowed_data)){
			next($this->_allowed_data);
			$var = key($this->_allowed_data);
		} else {
			next($this->_data);
			$var = key($this->_data);
		}
//		echo "next: $var\n";
		return isset($this->_data[$var])?$this->_data[$var]:null;
	}
  
	public function valid(){
		if(count($this->_allowed_data)){
			$key = key($this->_allowed_data);
		} else {
			$key = key($this->_data);
		}
		
		$var = ($key !== NULL && $key !== FALSE);
//		echo "valid: $var\n";
		return ($key !== NULL && $key !== false);
	}
	
	///////////////////////////
	// Serializable functions
	///////////////////////////
	
	public function serialize() {
		return serialize($this->_data);
	}
	
	// not sure what data fields will be filled. need to make sure that original white list fields are retained
	public function unserialize($data) {
		$this->_data = unserialize($data);
	}
	
}


