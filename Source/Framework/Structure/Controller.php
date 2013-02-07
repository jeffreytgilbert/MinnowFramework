<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

abstract class Controller {
	// these all have getters and shouldn't be manipulated once set
	private 
		$_AppSettings;
	
	protected 
		$_tpl,
		$_page_body,
		$_controller_path,
		$_controller_name,
		$_controller_format,
		$_ParentObject,
		
		$SystemNotifications,
		// All ok.
		$_Data,
		$_Errors,
		$_Notices,
		$_Confirmations,
		$_Input,
		
		// well these aren't cased appropriately are they
		$page_result_start = 0,
		$page_result_limit = 20,
		$cache_results = false;
	
	
	public function __construct($ParentObject=null){
		
		$this->_ParentObject = $ParentObject; // parent object makes it easier to traverse component and controller hierarchies.
		
		$this->_tpl = new TemplateParser();
		
		$this->_controller_path = RuntimeInfo::instance()->getControllerPath();
		$this->_controller_name = RuntimeInfo::instance()->getControllerName(); // default to Index
		$this->_controller_format = RuntimeInfo::instance()->getControllerFormat(); // default to HTML
		
		// all of these variables need to be reconciled and finalized///////
		
		// all ok
		$this->_SystemNotifications = new DataObject();	// store confirmations of an action
		$this->_Errors = new DataObject();			// store errors when processing
		$this->_Confirmations = new DataObject();	// store confirmations of an action
		$this->_Notices = new DataObject();			// store info notices  
		$this->_Data = new DataObject();			// store data that goes to the view as an array 
		
		// @todo Legacy functionality for form handling. May or may not be required. Will have to further research this to decide if its necessary for autofill forms.
		
		// store data from the form
		if(isset($_POST) && is_array($_POST)){
			$total_post_forms = count($_POST);
			if($total_post_forms == 1){
				$fields = current($_POST);
				if(is_array($fields) && count($fields)){
					$this->_Input = new DataObject(array(key($_POST) => new DataObject($fields)));
				} else {
					$this->_Input = new DataObject();
				}
			} else {
				foreach($_POST as $form => $fields){
					if(is_array($fields) && count($fields)){
						$this->_Input = new DataObject(array(key($_POST) => new DataObject($fields)));
					} else {
						$this->_Input = new DataObject();
					}
				}
				$this->_Input = new DataObject($_POST);
			}
		} else {
			$this->_Input = new DataObject();
		}
		
		$this->_AppSettings = RuntimeInfo::instance()->appSettings();
		
		$initialize_method = 'initialize'.$this->_controller_format;
		if(method_exists($this,$initialize_method)){
			$this->$initialize_method();
		}
		
	}
	
	public function getDataObject(){ return DataObject::cast($this->_Data); }
	public function getDataValue($field_name){ return $this->_Data->get($field_name); }
	public function setData($key_name, $data_value){ return $this->_Data->set($key_name, $data_value); }
	public function getInput($form_name=null){
		if(is_null($form_name)){ 
			return DataObject::cast($this->_Input); 
		} else {
			return $this->_Input->getObject($form_name); 
		}
	}
	public function setInput($form_name, Array $data = array()){ $this->_Input->set($form_name,new DataObject($data)); }
	
	public function getNotices(){ return DataObject::cast($this->_Notices); }
	public function getConfirmations(){ return DataObject::cast($this->_Confirmations); }
	public function getErrors(){ return DataObject::cast($this->_Errors); }
	public function getSystemNotifications(){ return DataObject::cast($this->_SystemNotifications); }
	
	public function getAppSettings(){ return DataObject::cast($this->_AppSettings); }
	public function getRuntimeInfo(){ return RuntimeInfo::instance(); }
	public function getHelpers(){ return RuntimeInfo::instance()->helpers(); }
	public function getConnections(){ return RuntimeInfo::instance()->connections(); }
	
	protected function loadIncludedFiles() { } // optional pre-execution stage for loading includes
	abstract public function handleRequest();
	
	private $_models = array();
	private $_actions = array();
	
	// read only models and actions arrays
	public function getLoadedModels(){ return $this->_models; }
	public function getLoadedActions(){ return $this->_actions; }
	
	public function getControllerPath(){ return $this->_controller_path; }
	public function getControllerName(){ return $this->_controller_name; }
	public function getControllerFormat(){ return $this->_controller_format; }
	
	public function loadModels($models_array = array()){
		if(is_array($models_array)){
			foreach($models_array as $model_path){
				if(!in_array($model_path,$this->_models)) { 
					Run::fromModels('Custom/'.$model_path.'.php'); 
					$this->_models[] = $model_path;
				}
			}
		} else {
			if(!in_array($models_array,$this->_models)) { 
				Run::fromModels('Custom/'.$models_array.'.php'); 
				$this->_models[] = $models_array;
			}
		}
	}

