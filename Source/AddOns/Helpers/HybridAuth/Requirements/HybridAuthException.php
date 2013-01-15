<?php 

/**
 * This is to create correctly implemented readable exception handling for HybridAuth
 */
class HybridAuthException extends Exception
{
	// Error codes
	const UNSPECIFIED_ERROR = 0;
	const CONFIGURATION_ERROR = 1;
	const BAD_PROVIDER_CONFIG = 2;
	const PROVIDER_DISABLED = 3;
	const PROVIDER_CREDENTIALS_MISSING = 4;
	const AUTHENTICATION_FAILED = 5;
	const PROFILE_REQUEST_FAILED = 6;
	const USER_NOT_CONNECTED = 7;
	const FEATURE_NOT_SUPPORTED = 8;
	const UNKNOWN_ERROR = 9;
	
	
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
