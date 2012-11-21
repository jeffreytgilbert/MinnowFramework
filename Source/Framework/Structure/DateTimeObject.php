<?php

/**
 */
class DateTimeObject extends DateTime {
	public function getMySQLFormat($data_type='datetime'){
		switch($data_type){
			case 'datetime':
				return $this->format('Y-m-d G:i:s');
				break;
			case 'date':
				return $this->format('Y-m-d');
				break;
			case 'time':
				return $this->format('G:i:s');
				break;
		}
	}

	public function getOracleFormat($data_type='datetime'){
		switch($data_type){
			case 'datetime':
				return $this->format('M j, y @ h:i a');
				break;
			case 'date':
				return $this->format('M j, y');
				break;
			case 'time':
				return $this->format('h:i a');
				break;
		}
	}
	
	public function getJavascriptFormat(){
		return $this->getTimestamp()*1000;
	}
}
