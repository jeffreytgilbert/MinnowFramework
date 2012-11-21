<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserAccountActions extends Actions{
	
	public static function insertUserAccount(UserAccount $UserAccount){
		return parent::MySQLCreateAction('
			INSERT INTO user_account (
				created_datetime,
				first_name,
				middle_name,
				last_name,
				alternative_name,
				account_status_id,
				thumbnail_id,
				avatar_path,
				last_online,
				latitude,
				longitude,
				gmt_offset,
				is_email_validated,
				is_online,
				is_closed,
				pass_code,
				unread_messages
			) VALUES (
				:created_datetime,
				:first_name,
				:middle_name,
				:last_name,
				:alternative_name,
				:account_status_id,
				:thumbnail_id,
				:avatar_path,
				:last_online,
				:latitude,
				:longitude,
				:gmt_offset,
				:is_email_validated,
				:is_online,
				:is_closed,
				:pass_code,
				:unread_messages
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RIGHT_NOW_GMT,
				':first_name' => $UserAccount->getString('first_name'),
				':middle_name' => $UserAccount->getString('middle_name'),
				':last_name' => $UserAccount->getString('last_name'),
				':alternative_name' => $UserAccount->getString('alternative_name'),
				':account_status_id' => $UserAccount->getInteger('account_status_id'),
				':thumbnail_id' => $UserAccount->getInteger('thumbnail_id'),
				':avatar_path' => $UserAccount->getString('avatar_path'),
				':last_online' => $UserAccount->getDateTimeObject('last_online')->getMySQLFormat('datetime'),
				':latitude' => $UserAccount->getInteger('latitude'),
				':longitude' => $UserAccount->getInteger('longitude'),
				':gmt_offset' => $UserAccount->getInteger('gmt_offset'),
				':is_email_validated' => $UserAccount->getBoolean('is_email_validated'),
				':is_online' => $UserAccount->getBoolean('is_online'),
				':is_closed' => $UserAccount->getBoolean('is_closed'),
				':pass_code' => $UserAccount->getString('pass_code'),
				':unread_messages' => $UserAccount->getInteger('unread_messages'),
				':user_id' => $UserAccount->getInteger('user_id')
			),
			// which fields are integers
			array(
				':account_status_id',
				':thumbnail_id',
				':latitude',
				':longitude',
				':gmt_offset',
				':unread_messages',
				':user_id'
			)
		);
	}
	
	public static function selectByUserAccountId($user_id){
		// Return one object by primary key selection
		return new UserAccount(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime,
				first_name,
				middle_name,
				last_name,
				alternative_name,
				account_status_id,
				thumbnail_id,
				avatar_path,
				last_online,
				latitude,
				longitude,
				gmt_offset,
				is_email_validated,
				is_online,
				is_closed,
				pass_code,
				unread_messages
			FROM user_account 
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
			'UserAccount'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$UserAccountCollection = new UserAccountCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime,
				first_name,
				middle_name,
				last_name,
				alternative_name,
				account_status_id,
				thumbnail_id,
				avatar_path,
				last_online,
				latitude,
				longitude,
				gmt_offset,
				is_email_validated,
				is_online,
				is_closed,
				pass_code,
				unread_messages
			FROM user_account 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UserAccount'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UserAccountCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UserAccountCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UserAccount', new UserAccount());
			if($DataObject->getInteger('user_id') > 0){
				$user_ids[] = $DataObject->getInteger('user_id');
			}
		}
		
		$user_ids = array_unique($user_ids);
		
		$UserAccountCollection = new UserAccountCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime,
				first_name,
				middle_name,
				last_name,
				alternative_name,
				account_status_id,
				thumbnail_id,
				avatar_path,
				last_online,
				latitude,
				longitude,
				gmt_offset,
				is_email_validated,
				is_online,
				is_closed,
				pass_code,
				unread_messages
			FROM user_account 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UserAccount'
		));
		
		foreach($UserAccountCollection->toArray() as $UserAccount){
			$array = $DataCollection->getItemsBy('user_id',$UserAccount->getInteger('user_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserAccount',$UserAccount);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserAccount(UserAccount $UserAccount){
		return parent::MySQLUpdateAction('
			UPDATE user_account 
			SET modified_datetime=:modified_datetime,
				first_name=:first_name,
				middle_name=:middle_name,
				last_name=:last_name,
				alternative_name=:alternative_name,
				account_status_id=:account_status_id,
				thumbnail_id=:thumbnail_id,
				avatar_path=:avatar_path,
				last_online=:last_online,
				latitude=:latitude,
				longitude=:longitude,
				gmt_offset=:gmt_offset,
				is_email_validated=:is_email_validated,
				is_online=:is_online,
				is_closed=:is_closed,
				pass_code=:pass_code,
				unread_messages=:unread_messages
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RIGHT_NOW_GMT,
				':first_name' => $UserAccount->getString('first_name'),
				':middle_name' => $UserAccount->getString('middle_name'),
				':last_name' => $UserAccount->getString('last_name'),
				':alternative_name' => $UserAccount->getString('alternative_name'),
				':account_status_id' => $UserAccount->getInteger('account_status_id'),
				':thumbnail_id' => $UserAccount->getInteger('thumbnail_id'),
				':avatar_path' => $UserAccount->getString('avatar_path'),
				':last_online' => $UserAccount->getDateTimeObject('last_online')->getMySQLFormat('datetime'),
				':latitude' => $UserAccount->getInteger('latitude'),
				':longitude' => $UserAccount->getInteger('longitude'),
				':gmt_offset' => $UserAccount->getInteger('gmt_offset'),
				':is_email_validated' => $UserAccount->getBoolean('is_email_validated'),
				':is_online' => $UserAccount->getBoolean('is_online'),
				':is_closed' => $UserAccount->getBoolean('is_closed'),
				':pass_code' => $UserAccount->getString('pass_code'),
				':unread_messages' => $UserAccount->getInteger('unread_messages'),
				':user_id' => $UserAccount->getInteger('user_id')
			),
			// which fields are integers
			array(
				':account_status_id',
				':thumbnail_id',
				':latitude',
				':longitude',
				':gmt_offset',
				':unread_messages',
				':user_id'
			)
		);
	}
	
	public static function deleteUserAccountById($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_account 
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