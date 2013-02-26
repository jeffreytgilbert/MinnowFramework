<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class LogDebugInfoActions extends Actions{
	
	public static function insertLogDebugInfo(LogDebugInfo $LogDebugInfo){
		return parent::MySQLCreateAction('
			INSERT INTO log_debug_info (
				channel,
				level,
				message,
				time
			) VALUES (
				:channel,
				:level,
				:message,
				:time
			)',
			// bind data to sql variables
			array(
				':channel' => $LogDebugInfo->getString('channel'),
				':level' => $LogDebugInfo->getInteger('level'),
				':message' => $LogDebugInfo->getString('message'),
				':time' => $LogDebugInfo->getInteger('time'),
				':id' => $LogDebugInfo->getInteger('id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':level',
				':time',
				':id'
			)
		);
	}
	
	public static function selectByLogDebugInfoId($id){
		// Return one object by primary key selection
		return new LogDebugInfo(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				channel,
				level,
				message,
				time
			FROM log_debug_info 
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
		$LogDebugInfoCollection = new LogDebugInfoCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				channel,
				level,
				message,
				time
			FROM log_debug_info 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'LogDebugInfo'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$LogDebugInfoCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $LogDebugInfoCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('LogDebugInfo', new LogDebugInfo());
			if($DataObject->getInteger('id') > 0){
				$ids[] = $DataObject->getInteger('id');
			}
		}
		
		$ids = array_unique($ids);
		
		$LogDebugInfoCollection = new LogDebugInfoCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				channel,
				level,
				message,
				time
			FROM log_debug_info 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'LogDebugInfo'
		));
		
		foreach($LogDebugInfoCollection->toArray() as $LogDebugInfo){
			$array = $DataCollection->getObjectByFieldValue('id',$LogDebugInfo->getInteger('id'));
			foreach($array as $DataObject){
				$DataObject->set('LogDebugInfo',$LogDebugInfo);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateLogDebugInfo(LogDebugInfo $LogDebugInfo){
		return parent::MySQLUpdateAction('
			UPDATE log_debug_info 
			SET channel=:channel,
				level=:level,
				message=:message,
				time=:time
			WHERE id=:id
			',
			// bind data to sql variables
			array(
				':channel' => $LogDebugInfo->getString('channel'),
				':level' => $LogDebugInfo->getInteger('level'),
				':message' => $LogDebugInfo->getString('message'),
				':time' => $LogDebugInfo->getInteger('time'),
				':id' => $LogDebugInfo->getInteger('id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':level',
				':time',
				':id'
			)
		);
	}
	
	public static function deleteLogDebugInfoById($id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM log_debug_info 
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