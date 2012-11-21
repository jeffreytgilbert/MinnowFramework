<?php 

abstract class Component{
	
	private $_Controller;
	
	public function __construct(Controller $Controller){
		$this->_Controller = $Controller;
	}
}