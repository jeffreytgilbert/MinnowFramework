<?php

class LocationServices
{
	private $_debug;
	
	public function __construct($debug){
		$this->_debug = $debug;
	}
	
	// this only works for ipv4. ip2long breaks on large ints so this is a workaround to phps bugginess
	public static function ipToLong($ip){
		$ips = explode('.',$ip); // why is this period escaped?
		if(count($ips)<4) {$long=0; } // was 1111111111. dunno why
		else { $long=($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256); }
		return $long;
	}
	
	// this logic could be changed. i dont know that its the perfect setup for grabbing the ip, but it tunnels through proxies (sorta). 
	// I think HTTP_X_FORWARDED_FOR sometimes returns a comma separated array and this thing expects a string
	public static function guessIP(){
		// because fake ips from proxies are bogus. // read up here: http://roshanbh.com.np/2007/12/getting-real-ip-address-in-php.html
		//check ip from share internet
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) { $ip=$_SERVER['HTTP_CLIENT_IP']; }
		//to check ip is pass from proxy
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $ip=$_SERVER['HTTP_X_FORWARDED_FOR']; }
		// otherwise, you're probably an average joe or jane
		else { $ip=$_SERVER['REMOTE_ADDR']; }
		
		if($ip == '::1') { return '127.0.0.1'; } // either way this is wrong.
		
		return $ip;
	}
	
	public static function guessProxyIP(){
		if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { return $_SERVER['HTTP_X_FORWARDED_FOR']; }
		return false;
	}
	
	public static function getLocationFromServices($ip){
		
		// this could likely use a try catch or throw block in here for failed requests 

		$Xml = new SimpleXMLElement(
			"http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20pidgets.geoip%20where%20ip%3D'{$ip}'&diagnostics=true&env=http%3A%2F%2Fdatatables.org%2Falltables.env", 
			null, true);
		
		$LocationFromIp = new LocationFromIp();
		$LocationFromIp->set('ip',$ip);
		
		if(
			$Xml instanceof SimpleXMLElement &&
			$Xml->results instanceof SimpleXMLElement &&
			$Xml->results->Result instanceof SimpleXMLElement
		){
			$Location = $Xml->results->Result;
			if($Location instanceof SimpleXMLElement){
				$LocationFromIp->set('city_name',$Location->city->__toString());
				$LocationFromIp->set('region_name',$Location->region->__toString());
				$LocationFromIp->set('country_code',$Location->country_code->__toString());
				$LocationFromIp->set('country_name',$Location->country_name->__toString());
				$LocationFromIp->set('postal_code',$Location->postal_code->__toString());
				$LocationFromIp->set('latitude',$Location->latitude->__toString());
				$LocationFromIp->set('longitude',$Location->longitude->__toString());
				$LocationFromIp->set('dma_code',$Location->dma_code->__toString());
				$LocationFromIp->set('area_code',$Location->area_code->__toString());
			}
		}
		
		$Xml = new SimpleXMLElement(
			"http://www.earthtools.org/timezone/{$LocationFromIp->get('latitude')}/{$LocationFromIp->get('longitude')}", 
			null, true);
		
		if($Xml instanceof SimpleXMLElement){
			$Location = $Xml;
			$LocationFromIp->set('gmt_offset',$Location->offset->__toString());
			$LocationFromIp->set('localtime',new DateTimeObject($Location->localtime->__toString()));
			$LocationFromIp->set('isotime',new DateTimeObject($Location->isotime->__toString()));
			$LocationFromIp->set('utctime',new DateTimeObject($Location->utctime->__toString()));
			$LocationFromIp->set('dst_offset',$Location->dst->__toString());
		}
		
		return $LocationFromIp;
	}
	
}