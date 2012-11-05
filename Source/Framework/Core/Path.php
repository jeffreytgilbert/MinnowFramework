<?php

final class Path{

	public static function toTemporaryFolder(){
		return self::toRoot().File::osPath('tmp/');
	}

	public static function toRoot(){
		static $root_path = '';
		
		if(empty($root_path)){
			$root_path = File::osPath(
					substr(
							dirname(__FILE__),
							0,
							-strlen('Source/Framework/Core')
					)
			);
			
		}
		return $root_path;
	}

	public static function toApplication($app_name=null){
		if(is_null($app_name)){
			$RuntimeInfo = RuntimeInfo::instance();
			return self::toRoot().File::osPath('Source/Applications/'.$RuntimeInfo->getApplicationName().'/');
		} else {
			return self::toRoot().File::osPath('Source/Applications/'.$app_name.'/');
		}
	}

	public static function toFramework(){
		return self::toRoot().File::osPath('Source/Framework/');
	}

	public static function toControllers($app_name=null){
		return self::toApplication($app_name).File::osPath('Controllers/');
	}

	public static function toModels($app_name=null){
		return self::toApplication($app_name).File::osPath('Models/');
	}

	public static function toViews($app_name=null){
		return self::toApplication($app_name).File::osPath('Views/');
	}

	public static function toAddOns($app_name=null){
		return self::toRoot().File::osPath('Source/AddOns/');
	}

	public static function toConnections($app_name=null){
		return self::toRoot().File::osPath('Source/AddOns/Connections/');
	}

	public static function toHelpers($app_name=null){
		return self::toRoot().File::osPath('Source/AddOns/Helpers/');
	}

	public static function toActions($app_name=null){
		return self::toApplication($app_name).File::osPath('Actions/');
	}

}
