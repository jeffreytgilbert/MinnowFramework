<?php

class MinnowRequest{
	
	private 
		$_controller_path = '',
		$_controller_name = 'Index',
		$_controller_format = 'html',
		$_component_controller_path = '',
		$_component_controller_name = '',
		$_requested_url = '',
		$_is_valid_url_format = false;
	
	public function __construct($request_url=''){
		$app_path = Path::toApplication();
		$controller_path = Path::toControllers();
		
		$this->_requested_url = $request_url;
		
		if(empty($this->_requested_url)){
			
			// Default page request
			$this->_controller_name = 'Index';
			$this->_controller_format = 'html';
			
		} else {
			$this->_requested_url;
			
			// condition the url to the expected format
			// ... remove trailing slashes and extraneous characters
			$this->_requested_url = rtrim($this->_requested_url,'\/');
			
// 			// ... remove duplicate slashes <-- seems this is already done by mod-rewrite
// 			$requested_url = preg_replace('#//+#', '/', $requested_url);
			
			$path_segments = explode('/-/',$this->_requested_url, 3);
			$total_segments = count($path_segments);

			if($total_segments == 0){ // index page request by default
				
				$this->_controller_name = 'Index';
				$this->_controller_format = 'html';
				
			} else { // page request
				
				// if at the webroot, and loading a component, the total segments will be 1, but the logic below wont work. so handle this exception seperately 
				if(substr($this->_requested_url,0,2) == '-/'){
					
					$this->_controller_name = 'Index';
					$this->_controller_format = 'html';
					
					$requested_component_url = substr($this->_requested_url,2,mb_strlen($this->_requested_url));
					$last_slash_position = mb_strripos($requested_component_url,'/');
					if($last_slash_position === false){
						$this->_component_controller_name = $requested_component_url;
					} else {
						$this->_component_controller_path = substr($requested_component_url, 0, $last_slash_position);
						$this->_component_controller_name = substr($requested_component_url, $last_slash_position+1);
						
					}
					
				// handle all other occasions 
				} else {
				
					$last_slash_position = strripos($path_segments[0],'/');
					if($last_slash_position === false){
						$this->_controller_name = $path_segments[0];
					} else {
						$this->_controller_path = substr($path_segments[0], 0, $last_slash_position);
						$this->_controller_name = substr($path_segments[0], $last_slash_position+1);
					}
					
					if(strripos($this->_controller_name,'.') === false){
	//					$f['controller_name'] = $path_segments[0];
					} else {
						$controller_parts = explode('.',$this->_controller_name);
						$this->_controller_name = $controller_parts[0];
						$this->_controller_format = array_pop($controller_parts);
					}
					
					if($total_segments > 1){ // page has components
						unset($last_slash_position);
						$last_slash_position = strripos($path_segments[1],'/');
						if($last_slash_position === false){
							$this->_component_controller_name = $path_segments[1];
						} else {
							$this->_component_controller_path = substr($path_segments[1], 0, $last_slash_position);
							$this->_component_controller_name = substr($path_segments[1], $last_slash_position+1);
						}
						
					}
				}
			}
		}
		
		// Add trailing slash if needed
		if(!empty($this->_controller_path) && substr($this->_controller_path,-1) != '/'){ $this->_controller_path .= '/'; }
		
		// Sanitize request
		$controller_name = preg_replace('/([^a-zA-Z0-9])/s','',$this->_controller_name);
		$controller_path = preg_replace('/([^a-zA-Z0-9\/])/s','',$this->_controller_path);
		$controller_format = preg_replace('/([^a-zA-Z0-9])/s','',$this->_controller_format);
		$component_controller_name = preg_replace('/([^a-zA-Z0-9])/s','',$this->_component_controller_name);
		$component_controller_path = preg_replace('/([^a-zA-Z0-9\/])/s','',$this->_component_controller_path);
		
		if(
			$controller_name == $this->_controller_name &&
			$controller_path == $this->_controller_path &&
			$controller_format == $this->_controller_format &&
			$component_controller_name == $this->_component_controller_name &&
			$component_controller_path == $this->_component_controller_path
		){
			$this->_is_valid_url_format = true;
		}
		
		$this->_controller_name = $controller_name;
		$this->_controller_path = $controller_path;
		$this->_controller_format = $controller_format;
		$this->_component_controller_name = $component_controller_name;
		$this->_component_controller_path = $component_controller_path;
		
	}
	
	public function isValidURLFormat(){ return (bool)$this->_is_valid_url_format; }
	
// 	public function setControllerPath($controller_path){ $this->_controller_path = $controller_path; }
// 	public function setControllerName($controller_name){ $this->_controller_name = $controller_name; }
// 	public function setControllerFormat($controller_format){ $this->_controller_format = $controller_format; }
// 	public function setComponentControllerPath($component_controller_path){ $this->_component_controller_path = $component_controller_path; }
// 	public function setComponentControllerName($component_controller_name){ $this->_component_controller_name = $component_controller_name; }

	public function getControllerPath(){ return $this->_controller_path; }
	public function getControllerName(){ return $this->_controller_name; }
	public function getControllerFormat(){ return $this->_controller_format; }
	public function getComponentControllerPath(){ return $this->_component_controller_path; }
	public function getComponentControllerName(){ return $this->_component_controller_name; }
	public function getRequestUrl(){ return $this->_requested_url; }
	
	public function getPathInfoAsArray(){
		return array(
			'controller_name'=>$this->_controller_name,
			'controller_path'=>$this->_controller_path,
			'controller_format'=>$this->_controller_format,
			'component_controller_name'=>$this->_component_controller_name,
			'component_controller_path'=>$this->_component_controller_path,
			'requested_url'=>$this->_requested_url
		);
	}
	
	public function isRequestingComponent(){
		return empty($this->_component_controller_name)?false:true;
	}
	
	public function hasJSFile(){
		// Can't resolve a component name from a path, so there's no way to check for a component js file. Just check for Controller JS
		return File::exists(Path::toJS().'Pages/'.$this->_controller_path.$this->_controller_name.'.js');
	}
	
	public function hasCSSFile(){
		return File::exists(Path::toCSS().'Pages/'.$this->_controller_path.$this->_controller_name.'.css');
	}
	
	public function hasFolder(){
		return File::exists(Path::toControllers().'Pages/'.$this->_controller_path);
	}
	
	public function hasControllerFile(){
		return File::exists(Path::toControllers().'Pages/'.$this->_controller_path.$this->_controller_name.'Page.php');
	}
	
	public function getPathToControllerFile($include_path_to_controllers=true){
		if($include_path_to_controllers){
			return File::osPath(Path::toControllers().'Pages/'.$this->_controller_path.$this->_controller_name.'Page.php');
		} else {
			return File::osPath('Pages/'.$this->_controller_path.$this->_controller_name.'Page.php');
		}
	}
	
	public function getPathToJSFile($include_path_to_js=true){
		if($include_path_to_js){
			return File::osPath(Path::toJS().'Pages/'.$this->_controller_path.$this->_controller_name.'.js');
		} else {
			return File::osPath('Pages/'.$this->_controller_path.$this->_controller_name.'.js');
		}
	}
	
	public function getPathToCSSFile($include_path_to_css=true){
		if($include_path_to_css){
			return File::osPath(Path::toCSS().'Pages/'.$this->_controller_path.$this->_controller_name.'.css');
		} else {
			return File::osPath('Pages/'.$this->_controller_path.$this->_controller_name.'.css');
		}
	}
	
}