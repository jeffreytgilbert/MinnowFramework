<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class PowerActions extends Actions{
	
	public static function insertPower(Power $Power){
		return parent::MySQLCreateAction('
			INSERT INTO power (
				created_datetime,
				name,
				hint
			) VALUES (
				:created_datetime,
				:name,
				:hint
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':name' => $Power->getString('name'),
				':hint' => $Power->getString('hint'),
				':power_id' => $Power->getInteger('power_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':power_id'
			)
		);
	}
	
	public static function selectByPowerId($power_id){
		// Return one object by primary key selection
		return new Power(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				power_id,
				created_datetime,
				name,
				hint
			FROM power 
			WHERE power_id=:power_id',
			// bind data to sql variables
			array(
				':power_id' => (int)$power_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':power_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$PowerCollection = new PowerCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				power_id,
				created_datetime,
				name,
				hint
			FROM power 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Power'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$PowerCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $PowerCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$power_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('Power', new Power());
			if($DataObject->getInteger('power_id') > 0){
				$power_ids[] = $DataObject->getInteger('power_id');
			}
		}
		
		$power_ids = array_unique($power_ids);
		
		$PowerCollection = new PowerCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				power_id,
				created_datetime,
				name,
				hint
			FROM power 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Power'
		));
		
		foreach($PowerCollection->toArray() as $Power){
			$array = $DataCollection->getObjectByFieldValue('power_id',$Power->getInteger('power_id'));
			foreach($array as $DataObject){
				$DataObject->set('Power',$Power);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updatePower(Power $Power){
		return parent::MySQLUpdateAction('
			UPDATE power 
			SET name=:name,
				hint=:hint
			WHERE power_id=:power_id
			',
			// bind data to sql variables
			array(
				':name' => $Power->getString('name'),
				':hint' => $Power->getString('hint'),
				':power_id' => $Power->getInteger('power_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':power_id'
			)
		);
	}
	
	public static function deletePowerById($power_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM power 
			WHERE power_id=:power_id',
			// bind data to sql variables
			array(
				':power_id' => (int)$power_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':power_id'
			)
		);
	}

}