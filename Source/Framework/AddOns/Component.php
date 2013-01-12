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
	public function getParentController(){ return $this->_Controller; }
	public function getComponentName(){ return $this->_component_name; }
	public function getComponentClassName(){ return $this->_component_class_name; }
	public function getConfig(){ return $this->_Config; }
	public function getInstanceName(){ return $this->_instance_name; }
	abstract public function getInstance(); // leave this to be defined by connection driver so it can be type cast correctly
	
	public function __construct(Controller $Controller, Model $Config, $instance_name='default'){
		$this->_Controller = $Controller;
		$this->_Config = $Config;
		
		$this->_instance_name = $instance_name;
		
		$this->_instance = $this; // by default, assume this connection references it's own class, but allow for driver makers to assign their own classes here.
		
		$this->_component_class_name = get_called_class();
		$this->_component_name = substr($this->_component_class_name, 0, mb_strlen('Component')*-1);
	}
	
	public function mapRequest(){
			
		$component_controller_name = RuntimeInfo::instance()->getComponentControllerName();
		$component_controller_path = RuntimeInfo::instance()->getComponentControllerPath();
		if(mb_strlen($component_controller_path) > 0){ $component_controller_path .= '/'; }
		
		$component_controller_class = $component_controller_name.'ComponentController';
		if(isset($this->_component_controllers[$component_controller_path.$component_controller_class])){
			// rerun request? Seems like maybe this would be an option somehow. loops or something. Cant imagine a use currently
			return $this->_component_controllers[$component_controller_path.$component_controller_class]->handleRequest();
		} else {
//			pr(Path::toComponents().$this->_component_name.'/Controllers/'.$component_controller_path.$component_controller_class.'.php');
			if(File::exists(Path::toComponents().$this->_component_name.'/Controllers/'.$component_controller_path.$component_controller_class.'.php')){
//				pr(__LINE__);
				Run::fromComponents($this->_component_name.'/Controllers/'.$component_controller_path.$component_controller_class.'.php');
				if(class_exists($component_controller_class)){
					// instantiation of a controller runs all the necessary controller methods
					$ComponentController = $this->_component_controllers[$component_controller_path.$component_controller_class] 
						= new $component_controller_class($this); // this isn't great for code completion, but because there are cast methods its excusable
					$ComponentController->handleRequest();
					return ComponentController::cast($ComponentController);
				} else {
					Run::fromControllers('Pages/Err404Page.php');
					return new Err404Page();
				}
			} else {
//				pr(__LINE__);
				Run::fromControllers('Pages/Err404Page.php');
				return new Err404Page();
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