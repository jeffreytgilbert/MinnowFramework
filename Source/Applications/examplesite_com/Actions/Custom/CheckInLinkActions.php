<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class CheckInLinkActions extends Actions{
	
	public static function insertCheckInLink(CheckInLink $CheckInLink){
		return parent::MySQLCreateAction('
			INSERT INTO check_in_link (
				check_in_link_image_url,
				url,
				created_datetime
			) VALUES (
				:check_in_link_image_url,
				:url,
				:created_datetime
			)',
			// bind data to sql variables
			array(
				':check_in_link_image_url' => $CheckInLink->getString('check_in_link_image_url'),
				':url' => $CheckInLink->getString('url'),
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':check_in_link_id' => $CheckInLink->getInteger('check_in_link_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':check_in_link_id'
			)
		);
	}
	
	public static function selectByCheckInLinkId($check_in_link_id){
		// Return one object by primary key selection
		return new CheckInLink(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				check_in_link_id,
				check_in_link_image_url,
				url,
				created_datetime
			FROM check_in_link 
			WHERE check_in_link_id=:check_in_link_id',
			// bind data to sql variables
			array(
				':check_in_link_id' => (int)$check_in_link_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':check_in_link_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$CheckInLinkCollection = new CheckInLinkCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				check_in_link_id,
				check_in_link_image_url,
				url,
				created_datetime
			FROM check_in_link 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'CheckInLink'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$CheckInLinkCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $CheckInLinkCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$check_in_link_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('CheckInLink', new CheckInLink());
			if($DataObject->getInteger('check_in_link_id') > 0){
				$check_in_link_ids[] = $DataObject->getInteger('check_in_link_id');
			}
		}
		
		$check_in_link_ids = array_unique($check_in_link_ids);
		
		$CheckInLinkCollection = new CheckInLinkCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				check_in_link_id,
				check_in_link_image_url,
				url,
				created_datetime
			FROM check_in_link 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'CheckInLink'
		));
		
		foreach($CheckInLinkCollection->toArray() as $CheckInLink){
			$array = $DataCollection->getObjectByFieldValue('check_in_link_id',$CheckInLink->getInteger('check_in_link_id'));
			foreach($array as $DataObject){
				$DataObject->set('CheckInLink',$CheckInLink);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateCheckInLink(CheckInLink $CheckInLink){
		return parent::MySQLUpdateAction('
			UPDATE check_in_link 
			SET check_in_link_image_url=:check_in_link_image_url,
				url=:url
			WHERE check_in_link_id=:check_in_link_id
			',
			// bind data to sql variables
			array(
				':check_in_link_image_url' => $CheckInLink->getString('check_in_link_image_url'),
				':url' => $CheckInLink->getString('url'),
				':check_in_link_id' => $CheckInLink->getInteger('check_in_link_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':check_in_link_id'
			)
		);
	}
	
	public static function deleteCheckInLinkById($check_in_link_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM check_in_link 
			WHERE check_in_link_id=:check_in_link_id',
			// bind data to sql variables
			array(
				':check_in_link_id' => (int)$check_in_link_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':check_in_link_id'
			)
		);
	}

}