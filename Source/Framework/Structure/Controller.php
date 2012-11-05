<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */


// When a trait is called, it will run functions based on its name. 
// Example:
// initializeJSON(){ ... }
// renderJSON(){ ... }
// and the Page that uses these traits can override this functionality, or they can roll with it, but this way, inheritance is still supported, collisions are avoided, and dynamic format handling is also handled and easily added with a 1 liner use declaration

abstract class Controller {
	protected $_data = array();
	protected $page_result_start = 0;
	protected $page_result_limit = 20;
	protected $cache_results = false;
	protected $RuntimeInfo;
	
	/*
	 * @depricated
	 */
	public $Messages; // depricated
	// All ok.
	public $Errors;
	public $Notices;
	public $Confirmations;
	public $Input;
	
	public function __construct(){
		// depricated
		$this->Messages = new DataObject();			// generic messages (a combination of confirmations and notices) which has been depricated
		// all ok
		$this->Errors = new DataObject();			// store errors when processing
		$this->Confirmations = new DataObject();	// store confirmations of an action
		$this->Notices = new DataObject();			// store info notices  
		$this->Input = new DataObject();			// store data from the form
		$this->RuntimeInfo = RuntimeInfo::instance();
	}
	
	public function getRuntimeInfo(){ return $this->RuntimeInfo; }
	
	abstract protected function loadIncludedFiles();
	abstract protected function handleRequest();	
	
	public function requires($requirement_or_array_of_requirements = array()){
		$ID = RuntimeInfo::instance()->id();
		
		if(is_array($requirement_or_array_of_requirements)){
			foreach($requirement_or_array_of_requirements as $requirement){
				$result = $ID->checkRequirement($requirement);
			}
		}
	}
	
	private $_models = array();
	private $_actions = array();
	
	// read only models and actions arrays
	public function getLoadedModels(){ return $this->_models; }
	public function getLoadedActions(){ return $this->_actions; }
	
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
	
	protected $_output='';
	public function getOutput(){ return $this->_output; }
		
	public function runCodeReturnOutput($path){
		$ID = RuntimeInfo::instance()->id();
		
		ob_start();
		
		extract($this->data());
		
		try{
			require(File::osPath(dirname(__FILE__).'/../../Applications/'.$this->RuntimeInfo->getApplicationName().'/Views/').$path);
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

