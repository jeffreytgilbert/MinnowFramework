<?php

trait ImageRequest {
	public function __construct(){
		parent::__construct();
		$this->loadIncludedFiles();
		$this->handleRequest();
	}
	
	//abstract public function renderImage();
}

