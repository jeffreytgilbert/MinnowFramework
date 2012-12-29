<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// set default timezone for date functions/objects to use
date_default_timezone_set('UTC');
// set internal encoding to utf8
mb_internal_encoding('UTF-8');
// set the string function handler to the default C style, or in the future check the availability of utf8 support by multibyte functions
setlocale(LC_CTYPE, 'C'); 
		
function handle_static_content_request(){
	if(isset($_GET['type']) && isset($_GET['path'])){
		
		if( strstr($_GET['path'],'//') || 
			strstr($_GET['path'],'..') ||
			!in_array($_GET['type'], array('css','js','img'))){
			header('HTTP/1.0 404 Not Found');
			exit;
		}
		
		if(substr($_GET['path'],0,1) == '/'){
			$path = substr($_GET['path'],1);
		} else {
			$path = $_GET['path'];
		}
		
		$file_path = dirname(__FILE__).'/../'.$_GET['type'].'/'.$path;
		if(!file_exists($file_path)){ header('HTTP/1.0 404 Not Found'); exit; }
		
		// better to not statically type the mime types because css folder sometimes contains web fonts, etc, by users. hybrid auth definitely does 
		switch($_GET['type']){
 			
			case 'css':
				if(substr($file_path,-4,4) == '.css'){
					header('Content-Type: text/css');
				} else {
					if(function_exists('mime_content_type')){
						header('Content-Type: '.mime_content_type($file_path));
					} else if(class_exists('finfo')) {
						header('Content-Type: '.finfo::file($file_path,FILEINFO_MIME_ENCODING));
					}
				}
 			break;
 			
			case 'js':
				if(substr($file_path,-3,3) == '.js'){
					header('Content-Type: text/javascript');
				} else {
					if(function_exists('mime_content_type')){
						header('Content-Type: '.mime_content_type($file_path));
					} else if(class_exists('finfo')) {
						header('Content-Type: '.finfo::file($file_path,FILEINFO_MIME_ENCODING));
					}
				}
			break;
			
			case 'img':
				header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600000));
				if(function_exists('mime_content_type')){
					header('Content-Type: '.mime_content_type($file_path));
				} else if(class_exists('finfo')) {
					header('Content-Type: '.finfo::file($file_path,FILEINFO_MIME_ENCODING));
				}
			break;
		}
		
		echo file_get_contents($file_path);
	} else {
		header('HTTP/1.0 404 Not Found');
		exit;
	}
}

handle_static_content_request();
