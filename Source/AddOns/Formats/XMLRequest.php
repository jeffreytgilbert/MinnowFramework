<?php

abstract class XMLRequest extends Controller{
	public function __construct(){
		parent::__construct();
		$this->loadIncludedFiles();
		$this->handleRequest();
	}
	
	abstract public function renderXML();
}