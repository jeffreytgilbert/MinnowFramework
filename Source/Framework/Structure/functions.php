<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */


// read a cookie
function _c($key){
	// i use the prefilled_ prefix here because i want these things to eventually expire
	$c = isset($_COOKIE[$key])?$_COOKIE[$key]:null;
	trim($c);
	return !empty($c)?$c:null;
}

// check if a post is set
function _p($key){
	$p = isset($_POST[$key])?$_POST[$key]:null;
	trim($p);
	return !empty($p)?$p:null;
}

// check if a server var is set
function _s($key){
	$s = isset($_SERVER[$key])?$_SERVER[$key]:null;
	return !empty($s)?$s:null;
}

// check if a get var is set
function _g($key){
	$g = isset($_GET[$key])?$_GET[$key]:null;
	return !empty($g)?$g:null;
}

function in($needle, $haystack, $strict = false){
	if(is_array($haystack) === false || count($haystack) < 1) { return false; }
	return in_array($needle, $haystack, $strict)?true:false;
}

function array_contains($needles, $haystack, $strict = false) { 
	if( is_array($haystack) === false || 
		count($haystack) < 1 ||
		is_array($needles) === false || 
		count($needles) < 1
	) { return false; }
	foreach ($needles as $needle) { 
		if ( !in_array($needle, $haystack, $strict) ) { return false; }
	}
	return true;
}

function pr($var, $return=false, $encode=false) {
	$pre = '<pre style="text-align:left;">'.($encode ? htmlentities(print_r($var, 1)) : print_r($var, 1)).'</pre>';
	if ( $return ) {
		return $pre;
	}
	echo $pre;
}

function lower($str){
	return strtolower($str);
}

function upper($str){
	return strtoupper($str);
}

function pre($var, $return=false) {
	if ( $return ) return pr($var, true);
	pr($var, $return);
}

function vd($obj, $return=false) {
	if($return){
		return '<pre>'.var_dump($obj,1).'</pre>';	
	}else{
		echo '<pre>'; var_dump($obj); echo '</pre>';
	}
}
