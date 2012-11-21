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

/*
 * Functions that make it easier to calculate sizes from ini's in human readable form
 */

function format_bytes_to_human_readable($a_bytes)
{
    if ($a_bytes < 1024) {
        return $a_bytes .' B';
    } elseif ($a_bytes < 1048576) {
        return round($a_bytes / 1024, 2) .' KiB';
    } elseif ($a_bytes < 1073741824) {
        return round($a_bytes / 1048576, 2) . ' MiB';
    } elseif ($a_bytes < 1099511627776) {
        return round($a_bytes / 1073741824, 2) . ' GiB';
    } elseif ($a_bytes < 1125899906842624) {
        return round($a_bytes / 1099511627776, 2) .' TiB';
    } elseif ($a_bytes < 1152921504606846976) {
        return round($a_bytes / 1125899906842624, 2) .' PiB';
    } elseif ($a_bytes < 1180591620717411303424) {
        return round($a_bytes / 1152921504606846976, 2) .' EiB';
    } elseif ($a_bytes < 1208925819614629174706176) {
        return round($a_bytes / 1180591620717411303424, 2) .' ZiB';
    } else {
        return round($a_bytes / 1208925819614629174706176, 2) .' YiB';
    }
}

function format_human_readable_to_bytes($str) { 
    $bytes = 0; 

    $bytes_array = array( 
        'B' => 1, 
        'K' => 1024, 
        'M' => 1024 * 1024, 
        'G' => 1024 * 1024 * 1024, 
        'T' => 1024 * 1024 * 1024 * 1024, 
        'P' => 1024 * 1024 * 1024 * 1024 * 1024, 
    ); 

    $bytes = floatval($str);
    
//     pr($str);
// 	preg_match('#([KMGTP]?)B?$#si', $str, $matches);
//     pr($matches);
//     pr($bytes_array[$matches[1]]);
    if(preg_match('#([KMGTP]?)B?$#si', $str, $matches) 
    && !empty($bytes_array[$matches[1]])) { 
        $bytes *= $bytes_array[$matches[1]];
//        pr($bytes);
    } 

    $bytes = intval(round($bytes, 2)); 

    return $bytes; 
} 
