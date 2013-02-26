<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class BlockActions extends Actions{
	
	public static function insertBlock(Block $Block){
		return parent::MySQLCreateAction('
			INSERT INTO block (
				created_datetime,
				target_user_id,
				block_reason_id,
				note
			) VALUES (
				:created_datetime,
				:target_user_id,
				:block_reason_id,
				:note
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':target_user_id' => $Block->getInteger('target_user_id'),
				':block_reason_id' => $Block->getInteger('block_reason_id'),
				':note' => $Block->getString('note'),
				':my_user_id' => $Block->getInteger('my_user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':target_user_id',
				':block_reason_id',
				':my_user_id'
			)
		);
	}
	
	public static function selectByBlockId($my_user_id){
		// Return one object by primary key selection
		return new Block(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				created_datetime,
				modified_datetime,
				my_user_id,
				target_user_id,
				block_reason_id,
				note
			FROM block 
			WHERE my_user_id=:my_user_id',
			// bind data to sql variables
			array(
				':my_user_id' => (int)$my_user_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':my_user_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$BlockCollection = new BlockCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				created_datetime,
				modified_datetime,
				my_user_id,
				target_user_id,
				block_reason_id,
				note
			FROM block 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Block'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$BlockCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $BlockCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$my_user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('Block', new Block());
			if($DataObject->getInteger('my_user_id') > 0){
				$my_user_ids[] = $DataObject->getInteger('my_user_id');
			}
		}
		
		$my_user_ids = array_unique($my_user_ids);
		
		$BlockCollection = new BlockCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				created_datetime,
				modified_datetime,
				my_user_id,
				target_user_id,
				block_reason_id,
				note
			FROM block 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Block'
		));
		
		foreach($BlockCollection->toArray() as $Block){
			$array = $DataCollection->getObjectByFieldValue('my_user_id',$Block->getInteger('my_user_id'));
			foreach($array as $DataObject){
				$DataObject->set('Block',$Block);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateBlock(Block $Block){
		return parent::MySQLUpdateAction('
			UPDATE block 
			SET modified_datetime=:modified_datetime,
				target_user_id=:target_user_id,
				block_reason_id=:block_reason_id,
				note=:note
			WHERE my_user_id=:my_user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':target_user_id' => $Block->getInteger('target_user_id'),
				':block_reason_id' => $Block->getInteger('block_reason_id'),
				':note' => $Block->getString('note'),
				':my_user_id' => $Block->getInteger('my_user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':target_user_id',
				':block_reason_id',
				':my_user_id'
			)
		);
	}
	
	public static function deleteBlockById($my_user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM block 
			WHERE my_user_id=:my_user_id',
			// bind data to sql variables
			array(
				':my_user_id' => (int)$my_user_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':my_user_id'
			)
		);
	}

}