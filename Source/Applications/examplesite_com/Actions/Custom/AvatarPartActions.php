<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class AvatarPartActions extends Actions{
	
	public static function insertAvatarPart(AvatarPart $AvatarPart){
		return parent::MySQLCreateAction('
			INSERT INTO avatar_part (
				created_datetime,
				layer,
				layer_order_id,
				grouping,
				gender,
				path,
				path_to_thumbnail,
				is_inactive
			) VALUES (
				:created_datetime,
				:layer,
				:layer_order_id,
				:grouping,
				:gender,
				:path,
				:path_to_thumbnail,
				:is_inactive
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RIGHT_NOW_GMT,
				':layer' => $AvatarPart->getInteger('layer'),
				':layer_order_id' => $AvatarPart->getInteger('layer_order_id'),
				':grouping' => $AvatarPart->getString('grouping'),
				':gender' => $AvatarPart->getString('gender'),
				':path' => $AvatarPart->getString('path'),
				':path_to_thumbnail' => $AvatarPart->getString('path_to_thumbnail'),
				':is_inactive' => $AvatarPart->getBoolean('is_inactive'),
				':avatar_part_id' => $AvatarPart->getInteger('avatar_part_id')
			),
			// which fields are integers
			array(
				':layer',
				':layer_order_id',
				':avatar_part_id'
			)
		);
	}
	
	public static function selectByAvatarPartId($avatar_part_id){
		// Return one object by primary key selection
		return new AvatarPart(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				avatar_part_id,
				created_datetime,
				layer,
				layer_order_id,
				grouping,
				gender,
				path,
				path_to_thumbnail,
				is_inactive
			FROM avatar_part 
			WHERE avatar_part_id=:avatar_part_id',
			// bind data to sql variables
			array(
				':avatar_part_id' => (int)$avatar_part_id
			),
			// which fields are integers
			array(
				':avatar_part_id'
			),
			// return as this object collection type
			'AvatarPart'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$AvatarPartCollection = new AvatarPartCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				avatar_part_id,
				created_datetime,
				layer,
				layer_order_id,
				grouping,
				gender,
				path,
				path_to_thumbnail,
				is_inactive
			FROM avatar_part 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'AvatarPart'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$AvatarPartCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $AvatarPartCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$avatar_part_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('AvatarPart', new AvatarPart());
			if($DataObject->getInteger('avatar_part_id') > 0){
				$avatar_part_ids[] = $DataObject->getInteger('avatar_part_id');
			}
		}
		
		$avatar_part_ids = array_unique($avatar_part_ids);
		
		$AvatarPartCollection = new AvatarPartCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				avatar_part_id,
				created_datetime,
				layer,
				layer_order_id,
				grouping,
				gender,
				path,
				path_to_thumbnail,
				is_inactive
			FROM avatar_part 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'AvatarPart'
		));
		
		foreach($AvatarPartCollection->toArray() as $AvatarPart){
			$array = $DataCollection->getItemsBy('avatar_part_id',$AvatarPart->getInteger('avatar_part_id'));
			foreach($array as $DataObject){
				$DataObject->set('AvatarPart',$AvatarPart);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateAvatarPart(AvatarPart $AvatarPart){
		return parent::MySQLUpdateAction('
			UPDATE avatar_part 
			SET layer=:layer,
				layer_order_id=:layer_order_id,
				grouping=:grouping,
				gender=:gender,
				path=:path,
				path_to_thumbnail=:path_to_thumbnail,
				is_inactive=:is_inactive
			WHERE avatar_part_id=:avatar_part_id
			',
			// bind data to sql variables
			array(
				':layer' => $AvatarPart->getInteger('layer'),
				':layer_order_id' => $AvatarPart->getInteger('layer_order_id'),
				':grouping' => $AvatarPart->getString('grouping'),
				':gender' => $AvatarPart->getString('gender'),
				':path' => $AvatarPart->getString('path'),
				':path_to_thumbnail' => $AvatarPart->getString('path_to_thumbnail'),
				':is_inactive' => $AvatarPart->getBoolean('is_inactive'),
				':avatar_part_id' => $AvatarPart->getInteger('avatar_part_id')
			),
			// which fields are integers
			array(
				':layer',
				':layer_order_id',
				':avatar_part_id'
			)
		);
	}
	
	public static function deleteAvatarPartById($avatar_part_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM avatar_part 
			WHERE avatar_part_id=:avatar_part_id',
			// bind data to sql variables
			array(
				':avatar_part_id' => (int)$avatar_part_id
			),
			// which fields are integers
			array(
				':avatar_part_id'
			)
		);
	}

}