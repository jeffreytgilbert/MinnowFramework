<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// Add your own method checks here so when the source gets upgraded for the framework and its addons you can keep your work intact.
// Validators are largely a collection of column or data types which require validation before SQL entry. 
// Because there is no way to practically plan for every specific need developers might have for their forms, 
// the choice was made to have this layer be extendable by the developer for their own commonly used validator types.
class FormValidator extends Validator{
	
	// Make sure the framework loads your validators when it calls the following method:
	
	public static function loadCustomValidators(){
		// Preload all the validation rules you've linked up and plan to use.
		// This isn't done automatically. If it were, object inheritance might break.
		
		Run::fromFormValidators('CustomValidators/ValidPassword.php');
	}
	
	// copy and paste example code (just change the Object and Method name accordingly): 
	
	function checkPassword($field_name){
		$this->_field_validators[$field_name] = $Check = new ValidPassword($this->getFieldData($field_name));
		return $Check;
	}
	
}
