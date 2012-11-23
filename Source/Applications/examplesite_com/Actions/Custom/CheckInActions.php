<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class CheckInActions extends Actions{
	
	public static function insertCheckIn(CheckIn $CheckIn){
		return parent::MySQLCreateAction('
			INSERT INTO check_in (
				user_id,
				created_datetime,
				location_id,
				check_in_image_url,
				check_in_link_id,
				check_in_link_url,
				message
			) VALUES (
				:user_id,
				:created_datetime,
				:location_id,
				:check_in_image_url,
				:check_in_link_id,
				:check_in_link_url,
				:message
			)',
			// bind data to sql variables
			array(
				':user_id' => $CheckIn->getInteger('user_id'),
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':location_id' => $CheckIn->getInteger('location_id'),
				':check_in_image_url' => $CheckIn->getString('check_in_image_url'),
				':check_in_link_id' => $CheckIn->getInteger('check_in_link_id'),
				':check_in_link_url' => $CheckIn->getString('check_in_link_url'),
				':message' => $CheckIn->getString('message'),
				':check_in_id' => $CheckIn->getInteger('check_in_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id',
				':location_id',
				':check_in_link_id',
				':check_in_id'
			)
		);
	}
	
	public static function selectByCheckInId($check_in_id){
		// Return one object by primary key selection
		return new CheckIn(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				check_in_id,
				user_id,
				created_datetime,
				location_id,
				check_in_image_url,
				check_in_link_id,
				check_in_link_url,
				message
			FROM check_in 
			WHERE check_in_id=:check_in_id',
			// bind data to sql variables
			array(
				':check_in_id' => (int)$check_in_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':check_in_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$CheckInCollection = new CheckInCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				check_in_id,
				user_id,
				created_datetime,
				location_id,
				check_in_image_url,
				check_in_link_id,
				check_in_link_url,
				message
			FROM check_in 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'CheckIn'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$CheckInCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $CheckInCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$check_in_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('CheckIn', new CheckIn());
			if($DataObject->getInteger('check_in_id') > 0){
				$check_in_ids[] = $DataObject->getInteger('check_in_id');
			}
		}
		
		$check_in_ids = array_unique($check_in_ids);
		
		$CheckInCollection = new CheckInCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				check_in_id,
				user_id,
				created_datetime,
				location_id,
				check_in_image_url,
				check_in_link_id,
				check_in_link_url,
				message
			FROM check_in 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'CheckIn'
		));
		
		foreach($CheckInCollection->toArray() as $CheckIn){
			$array = $DataCollection->getObjectByFieldValue('check_in_id',$CheckIn->getInteger('check_in_id'));
			foreach($array as $DataObject){
				$DataObject->set('CheckIn',$CheckIn);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateCheckIn(CheckIn $CheckIn){
		return parent::MySQLUpdateAction('
			UPDATE check_in 
			SET user_id=:user_id,
				location_id=:location_id,
				check_in_image_url=:check_in_image_url,
				check_in_link_id=:check_in_link_id,
				check_in_link_url=:check_in_link_url,
				message=:message
			WHERE check_in_id=:check_in_id
			',
			// bind data to sql variables
			array(
				':user_id' => $CheckIn->getInteger('user_id'),
				':location_id' => $CheckIn->getInteger('location_id'),
				':check_in_image_url' => $CheckIn->getString('check_in_image_url'),
				':check_in_link_id' => $CheckIn->getInteger('check_in_link_id'),
				':check_in_link_url' => $CheckIn->getString('check_in_link_url'),
				':message' => $CheckIn->getString('message'),
				':check_in_id' => $CheckIn->getInteger('check_in_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id',
				':location_id',
				':check_in_link_id',
				':check_in_id'
			)
		);
	}
	
	public static function deleteCheckInById($check_in_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM check_in 
			WHERE check_in_id=:check_in_id',
			// bind data to sql variables
			array(
				':check_in_id' => (int)$check_in_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':check_in_id'
			)
		);
	}

}