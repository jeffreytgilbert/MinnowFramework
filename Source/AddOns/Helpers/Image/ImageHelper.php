<?php

// this one needs to have the sessions config stored in the config (which should extend model) and then call its settings from within if using db as the config

final class ImageHelper extends Helper{

	public function __construct(Model $Config){
		// run parent construct action to save settings
		parent::__construct($Config);
		
		require_once('phar://'.Path::toHelpers().'Image/Requirements/imagine-v0.3.0.phar');
		Run::fromHelpers('Image/Requirements/ImageAbstraction.php');
		
		$memory_used = format_human_readable_to_bytes(ini_get('memory_limit'));
		if($memory_used < 1024){ // nothing runs under a meg... have a fallback plan
			$memory_used = memory_get_peak_usage(true);
		}
		
		$memory_limit = format_human_readable_to_bytes($this->getConfig()->get('image_editing_memory_limit'));
		
		// add existing memory limit to image limit to get the new limit
		$memory_limit += $memory_used;
		//pr($memory_limit);
		
		ini_set('memory_limit', $memory_limit);
		
		$this->_instance = new ImageAbstraction(
			$this->getConfig()->get('default_format'),
			$this->getConfig()->get('interface'),
			$this->getConfig()->get('debug')
		);
	}

	public function getInstance(){
		if($this->_instance instanceof ImageAbstraction) return $this->_instance;
		return new ImageAbstraction();
	}

	public function __destruct(){
		// unset($this);
	}
}
