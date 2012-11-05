<?php

/**
 */
class DateTimeObject extends DateTime {
	public function getMySQLFormat(){
		return $this->format('Y-m-d G:i:s');
	}

	public function getOracleFormat(){
		return $this->format('M j, y @ h:i a');
	}
	
	public function getJavascriptFormat(){
		return $this->getTimestamp()*1000;
	}
}

?>