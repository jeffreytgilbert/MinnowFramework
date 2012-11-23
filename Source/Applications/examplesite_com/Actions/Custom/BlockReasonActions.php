<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class BlockReasonActions extends Actions{
	
	public static function insertBlockReason(BlockReason $BlockReason){
		return parent::MySQLCreateAction('
			INSERT INTO block_reason (
				dropdown_choice,
				view_text
			) VALUES (
				:dropdown_choice,
				:view_text
			)',
			// bind data to sql variables
			array(
				':dropdown_choice' => $BlockReason->getString('dropdown_choice'),
				':view_text' => $BlockReason->getString('view_text'),
				':block_reason_id' => $BlockReason->getInteger('block_reason_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':block_reason_id'
			)
		);
	}
	
	public static function selectByBlockReasonId($block_reason_id){
		// Return one object by primary key selection
		return new BlockReason(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				block_reason_id,
				dropdown_choice,
				view_text
			FROM block_reason 
			WHERE block_reason_id=:block_reason_id',
			// bind data to sql variables
			array(
				':block_reason_id' => (int)$block_reason_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':block_reason_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$BlockReasonCollection = new BlockReasonCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				block_reason_id,
				dropdown_choice,
				view_text
			FROM block_reason 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'BlockReason'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$BlockReasonCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $BlockReasonCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$block_reason_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('BlockReason', new BlockReason());
			if($DataObject->getInteger('block_reason_id') > 0){
				$block_reason_ids[] = $DataObject->getInteger('block_reason_id');
			}
		}
		
		$block_reason_ids = array_unique($block_reason_ids);
		
		$BlockReasonCollection = new BlockReasonCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				block_reason_id,
				dropdown_choice,
				view_text
			FROM block_reason 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'BlockReason'
		));
		
		foreach($BlockReasonCollection->toArray() as $BlockReason){
			$array = $DataCollection->getObjectByFieldValue('block_reason_id',$BlockReason->getInteger('block_reason_id'));
			foreach($array as $DataObject){
				$DataObject->set('BlockReason',$BlockReason);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateBlockReason(BlockReason $BlockReason){
		return parent::MySQLUpdateAction('
			UPDATE block_reason 
			SET dropdown_choice=:dropdown_choice,
				view_text=:view_text
			WHERE block_reason_id=:block_reason_id
			',
			// bind data to sql variables
			array(
				':dropdown_choice' => $BlockReason->getString('dropdown_choice'),
				':view_text' => $BlockReason->getString('view_text'),
				':block_reason_id' => $BlockReason->getInteger('block_reason_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':block_reason_id'
			)
		);
	}
	
	public static function deleteBlockReasonById($block_reason_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM block_reason 
			WHERE block_reason_id=:block_reason_id',
			// bind data to sql variables
			array(
				':block_reason_id' => (int)$block_reason_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':block_reason_id'
			)
		);
	}

}