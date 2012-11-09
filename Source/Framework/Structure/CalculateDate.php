<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */


//////////////////////////////////
// BEGIN DATE PROCESSING FUNCTIONS

//$target_date = '2011-09-20 10:00:00';
//$result = CalculateDate::returnFullDatetimeArraySinceNow($target_date);
//
//pr($result);
//
//echo 'Current date: '.date('Y-m-d H:i:s').'<br>';
//
//echo ' It\'s been '.number_format($result['major']['weeks']).' weeks and '.number_format($result['minor']['days']).' days since '.$target_date.'<br>';
//echo ' It\'s been '.number_format($result['major']['days']).' days and '.number_format($result['minor']['hours']).' hours since '.$target_date.'<br>';
//echo ' It\'s been '.number_format($result['major']['hours']).' hours and '.number_format($result['minor']['minutes']).' minutes since '.$target_date.'<br>';
//echo ' It\'s been '.number_format($result['major']['minutes']).' minutes and '.number_format($result['minor']['seconds']).' seconds since '.$target_date.'<br>';
//
//echo CalculateDate::returnSimpleDatetimeStringSinceNow($target_date);
//
//die;

/**
 * Library for date calculations 
 * @package CoreComponents
 */
class CalculateDate
{
	const SECOND = 1;
	const MINUTE = 60;
	const HOUR = 3600;
	const DAY = 86400;
	const WEEK = 604800;
	
	/**
	* Calculates the difference in seconds between two times
	* @param datetime $date_from
	* @param datetime $date_to
	* @param bool $use_timestamps
	* @return int
	*/
	public static function difference($date_from, $date_to=null, $use_timestamps=null)
	{
		if(!$use_timestamps)
		{
			$date_from=strtotime($date_from, 0);
			$date_to=strtotime($date_to, 0);
		}
		$difference=abs($date_to - $date_from); // Difference in seconds
	
		return $difference;
	}
	
	public static function returnSimpleDatetimeArraySinceNow($datetime){
		$full_results = self::returnFullDatetimeArraySinceNow($datetime);
		foreach($full_results['major'] as $key => $result){
			if($result > 0){
				switch($key){
					case 'weeks': return array('major'=>$result,'minor'=>$full_results['minor']['days'],'format'=>'weeks');
					case 'days': return array('major'=>$result,'minor'=>$full_results['minor']['hours'],'format'=>'days');
					case 'hours': return array('major'=>$result,'minor'=>$full_results['minor']['minutes'],'format'=>'hours');
					case 'minutes': return array('major'=>$result,'minor'=>$full_results['minor']['seconds'],'format'=>'minutes');
				}
			}
		}
		return array('major'=>0,'minor'=>$full_results['minor']['seconds'],'format'=>'seconds');
	}
	
	public static function returnSimpleDatetimeStringSinceNow($datetime){
		$full_results = self::returnFullDatetimeArraySinceNow($datetime);
		foreach($full_results['major'] as $key => $result){
			if($result > 0){
				switch($key){
					case 'weeks': 
						return number_format($result).' week'.($result==1?'':'s').
							(
								$full_results['minor']['days']==0?'':' and '.number_format($full_results['minor']['days']).' day'.($full_results['minor']['days']==1?'':'s')
							);
					case 'days': 
						return number_format($result).' day'.($result==1?'':'s').
							(
								$full_results['minor']['hours']==0?'':' and '.number_format($full_results['minor']['hours']).' hour'.($full_results['minor']['days']==1?'':'s')
							);
					case 'hours': 
						return number_format($result).' hour'.($result==1?'':'s').
							(
								$full_results['minor']['minutes']==0?'':' and '.number_format($full_results['minor']['minutes']).' minute'.($full_results['minor']['days']==1?'':'s')
							);
					case 'minutes': 
						return number_format($result).' minute'.($result==1?'':'s').
							(
								$full_results['minor']['seconds']==0?'':' and '.number_format($full_results['minor']['seconds']).' second'.($full_results['minor']['days']==1?'':'s')
							);
				}
			}
		}
		return 'a few seconds';
	}
	
	public static function returnFullDatetimeArraySinceNow($datetime){
		return self::returnFullDatetimeArraySinceDatetime(date('Y-m-d H:i:s'), $datetime);
	}
	
