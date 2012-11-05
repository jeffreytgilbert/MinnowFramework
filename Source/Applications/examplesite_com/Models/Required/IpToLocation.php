<?php

final class IpToLocation extends DataObject
{	
	public function __construct($data=array(), $default_filter='Parse::decode'){
		$this->setAllowedData(array('ip','city','region','country','country_code','postal_code'));
		parent::__construct();
	}
	
	public function setDataFromYahoo(){
		$ip = guess_ip();
		$location_info = file_get_contents('http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20ip.location%20where%20ip%3D\''
										   . $ip .'\'&format=xml&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys');
		$p = xml_parser_create();
		xml_parse_into_struct($p, $location_info, $vals, $index);
		xml_parser_free($p);
	//	print_r($location_info);
	
		$this->set('ip',$ip);
		
		foreach($vals as $tag){
			if($tag['tag'] == 'CITY'){ $this->set('city',$tag['value']); }
			if($tag['tag'] == 'REGIONNAME'){ $this->set('region',$tag['value']); }
			if($tag['tag'] == 'COUNTRYCODE'){ $this->set('country_code',$tag['value']); }
			if($tag['tag'] == 'COUNTRY'){ $this->set('country',$tag['value']); }
			if($tag['tag'] == 'POSTALCODE'){ $this->set('postal_code',$tag['value']); }
		}
	}
	
	public static function getCountries()
	{
		$db = RuntimeInfo::instance()->mysql();
		$query='SELECT abbreviation, country_name FROM country ORDER BY country_name DESC';
		$db->query($query,__LINE__,__FILE__);
		while($db->readRow()) { $countries[]=new DataObject($db->row_data); }
		return $countries;
	}
	
	public static function getZipcodeInfo($code, $country='US')
	{
		$db = RuntimeInfo::instance()->mysql();
		switch(strtolower($country))
		{
			case 'us':
				$query	='SELECT zip_code, city, city_type, state, state_code, latitude, longitude '
						.'FROM postal_code_us '
						.'WHERE zip_code LIKE "'.Filter::zipcode($code, 'us').'"';
				$db->query($query,__LINE__,__FILE__); $db->readRow();
				if(!empty($db->row_data['zip_code'])) { return $db->row_data; }
				else { return array(); }
			break;
			case 'ca':
				$query	='SELECT postal_code AS zip_code, city, city_type, province AS state, province_code AS state_code, '
						.'latitude, longitude '
						.'FROM postal_code_ca '
						.'WHERE postal_code LIKE "'.Filter::zipcode($code, 'ca').'"';
				$db->query($query,__LINE__,__FILE__); $db->readRow();
				if(!empty($db->row_data['zip_code'])) { return $db->row_data; }
				else { return array(); }
			break;
			default:
				return array();
			break;
		}
	}

	public static function getCountry($abbreviation)
	{
		$db = RuntimeInfo::instance()->mysql();
		$query = 'SELECT abbreviation, country_name FROM country '
				.'WHERE abbreviation="'.Filter::abbreviation($abbreviation).'"';
		$db->query($query,__LINE__,__FILE__); $db->readRow();

		if(!empty($db->row_data['abbreviation'])) { return $db->row_data; }
		else { return false; }
	}
	
	public static function getIp2LocationShort($ip)
	{
		$db = RuntimeInfo::instance()->mysql();
		// Can't use ip2long because it requires big integers... 
		// and php couldnt handle it. probably a bug in the lang... maybe they'll fix it one day
		// $long = ip2long($this->ip);
		if($ip=='Unknown') { $long=0; }
		else
		{
			$ips = explode('\.',$ip);
			if(sizeof($ips)<4) {$long=1111111111; }
			else { $long=($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256); }
		}
// old and busted
//		$query = 'SELECT latitude, longitude FROM ip2location_disk '
//				.'WHERE ip_first <= '.$long.' AND ip_last >= '.$long;
		$query = 'SELECT latitude, longitude FROM ip2location '
				.'WHERE ip_first <= '.$long.' ORDER BY ip_first DESC LIMIT 1';
		$db->query($query,__LINE__,__FILE__); $db->readRow();
		
		if(isset($db->row_data['latitude']))
		{
			$data['ip'] = $ip;
			$data['latitude'] = doubleval($db->row_data['latitude']);
			$data['longitude'] = doubleval($db->row_data['longitude']);
		}
		else //this was added because core was throwing errors for ip: 72.146.122.14, maybe it was just a slow query?
		{
			$data['ip'] = $ip;
			$data['latitude'] = '32.7961';
			$data['longitude'] = '-96.8024';
		}

		return $data;
	}
	
	public static function getIp2LocationDetailed($ip=null)
	{
		$db = RuntimeInfo::instance()->mysql();
		// Can't use ip2long because it requires big integers... 
		// and php couldnt handle it. probably a bug in the lang... maybe they'll fix it one day
		// $long = ip2long($this->ip);
		if(empty($ip) || $ip=='Unknown')	{ $_SERVER['REMOTE_ADDR']; } // fix later, not spoof resistant

		$ips = split ('\.',$ip);
		
		if(sizeof($ips)<4)	{ $long=1111111111; }
		else				{ $long=($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256); }

// old and busted
//		$query = 'SELECT country_abbreviation, country_name, region, city, isp, latitude, longitude FROM ip2location_disk ' // changed from memory /// might produce errors
//				.'WHERE ip_first <= '.$long.' AND ip_last >= '.$long;
		$query = 'SELECT country_abbreviation, country_name, region, city, isp, latitude, longitude FROM ip2location '
				.'WHERE ip_first <= '.$long.' ORDER BY ip_first DESC LIMIT 1';

		$db->query($query,__LINE__,__FILE__); $db->readRow();
		
		if(isset($db->row_data['latitude']))
		{
			$data['ip'] = $ip;
			$data['country_abbreviation'] = ucwords($db->row_data['country_abbreviation']);
			$data['country_name'] = ucwords(strtolower($db->row_data['country_name']));
			$data['region'] = ucwords(strtolower($db->row_data['region']));
			$data['city'] = ucwords(strtolower($db->row_data['city']));
			$data['isp'] = ucwords(strtolower($db->row_data['isp']));
			$data['latitude'] = doubleval($db->row_data['latitude']);
			$data['longitude'] = doubleval($db->row_data['longitude']);
		}
		else //this was added because core was throwing errors for ip: 72.146.122.14, maybe it was just a slow query?
		{
			$data['ip'] = $ip;
			$data['country_abbreviation'] = 'USA';
			$data['country_name'] = 'United States';
			$data['region'] = 'Texas';
			$data['city'] = 'Dallas';
			$data['isp'] = 'Unknown';
			$data['latitude'] = '32.7961';
			$data['longitude'] = '-96.8024';
		}
		
		return $data;
	}
}