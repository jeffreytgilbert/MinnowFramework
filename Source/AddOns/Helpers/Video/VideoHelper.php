<?php

// this one needs to have the sessions config stored in the config (which should extend model) and then call its settings from within if using db as the config

final class VideoHelper extends Helper{

	public function __construct(Model $Config){
		// run parent construct action to save settings
		parent::__construct($Config);

		Run::fromHelpers('Video/Requirements/FFmpegAutoloader.php');
		Run::fromHelpers('Video/Requirements/VideoAbstraction.php');
		
		$this->_instance = new VideoAbstraction(
				$this->_Config->get('debug')
		);
	}
	
	public function getInstance(){ 
		if($this->_instance instanceof VideoAbstraction) return $this->_instance;
		return new VideoAbstraction();
	}
	
	public function __destruct(){
		// unset($this);
	}
}

