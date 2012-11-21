<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class ThemeActions extends Actions{
	
	public static function insertTheme(Theme $Theme){
		return parent::MySQLCreateAction('
			INSERT INTO theme (
				created_datetime,
				theme_name,
				thumb_path,
				css_file_path
			) VALUES (
				:created_datetime,
				:theme_name,
				:thumb_path,
				:css_file_path
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RIGHT_NOW_GMT,
				':theme_name' => $Theme->getString('theme_name'),
				':thumb_path' => $Theme->getString('thumb_path'),
				':css_file_path' => $Theme->getString('css_file_path'),
				':theme_id' => $Theme->getInteger('theme_id')
			),
			// which fields are integers
			array(
				
				':theme_id'
			)
		);
	}
	
	public static function selectByThemeId($theme_id){
		// Return one object by primary key selection
		return new Theme(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				theme_id,
				created_datetime,
				modified_datetime,
				theme_name,
				thumb_path,
				css_file_path
			FROM theme 
			WHERE theme_id=:theme_id',
			// bind data to sql variables
			array(
				':theme_id' => (int)$theme_id
			),
			// which fields are integers
			array(
				':theme_id'
			),
			// return as this object collection type
			'Theme'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$ThemeCollection = new ThemeCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				theme_id,
				created_datetime,
				modified_datetime,
				theme_name,
				thumb_path,
				css_file_path
			FROM theme 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'Theme'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$ThemeCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $ThemeCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$theme_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('Theme', new Theme());
			if($DataObject->getInteger('theme_id') > 0){
				$theme_ids[] = $DataObject->getInteger('theme_id');
			}
		}
		
		$theme_ids = array_unique($theme_ids);
		
		$ThemeCollection = new ThemeCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				theme_id,
				created_datetime,
				modified_datetime,
				theme_name,
				thumb_path,
				css_file_path
			FROM theme 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'Theme'
		));
		
		foreach($ThemeCollection->toArray() as $Theme){
			$array = $DataCollection->getItemsBy('theme_id',$Theme->getInteger('theme_id'));
			foreach($array as $DataObject){
				$DataObject->set('Theme',$Theme);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateTheme(Theme $Theme){
		return parent::MySQLUpdateAction('
			UPDATE theme 
			SET modified_datetime=:modified_datetime,
				theme_name=:theme_name,
				thumb_path=:thumb_path,
				css_file_path=:css_file_path
			WHERE theme_id=:theme_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RIGHT_NOW_GMT,
				':theme_name' => $Theme->getString('theme_name'),
				':thumb_path' => $Theme->getString('thumb_path'),
				':css_file_path' => $Theme->getString('css_file_path'),
				':theme_id' => $Theme->getInteger('theme_id')
			),
			// which fields are integers
			array(
				
				':theme_id'
			)
		);
	}
	
	public static function deleteThemeById($theme_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM theme 
			WHERE theme_id=:theme_id',
			// bind data to sql variables
			array(
				':theme_id' => (int)$theme_id
			),
			// which fields are integers
			array(
				':theme_id'
			)
		);
	}

}