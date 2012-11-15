<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class SampleOptionalActions extends Actions{
	
	public static function insertSampleOptional(SampleOptional $SampleOptional){
		return parent::MySQLCreateAction(/* insert */);
	}
	
	public static function selectBySampleOptionalId($id){
		// Return one object by primary key selection
		return parent::MySQLReadAction(/* select */)->getItemAt(0);
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$primary_keys = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('SampleOptional', new SampleOptional());
			if($DataObject->get('primary_key') > 0){
				$primary_keys[] = $DataObject->get('primary_key');
			}
		}
		
		$primary_keys = array_unique($primary_keys);
		
		$SampleOptionalCollection = parent::MySQLReadAction(/* list */);
		
		foreach($SampleOptionalCollection->toArray() as $SampleOptional){
			$array = $DataCollection->getItemsBy('primary_key',$SampleOptional->get('primary_key'));
			foreach($array as $DataObject){
				$DataObject->set('SampleOptional',$SampleOptional);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateSampleOptional(SampleOptional $SampleOptional){
		return parent::MySQLUpdateAction(/* update */);
	}
	
	public static function deleteSampleOptionalById($id){
		return parent::MySQLUpdateAction(/* delete */);
	}

}