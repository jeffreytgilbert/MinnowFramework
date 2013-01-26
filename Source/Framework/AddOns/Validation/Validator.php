<?php 

class Validator{
	
	const METHOD_POST = 'POST';
	const METHOD_GET = 'GET';
	
	protected 
		$_field_validators = array(),
		$_name,
		$_method,
		$_form_data_in_array,
		$_FormDataObject,
		$_errors;
	
	public function __construct($form_name, $method=self::METHOD_POST){
		$this->_name = $form_name;
		$this->_method = $method;
		
		if($this->_method == self::METHOD_POST && isset($_POST[$this->_name]) && is_array($_POST[$this->_name])){
			$this->_form_data_in_array = $_POST[$this->_name];
		} else if($this->_method == self::METHOD_GET && isset($_GET[$this->_name]) && is_array($_GET[$this->_name])) {
			$this->_form_data_in_array = $_GET[$this->_name];
		} else {
			$this->_form_data_in_array = array();
		}
		
		$this->_FormDataObject = new DataObject($this->_form_data_in_array);
	}
	
	public function isSubmitted(){
		return self::hasBeenSubmitted();
	}
	
	public function hasBeenSubmitted(){
		return (count($this->_form_data_in_array))?true:false;
	}

	// This is a bad method to add. It gives the impression that checks have already been run. 
	// Instead there should be a method to grab the cumulative errors.
// 	public function hasErrors(){
// 	}
	
	public function getCurrentErrors(){
		$errors = array();
		foreach($this->_field_validators as $field_name => $Validator){
			$Validator = ValidationRule::cast($Validator);
			$errors[$field_name] = $Validator->getErrors();
		}
		return $errors;
	}
	
	public function getFormDataAsDataObject(){
		return $this->_FormDataObject;
	}
	
	public function getFieldData($field_name){
		return isset($this->_form_data_in_array[$field_name])?$this->_form_data_in_array[$field_name]:null;
	}
	
	function checkInteger($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidInteger($this->getFieldData($field_name));
		return $Check;
	}
	
	function checkNumber($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidNumber($this->getFieldData($field_name));
		return $Check;
	}
	
	function checkString($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidString($this->getFieldData($field_name));
		return $Check;
	}
	
	function checkEmail($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidEmail($this->getFieldData($field_name));
		return $Check;
	}

	function checkURL($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidURL($this->getFieldData($field_name));
		return $Check;
	}
	
	function checkPhoneNumber($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidPhoneNumber($this->getFieldData($field_name));
		return $Check;
	}
	
	function checkCreditCard($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidCreditCard($this->getFieldData($field_name));
		return $Check;
	}
	
	function checkIP($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidIP($this->getFieldData($field_name));
		return $Check;
	}
	
	function checkPostalCode($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidPostalCode($this->getFieldData($field_name));
		return $Check;
	}
	
	function checkDate($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidDate($this->getFieldData($field_name));
		return $Check;
	}
	
	function checkTime($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidTime($this->getFieldData($field_name));
		return $Check;
	}
	
	function checkColor($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidColor($this->getFieldData($field_name));
		return $Check;
	}
	
	function checkSSN($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidSocialSecurityNumber($this->getFieldData($field_name));
		return $Check;
	}
	
	function checkWords($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidWords($this->getFieldData($field_name));
		return $Check;
	}
	
}