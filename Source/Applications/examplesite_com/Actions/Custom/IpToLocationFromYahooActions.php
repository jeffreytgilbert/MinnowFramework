<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class IpToLocationFromYahooActions extends Actions{
	
	public static function insertIpToLocationFromYahoo(IpToLocationFromYahoo $IpToLocationFromYahoo){
		return parent::MySQLCreateAction('
			INSERT INTO ip_to_location_from_yahoo (
				`ip`,
				`country_code`,
				`country_name`,
				`region_name`,
				`city_name`,
				`latitude`,
				`longitude`,
				`postal_code`,
				`gmt_offset`,
				`dst_offset`,
				`area_code`,
				`dma_code`,
				`local_time`,
				`iso_time`,
				`utc_time`
			) VALUES (
				:ip,
				:country_code,
				:country_name,
				:region_name,
				:city_name,
				:latitude,
				:longitude,
				:postal_code,
				:gmt_offset,
				:dst_offset,
				:area_code,
				:dma_code,
				:local_time,
				:iso_time,
				:utc_time
			)',
			// bind data to sql variables
			array(
				':ip' => $IpToLocationFromYahoo->getString('ip'),
				':country_code' => $IpToLocationFromYahoo->getString('country_code'),
				':country_name' => $IpToLocationFromYahoo->getString('country_name'),
				':region_name' => $IpToLocationFromYahoo->getString('region_name'),
				':city_name' => $IpToLocationFromYahoo->getString('city_name'),
				':latitude' => $IpToLocationFromYahoo->getInteger('latitude'),
				':longitude' => $IpToLocationFromYahoo->getInteger('longitude'),
				':postal_code' => $IpToLocationFromYahoo->getString('postal_code'),
				':gmt_offset' => $IpToLocationFromYahoo->getInteger('gmt_offset'),
				':dst_offset' => $IpToLocationFromYahoo->getInteger('dst_offset'),
				':area_code' => $IpToLocationFromYahoo->getString('area_code'),
				':dma_code' => $IpToLocationFromYahoo->getString('dma_code'),
				':local_time' => $IpToLocationFromYahoo->getDateTimeObject('local_time')->getMySQLFormat('datetime'),
				':iso_time' => $IpToLocationFromYahoo->getDateTimeObject('iso_time')->getMySQLFormat('datetime'),
				':utc_time' => $IpToLocationFromYahoo->getDateTimeObject('utc_time')->getMySQLFormat('datetime')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':latitude',
				':longitude',
				':gmt_offset',
				':dst_offset'
			)
		);
	}
	
	public static function selectByIp($ip){
		// Return one object by primary key selection
		return new IpToLocationFromYahoo(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				`ip`,
				`country_code`,
				`country_name`,
				`region_name`,
				`city_name`,
				`latitude`,
				`longitude`,
				`postal_code`,
				`gmt_offset`,
				`dst_offset`,
				`area_code`,
				`dma_code`,
				`local_time`,
				`iso_time`,
				`utc_time`
			FROM ip_to_location_from_yahoo 
			WHERE ip=:ip',
			// bind data to sql variables
			array(
				':ip' => $ip
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$IpToLocationFromYahooCollection = new IpToLocationFromYahooCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				`ip`,
				`country_code`,
				`country_name`,
				`region_name`,
				`city_name`,
				`latitude`,
				`longitude`,
				`postal_code`,
				`gmt_offset`,
				`dst_offset`,
				`area_code`,
				`dma_code`,
				`local_time`,
				`iso_time`,
				`utc_time`
			FROM ip_to_location_from_yahoo 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'IpToLocationFromYahoo'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$IpToLocationFromYahooCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $IpToLocationFromYahooCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('IpToLocationFromYahoo', new IpToLocationFromYahoo());
			if($DataObject->getInteger('id') > 0){
				$ids[] = $DataObject->getInteger('id');
			}
		}
		
		$ids = array_unique($ids);
		
		$IpToLocationFromYahooCollection = new IpToLocationFromYahooCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				`ip`,
				`country_code`,
				`country_name`,
				`region_name`,
				`city_name`,
				`latitude`,
				`longitude`,
				`postal_code`,
				`gmt_offset`,
				`dst_offset`,
				`area_code`,
				`dma_code`,
				`local_time`,
				`iso_time`,
				`utc_time`
			FROM ip_to_location_from_yahoo 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'IpToLocationFromYahoo'
		));
		
		foreach($IpToLocationFromYahooCollection->toArray() as $IpToLocationFromYahoo){
			$array = $DataCollection->getObjectByFieldValue('id',$IpToLocationFromYahoo->getInteger('id'));
			foreach($array as $DataObject){
				$DataObject->set('IpToLocationFromYahoo',$IpToLocationFromYahoo);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateIpToLocationFromYahoo(IpToLocationFromYahoo $IpToLocationFromYahoo){
		return parent::MySQLUpdateAction('
			UPDATE ip_to_location_from_yahoo 
			SET ip=:ip,
				country_code=:country_code,
				country_name=:country_name,
				region_name=:region_name,
				city_name=:city_name,
				latitude=:latitude,
				longitude=:longitude,
				postal_code=:postal_code,
				gmt_offset=:gmt_offset,
				dst_offset=:dst_offset,
				area_code=:area_code,
				dma_code=:dma_code,
				`local_time`=:local_time,
				`iso_time`=:iso_time,
				`utc_time`=:utc_time
			WHERE id=:id
			',
			// bind data to sql variables
			array(
				':ip' => $IpToLocationFromYahoo->getString('ip'),
				':country_code' => $IpToLocationFromYahoo->getString('country_code'),
				':country_name' => $IpToLocationFromYahoo->getString('country_name'),
				':region_name' => $IpToLocationFromYahoo->getString('region_name'),
				':city_name' => $IpToLocationFromYahoo->getString('city_name'),
				':latitude' => $IpToLocationFromYahoo->getInteger('latitude'),
				':longitude' => $IpToLocationFromYahoo->getInteger('longitude'),
				':postal_code' => $IpToLocationFromYahoo->getString('postal_code'),
				':gmt_offset' => $IpToLocationFromYahoo->getInteger('gmt_offset'),
				':dst_offset' => $IpToLocationFromYahoo->getInteger('dst_offset'),
				':area_code' => $IpToLocationFromYahoo->getString('area_code'),
				':dma_code' => $IpToLocationFromYahoo->getString('dma_code'),
				':local_time' => $IpToLocationFromYahoo->getDateTimeObject('local_time')->getMySQLFormat('datetime'),
				':iso_time' => $IpToLocationFromYahoo->getDateTimeObject('iso_time')->getMySQLFormat('datetime'),
				':utc_time' => $IpToLocationFromYahoo->getDateTimeObject('utc_time')->getMySQLFormat('datetime'),
				':id' => $IpToLocationFromYahoo->getInteger('id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':latitude',
				':longitude',
				':gmt_offset',
				':dst_offset',
				':id'
			)
		);
	}
	
	public static function deleteIpToLocationFromYahooById($id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM ip_to_location_from_yahoo 
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