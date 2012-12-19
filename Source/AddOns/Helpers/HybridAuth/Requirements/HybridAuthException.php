<?php 

/**
 * This is to create correctly implemented readable exception handling for HybridAuth
 */
class HybridAuthException extends Exception
{
	// Error codes
	const UNSPECIFIED_ERROR = '1';
	const CONFIGURATION_ERROR = '2';
	const BAD_PROVIDER_CONFIG = '3';
	const PROVIDER_DISABLED = '4';
	const PROVIDER_CREDENTIALS_MISSING = '5';
	const AUTHENTICATION_FAILED = '6';
	const PROFILE_REQUEST_FAILED = '7';
	const USER_NOT_CONNECTED = '8';
	
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
