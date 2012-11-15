<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class Helpers{
	
	use ConfigReader;
	
	public static function cast(Helpers $Helpers){ return $Helpers; }
	
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
	// RuntimeInfo->Helpers->Video('default')->load($file)->etc;
	
	// 	this is how connector/drivers should be installed. This code can be reused identically while just changing the instance name of each connection type
	
	public function BrowserDetection(){
		if(isset($this->_helpers['BrowserDetection'])
				&& $this->_helpers['BrowserDetection'] instanceof BrowserDetectionHelper){
			return $this->_helpers['BrowserDetection']->getInstance();
		}
		Run::fromHelpers('BrowserDetection/BrowserDetectionHelper.php');
		$this->_helpers['BrowserDetection'] = $BrowserDetectionHelper = new BrowserDetectionHelper($this->config('Helpers/BrowserDetection/'));
		return $BrowserDetectionHelper->getInstance();
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
	
	public function Location(){
		if(isset($this->_helpers['Location'])
				&& $this->_helpers['Location'] instanceof LocationHelper){
			return $this->_helpers['Location']->getInstance();
		}
		Run::fromHelpers('Location/LocationHelper.php');
		$this->_helpers['Location'] = $LocationHelper = new LocationHelper($this->config('Helpers/Location/'));
		return $LocationHelper->getInstance();
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
	
	public function Video(){
		if(isset($this->_helpers['Video'])
				&& $this->_helpers['Video'] instanceof VideoHelper){
			return $this->_helpers['Video']->getInstance();
		}
		Run::fromHelpers('Video/VideoHelper.php');
		$this->_helpers['Video'] = $VideoHelper = new VideoHelper($this->config('Helpers/Video/'));
		return $VideoHelper->getInstance();
	}

}


