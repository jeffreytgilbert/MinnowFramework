<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class NotificationTypeActions extends Actions{
	
	public static function insertNotificationType(NotificationType $NotificationType){
		return parent::MySQLCreateAction('
			INSERT INTO notification_type (
				created_datetime,
				notification_type,
				template,
				application_id
			) VALUES (
				:created_datetime,
				:notification_type,
				:template,
				:application_id
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RIGHT_NOW_GMT,
				':notification_type' => $NotificationType->getString('notification_type'),
				':template' => $NotificationType->getString('template'),
				':application_id' => $NotificationType->getInteger('application_id'),
				':notification_type_id' => $NotificationType->getInteger('notification_type_id')
			),
			// which fields are integers
			array(
				':application_id',
				':notification_type_id'
			)
		);
	}
	
	public static function selectByNotificationTypeId($notification_type_id){
		// Return one object by primary key selection
		return new NotificationType(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				notification_type_id,
				created_datetime,
				modified_datetime,
				notification_type,
				template,
				application_id
			FROM notification_type 
			WHERE notification_type_id=:notification_type_id',
			// bind data to sql variables
			array(
				':notification_type_id' => (int)$notification_type_id
			),
			// which fields are integers
			array(
				':notification_type_id'
			),
			// return as this object collection type
			'NotificationType'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$NotificationTypeCollection = new NotificationTypeCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				notification_type_id,
				created_datetime,
				modified_datetime,
				notification_type,
				template,
				application_id
			FROM notification_type 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'NotificationType'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$NotificationTypeCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $NotificationTypeCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$notification_type_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('NotificationType', new NotificationType());
			if($DataObject->getInteger('notification_type_id') > 0){
				$notification_type_ids[] = $DataObject->getInteger('notification_type_id');
			}
		}
		
		$notification_type_ids = array_unique($notification_type_ids);
		
		$NotificationTypeCollection = new NotificationTypeCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				notification_type_id,
				created_datetime,
				modified_datetime,
				notification_type,
				template,
				application_id
			FROM notification_type 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'NotificationType'
		));
		
		foreach($NotificationTypeCollection->toArray() as $NotificationType){
			$array = $DataCollection->getItemsBy('notification_type_id',$NotificationType->getInteger('notification_type_id'));
			foreach($array as $DataObject){
				$DataObject->set('NotificationType',$NotificationType);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateNotificationType(NotificationType $NotificationType){
		return parent::MySQLUpdateAction('
			UPDATE notification_type 
			SET modified_datetime=:modified_datetime,
				notification_type=:notification_type,
				template=:template,
				application_id=:application_id
			WHERE notification_type_id=:notification_type_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RIGHT_NOW_GMT,
				':notification_type' => $NotificationType->getString('notification_type'),
				':template' => $NotificationType->getString('template'),
				':application_id' => $NotificationType->getInteger('application_id'),
				':notification_type_id' => $NotificationType->getInteger('notification_type_id')
			),
			// which fields are integers
			array(
				':application_id',
				':notification_type_id'
			)
		);
	}
	
	public static function deleteNotificationTypeById($notification_type_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM notification_type 
			WHERE notification_type_id=:notification_type_id',
			// bind data to sql variables
			array(
				':notification_type_id' => (int)$notification_type_id
			),
			// which fields are integers
			array(
				':notification_type_id'
			)
		);
	}

}