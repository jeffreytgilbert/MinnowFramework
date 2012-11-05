<?php

abstract class TemplatedPageRequest extends PageRequest{
	
	protected $_page_billboard='';
	protected $_page_menu='';
	protected $_page_body='';
	protected $_current_page='';
	protected $_breadcrumbs='';
	
	public function __construct(){
		$this->getParentChildLinks();
		parent::__construct();
	}
	
	public function getParentChildLinks(){
		$ID = RuntimeInfo::instance()->id();
		if($ID->isOnline()){
			$MyID = RuntimeInfo::instance()->idAsMember();
			$this->loadModels(array('ParentChildLink'));
			$this->loadActions(array('ParentChildLinkActions','Account/UserActions'));
			if($MyID->isRole(Role::_PARENT)){
				$MyID->set('ChildCollection',ParentChildLinkActions::selectMyChildren());
				$MyID->set('UnapprovedRequestCollection',ParentChildLinkActions::selectUnapprovedLinksByParentUserId($MyID->get('user_id')));
			} else if($MyID->isRole(Role::_CHILD)) {
				$MyID->set('Parent',ParentChildLinkActions::selectMyParent());
			}
		}
	}
	
	public function setPageMenu($page_menu, $hide_page_menu=false){
		$this->_page_menu = $page_menu;
		if($hide_page_menu == true){
			$this->addCss('hide_page_menu');
		} else {
			$this->addCss('show_page_menu');
		}
	}
	public function getPageMenu(){ return $this->_page_menu; }
	
	public function getPageBody(){ return $this->_page_body; }
	
	public function getUserMenu(){ 
		global $Store;
		$ID = RuntimeInfo::instance()->id();
		
		$this->loadModels(array('Store'));
		
		if($ID->isOnline()){
			$MyID = RuntimeInfo::instance()->idAsMember();
			if($MyID->isRole(Role::_CHILD)){
				$menu = 'child';
			} else if($MyID->isRole('Parent')){
				$menu = 'parent';
			} else if($MyID->isRole('Administrator')){
				$menu = 'admin';
			} else {
				$menu = 'guest';
			}
		} else {
			$menu = 'guest';
		}
		
		if( $menu == 'child' &&
			isset($Store) && 
			$Store instanceof Store){
			$menu_buffer = $this->runCodeReturnOutput('fragments/menus/seller_menu.php');
		} else {
			$menu_buffer = $this->runCodeReturnOutput('fragments/menus/'.$menu.'_menu.php');
		}
		
		return $menu_buffer;
	}
	
	public function getToyMenu(){
		// used on a ton of pages on the site for menus
		$this->loadActions(array('ItemCategoryActions','ItemSubCategoryActions'));
		$this->loadModels(array('ItemCategory','ItemSubCategory'));
		
		$this->_data['ItemCategoryCollection'] = ItemCategoryActions::selectAll();
		return $this->runCodeReturnOutput('fragments/menus/toy_menu.php');
	}
	
	public function getHowToMenu(){
		// used on a ton of pages on the site for menus
		return $this->runCodeReturnOutput('fragments/menus/how_to_menu.php');
	}
	
	public function getAdminMenu(){
		// used on a ton of pages on the site for menus
		return $this->runCodeReturnOutput('fragments/menus/admin_menu.php');
	}
		
	public function prepareTemplate(){
		$link_path = '/';
		$required_css[]='style';
		$required_css[]='structure';
		$required_css[]='wufoo';
		
		$required_js[]='plugins';
		$required_js[]='script';
		$required_js[]='jquery-impromptu.4.0.min';
		
		$this->_extra_js = array_merge($required_js,$this->_extra_js);
		$this->_extra_css = array_merge($required_css,$this->_extra_css);
	}
	
	public function renderTemplatedPage(){
		header('Content-Type: text/html; charset=UTF-8');
		
		$this->_output = $this->runCodeReturnOutput('themes/desktop.php');
	}
}

