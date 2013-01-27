<?php 

/**
 * This is to create correctly implemented readable exception handling for HybridAuth
 */
class AuthenticationException extends Exception
{
	// Error codes
	const BAD_CREDENTIALS = '1';
	const TOO_MANY_BAD_REQUESTS = '2';
	const USER_BAN = '3';
	const USER_ACCOUNT_NOT_REGISTERED = '4';
	const ACCOUNT_CLOSED_BY_ADMIN = '5';
	
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        // some code
    
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}
