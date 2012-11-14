<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

abstract class Controller {
	// these all have getters and shouldn't be manipulated once set
	private 
		$_AppSettings,
		$_RuntimeInfo,
		$_Helpers,
		$_Connections;
	
	protected 
		$_data = array(),
		$_tpl,
		$_page_body,
		$_controller_name,
		$_controller_format,
		
		// well these aren't cased appropriately are they
		$page_result_start = 0,
		$page_result_limit = 20,
		$cache_results = false;
	
	/*
	 * @depricated
	 */
	public 
		$SystemNotifications,
		$Messages, // depricated
		// All ok.
		$Errors,
		$Notices,
		$Confirmations,
		$Input;
	
	public function __construct(){
		$this->_tpl = new TemplateParser();
		
		$this->_controller_name = isset($_GET['framework']['controller_name'])?$_GET['framework']['controller_name']:'Index'; // default to Index
		$this->_controller_format = isset($_GET['framework']['controller_format'])?$_GET['framework']['controller_format']:'html'; // default to HTML
		
		// all of these variables need to be reconciled and finalized///////
		
		// depricated
		$this->Messages = new DataObject();			// generic messages (a combination of confirmations and notices) which has been depricated
		// all ok
		$this->SystemNotifications = new DataObject();	// store confirmations of an action
		$this->Errors = new DataObject();			// store errors when processing
		$this->Confirmations = new DataObject();	// store confirmations of an action
		$this->Notices = new DataObject();			// store info notices  
		$this->Input = new DataObject();			// store data from the form
		
		$this->_RuntimeInfo = RuntimeInfo::instance();
		$this->_AppSettings = new DataObject($this->_RuntimeInfo->appSettings());
		$this->_Helpers = $this->_RuntimeInfo->helpers();
		$this->_Connections = $this->_RuntimeInfo->connections();
		
		$initialize_method = 'initialize'.$this->_controller_format;
		if(method_exists($this,$initialize_method)){
			$this->$initialize_method();
		}
		
	}
	
	public function getAppSettings(){ return DataObject::cast($this->_AppSettings); }
	public function getRuntimeInfo(){ return Startup::cast($this->_RuntimeInfo); }
	public function getHelpers(){ return Helpers::cast($this->_Helpers); }
	public function getConnections(){ return Connections::cast($this->_Connections); }
	
	abstract protected function loadIncludedFiles();
	abstract protected function handleRequest();
	
	private $_models = array();
	private $_actions = array();
	
	// read only models and actions arrays
	public function getLoadedModels(){ return $this->_models; }
	public function getLoadedActions(){ return $this->_actions; }
	
	public function getControllerName(){ return $this->_controller_name; }
	public function getControllerFormat(){ return $this->_controller_format; }
	
	public function loadModels($models_array = array()){
		if(is_array($models_array)){
			foreach($models_array as $model_path){
				if(!in_array($model_path,$this->_models)) { 
					Run::fromModels($model_path.'.php'); 
					$this->_models[] = $model_path;
				}
			}
		} else {
			if(!in_array($models_array,$this->_models)) { 
				Run::fromModels($models_array.'.php'); 
				$this->_models[] = $models_array;
			}
		}
	}

	public function loadActions($actions_array = array()){
		if(is_array($actions_array)){
			foreach($actions_array as $action_path){
				if(!in_array($action_path,$this->_actions)) { 
					Run::fromActions($action_path.'.php'); 
					$this->_actions[] = $action_path;
				}
			}
		} else {
			if(!in_array($actions_array,$this->_actions)) { 
				Run::fromActions($actions_array.'.php'); 
				$this->_actions[] = $actions_array;
			}
		}
	}
	
	public function setPageResultStart($page_result_limit){ $this->page_result_limit = $page_result_limit; }
	public function getPageResultStart(){ return $this->page_result_limit; }
	
	public function setPageResultLimit($page_result_start){ $this->page_result_start = $page_result_start; }
	public function getPageResultLimit(){ return $this->page_result_start; }
	
	public function setOutputFormat($output_format){ $this->output_format = $output_format; }
	public function getOutputFormat(){ return $this->output_format; }
	
	public function setCacheResults($cache_results){ $this->cache_results = $cache_results; }
	public function getCacheResults(){ return $this->cache_results; }
	
	public function data(){ return $this->_data; }

	public function getPageBody(){ return $this->_page_body; }
	public function getTemplateEngine(){ return $this->_tpl; }
	
	protected $_output='';
	public function getOutput(){ return $this->_output; }
		
	public function runCodeReturnOutput($path){
//		$ID = RuntimeInfo::instance()->id();
		
		ob_start();
		
		extract($this->data());
		
		try{
			$file = File::osPath(dirname(__FILE__).'/../../Applications/'.$this->getRuntimeInfo()->getApplicationName().'/Views/').$path;
			if(file_exists($file)){ require($file); }
			else { exit('Could not load required file:'. $file); }
		} catch (Exception $e){
			echo 'Error: '.$e->getCode()."\n<br>";
			echo 'File: '.$e->getFile()."\n<br>";
			echo 'Line: '.$e->getLine()."\n<br>";
			echo 'Message: '.$e->getMessage()."\n<br>";
			pr($e);
		}
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}

