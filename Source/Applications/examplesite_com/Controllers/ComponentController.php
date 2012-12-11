<?php


abstract class ComponentController extends Controller{
	
	use HTMLFormat, JSONFormat, XMLFormat;
	
	protected 
		$_component_controller_class_name, // the name of this class
		$_component_controller_name,
		$_component_name,
		$_component_class_name; // the short name of this controller
	
	public function __construct($ParentObject=null){
		parent::__construct($ParentObject);
		
		$this->_component_controller_class_name = get_called_class();
		$this->_component_controller_name = substr($this->_component_controller_class_name, 0, strlen('ComponentController')*-1);
		
		$this->_ParentObject = $Component = Component::cast($this->_ParentObject);
		
		$this->_component_class_name = $Component->getComponentClassName();
		$this->_component_name = $Component->getComponentName();
		
		// load required files for this controller automatically and do so before components so components can use included files
		$this->loadIncludedFiles();
	}
	
	public function renderHTML(){
// 		$Page = ComponentController::cast($this);
// 		$path = ($Page->getControllerPath() == '')?$Page->getControllerName():$Page->getControllerPath().'/'.$Page->getControllerName();
// 		$this->addCss('Components/'.$path);
// 		$this->addJs('Components/'.$path);
		
		$this->_page_body = $this->runCodeReturnOutput(Path::toComponents().$this->_component_name.'/Views/'.$this->_component_controller_name.'/layout.php', false);
				
		return $this->_page_body;
	}
	
	public static function cast(ComponentController $ComponentController){
		return $ComponentController;
	}
}