	public static function returnFullDatetimeArraySinceDatetime($start_datetime,$end_datetime){
		$t1 = strtotime($start_datetime);
		$t2 = strtotime($end_datetime);
		if($t1 > $t2){
			$diffrence = $t1 - $t2;
		} else {
			$diffrence = $t2 - $t1;
		}
		 
		$results['major'] = array(); // whole number representing larger number in date time relationship
		$results['minor'] = array(); // time elapsed not yet constituting an iteration in the major number
		
		$results['major']['weeks'] = floor($diffrence/self::WEEK);
		$results['major']['days'] = floor($diffrence/self::DAY);
		$results['major']['hours'] = floor($diffrence/self::HOUR);
		$results['major']['minutes'] = floor($diffrence/self::MINUTE);
		$results['major']['seconds'] = floor($diffrence/self::SECOND);
		
		// Logic:
		// Step 1: Take the major result and transform it into raw seconds (it will be less the number of seconds of the difference)
		// 	ex: $result = ($results['major']['weeks']*WEEK)
		// Step 2: Subtract smaller number (the result) from the difference (total time)
		// 	ex: $minor_result = $difference - $result
		// Step 3: Take the resulting time in seconds and convert it to the minor format
		// 	ex: floor($minor_result/DAY)
		
		$results['minor']['days'] = floor( (($diffrence - ($results['major']['weeks']*self::WEEK)) / self::DAY) );
		$results['minor']['hours'] = floor( (($diffrence - ($results['major']['days']*self::DAY)) / self::HOUR) );
		$results['minor']['minutes'] = floor( (($diffrence - ($results['major']['hours']*self::HOUR)) / self::MINUTE) );
		$results['minor']['seconds'] = floor( (($diffrence - ($results['major']['minutes']*self::MINUTE)) / self::SECOND) );
		
		return $results; 
	}
	
	public static function since($time) {
	    return self::returnSimpleDatetimeStringSinceNow($time);
	}
	
	/**
	* Converts the time entered to the time relative to the users timezone
	* @param datetime $date_time
	* @param int $gmt_ofset
	* @return string
	*/
	public static function offsetConversion($date_time, $gmt_offset=null) 
	{
		return strtotime($date_time)+($gmt_offset*self::HOUR)+(date('I',strtotime($date_time))*self::HOUR);
	}
	
	/**
	* Converts seconds to days
	* @param int MINUTEs
	* @return int
	*/
	public static function secondsToDays($seconds) { return floor($seconds / self::DAY); }
	
	/**
	* Converts seconds to hours
	* @param int $seconds
	* @return int
	*/
	public static function secondsToHours($seconds) { return floor($seconds / self::HOUR); }
	
	/**
	* Converts seconds to minutes
	* @param int $seconds
	* @return int
	*/
	public static function secondsToMinutes($seconds) { return floor($seconds / self::MINUTE); }
	
	/**
	 * Easy method for converting timestamps to mysql datetimes
	 * @param unix_timestamp $timestamp
	 * @return string
	 */
	public static function mysqlDatetime($timestamp) { return date('Y-m-d H:i:s',$timestamp); }
	
	/**
	 * Easy method for converting timestamps to mysql dates
	 * @param unix_timestamp $timestamp
	 * @return string
	 */
	public static function mysqlDate($timestamp) { return date('Y-m-d',$timestamp); }
	
	/**
	 * Easy method for converting timestamps to mysql times
	 * @param unix_timestamp $timestamp
	 * @return string
	 */
	public static function mysqlTime($timestamp) { return date('H:i:s',$timestamp); }
	
	/**
	 * Calculate the age from a bday until now
	 * @param string birthday
	 * @return int age
	 */
	public static function age($birthday) {
		if(preg_match('/^([1-2][0-9]{3})-([0-1][0-9])-([0-3][0-9])$/',$birthday))
		{
			if(strtotime($birthday)>time()) { return false; }
			else
			{
				return intval(floor((time() - strtotime($birthday)) / (365.25 * 24 * 60 * 60)));
			}
		}
		else { return false; }
	}
}

// END DATE PROCESSING FUNCTIONS
////////////////////////////////
