<?php

abstract class ThemedPageRequest extends TemplatedPageRequest{
	
	private $_Theme;
	public function getTheme(){ return $this->_Theme; }
	
	protected function setThemeByStoreId($store_id){
		$this->loadModels('Theme');
		$this->loadActions('ThemeActions');
		
		$this->_Theme = ThemeActions::selectByStoreId($store_id);
	}
	
	public function prepareTemplate(){
		
		$link_path = '/';
		$required_css[]='style';
		$required_css[]='structure';
		$required_css[]='wufoo';
		
		$required_js[]='plugins';
		$required_js[]='script';
		
		$this->_extra_js = array_merge($required_js,$this->_extra_js);
		$this->_extra_css = array_merge($required_css,$this->_extra_css);
	}
	
	public function renderTemplatedPage(){
		if($this->_Theme instanceof Theme){
			$this->addCss(substr($this->_Theme->get('css_file_path'),5,-4));
		}
		
		header('Content-Type: text/html; charset=UTF-8');
		
		$this->_output = $this->runCodeReturnOutput('themes/desktop.php');
		
	}
}
