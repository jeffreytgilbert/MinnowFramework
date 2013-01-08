<?php

// Essentially an array of settings with getters / setters for storing a reference to all the ini files collected from the application

class SettingsRegistry{
	
// 	private static $_config_path = null;
// 	protected function setConfigPath($path){ $this->_config_path = File::osPath($path); }
// 	protected function getConfigPath(){ return $this->_config_path; }
	
	// combo getter/setter. Set it once and call it anytime. Update it, and get the new path. get syntax is dont supply the path. will only be set on startup
	public static function configPath($config_path=null){
		static $base_path = '';
		if(!is_null($config_path)){ $base_path = File::osPath($config_path); }
		return $base_path;
	}
	
	public static function settings($ini_path, $settings=null){
		static $all_settings = array();
		if(!is_null($settings)){ return isset($all_settings[$ini_path])?$all_settings[$ini_path]:null; }
		else { $all_settings[$ini_path] = $settings; return $settings; }
	}
	
	public static function get($ini_path, $example_ini_path){
		// im strugling with how to reconcile the config path vs the full example path and how annoying it is to not have a 
		// way to make them hardwired into the same structure, but then i also need to have the flexibility to handle helpers 
		// like sessions and html versions and image gd preferences and things vs connections where there will be multiple 
		// groupings for things like master and slave connections to a database and then just simple site settings. 
		// I think the only clear way to do this is to have the settings reader part be a generic path and have the individual 
		// applications provide non-generic paths for individual files, which is how its setup now. 
		
		$ini_path = self::configPath().$ini_path;
		
		if(self::settings($ini_path)){
			return self::settings($ini_path);
		} else {
			if(File::exists($ini_path)){
				$settings = parse_ini_file($ini_path, true);
				if (!$settings) {
					throw new Exception(self::getErrorMessage($ini_path,$example_ini_path));
				} else {
					self::settings($ini_path, $settings);
					return $settings;
				}
			} else {
				throw new Exception(self::getErrorMessage($ini_path,$example_ini_path));
			}
		}
	}
	
	private static function getErrorMessage($ini_path, $example_ini_path){
		if(File::exists($example_ini_path)){
			$example_settings = file_get_contents($example_ini_path);
			if($example_settings != ''){
				return '<h1>Settings file missing.</h1>'."\n"
						.'<strong>The file should be located at this location:</strong> '.$ini_path."\n<br>"
						.'An example of this configuration file looks like the following: '."\n<br>\n<br>"
						.'<pre style="border:1px solid; padding:10px; margin:10px;">'.nl2br($example_settings).'</pre>';
			} else {
				return 'Unable to open configuration file: '.$ini_path."\n<br>"
					.'Check file permissions and make sure parent directories exist.';
			}
		} else {
			return 'Unable to open '.$ini_path.'. Also unable to open configuration file: '.$example_ini_path."\n<br>"
					.'Check file permissions and make sure parent directories exist.';
		}
	}
}
