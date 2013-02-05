<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class IndexPage extends PageController implements HTMLCapable, JSONCapable, XMLCapable{
	protected function loadIncludedFiles(){
// 		$this->loadModels('AccountStatus');
	}
	
	public function handleRequest(){
		// business logic here
		$DataObject = new AccountStatus(array(
			'account_status_id'=>1,
			'status_type'=>'This is the status type'
		));
		$this->log()->debug('This is a sample debug message');
		$this->log()->info('This is a sample info message WITH DATA!',$DataObject);
		$this->log()->notice('This is a sample notice message');
		$this->log()->warning('This is a sample warning message');
		$this->log()->error('This is a sample error message');
		$this->log()->critical('This is a sample critical message');
		$this->log()->alert('This is a sample alert message');
		$this->log()->emergency('This is a sample emergency message');
		
	}
	
	public function renderJSON(){ return $this->output = parent::renderJSON(); }
	public function renderXML(){ return $this->output = parent::renderXML(); }
	public function renderHTML(){ return $this->_page_body = parent::renderHTML(); }
	
}
