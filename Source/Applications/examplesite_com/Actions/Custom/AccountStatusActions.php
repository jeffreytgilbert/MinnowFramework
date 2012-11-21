<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class AccountStatusActions extends Actions{
	
	public static function insertAccountStatus(AccountStatus $AccountStatus){
		return parent::MySQLCreateAction('
			INSERT INTO account_status (
				status_type,
				hierarchical_order,
				created_datetime
			) VALUES (
				:status_type,
				:hierarchical_order,
				:created_datetime
			)',
			// bind data to sql variables
			array(
				':status_type' => $AccountStatus->getString('status_type'),
				':hierarchical_order' => $AccountStatus->getString('hierarchical_order'),
				':created_datetime' => RIGHT_NOW_GMT,
				':account_status_id' => $AccountStatus->getInteger('account_status_id')
			),
			// which fields are integers
			array(
				
				':account_status_id'
			)
		);
	}
	
	public static function selectByAccountStatusId($account_status_id){
		// Return one object by primary key selection
		return new AccountStatus(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				account_status_id,
				status_type,
				hierarchical_order,
				created_datetime
			FROM account_status 
			WHERE account_status_id=:account_status_id',
			// bind data to sql variables
			array(
				':account_status_id' => (int)$account_status_id
			),
			// which fields are integers
			array(
				':account_status_id'
			),
			// return as this object collection type
			'AccountStatus'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$AccountStatusCollection = new AccountStatusCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				account_status_id,
				status_type,
				hierarchical_order,
				created_datetime
			FROM account_status 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'AccountStatus'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$AccountStatusCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $AccountStatusCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$account_status_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('AccountStatus', new AccountStatus());
			if($DataObject->getInteger('account_status_id') > 0){
				$account_status_ids[] = $DataObject->getInteger('account_status_id');
			}
		}
		
		$account_status_ids = array_unique($account_status_ids);
		
		$AccountStatusCollection = new AccountStatusCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				account_status_id,
				status_type,
				hierarchical_order,
				created_datetime
			FROM account_status 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'AccountStatus'
		));
		
		foreach($AccountStatusCollection->toArray() as $AccountStatus){
			$array = $DataCollection->getItemsBy('account_status_id',$AccountStatus->getInteger('account_status_id'));
			foreach($array as $DataObject){
				$DataObject->set('AccountStatus',$AccountStatus);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateAccountStatus(AccountStatus $AccountStatus){
		return parent::MySQLUpdateAction('
			UPDATE account_status 
			SET status_type=:status_type,
				hierarchical_order=:hierarchical_order
			WHERE account_status_id=:account_status_id
			',
			// bind data to sql variables
			array(
				':status_type' => $AccountStatus->getString('status_type'),
				':hierarchical_order' => $AccountStatus->getString('hierarchical_order'),
				':account_status_id' => $AccountStatus->getInteger('account_status_id')
			),
			// which fields are integers
			array(
				
				':account_status_id'
			)
		);
	}
	
	public static function deleteAccountStatusById($account_status_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM account_status 
			WHERE account_status_id=:account_status_id',
			// bind data to sql variables
			array(
				':account_status_id' => (int)$account_status_id
			),
			// which fields are integers
			array(
				':account_status_id'
			)
		);
	}

}