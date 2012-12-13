<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

abstract class Component{
	
	protected 
		$_component_class_name,
		$_component_name,
		$_Controller,
		$_Config,
		$_instance_name,
		$_instance,
		$_component_controllers = array(); // instances of component controller run from this request
	
	// set up read only variables
	public function getComponentName(){ return $this->_component_name; }
	public function getComponentClassName(){ return $this->_component_class_name; }
	public function getConfig(){ return $this->_Config; } // this should probably be a model object for simplicity in calling unknown values without running checks
	public function getInstanceName(){ return $this->_instance_name; }
	abstract public function getInstance(); // leave this to be defined by connection driver so it can be type cast correctly
	
	public function __construct(Controller $Controller, Model $Config, $instance_name='default'){
		$this->_Controller = $Controller;
		$this->_Config = $Config;
		
		$this->_instance_name = $instance_name;
		
		$this->_instance = $this; // by default, assume this connection references it's own class, but allow for driver makers to assign their own classes here.
		
		$this->_component_class_name = get_called_class();
		$this->_component_name = substr($this->_component_class_name, 0, strlen('Component')*-1);
	}
	
//	the handling of 1 page request bound to an alias vs all page requests bound to a folder need to be easier to author and understand.
	
	// The component checks to see if the controller exists, and if it does it creates an instance to the component
	public function checkRequest($component_controller_name){
		$component_controller_class = $component_controller_name.'ComponentController';
		if(isset($this->_component_controllers[$component_controller_class])){
			// rerun request? Seems like maybe this would be an option somehow. loops or something. Cant imagine a use currently
			return $this->_component_controllers[$component_controller_class]->handleRequest();
		} else {
			Run::fromComponents($this->_component_name.'/Controllers/'.$component_controller_class.'.php');
			if(class_exists($component_controller_class)){
				// instantiation of a controller runs all the necessary controller methods
				return $this->_component_controllers[$component_controller_class] 
					= new $component_controller_class($this); // this isn't great for code completion, but because there are cast methods its excusable
			} else {
				return new ComponentController();
			}
		}
	}
	
	public function getComponentController($component_controller_name){
		$component_controller_class = $component_controller_name.'ComponentController';
		if(isset($this->_component_controllers[$component_controller_class])){
			// rerun request? Seems like maybe this would be an option somehow. loops or something. Cant imagine a use currently
			return ComponentController::cast($this->_component_controllers[$component_controller_class]);
		} else {
			return false;
		}
	}
	
	public static function cast(Component $Component){ return $Component; }
	
	// require all connections to have destruct methods
	abstract public function __destruct();
	
}