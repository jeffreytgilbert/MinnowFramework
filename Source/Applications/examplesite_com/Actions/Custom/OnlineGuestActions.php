<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class OnlineGuestActions extends Actions{
	
	public static function insertOnlineGuest(OnlineGuest $OnlineGuest){
		return parent::MySQLCreateAction('
			REPLACE INTO online_guest (
				php_session_id,
				last_active
			) VALUES (
				:php_session_id,
				:last_active
			)',
			// bind data to sql variables
			array(
				':last_active' => $OnlineGuest->getDateTimeObject('last_active')->getMySQLFormat('datetime'),
				':php_session_id' => $OnlineGuest->getString('php_session_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			)
		);
	}
	
	public static function selectByOnlineGuestId($php_session_id){
		// Return one object by primary key selection
		return new OnlineGuest(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				php_session_id,
				last_active
			FROM online_guest 
			WHERE php_session_id=:php_session_id',
			// bind data to sql variables
			array(
				':php_session_id' => $php_session_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$OnlineGuestCollection = new OnlineGuestCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				php_session_id,
				last_active
			FROM online_guest 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'OnlineGuest'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$OnlineGuestCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $OnlineGuestCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$php_session_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('OnlineGuest', new OnlineGuest());
			if($DataObject->getString('php_session_id') > 0){
				$php_session_ids[] = $DataObject->getString('php_session_id');
			}
		}
		
		$php_session_ids = array_unique($php_session_ids);
		
		$OnlineGuestCollection = new OnlineGuestCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				php_session_id,
				last_active
			FROM online_guest 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'OnlineGuest'
		));
		
		foreach($OnlineGuestCollection->toArray() as $OnlineGuest){
			$array = $DataCollection->getObjectByFieldValue('php_session_id',$OnlineGuest->getInteger('php_session_id'));
			foreach($array as $DataObject){
				$DataObject->set('OnlineGuest',$OnlineGuest);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateOnlineGuest(OnlineGuest $OnlineGuest){
		return parent::MySQLUpdateAction('
			UPDATE online_guest 
			SET last_active=:last_active
			WHERE php_session_id=:php_session_id
			',
			// bind data to sql variables
			array(
				':last_active' => $OnlineGuest->getDateTimeObject('last_active')->getMySQLFormat('datetime'),
				':php_session_id' => $OnlineGuest->getString('php_session_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			)
		);
	}
	
	public static function deleteOnlineGuestById($php_session_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM online_guest 
			WHERE php_session_id=:php_session_id',
			// bind data to sql variables
			array(
				':php_session_id' => $php_session_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			)
		);
	}

}