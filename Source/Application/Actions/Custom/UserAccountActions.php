<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserAccountActions extends Actions{
	
	public static function insertUserAccountFromHybridAuthRegistration(UserAccount $UserAccount){
		
		return parent::MySQLCreateAction('
			INSERT INTO user_account (
				created_datetime,
				first_name,
				last_name,
				last_online,
				latitude,
				longitude,
				gmt_offset,
				is_login_collection_validated,
				is_online
			) VALUES (
				:created_datetime,
				:first_name,
				:last_name,
				:last_online,
				:latitude,
				:longitude,
				:gmt_offset,
				:is_login_collection_validated,
				:is_online
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':first_name' => $UserAccount->getString('first_name'),
				':last_name' => $UserAccount->getString('last_name'),
				':last_online' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':latitude' => $UserAccount->getNumber('latitude'),
				':longitude' => $UserAccount->getNumber('longitude'),
				':gmt_offset' => $UserAccount->getNumber('gmt_offset'),
				':is_login_collection_validated' => true,
				':is_online' => true
			),
			// which fields are integers
			array(
				':latitude',
				':longitude',
				':gmt_offset'
			)
		);
		
	}
	
	public static function insertUserAccountFromForm(UserAccount $UserAccount){
		
		return parent::MySQLCreateAction('
			INSERT INTO user_account (
				created_datetime,
				first_name,
				last_name,
				last_online,
				latitude,
				longitude,
				gmt_offset,
				is_login_collection_validated,
				is_online,
				password_hash
			) VALUES (
				:created_datetime,
				:first_name,
				:last_name,
				:last_online,
				:latitude,
				:longitude,
				:gmt_offset,
				:is_login_collection_validated,
				:is_online,
				:password_hash
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat(),
				':first_name' => $UserAccount->getString('first_name'),
				':last_name' => $UserAccount->getString('last_name'),
				':last_online' => RuntimeInfo::instance()->now()->getMySQLFormat(),
				':latitude' => $UserAccount->getNumber('latitude'),
				':longitude' => $UserAccount->getNumber('longitude'),
				':gmt_offset' => $UserAccount->getNumber('gmt_offset'),
				':is_login_collection_validated' => false,
				':is_online' => true,
				':password_hash' => $UserAccount->getDataAsOriginallyCast('password_hash')
			),
			// which fields are integers
			array(
				':latitude',
				':longitude',
				':gmt_offset'
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
				is_login_collection_validated,
				is_online,
				is_closed,
				password_hash,
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
				is_login_collection_validated,
				is_online,
				is_closed,
				password_hash,
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
				is_login_collection_validated,
				is_online,
				is_closed,
				password_hash,
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
	
	public static function setUserLoginValidationAsFalse($user_id){
		return parent::MySQLUpdateAction('
			UPDATE user_account 
			SET is_login_collection_validated = NULL
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':user_id' => $user_id
			),
			// which fields are integers
			array(
				':user_id'
			)
		);
	}
	
	public static function setUserLoginValidationAsTrue($user_id){
		return parent::MySQLUpdateAction('
			UPDATE user_account 
			SET is_login_collection_validated = NULL
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':user_id' => $user_id
			),
			// which fields are integers
			array(
				':user_id'
			)
		);
	}
	
	public static function setUserAsOnline($user_id){
		return parent::MySQLUpdateAction('
			UPDATE user_account 
			SET last_online=:last_online,
				is_online=true
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':last_online' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':user_id' => $user_id
			),
			// which fields are integers
			array(
				':user_id'
			)
		);
	}
	
	public static function setUserPassword(UserAccount $UserAccount){
		return parent::MySQLUpdateAction('
			UPDATE user_account 
			SET modified_datetime=:modified_datetime,
				password_hash=:password_hash
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':password_hash' => $UserAccount->getString('password_hash'),
				':user_id' => $UserAccount->getInteger('user_id')
			),
			// which fields are integers
			array(
				':user_id'
			)
		);
	}
	
	public static function secureUserAccount(UserAccount $UserAccount){
		return parent::MySQLUpdateAction('
			UPDATE user_account 
			SET modified_datetime=:modified_datetime,
				first_name=:first_name,
				last_name=:last_name,
				password_hash=:password_hash
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':password_hash' => $UserAccount->getString('password_hash'),
				':first_name' => $UserAccount->getString('first_name'),
				':last_name' => $UserAccount->getString('last_name'),
				':user_id' => $UserAccount->getInteger('user_id')
			),
			// which fields are integers
			array(
				':user_id'
			)
		);
	}
	
	public static function closeAccountByAdmin($user_id){
		return parent::MySQLUpdateAction('
			UPDATE user_account 
			SET modified_datetime=:modified_datetime,
				is_closed=2
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':user_id' => $user_id
			),
			// which fields are integers
			array(
				':user_id'
			)
		);
	}
	
	public static function closeAccount($user_id){
		return parent::MySQLUpdateAction('
			UPDATE user_account 
			SET modified_datetime=:modified_datetime,
				is_closed=1
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':user_id' => $user_id
			),
			// which fields are integers
			array(
				':user_id'
			)
		);
	}
	
	public static function reopenAccount($user_id){
		return parent::MySQLUpdateAction('
			UPDATE user_account 
			SET modified_datetime=:modified_datetime,
				is_closed=NULL
			WHERE user_id=:user_id AND is_closed <> 2
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':user_id' => $user_id
			),
			// which fields are integers
			array(
				':user_id'
			)
		);
	}
	
	public static function reopenAccountByAdmin($user_id){
		return parent::MySQLUpdateAction('
			UPDATE user_account 
			SET modified_datetime=:modified_datetime,
				is_closed=NULL
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':user_id' => $user_id
			),
			// which fields are integers
			array(
				':user_id'
			)
		);
	}
	
	
}