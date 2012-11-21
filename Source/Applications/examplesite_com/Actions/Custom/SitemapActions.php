<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class SitemapActions extends Actions{
	
	public static function insertSitemap(Sitemap $Sitemap){
		return parent::MySQLCreateAction('
			INSERT INTO sitemap (
				parent,
				title,
				url,
				ignore_in_sitemap,
				keywords,
				description,
				order_id
			) VALUES (
				:parent,
				:title,
				:url,
				:ignore_in_sitemap,
				:keywords,
				:description,
				:order_id
			)',
			// bind data to sql variables
			array(
				':parent' => $Sitemap->getInteger('parent'),
				':title' => $Sitemap->getString('title'),
				':url' => $Sitemap->getString('url'),
				':ignore_in_sitemap' => $Sitemap->getInteger('ignore_in_sitemap'),
				':keywords' => $Sitemap->getString('keywords'),
				':description' => $Sitemap->getString('description'),
				':order_id' => $Sitemap->getInteger('order_id'),
				':link_id' => $Sitemap->getInteger('link_id')
			),
			// which fields are integers
			array(
				':parent',
				':ignore_in_sitemap',
				':order_id',
				':link_id'
			)
		);
	}
	
	public static function selectBySitemapId($link_id){
		// Return one object by primary key selection
		return new Sitemap(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				link_id,
				parent,
				title,
				url,
				ignore_in_sitemap,
				keywords,
				description,
				order_id
			FROM sitemap 
			WHERE link_id=:link_id',
			// bind data to sql variables
			array(
				':link_id' => (int)$link_id
			),
			// which fields are integers
			array(
				':link_id'
			),
			// return as this object collection type
			'Sitemap'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$SitemapCollection = new SitemapCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				link_id,
				parent,
				title,
				url,
				ignore_in_sitemap,
				keywords,
				description,
				order_id
			FROM sitemap 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'Sitemap'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$SitemapCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $SitemapCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$link_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('Sitemap', new Sitemap());
			if($DataObject->getInteger('link_id') > 0){
				$link_ids[] = $DataObject->getInteger('link_id');
			}
		}
		
		$link_ids = array_unique($link_ids);
		
		$SitemapCollection = new SitemapCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				link_id,
				parent,
				title,
				url,
				ignore_in_sitemap,
				keywords,
				description,
				order_id
			FROM sitemap 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'Sitemap'
		));
		
		foreach($SitemapCollection->toArray() as $Sitemap){
			$array = $DataCollection->getItemsBy('link_id',$Sitemap->getInteger('link_id'));
			foreach($array as $DataObject){
				$DataObject->set('Sitemap',$Sitemap);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateSitemap(Sitemap $Sitemap){
		return parent::MySQLUpdateAction('
			UPDATE sitemap 
			SET parent=:parent,
				title=:title,
				url=:url,
				ignore_in_sitemap=:ignore_in_sitemap,
				keywords=:keywords,
				description=:description,
				order_id=:order_id
			WHERE link_id=:link_id
			',
			// bind data to sql variables
			array(
				':parent' => $Sitemap->getInteger('parent'),
				':title' => $Sitemap->getString('title'),
				':url' => $Sitemap->getString('url'),
				':ignore_in_sitemap' => $Sitemap->getInteger('ignore_in_sitemap'),
				':keywords' => $Sitemap->getString('keywords'),
				':description' => $Sitemap->getString('description'),
				':order_id' => $Sitemap->getInteger('order_id'),
				':link_id' => $Sitemap->getInteger('link_id')
			),
			// which fields are integers
			array(
				':parent',
				':ignore_in_sitemap',
				':order_id',
				':link_id'
			)
		);
	}
	
	public static function deleteSitemapById($link_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM sitemap 
			WHERE link_id=:link_id',
			// bind data to sql variables
			array(
				':link_id' => (int)$link_id
			),
			// which fields are integers
			array(
				':link_id'
			)
		);
	}

}