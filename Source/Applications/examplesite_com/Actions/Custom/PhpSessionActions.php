<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class PhpSessionActions extends Actions{
	
	public static function insertPhpSession(PhpSession $PhpSession){
		return parent::MySQLCreateAction('
			INSERT INTO php_session (
				access,
				data
			) VALUES (
				:access,
				:data
			)',
			// bind data to sql variables
			array(
				':access' => $PhpSession->getInteger('access'),
				':data' => $PhpSession->getString('data'),
				':id' => $PhpSession->getInteger('id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':access',
				':id'
			)
		);
	}
	
	public static function selectByPhpSessionId($id){
		// Return one object by primary key selection
		return new PhpSession(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				id,
				access,
				data
			FROM php_session 
			WHERE id=:id',
			// bind data to sql variables
			array(
				':id' => (int)$id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$PhpSessionCollection = new PhpSessionCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				id,
				access,
				data
			FROM php_session 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'PhpSession'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$PhpSessionCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $PhpSessionCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('PhpSession', new PhpSession());
			if($DataObject->getInteger('id') > 0){
				$ids[] = $DataObject->getInteger('id');
			}
		}
		
		$ids = array_unique($ids);
		
		$PhpSessionCollection = new PhpSessionCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				id,
				access,
				data
			FROM php_session 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'PhpSession'
		));
		
		foreach($PhpSessionCollection->toArray() as $PhpSession){
			$array = $DataCollection->getObjectByFieldValue('id',$PhpSession->getInteger('id'));
			foreach($array as $DataObject){
				$DataObject->set('PhpSession',$PhpSession);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updatePhpSession(PhpSession $PhpSession){
		return parent::MySQLUpdateAction('
			UPDATE php_session 
			SET access=:access,
				data=:data
			WHERE id=:id
			',
			// bind data to sql variables
			array(
				':access' => $PhpSession->getInteger('access'),
				':data' => $PhpSession->getString('data'),
				':id' => $PhpSession->getInteger('id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':access',
				':id'
			)
		);
	}
	
	public static function deletePhpSessionById($id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM php_session 
			WHERE id=:id',
			// bind data to sql variables
			array(
				':id' => (int)$id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':id'
			)
		);
	}

}