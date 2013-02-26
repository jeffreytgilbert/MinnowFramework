<?php

final class Path{

	public static function toTemporaryFolder(){
		$folder = RuntimeInfo::instance()->config('',null,'temporary_files_folder');
		if($folder != ''){
			if(!in(substr($folder,0,1), array('/','\\'))){
				$folder = self::toRoot().File::osPath($folder);
			}
		} else {
			$folder = self::toRoot().File::osPath('Temp/');
		}
		return $folder;
	}

	public static function toRoot(){
		static $root_path = '';
		
		if(empty($root_path)){
			$root_path = File::osPath(
					substr(
							dirname(__FILE__),
							0,
							- mb_strlen('Source/Framework/Core')
					)
			);
			
		}
		return $root_path;
	}
	
	public static function toWebRoot(){
		static $web_root = null;
		if(is_null($web_root)){
			$web_root = File::osPath(RuntimeInfo::instance()->getWebRoot());
		}
		return $web_root;
	}
	
	public static function toApplication(){
		return self::toRoot().File::osPath('Source/Application/');
	}
	
	public static function toFramework(){
		return self::toRoot().File::osPath('Source/Framework/');
	}

	public static function toComponents(){
		return self::toApplication().File::osPath('Controllers/Components/');
	}
	
	public static function toControllers(){
		return self::toApplication().File::osPath('Controllers/');
	}

	public static function toModels(){
		return self::toApplication().File::osPath('Models/');
	}

	public static function toViews(){
		return self::toApplication().File::osPath('Views/');
	}

	public static function toAddOns(){
		return self::toRoot().File::osPath('Source/AddOns/');
	}

	public static function toConnections(){
		return self::toRoot().File::osPath('Source/AddOns/Connections/');
	}

	public static function toHelpers(){
		return self::toRoot().File::osPath('Source/AddOns/Helpers/');
	}

	public static function toFormValidators(){
		return self::toRoot().File::osPath('Source/AddOns/FormValidators/');
	}

	public static function toActions(){
		return self::toApplication().File::osPath('Actions/');
	}

	public static function toJS(){
		return self::toRoot().File::osPath('www/js/');
	}
	
	public static function toCSS(){
		return self::toRoot().File::osPath('www/css/');
	}
	
}
