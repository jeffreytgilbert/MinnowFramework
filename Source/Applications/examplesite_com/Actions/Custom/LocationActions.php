<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class LocationActions extends Actions{
	
	public static function insertLocation(Location $Location){
		return parent::MySQLCreateAction('
			INSERT INTO location (
				created_datetime,
				name,
				address,
				latitude,
				longitude,
				phone_number,
				website_url,
				email_address,
				location_thumbnail_url,
				user_id
			) VALUES (
				:created_datetime,
				:name,
				:address,
				:latitude,
				:longitude,
				:phone_number,
				:website_url,
				:email_address,
				:location_thumbnail_url,
				:user_id
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':name' => $Location->getString('name'),
				':address' => $Location->getString('address'),
				':latitude' => $Location->getInteger('latitude'),
				':longitude' => $Location->getInteger('longitude'),
				':phone_number' => $Location->getString('phone_number'),
				':website_url' => $Location->getString('website_url'),
				':email_address' => $Location->getString('email_address'),
				':location_thumbnail_url' => $Location->getString('location_thumbnail_url'),
				':user_id' => $Location->getInteger('user_id'),
				':location_id' => $Location->getInteger('location_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':latitude',
				':longitude',
				':user_id',
				':location_id'
			)
		);
	}
	
	public static function selectByLocationId($location_id){
		// Return one object by primary key selection
		return new Location(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				location_id,
				created_datetime,
				modified_datetime,
				name,
				address,
				latitude,
				longitude,
				phone_number,
				website_url,
				email_address,
				location_thumbnail_url,
				user_id
			FROM location 
			WHERE location_id=:location_id',
			// bind data to sql variables
			array(
				':location_id' => (int)$location_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':location_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$LocationCollection = new LocationCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				location_id,
				created_datetime,
				modified_datetime,
				name,
				address,
				latitude,
				longitude,
				phone_number,
				website_url,
				email_address,
				location_thumbnail_url,
				user_id
			FROM location 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Location'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$LocationCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $LocationCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$location_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('Location', new Location());
			if($DataObject->getInteger('location_id') > 0){
				$location_ids[] = $DataObject->getInteger('location_id');
			}
		}
		
		$location_ids = array_unique($location_ids);
		
		$LocationCollection = new LocationCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				location_id,
				created_datetime,
				modified_datetime,
				name,
				address,
				latitude,
				longitude,
				phone_number,
				website_url,
				email_address,
				location_thumbnail_url,
				user_id
			FROM location 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Location'
		));
		
		foreach($LocationCollection->toArray() as $Location){
			$array = $DataCollection->getObjectByFieldValue('location_id',$Location->getInteger('location_id'));
			foreach($array as $DataObject){
				$DataObject->set('Location',$Location);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateLocation(Location $Location){
		return parent::MySQLUpdateAction('
			UPDATE location 
			SET modified_datetime=:modified_datetime,
				name=:name,
				address=:address,
				latitude=:latitude,
				longitude=:longitude,
				phone_number=:phone_number,
				website_url=:website_url,
				email_address=:email_address,
				location_thumbnail_url=:location_thumbnail_url,
				user_id=:user_id
			WHERE location_id=:location_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':name' => $Location->getString('name'),
				':address' => $Location->getString('address'),
				':latitude' => $Location->getInteger('latitude'),
				':longitude' => $Location->getInteger('longitude'),
				':phone_number' => $Location->getString('phone_number'),
				':website_url' => $Location->getString('website_url'),
				':email_address' => $Location->getString('email_address'),
				':location_thumbnail_url' => $Location->getString('location_thumbnail_url'),
				':user_id' => $Location->getInteger('user_id'),
				':location_id' => $Location->getInteger('location_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':latitude',
				':longitude',
				':user_id',
				':location_id'
			)
		);
	}
	
	public static function deleteLocationById($location_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM location 
			WHERE location_id=:location_id',
			// bind data to sql variables
			array(
				':location_id' => (int)$location_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':location_id'
			)
		);
	}

}