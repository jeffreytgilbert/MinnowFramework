<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// What if each of the plugins loaded is a trait that can be used, then things could just access them through the action class dependant on the trait needed.
// i like this idea, but each trait needs to have some way to be identified (installed as it were) or would it, since all I'd need to do is autoload them and wait for their use in actions
// but then the connectors could all be required to have config readers and common methods.

// Each connector should be allowed to have a trait or an interface it can inherit from where certain methods and properties are defined that help identify it and allow it to read its own config

// @todo make outputs on DataObject/Models trait savvy so they can output to common html / parser / filter functions

class Helpers{
	
	use ConfigReader;
	
	private $_helpers = array();
	
	// make sure connections are freed at the end of a process execution
	public function __destruct(){
		foreach($this->_helpers as $HelperType){
			foreach ($HelperType as $Helper){
				if($Helper instanceof Helper){ $Helper->__destruct(); }
			}
		}
	}
	
	// example usage: 
	// RuntimeInfo->Helpers->Video('default')->prepare($query);
	
	// 	this is how connector/drivers should be installed. This code can be reused identically while just changing the instance name of each connection type
	
	public function Authentication(){
		if(isset($this->_helpers['Authentication'])
				&& $this->_helpers['Authentication'] instanceof AuthenticationHelper){
			return $this->_helpers['Authentication']->getInstance();
		}
		Run::fromHelpers('Authentication/AuthenticationHelper.php');
		$this->_helpers['Authentication'] = $AuthenticationHelper = new AuthenticationHelper($this->config('Helpers/Authentication/'));
		return $AuthenticationHelper->getInstance();
	}
	
	public function HybridAuth(){
		if(isset($this->_helpers['HybridAuth'])
				&& $this->_helpers['HybridAuth'] instanceof HybridAuthHelper){
			return $this->_helpers['HybridAuth']->getInstance();
		}
		Run::fromHelpers('HybridAuth/HybridAuthHelper.php');
		$this->_helpers['HybridAuth'] = $HybridAuthHelper = new HybridAuthHelper($this->config('Helpers/HybridAuth/'));
		return $HybridAuthHelper->getInstance();
	}
	
	public function Image(){
		if(isset($this->_helpers['Image'])
				&& $this->_helpers['Image'] instanceof ImageHelper){
			return $this->_helpers['Image']->getInstance();
		}
		Run::fromHelpers('Image/ImageHelper.php');
		$this->_helpers['Image'] = $ImageHelper = new ImageHelper($this->config('Helpers/Image/'));
		return $ImageHelper->getInstance();
	}
	
	public function Video(){
		if(isset($this->_helpers['Video'])
				&& $this->_helpers['Video'] instanceof VideoHelper){
			return $this->_helpers['Video']->getInstance();
		}
		Run::fromHelpers('Video/VideoHelper.php');
		$this->_helpers['Video'] = $VideoHelper = new VideoHelper($this->config('Helpers/Video/'));
		return $VideoHelper->getInstance();
	}


	public function Session(){
		if(isset($this->_helpers['Session'])
				&& $this->_helpers['Session'] instanceof SessionHelper){
			return $this->_helpers['Session']->getInstance();
		}
		Run::fromHelpers('Session/SessionHelper.php');
		$this->_helpers['Session'] = $SessionHelper = new SessionHelper($this->config('Helpers/Session/'));
		return $SessionHelper->getInstance();
	}
	
}


