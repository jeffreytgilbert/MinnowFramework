<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

/**
 * This Library returns boolean values based on conditions passed to it.
 * @package Sanitize
 */
class Is
{
	/**
	* Returns true if the string is of proper string length and format. 
	* This method is limited to internal class implimentation to promote use of 
	* internal well named methods.
	* @param string $str
	* @param int $min
	* @param int $max
	* @param string $regex
	* @return bool
	*/
	private static function valid($str, $min, $max, $regex)
	{
		if(!preg_match('/'.$regex.'/si',$str)) { return false; }
		// Check for the optional length specifiers
		$str_len=strlen($str);
		if(($min != 0 && $str_len < $min) || ($max != 0 && $str_len > $max)) { return false; }
		// Passed all tests
		else { return true; }
	}
	
	/**
	* Returns true if the string is a valid email address // function syntax lifted from http://us3.php.net/manual/en/function.preg-match.php#68291
	* @param string $email
	* @param bool $check_mx
	* @return bool
	*/
	public static function email($email, $a_check_mx=null)
	{
		if($a_check_mx) { unset($a_check_mx); }
		$email_parts = explode('@', $email, 3);
		return count($email_parts) == 2
			&& strlen($email_parts[0]) < 65
			&& strlen($email_parts[1]) < 256
			&& preg_match('/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|mobi|me|uk|[a-z]{2,4}))$)\\z/i', $email);
	}
	
	/**
	* Returns true if the string is a valid MySQL date of format (YYYY-MM-DD).
	* @param string $date
	* @return bool
	*/
	public static function date($date)
	{
		$matches=array();
		if(preg_match('/(\d{4})\-(\d{2})\-(\d{2})/', $date, $matches))
		{
			if((int) $matches[2] > 0 && (int) $matches[2] < 13)
			{
				if((int) $matches[3] > 0 && (int) $matches[3] < 32)
				{
					if(checkdate($matches[2],$matches[3],$matches[1])){
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/**
	* Returns true if the string is of a valid MySQL time format (HH:MM:SS)
	* @param string $time
	* @return bool
	*/
	public static function time($time)
	{
		$matches=array();
		if(preg_match('/(\d{2}):(\d{2}):(\d{2})/', $time, $matches))
		{
			if((int) $matches[1] > 0 && (int) $matches[1] < 25)
			{
				if((int) $matches[2] > 0 && (int) $matches[2] < 61)
				{
					if((int) $matches[3] > 0 && (int) $matches[3] < 61)
					{
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/**
	* Returns true if the string is a MySQL formatted datetime of format (YYYY-MM-DD HH:MM:SS).
	* @param string $dateTime
	* @return bool
	*/
	public static function datetime($datetime)
	{
		$matches=array();
		if(preg_match('/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/', $datetime, $matches))
		{
			if(checkdate($matches[2],$matches[3],$matches[1])){ return true; }
			
			return (int) $matches[1] >= 1000 && (int) $matches[2] > 0 &&
				  (int) $matches[2] < 13 && (int) $matches[3] > 0 &&
				  (int) $matches[3] < 32 && (int) $matches[4] > 0 &&
				  (int) $matches[4] < 25 && (int) $matches[5] > 0 &&
				  (int) $matches[5] < 61 && (int) $matches[6] > 0 &&
				  (int) $matches[6] < 61;
		}
		else { return false; }
	}
	
	/**
	* Returns true if the string is a year of proper 4 digit format (YYYY)
	* @param string $year
	* @return bool
	*/
	public static function year($year)
	{
		if(preg_match('/\d{4}/', $year)) { return (int) $year >= 1000; }
		else { return false; }
	}
	
	/**
	* Returns true if the given text is is formed of word characters and fits in the required length
	* @param string $str
	* @param int $min
	* @param int $max
	* @return bool
	*/
	public static function word($str, $a_min=null, $a_max=null)
	{
		$SetReg = RuntimeInfo::instance()->settings();
		if(isset($a_min)) { $min = $a_min; }
		else { $min = $SetReg->getCurrent('min'); }
		if(isset($a_max)) { $max = $a_max; }
		else { $max = $SetReg->getCurrent('max'); }
		
		return Is::valid($str, $min, $max, '[A-Za-z0-9_]+');
	}
	
	/**
	* Returns true if the string is of a valid length
	* @param string $str
	* @param int $min
	* @param int $max
	* @return bool
	*/
	public static function length($str, $a_min=null, $a_max=null)
	{
		$SetReg = RuntimeInfo::instance()->settings();
		if(isset($a_min)) { $min = $a_min; }
		else { $min = $SetReg->getCurrent('min'); }
		if(isset($a_max)) { $max = $a_max; }
		else { $max = $SetReg->getCurrent('max'); }
	
		// Check if the string is empty
		$str=trim($str);
		if(empty($str)) { return false; }
		// Check for the optional length specifiers
		$strLen=strlen($str);
		if(($min != 0 && $strLen < $min) || 
			($max != 0 && $strLen > $max)) 
		{
			return false;
		}
		// Passed all tests
		return true;
	}
	
	/**
	* Returns true if the input is letters only
	* @param string $str
	* @param int $min
	* @param int $max
	* @return bool
	*/
	public static function alpha($str, $a_min=null, $a_max=null)
	{
		$SetReg = RuntimeInfo::instance()->settings();
		if(isset($a_min)) { $min = $a_min; }
		else { $min = $SetReg->getCurrent('min'); }
		if(isset($a_max)) { $max = $a_max; }
		else { $max = $SetReg->getCurrent('max'); }
		
		return Is::valid($str, $min, $max, '[[:alpha:]]+');
	}
	
	/**
	* Returns true if the given text is formed of numeric characters
	* @param string $str
	* @param int $min
	* @param int $max
	* @return bool
	*/
	public static function number($str, $a_min=null, $a_max=null)
	{
		$SetReg = RuntimeInfo::instance()->settings();
		if(isset($a_min)) { $min = $a_min; }
		else { $min = $SetReg->getCurrent('min'); }
		if(isset($a_max)) { $max = $a_max; }
		else { $max = $SetReg->getCurrent('max'); }
	
		return Is::valid($str, $min, $max, '[[:digit:]]+');
	}
	
	/**
	 * Returns true if the username entered is between 1 and 25 valid characters long
	 * @param string $str
	 * @return bool
	 */
	public static function name($str, $minimum_characters=2, $maximum_characters=30) 
	{
		// Definitely fail if you see these coming
		if(preg_match('/[^a-zA-Z_0-9\- ]+/si',$str)) { return false; }
		// otherwise make sure the string is long enough and has all the chars it needs
		return (bool)(Is::valid($str, $minimum_characters, $maximum_characters, '[a-zA-Z_0-9\-\[\] ]+'));
	}
	
	/**
	 * Returns true if the age of the user is between 18 and 60
	 * @param int $str
	 * @return bool
	 */
	public static function age($str,$a_min=null, $a_max=null)
	{
		return Is::valid($str, $a_min, $a_min, '[[:digit:]]+');
	}
	
	/**
	 * Returns true or false dependant on the contents of the variable
	 * @param unknown $var
	 * @return bool
	 */
	public static function set($var=null, $trimmed=true) { 
		$var = $trimmed?trim($var):$var;
		return (isset($var) && !empty($var)); 
	}
}
