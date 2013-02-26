<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserPasswordResetRequestActions extends Actions{
	
	public static function insertUserPasswordResetRequest($user_id){
		$code = self::createRandomCode();
		
		parent::MySQLCreateAction('
			REPLACE INTO user_password_reset_request (
				user_id,
				created_datetime,
				reset_code
			) VALUES (
				:user_id,
				:created_datetime,
				:reset_code
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':reset_code' => $code,
				':user_id' => $user_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id'
			)
		);
		
		return $code;
	}
	
	public static function selectByResetCode($reset_code){
		
		self::deleteOldResetCodes();
		
		// Return one object by primary key selection
		$UserPasswordResetRequest = new UserPasswordResetRequest(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id,
				reset_code
			FROM user_password_reset_request 
			WHERE reset_code=:reset_code',
			// bind data to sql variables
			array(
				':reset_code' => $reset_code
			)
		));
		
		return $UserPasswordResetRequest;
	}
	
	public static function deleteOldResetCodes(){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_password_reset_request 
			WHERE created_datetime BETWEEN "2000-01-01 00:00:01" AND NOW() - INTERVAL 1 DAY
		');
	}
	
	public static function deleteUserPasswordResetRequestById($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_password_reset_request 
			WHERE user_id=:user_id',
			// bind data to sql variables
			array(
				':user_id' => (int)$user_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id'
			)
		);
	}
	
}