<?php

class File
{
	// os agnostic file exists method
	public static function exists($file_path){
		return (bool)file_exists(self::osPath($file_path));
	}
	
	public static function osPath($file_path){
		return str_replace('/',SLASH,$file_path);
	}
	
	
	public static function foldersInFolderToArray($path, $include_hidden_files=false){ // also works for .svn folders and such on linux/mac
		$file_array = array();

		// if there's a trailing slash, lose it
		$trailing_slash = substr($path,strlen($path)-1);
		if($trailing_slash == SLASH){ $path = substr($path, 0, strlen($path)-1);; }
		unset($trailing_slash);

		if(is_dir($path) && $handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && is_dir($path.SLASH.$file)) {
					if($include_hidden_files){
						$file_array[$path.SLASH.$file] = $file;
					} else {
						if(substr($file,0,1) != '.'){
							$file_array[$path.SLASH.$file] = $file;
						}
					}
				}
			}
			closedir($handle);
		}
		return $file_array;
	}

	public static function filesInFolderToArray($path, $include_hidden_files=false){
		$file_array = array();

		// if there's a trailing slash, lose it
		$trailing_slash = substr($path,strlen($path)-1);
		if($trailing_slash == SLASH){ $path = substr($path, 0, strlen($path)-1);; }
		unset($trailing_slash);

		if(is_dir($path) && $handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && !is_dir($path.SLASH.$file)) {
					if($include_hidden_files){
						$file_array[$path.SLASH.$file] = $file;
					} else {
						if(substr($file,0,1) != '.' && strtolower($file) !== 'thumb.db'){
							$file_array[$path.SLASH.$file] = $file;
						}
					}
				}
			}
			closedir($handle);
		}
		return $file_array;
	}

	public static function folderToArray($path, $include_hidden_files=false){
		$file_array = array();

		// if there's a trailing slash, lose it
		$trailing_slash = substr($path,strlen($path)-1);
		if($trailing_slash == SLASH){ $path = substr($path, 0, strlen($path)-1);; }
		unset($trailing_slash);

		if(is_dir($path) && $handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if($include_hidden_files){
						$file_array[$path.SLASH.$file] = $file;
					} else {
						if(substr($file,0,1) != '.' && strtolower($file) !== 'thumb.db'){
							$file_array[$path.SLASH.$file] = $file;
						}
					}
				}
			}
			closedir($handle);
		}
		return $file_array;
	}

	public static function folderWithSubfoldersToArrays($path, $include_hidden_files=false){
		$file_array = array();

		// if there's a trailing slash, lose it
		$trailing_slash = substr($path,strlen($path)-1);
		if($trailing_slash == SLASH){ $path = substr($path, 0, strlen($path)-1);; }
		unset($trailing_slash);

		if(is_dir($path) && $handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if(is_dir($path.SLASH.$file)){
						if($include_hidden_files){
							$file_array[$path.SLASH.$file] = self::folderWithSubfoldersToArrays($path.SLASH.$file, $include_hidden_files);
						} else {
							if(substr($file,0,1) != '.' && strtolower($file) !== 'thumb.db'){
								$file_array[$path.SLASH.$file] = self::folderWithSubfoldersToArrays($path.SLASH.$file, $include_hidden_files);
							}
						}
					} else {
						if($include_hidden_files){
							$file_array[$path.SLASH.$file] = $file;
						} else {
							if(substr($file,0,1) != '.' && strtolower($file) !== 'thumb.db'){
								$file_array[$path.SLASH.$file] = $file;
							}
						}
					}
				}
			}
			closedir($handle);
		}
		return $file_array;
	}
}
