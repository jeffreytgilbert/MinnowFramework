<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class PagingConfig{
	private $_start;
	private $_limit;
	private $_isQueryUpdatingOk;
	
	public function __construct($start, $limit, $do_add_paging_to_query=true){
		$this->_start = $start;
		$this->_limit = $limit;
		$this->_isQueryUpdatingOk = $do_add_paging_to_query;
	}
	
	public function getStart(){ return (int)$this->_start; }
	public function getLimit(){ return (int)$this->_limit; }
	public function isQueryUpdatingOk(){ return (bool)$this->_isQueryUpdatingOk; }
}
