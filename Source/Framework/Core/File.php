<?php

class File
{
	// os agnostic file exists method
	public static function exists($file_path){
		return (bool)is_readable(self::osPath($file_path));
	}
	
	public static function osPath($file_path){
		return str_replace('/',SLASH,$file_path);
	}
	
	
	public static function foldersInFolderToArray($path, $include_hidden_files=false){ // also works for .svn folders and such on linux/mac
		$file_array = array();

		// if there's a trailing slash, lose it
		$trailing_slash = mb_substr($path, mb_strlen($path)-1);
		if($trailing_slash == SLASH){ $path = mb_substr($path, 0, mb_strlen($path)-1);; }
		unset($trailing_slash);

		if(is_dir($path) && $handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && is_dir($path.SLASH.$file)) {
					if($include_hidden_files){
						$file_array[$path.SLASH.$file] = $file;
					} else {
						if(mb_substr($file,0,1) != '.'){
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
		$trailing_slash = mb_substr($path, mb_strlen($path)-1);
		if($trailing_slash == SLASH){ $path = mb_substr($path, 0, mb_strlen($path)-1);; }
		unset($trailing_slash);

		if(is_dir($path) && $handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && !is_dir($path.SLASH.$file)) {
					if($include_hidden_files){
						$file_array[$path.SLASH.$file] = $file;
					} else {
						if(mb_substr($file,0,1) != '.' && strtolower($file) !== 'thumb.db'){
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
		$trailing_slash = mb_substr($path, mb_strlen($path)-1);
		if($trailing_slash == SLASH){ $path = mb_substr($path, 0, mb_strlen($path)-1);; }
		unset($trailing_slash);

		if(is_dir($path) && $handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if($include_hidden_files){
						$file_array[$path.SLASH.$file] = $file;
					} else {
						if(mb_substr($file,0,1) != '.' && strtolower($file) !== 'thumb.db'){
							$file_array[$path.SLASH.$file] = $file;
						}
					}
				}
			}
			closedir($handle);
		}
		return $file_array;
	}

	public static function folderWithSubfoldersToArrays($path, $include_hidden_files=false, $return_as_nested_array=true){
		$file_array = array();

		// if there's a trailing slash, lose it
		$trailing_slash = mb_substr($path, mb_strlen($path)-1);
		if($trailing_slash == SLASH){ $path = mb_substr($path, 0, mb_strlen($path)-1);; }
		unset($trailing_slash);

		if(is_dir($path) && $handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if(is_dir($path.SLASH.$file)){
						if($include_hidden_files){
							if($return_as_nested_array){
								$file_array[$path.SLASH.$file] = self::folderWithSubfoldersToArrays($path.SLASH.$file, $include_hidden_files, $return_as_nested_array);
							} else {
								$file_array = array_merge($file_array, self::folderWithSubfoldersToArrays($path.SLASH.$file, $include_hidden_files, $return_as_nested_array));
							}
						} else {
							if(mb_substr($file,0,1) != '.' && strtolower($file) !== 'thumb.db'){
								if($return_as_nested_array){
									$file_array[$path.SLASH.$file] = self::folderWithSubfoldersToArrays($path.SLASH.$file, $include_hidden_files, $return_as_nested_array);
								} else {
									$file_array = array_merge($file_array, self::folderWithSubfoldersToArrays($path.SLASH.$file, $include_hidden_files, $return_as_nested_array));
								}
							}
						}
					} else {
						if($include_hidden_files){
							$file_array[$path.SLASH.$file] = $file;
						} else {
							if(mb_substr($file,0,1) != '.' && strtolower($file) !== 'thumb.db'){
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
	
	// Get file paths from a folder, recursively, minus the relative path 
	public static function relativePathsFromFilesInAppIncludingSubFoldersAsArray($relative_path, $path, $include_hidden_files=false, $characters_to_cut_from_tail_of_path=0){
		$relative_path_length = mb_strlen($relative_path);
		
		$file_array = array();

		// if there's a trailing slash, lose it
		$trailing_slash = mb_substr($path, mb_strlen($path)-1);
		if($trailing_slash == SLASH){ $path = mb_substr($path, 0, mb_strlen($path)-1);; }
		unset($trailing_slash);

		if(is_dir($path) && $handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					$full_path = $path.SLASH.$file;
					$character_limit = (mb_strlen($full_path) - $relative_path_length) - $characters_to_cut_from_tail_of_path;
					$partial_path = substr($full_path, $relative_path_length, $character_limit);
					
					if(is_dir($path.SLASH.$file)){
						if($include_hidden_files){
							$file_array = array_merge($file_array, self::relativePathsFromFilesInAppIncludingSubFoldersAsArray($relative_path, $full_path, $include_hidden_files, $characters_to_cut_from_tail_of_path));
						} else {
							if(mb_substr($file,0,1) != '.' && strtolower($file) !== 'thumb.db'){
								$file_array = array_merge($file_array, self::relativePathsFromFilesInAppIncludingSubFoldersAsArray($relative_path, $full_path, $include_hidden_files, $characters_to_cut_from_tail_of_path));
							}
						}
					} else {
						if($include_hidden_files){
							$file_array[$partial_path] = $file;
						} else {
							if(mb_substr($file,0,1) != '.' && strtolower($file) !== 'thumb.db'){
								$file_array[$partial_path] = $file;
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
