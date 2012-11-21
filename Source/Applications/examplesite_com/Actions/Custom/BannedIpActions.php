<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class BannedIpActions extends Actions{
	
	public static function insertBannedIp(BannedIp $BannedIp){
		return parent::MySQLCreateAction('
			INSERT INTO banned_ip (
				banned_ip,
				user_name,
				date_issued,
				expiry_date
			) VALUES (
				:banned_ip,
				:user_name,
				:date_issued,
				:expiry_date
			)',
			// bind data to sql variables
			array(
				':banned_ip' => $BannedIp->getString('banned_ip'),
				':user_name' => $BannedIp->getString('user_name'),
				':date_issued' => $BannedIp->getDateTimeObject('date_issued')->getMySQLFormat('datetime'),
				':expiry_date' => $BannedIp->getDateTimeObject('expiry_date')->getMySQLFormat('datetime'),
				':user_id' => $BannedIp->getInteger('user_id')
			),
			// which fields are integers
			array(
				
				':user_id'
			)
		);
	}
	
	public static function selectByBannedIpId($user_id){
		// Return one object by primary key selection
		return new BannedIp(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				banned_ip,
				user_id,
				user_name,
				date_issued,
				expiry_date
			FROM banned_ip 
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
			'BannedIp'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$BannedIpCollection = new BannedIpCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				banned_ip,
				user_id,
				user_name,
				date_issued,
				expiry_date
			FROM banned_ip 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'BannedIp'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$BannedIpCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $BannedIpCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('BannedIp', new BannedIp());
			if($DataObject->getInteger('user_id') > 0){
				$user_ids[] = $DataObject->getInteger('user_id');
			}
		}
		
		$user_ids = array_unique($user_ids);
		
		$BannedIpCollection = new BannedIpCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				banned_ip,
				user_id,
				user_name,
				date_issued,
				expiry_date
			FROM banned_ip 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'BannedIp'
		));
		
		foreach($BannedIpCollection->toArray() as $BannedIp){
			$array = $DataCollection->getItemsBy('user_id',$BannedIp->getInteger('user_id'));
			foreach($array as $DataObject){
				$DataObject->set('BannedIp',$BannedIp);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateBannedIp(BannedIp $BannedIp){
		return parent::MySQLUpdateAction('
			UPDATE banned_ip 
			SET banned_ip=:banned_ip,
				user_name=:user_name,
				date_issued=:date_issued,
				expiry_date=:expiry_date
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':banned_ip' => $BannedIp->getString('banned_ip'),
				':user_name' => $BannedIp->getString('user_name'),
				':date_issued' => $BannedIp->getDateTimeObject('date_issued')->getMySQLFormat('datetime'),
				':expiry_date' => $BannedIp->getDateTimeObject('expiry_date')->getMySQLFormat('datetime'),
				':user_id' => $BannedIp->getInteger('user_id')
			),
			// which fields are integers
			array(
				
				':user_id'
			)
		);
	}
	
	public static function deleteBannedIpById($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM banned_ip 
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