	public function loadActions($actions_array = array()){
		if(is_array($actions_array)){
			foreach($actions_array as $action_path){
				if(!in_array($action_path,$this->_actions)) { 
					Run::fromActions('Custom/'.$action_path.'.php'); 
					$this->_actions[] = $action_path;
				}
			}
		} else {
			if(!in_array($actions_array,$this->_actions)) { 
				Run::fromActions('Custom/'.$actions_array.'.php'); 
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
	
	public function getPageBody(){ return $this->_page_body; }
	public function getTemplateEngine(){ return $this->_tpl; }
	
	protected $_output='';
	public function getOutput(){ return $this->_output; }
	
	public function redirect($url, $status = null, $exit = true){
		
		// Handle redirects for JSON, XML, etc requests.
		if(!in(strtolower($this->_controller_format),array('html','page',''))){
			if($exit){
				if($status >= 400 && $status <= 599){
					$this->_Errors->set('Redirect',$status);
				} else {
					$this->_Notices->set('Redirect',$status);
				}
				$render_method_name = 'render'.$this->_controller_format;
				if(method_exists($this, $render_method_name)){
					echo $this->$render_method_name();
					exit;
				} else {
					die('Specified format not supported');
				}
			}
		}
		
		if (!empty($status)) {
			$codes = array(
				100 => 'Continue',
				101 => 'Switching Protocols',
				200 => 'OK',
				201 => 'Created',
				202 => 'Accepted',
				203 => 'Non-Authoritative Information',
				204 => 'No Content',
				205 => 'Reset Content',
				206 => 'Partial Content',
				300 => 'Multiple Choices',
				301 => 'Moved Permanently',
				302 => 'Found',
				303 => 'See Other',
				304 => 'Not Modified',
				305 => 'Use Proxy',
				307 => 'Temporary Redirect',
				400 => 'Bad Request',
				401 => 'Unauthorized',
				402 => 'Payment Required',
				403 => 'Forbidden',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				406 => 'Not Acceptable',
				407 => 'Proxy Authentication Required',
				408 => 'Request Time-out',
				409 => 'Conflict',
				410 => 'Gone',
				411 => 'Length Required',
				412 => 'Precondition Failed',
				413 => 'Request Entity Too Large',
				414 => 'Request-URI Too Large',
				415 => 'Unsupported Media Type',
				416 => 'Requested range not satisfiable',
				417 => 'Expectation Failed',
				500 => 'Internal Server Error',
				501 => 'Not Implemented',
				502 => 'Bad Gateway',
				503 => 'Service Unavailable',
				504 => 'Gateway Time-out'
			);
			if (is_string($status)) {
				$codes = array_combine(array_values($codes), array_keys($codes));
			}

			if (isset($codes[$status])) {
				$code = $msg = $codes[$status];
				if (is_numeric($status)) {
					$code = $status;
				}
				if (is_string($status)) {
					$msg = $status;
				}
				$status = "HTTP/1.1 {$code} {$msg}";
			} else {
				$status = null;
			}
		}

		if (!empty($status)) {
			header($status);
		}
		
		if ($url !== null) {
			header('Location: '.$url);
		}

		if (!empty($status) && ($status >= 300 && $status < 400)) {
			header($status);
		}

		if ($exit) {
			if (function_exists('session_write_close')) {
				session_write_close();
			}
			exit;
		}
	}
	
	public function runCodeReturnOutput($path, $start_path_in_view_folder=true){
//		$ID = RuntimeInfo::instance()->id();
		
		ob_start();
		
		extract($this->getDataObject()->toArray());
		
		if($start_path_in_view_folder){
			$base_path = dirname(__FILE__).'/../../Applications/'.$this->getRuntimeInfo()->getApplicationName().'/Views/';
		} else {
			$base_path = '';
		}
		
		try{
			$file = File::osPath($base_path.$path);
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
	

	private $_forms = array(
		FormValidator::METHOD_POST=>array(),
		FormValidator::METHOD_GET=>array()	
	);
	
	// for overwriting whats in a form
	public function setForm($form_name, $method=FormValidator::METHOD_POST){
		$this->_forms[$method][$form_name] = $FormValidator = new FormValidator($form_name, $method);
		return $FormValidator;
	}
	
	public function getForm($form_name, $method=FormValidator::METHOD_POST){
		$FormValidator = isset($this->_forms[$method][$form_name])?$this->_forms[$method][$form_name]:array();
		if($FormValidator instanceof FormValidator){
			return $FormValidator;
		} else {
			$this->_forms[$method][$form_name] = $FormValidator = new FormValidator($form_name, $method);
			return $FormValidator;
		}
	}	
	
}

