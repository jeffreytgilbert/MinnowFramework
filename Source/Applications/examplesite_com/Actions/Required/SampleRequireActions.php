<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class SampleRequiredActions extends Actions{
	
	public static function insertSampleRequired(SampleRequired $SampleRequired){
		return parent::MySQLCreateAction(/* insert */);
	}
	
	public static function selectBySampleRequiredId($id){
		// Return one object by primary key selection
		return parent::MySQLReadAction(/* select */)->getItemAt(0);
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$primary_keys = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('SampleRequired', new SampleRequired());
			if($DataObject->get('primary_key') > 0){
				$primary_keys[] = $DataObject->get('primary_key');
			}
		}
		
		$primary_keys = array_unique($primary_keys);
		
		$SampleRequiredCollection = parent::MySQLReadAction(/* list */);
		
		foreach($SampleRequiredCollection->toArray() as $SampleRequired){
			$array = $DataCollection->getItemsBy('primary_key',$SampleRequired->get('primary_key'));
			foreach($array as $DataObject){
				$DataObject->set('SampleRequired',$SampleRequired);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateSampleRequired(SampleRequired $SampleRequired){
		return parent::MySQLUpdateAction(/* update */);
	}
	
	public static function deleteSampleRequiredById($id){
		return parent::MySQLUpdateAction(/* delete */);
	}

}