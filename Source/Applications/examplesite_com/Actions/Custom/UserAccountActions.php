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
	
	/*
	public static function closeAccount($password=' ', $reason=null)
	{
		global $ID;
		$db = RuntimeInfo::instance()->connections()->MySQL();
		$reasons[1] = 'Abuse Related - Someone was abusing me or my profile.';
		$reasons[2] = 'Boredom - There isn\'t enough here to keep me happy.';
		$reasons[3] = 'Competition - I found a better site.';
		$reasons[4] = 'Reliability - Features were broken or the site was down too much.';
		$reasons[5] = 'Other Reason';
	
		$query='UPDATE user_account SET '.
			'last_online="'.		RuntimeInfo::instance()->now()->getMySQLFormat('datetime').'", '.
			'deleted_on="'.			RuntimeInfo::instance()->now()->getMySQLFormat('datetime').'", '.
			'is_suicide=1, '.
			'is_killed=1, '.
			'is_online=NULL, '.
			'quit_reason="'.		Filter::string($reason).'" '.
			'WHERE user_id='.		$ID->get('user_id').
			' AND password="'.		Filter::string($password).'" '.
			'LIMIT 1';
	
		$db->query($query);
		
		$query='DELETE FROM recurring_que WHERE user_id='.(int)$ID->get('user_id').' LIMIT 1';
		$db->query($query);
		
		$query='SELECT user_id, is_suicide, is_killed FROM user_account WHERE user_id='.(int)$ID->get('user_id').' LIMIT 1';
		$db->query($query);
		$db->readRow();
		
		if(isset($db->row_data['is_killed']))	{ return true; }
		else 									{ return false; }		// there is not a dead account with that account number
	}
	
	public static function reopenAccount($name, $password)
	{
		$db = RuntimeInfo::instance()->connections()->MySQL();
		$query = 'UPDATE user_account '
				.'SET is_killed=NULL, is_suicide=NULL '
				.'WHERE (email="'.Filter::string($name).'" OR login_name="'.Filter::string($name).'") AND '
				.'password="'.Filter::string($password).'"';
		$db->query($query);
		
		$query = 'SELECT user_id, is_suicide, is_killed '
				.'FROM user_account '
				.'WHERE (email="'.Filter::string($name).'" OR login_name="'.Filter::string($name).'") AND '
				.'password="'.Filter::string($password).'"';
		$db->query($query);
		$db->readRow();
		
		if(isset($db->row_data['user_id']))		{ return true; }
		else 									{ return false; }
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
				is_login_collection_validated=:is_login_collection_validated,
				is_online=:is_online,
				is_closed=:is_closed,
				password_hash=:password_hash,
				unread_messages=:unread_messages
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
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
				':is_login_collection_validated' => $UserAccount->getBoolean('is_login_collection_validated'),
				':is_online' => $UserAccount->getBoolean('is_online'),
				':is_closed' => $UserAccount->getBoolean('is_closed'),
				':password_hash' => $UserAccount->getString('password_hash'),
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
	
// 	public static function deleteUserAccountById($user_id){
// 		return parent::MySQLUpdateAction('
// 			DELETE 
// 			FROM user_account 
// 			WHERE user_id=:user_id',
// 			// bind data to sql variables
// 			array(
// 				':user_id' => (int)$user_id
// 			),
// 			// which fields are integers
// 			array(
// 				':user_id'
// 			)
// 		);
// 	}
	*/
}