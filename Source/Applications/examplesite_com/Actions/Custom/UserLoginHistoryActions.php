<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserLoginHistoryActions extends Actions{
	
	public static function insertUserLoginHistory(UserLoginHistory $UserLoginHistory){
		return parent::MySQLCreateAction('
			INSERT INTO user_login_history (
				created_datetime,
				login,
				user_agent,
				ip,
				proxy,
				description,
				isp,
				success
			) VALUES (
				:created_datetime,
				:login,
				:user_agent,
				:ip,
				:proxy,
				:description,
				:isp,
				:success
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RIGHT_NOW_GMT,
				':login' => $UserLoginHistory->getString('login'),
				':user_agent' => $UserLoginHistory->getString('user_agent'),
				':ip' => $UserLoginHistory->getString('ip'),
				':proxy' => $UserLoginHistory->getString('proxy'),
				':description' => $UserLoginHistory->getString('description'),
				':isp' => $UserLoginHistory->getString('isp'),
				':success' => $UserLoginHistory->getInteger('success'),
				':user_id' => $UserLoginHistory->getInteger('user_id')
			),
			// which fields are integers
			array(
				':success',
				':user_id'
			)
		);
	}
	
	public static function selectByUserLoginHistoryId($user_id){
		// Return one object by primary key selection
		return new UserLoginHistory(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime,
				login,
				user_agent,
				ip,
				proxy,
				description,
				isp,
				success
			FROM user_login_history 
			WHERE user_id=:user_id',
			// bind data to sql variables
			array(
				':user_id' => (int)$user_id
			),
			// which fields are integers
			array(
				':user_id'
			),
			// return as this object collection type
			'UserLoginHistory'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$UserLoginHistoryCollection = new UserLoginHistoryCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime,
				login,
				user_agent,
				ip,
				proxy,
				description,
				isp,
				success
			FROM user_login_history 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UserLoginHistory'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UserLoginHistoryCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UserLoginHistoryCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UserLoginHistory', new UserLoginHistory());
			if($DataObject->getInteger('user_id') > 0){
				$user_ids[] = $DataObject->getInteger('user_id');
			}
		}
		
		$user_ids = array_unique($user_ids);
		
		$UserLoginHistoryCollection = new UserLoginHistoryCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime,
				login,
				user_agent,
				ip,
				proxy,
				description,
				isp,
				success
			FROM user_login_history 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UserLoginHistory'
		));
		
		foreach($UserLoginHistoryCollection->toArray() as $UserLoginHistory){
			$array = $DataCollection->getItemsBy('user_id',$UserLoginHistory->getInteger('user_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserLoginHistory',$UserLoginHistory);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserLoginHistory(UserLoginHistory $UserLoginHistory){
		return parent::MySQLUpdateAction('
			UPDATE user_login_history 
			SET modified_datetime=:modified_datetime,
				login=:login,
				user_agent=:user_agent,
				ip=:ip,
				proxy=:proxy,
				description=:description,
				isp=:isp,
				success=:success
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RIGHT_NOW_GMT,
				':login' => $UserLoginHistory->getString('login'),
				':user_agent' => $UserLoginHistory->getString('user_agent'),
				':ip' => $UserLoginHistory->getString('ip'),
				':proxy' => $UserLoginHistory->getString('proxy'),
				':description' => $UserLoginHistory->getString('description'),
				':isp' => $UserLoginHistory->getString('isp'),
				':success' => $UserLoginHistory->getInteger('success'),
				':user_id' => $UserLoginHistory->getInteger('user_id')
			),
			// which fields are integers
			array(
				':success',
				':user_id'
			)
		);
	}
	
	public static function deleteUserLoginHistoryById($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_login_history 
			WHERE user_id=:user_id',
			// bind data to sql variables
			array(
				':user_id' => (int)$user_id
			),
			// which fields are integers
			array(
				':user_id'
			)
		);
	}

}