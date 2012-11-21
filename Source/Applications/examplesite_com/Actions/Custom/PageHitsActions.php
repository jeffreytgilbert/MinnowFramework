<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class PageHitsActions extends Actions{
	
	public static function insertPageHits(PageHits $PageHits){
		return parent::MySQLCreateAction('
			INSERT INTO page_hits (
				page_date,
				page_name,
				guest_hits,
				member_hits
			) VALUES (
				:page_date,
				:page_name,
				:guest_hits,
				:member_hits
			)',
			// bind data to sql variables
			array(
				':page_date' => $PageHits->getDateTimeObject('page_date')->getMySQLFormat('date'),
				':page_name' => $PageHits->getString('page_name'),
				':guest_hits' => $PageHits->getInteger('guest_hits'),
				':member_hits' => $PageHits->getInteger('member_hits'),
				':id' => $PageHits->getInteger('id')
			),
			// which fields are integers
			array(
				':guest_hits',
				':member_hits',
				':id'
			)
		);
	}
	
	public static function selectByPageHitsId($id){
		// Return one object by primary key selection
		return new PageHits(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				page_date,
				page_name,
				guest_hits,
				member_hits
			FROM page_hits 
			WHERE id=:id',
			// bind data to sql variables
			array(
				':id' => (int)$id
			),
			// which fields are integers
			array(
				':id'
			),
			// return as this object collection type
			'PageHits'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$PageHitsCollection = new PageHitsCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				page_date,
				page_name,
				guest_hits,
				member_hits
			FROM page_hits 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'PageHits'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$PageHitsCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $PageHitsCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('PageHits', new PageHits());
			if($DataObject->getInteger('id') > 0){
				$ids[] = $DataObject->getInteger('id');
			}
		}
		
		$ids = array_unique($ids);
		
		$PageHitsCollection = new PageHitsCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				page_date,
				page_name,
				guest_hits,
				member_hits
			FROM page_hits 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'PageHits'
		));
		
		foreach($PageHitsCollection->toArray() as $PageHits){
			$array = $DataCollection->getItemsBy('id',$PageHits->getInteger('id'));
			foreach($array as $DataObject){
				$DataObject->set('PageHits',$PageHits);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updatePageHits(PageHits $PageHits){
		return parent::MySQLUpdateAction('
			UPDATE page_hits 
			SET page_date=:page_date,
				page_name=:page_name,
				guest_hits=:guest_hits,
				member_hits=:member_hits
			WHERE id=:id
			',
			// bind data to sql variables
			array(
				':page_date' => $PageHits->getDateTimeObject('page_date')->getMySQLFormat('date'),
				':page_name' => $PageHits->getString('page_name'),
				':guest_hits' => $PageHits->getInteger('guest_hits'),
				':member_hits' => $PageHits->getInteger('member_hits'),
				':id' => $PageHits->getInteger('id')
			),
			// which fields are integers
			array(
				':guest_hits',
				':member_hits',
				':id'
			)
		);
	}
	
	public static function deletePageHitsById($id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM page_hits 
			WHERE id=:id',
			// bind data to sql variables
			array(
				':id' => (int)$id
			),
			// which fields are integers
			array(
				':id'
			)
		);
	}

}