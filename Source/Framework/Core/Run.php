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
	
	public static function fromFormValidators($source_path){
		self::fromRoot('Source/AddOns/FormValidators/'.$source_path);
	}
	
	public static function fromWidgets($source_path){
		self::fromRoot('Source/AddOns/Widgets/'.$source_path);
	}

	public static function fromFormats($source_path){
		self::fromRoot('Source/AddOns/Formats/'.$source_path);
	}

	public static function fromApp($source_path){
		self::fromRoot('Source/Application/'.$source_path);
	}

	public static function fromActions($source_path){
		self::fromRoot('Source/Application/Actions/'.$source_path);
	}

	public static function fromComponents($source_path){
		self::fromRoot('Source/Application/Controllers/Components/'.$source_path);
	}

	public static function fromControllers($source_path){
		self::fromRoot('Source/Application/Controllers/'.$source_path);
	}

	public static function fromModels($source_path){
		self::fromRoot('Source/Application/Models/'.$source_path);
	}
	
	public static function fromViews($source_path){
		self::fromRoot('Source/Application/Actions/'.$source_path);
	}

	public static function fromFramework($source_path){
		self::fromRoot('Source/Framework/'.$source_path);
	}
}
