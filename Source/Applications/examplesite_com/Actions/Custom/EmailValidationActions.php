<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class EmailValidationActions extends Actions{
	
	public static function validateEmail($email_code){
		
		$CodeResult = parent::MySQLReadAction('
			SELECT user_id FROM email_validation WHERE code = :code LIMIT 1',
			array(':code'=>$email_code),
			array(':code')
		)->getItemAt(0);
		
		if( $CodeResult instanceof Model && $CodeResult->getInteger('user_id') ){
			$user_id = $CodeResult->getInteger('user_id');
			
			parent::MySQLUpdateAction('
				DELETE FROM email_validation WHERE user_id=:user_id',
				array(':user_id'=>(int)$user_id),
				array(':user_id')
			);
			
			$UserLoginCollection = UserLoginActions::selectUnvalidatedLoginsListByUserId($user_id);
			
			if($UserLoginCollection->length() > 0){
				UserAccountActions::setUserLoginValidationAsFalse($user_id);
			} else {
				UserAccountActions::setUserLoginValidationAsTrue($user_id);
			}
			
			return true;
		}
		return false;
	}
	
	public static function insertEmailValidation(EmailValidation $EmailValidation){
		return parent::MySQLCreateAction('
			INSERT INTO email_validation (
				created_datetime,
				code,
				email_address
			) VALUES (
				:created_datetime,
				:code,
				:email_address
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':code' => $EmailValidation->getString('code'),
				':email_address' => $EmailValidation->getString('email_address'),
				':user_id' => $EmailValidation->getInteger('user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':user_id'
			)
		);
	}
	
	public static function selectByEmailValidationId($user_id){
		// Return one object by primary key selection
		return new EmailValidation(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime,
				code,
				email_address
			FROM email_validation 
			WHERE user_id=:user_id',
			// bind data to sql variables
			array(
				':user_id' => (int)$user_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$EmailValidationCollection = new EmailValidationCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime,
				code,
				email_address
			FROM email_validation 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'EmailValidation'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$EmailValidationCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $EmailValidationCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('EmailValidation', new EmailValidation());
			if($DataObject->getInteger('user_id') > 0){
				$user_ids[] = $DataObject->getInteger('user_id');
			}
		}
		
		$user_ids = array_unique($user_ids);
		
		$EmailValidationCollection = new EmailValidationCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime,
				code,
				email_address
			FROM email_validation 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'EmailValidation'
		));
		
		foreach($EmailValidationCollection->toArray() as $EmailValidation){
			$array = $DataCollection->getObjectByFieldValue('user_id',$EmailValidation->getInteger('user_id'));
			foreach($array as $DataObject){
				$DataObject->set('EmailValidation',$EmailValidation);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateEmailValidation(EmailValidation $EmailValidation){
		return parent::MySQLUpdateAction('
			UPDATE email_validation 
			SET modified_datetime=:modified_datetime,
				code=:code,
				email_address=:email_address
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':code' => $EmailValidation->getString('code'),
				':email_address' => $EmailValidation->getString('email_address'),
				':user_id' => $EmailValidation->getInteger('user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':user_id'
			)
		);
	}
	
	public static function deleteEmailValidationById($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM email_validation 
			WHERE user_id=:user_id',
			// bind data to sql variables
			array(
				':user_id' => (int)$user_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id'
			)
		);
	}

}