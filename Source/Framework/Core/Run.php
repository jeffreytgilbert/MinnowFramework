<?php

final class Run{
	public static function fromRoot($source_path){
		$native_path = Path::toRoot().File::osPath($source_path);
		if(!file_exists($native_path)){ die('Failed to load file. File doesn\'t exist. '.$native_path); }
		require_once( $native_path );
		//		echo $native_path."<br>\n";
	}

	public static function formSource($source_path){
		self::fromRoot('Source/'.$source_path);
	}

	public static function fromConnections($source_path){
		self::fromRoot('Source/AddOns/Connections/'.$source_path);
	}

	public static function fromHelpers($source_path){
		self::fromRoot('Source/AddOns/Helpers/'.$source_path);
	}

	public static function fromWidgets($source_path){
		self::fromRoot('Source/AddOns/Widgets/'.$source_path);
	}

	public static function fromFormats($source_path){
		self::fromRoot('Source/AddOns/Formats/'.$source_path);
	}

	public static function fromApp($source_path, $app_name=null){
		if(is_null($app_name)){
			$RuntimeInfo = RuntimeInfo::instance();
			self::fromRoot('Source/Applications/'.$RuntimeInfo->getApplicationName().'/'.$source_path);
		} else {
			self::fromRoot('Source/Applications/'.$app_name.'/'.$source_path);
		}
	}

	public static function fromActions($source_path, $app_name=null){
		if(is_null($app_name)){
			$RuntimeInfo = RuntimeInfo::instance();
			self::fromRoot('Source/Applications/'.$RuntimeInfo->getApplicationName().'/Actions/'.$source_path);
		} else {
			self::fromRoot('Source/Applications/'.$app_name.'/Actions/'.$source_path);
		}
	}

	public static function fromComponents($source_path, $app_name=null){
		if(is_null($app_name)){
			$RuntimeInfo = RuntimeInfo::instance();
			self::fromRoot('Source/Applications/'.$RuntimeInfo->getApplicationName().'/Controllers/Components/'.$source_path);
		} else {
			self::fromRoot('Source/Applications/'.$app_name.'/Controllers/Components/'.$source_path);
		}
	}

	public static function fromControllers($source_path, $app_name=null){
		if(is_null($app_name)){
			$RuntimeInfo = RuntimeInfo::instance();
			self::fromRoot('Source/Applications/'.$RuntimeInfo->getApplicationName().'/Controllers/'.$source_path);
		} else {
			self::fromRoot('Source/Applications/'.$app_name.'/Controllers/'.$source_path);
		}
	}

	public static function fromModels($source_path, $app_name=null){
		if(is_null($app_name)){
			$RuntimeInfo = RuntimeInfo::instance();
			self::fromRoot('Source/Applications/'.$RuntimeInfo->getApplicationName().'/Models/'.$source_path);
		} else {
			self::fromRoot('Source/Applications/'.$app_name.'/Models/'.$source_path);
		}
	}
	
	public static function fromViews($source_path, $app_name=null){
		if(is_null($app_name)){
			$RuntimeInfo = RuntimeInfo::instance();
			self::fromRoot('Source/Applications/'.$RuntimeInfo->getApplicationName().'/Actions/'.$source_path);
		} else {
			self::fromRoot('Source/Applications/'.$app_name.'/Actions/'.$source_path);
		}
	}

	public static function fromFramework($source_path){
		self::fromRoot('Source/Framework/'.$source_path);
	}
}
