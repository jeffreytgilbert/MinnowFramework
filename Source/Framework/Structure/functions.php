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

// output something from the db to the webpage. if you don't want it to break the page, use htmlentities to make it sanitized.
function _o($string){
	return htmlentities($string);
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

function e($string,$encryption_type='sha256'){ // encrypt zee stringz
	return hash($encryption_type,$string);
}

function he($str) {
	return htmlentities($str);
}

function pr($var, $return=false, $encode=false) {
	$pre = '<pre style="text-align:left;">'.($encode ? he(print_r($var, 1)) : print_r($var, 1)).'</pre>';
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

// because fake ips from proxies are bogus. // read up here: http://roshanbh.com.np/2007/12/getting-real-ip-address-in-php.html
function guess_ip(){
	//check ip from share internet
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) { $ip=$_SERVER['HTTP_CLIENT_IP']; }
	//to check ip is pass from proxy
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $ip=$_SERVER['HTTP_X_FORWARDED_FOR']; }
	// otherwise, you're probably an average joe or jane
	else { $ip=$_SERVER['REMOTE_ADDR']; }
	
	if($ip == '::1') {return '108.100.193.129';}
	return $ip;
}

function get_load_average(){
	$load_average = exec('/usr/bin/uptime');
	$load_average = explode('load average: ',$load_average);
	$load_average = explode(',',$load_average[1]);
	$load_average = trim($load_average[0]);
	return $load_average;
}

function get_url_params($params) {
	$post_params = array();
	foreach ($params as $key => &$val) {
		if (is_array($val)){
			$val = implode(',', $val);
		}
		$post_params[] = $key.'='.urlencode($val);
	}
	return implode('&', $post_params);
}

// this only works for ipv4. ip2long breaks on large ints so this is a workaround to phps bugginess
function ip_to_long($ip){
	$ips = explode('.',$ip); // why is this period escaped?
	if(count($ips)<4) {$long=0; } // was 1111111111. dunno why
	else { $long=($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256); }
	return $long;
}

/**
 * Turn a user name into a users folder by their user id.
 * @param int $user_id
 * @return string
 */
//function user_path($user_id) { return floor((int)$user_id/10000).'/'.$user_id; }

function build_options($options, $selected_val=null){
	$html_options = '';
	foreach($options as $val => $label){
		$html_options .= '<option value="'.$val.'" '. ($selected_val==$val?'selected="selected"':'') .'>'.$label.'</option>';
	}
	return $html_options;
}

function array_of_years($start_year, $less_years_from_today=0, $reversed=true){ 
	$a = array();
	if($reversed){
		for($i = date('Y')-$less_years_from_today;$i >= $start_year;$i--){ $a[$i]=$i; }
	} else {
		for($i = $start_year;$i <= date('Y')-$less_years_from_today;$i++){ $a[$i]=$i; }
	}
	return $a;
}


function random_code(){
	return sha1(uniqid(rand(), true));
}
