<?php 

abstract class Component{
	
	protected $_Controller;
	
	public function __construct(Controller $Controller){
		$this->_Controller = $Controller;
	}
}