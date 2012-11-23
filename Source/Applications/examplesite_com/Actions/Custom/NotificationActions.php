<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class NotificationActions extends Actions{
	
	public static function insertNotification(Notification $Notification){
		return parent::MySQLCreateAction('
			INSERT INTO notification (
				user_id,
				created_datetime,
				is_read,
				message,
				primary_link,
				notification_type_id,
				notification_type
			) VALUES (
				:user_id,
				:created_datetime,
				:is_read,
				:message,
				:primary_link,
				:notification_type_id,
				:notification_type
			)',
			// bind data to sql variables
			array(
				':user_id' => $Notification->getInteger('user_id'),
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':is_read' => $Notification->getBoolean('is_read'),
				':message' => $Notification->getString('message'),
				':primary_link' => $Notification->getString('primary_link'),
				':notification_type_id' => $Notification->getInteger('notification_type_id'),
				':notification_type' => $Notification->getString('notification_type'),
				':notification_id' => $Notification->getInteger('notification_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id',
				':is_read',
				':notification_type_id',
				':notification_id'
			)
		);
	}
	
	public static function selectByNotificationId($notification_id){
		// Return one object by primary key selection
		return new Notification(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				notification_id,
				user_id,
				created_datetime,
				modified_datetime,
				is_read,
				message,
				primary_link,
				notification_type_id,
				notification_type
			FROM notification 
			WHERE notification_id=:notification_id',
			// bind data to sql variables
			array(
				':notification_id' => (int)$notification_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':notification_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$NotificationCollection = new NotificationCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				notification_id,
				user_id,
				created_datetime,
				modified_datetime,
				is_read,
				message,
				primary_link,
				notification_type_id,
				notification_type
			FROM notification 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Notification'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$NotificationCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $NotificationCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$notification_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('Notification', new Notification());
			if($DataObject->getInteger('notification_id') > 0){
				$notification_ids[] = $DataObject->getInteger('notification_id');
			}
		}
		
		$notification_ids = array_unique($notification_ids);
		
		$NotificationCollection = new NotificationCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				notification_id,
				user_id,
				created_datetime,
				modified_datetime,
				is_read,
				message,
				primary_link,
				notification_type_id,
				notification_type
			FROM notification 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Notification'
		));
		
		foreach($NotificationCollection->toArray() as $Notification){
			$array = $DataCollection->getObjectByFieldValue('notification_id',$Notification->getInteger('notification_id'));
			foreach($array as $DataObject){
				$DataObject->set('Notification',$Notification);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateNotification(Notification $Notification){
		return parent::MySQLUpdateAction('
			UPDATE notification 
			SET user_id=:user_id,
				modified_datetime=:modified_datetime,
				is_read=:is_read,
				message=:message,
				primary_link=:primary_link,
				notification_type_id=:notification_type_id,
				notification_type=:notification_type
			WHERE notification_id=:notification_id
			',
			// bind data to sql variables
			array(
				':user_id' => $Notification->getInteger('user_id'),
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':is_read' => $Notification->getBoolean('is_read'),
				':message' => $Notification->getString('message'),
				':primary_link' => $Notification->getString('primary_link'),
				':notification_type_id' => $Notification->getInteger('notification_type_id'),
				':notification_type' => $Notification->getString('notification_type'),
				':notification_id' => $Notification->getInteger('notification_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id',
				':notification_type_id',
				':notification_id'
			)
		);
	}
	
	public static function deleteNotificationById($notification_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM notification 
			WHERE notification_id=:notification_id',
			// bind data to sql variables
			array(
				':notification_id' => (int)$notification_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':notification_id'
			)
		);
	}

}