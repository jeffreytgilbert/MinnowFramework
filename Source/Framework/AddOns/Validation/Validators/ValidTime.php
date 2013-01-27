<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ValidTime extends ValidationRule{
	
	const INVALID_TIME_FORMAT = 'INVALID_TIME_FORMAT';
	
	// @todo Write a functional time validation class
// 	public function __construct($data){
// 		parent::__construct($data);
// 		if( !preg_match("/([0-9]):([0-5][0-9]):([0-5][0-9])$/", $data) ){
// 			throw new Exception(self::INVALID_TIME_FORMAT);
// 		}
// 	}
		
        #05/12/2109
        #05-12-0009
        #05.12.9909
        #05.12.99
//         return preg_match('/^((0?[1-9]|1[012])[- /.](0?[1-9]|[12][0-9]|3[01])[- /.][0-9]?[0-9]?[0-9]{2})*$/', $date);
        #2009/12/11
        #2009-12-11
        #2009.12.11
        #09.12.11
//         return preg_match('#^([0-9]?[0-9]?[0-9]{2}[- /.](0?[1-9]|1[012])[- /.](0?[1-9]|[12][0-9]|3[01]))*$#'', $date);
	
	// min
	// max
	// 24 hr clock
	// 12 hr clock
}
