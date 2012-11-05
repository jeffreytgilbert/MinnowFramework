<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

// @todo add chirags idea for ticks in here for monitoring individual tasks in a timeline

class Timer {

	var $startTime;
	var $endTime=null;
	var $accuracy;

	function __construct($start=true, $accuracy=2){
		if($start){
			$this->start();
		}
		$this->accuracy = $accuracy;
	}

	function start(){
		$this->startTime = microtime(true);
		$this->endTime = null;
	}
	
	function stop(){
		$this->endTime = microtime(true);
	}
	
	function getDuration($number_format=true){
		if(!$number_format){
			if($this->endTime!==null){
				return $this->endTime - $this->startTime;
			}else{
				return microtime(true) - $this->startTime;
			}
		}
		if($this->endTime!==null){
			return number_format($this->endTime - $this->startTime, $this->accuracy);
		}
		return number_format(microtime(true) - $this->startTime, $this->accuracy);
	}
	
	function __toString(){
		return $this->toString();
	}
	
	function toString(){
		return number_format($this->getDuration(),$this->accuracy).' seconds';
	}
	
}
