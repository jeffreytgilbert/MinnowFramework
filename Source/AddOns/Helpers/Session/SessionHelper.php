<?php

// this one needs to have the sessions config stored in the config (which should extend model) and then call its settings from within if using db as the config

final class SessionHelper extends Helper{

	public function __construct(Model $Config){
		// run parent construct action to save settings
		parent::__construct($Config);
		
		Run::fromHelpers('Session/Requirements/SessionConfig.php');
		Run::fromHelpers('Session/Requirements/SessionAbstraction.php');
		
		try{
			$timeout = $this->getConfig()->get('timeout');
				
			if( session_id() != '' ){ throw new Exception('Can\'t use sessions through framework. Sessions were started previously without initialing framework sessions first.'); }
			
			$this->_instance = $Sessions = new SessionAbstraction(new SessionConfig(
				$this->getConfig()->get('storage_method'),
				$this->getConfig()->get('hosts'),
				Is::set($timeout)?$timeout:null
			));
			
			if($this->_instance->getSessionConfig()->isUsingMySQL()){
				Run::fromHelpers('Session/Requirements/SessionMySQLActions.php');
				
				// Declare the session handling functions for php for db handling of session ids (faster, more accurate, more secure)
				session_set_save_handler(
					'SessionMySQLActions::open',
					'SessionMySQLActions::close',
					'SessionMySQLActions::read',
					'SessionMySQLActions::write',
					'SessionMySQLActions::destroy',
					'SessionMySQLActions::clean'
				);
			} else if($this->_instance->getSessionConfig()->isUsingMemcached()) {
				ini_set('session.save_handler', 'memcache');
				ini_set('session.save_path', $this->_instance->getSessionConfig()->getHosts());
			} else { // must be using default file system setup
				ini_set('session.save_handler', 'files');
			}
			
			// set GC timeout
			ini_set('session.gc_maxlifetime',$timeout);
				
		} catch(Exception $e){
			if($this->_Config->get('debug')){ pr($e); }
			die;
		}
		
		session_start();
	}
	
	public function getInstance(){ 
		if($this->_instance instanceof SessionAbstraction) return $this->_instance;
		return new SessionAbstraction();
	}
	
	public function __destruct(){
		// unset($this);
	}
}

