<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

/*
 * Signed cookie code from http://code.google.com/p/mrclay/source/browse/trunk/php/MrClay/#MrClay%253Fstate%253Dclosed
 */

class SecureCookie{
	
	// size limit for cookie
	const LENGTH_LIMIT = 3896;
	const MODE_VISIBLE = 0;
	const MODE_ENCRYPT = 1;
	
	private 
		$_hash_algorithm = 'ripemd160',
		$_iterations = 32,
		$_salt_bytes = 24,
		$_hash_bytes = 24,
		
		$_domain,
		$_enable_ssl = false,
		$_path = '/',
		$_expiration_period = 'two weeks',
		$_secret,
		$_cookie_mode,
		$_hmac; // not sure what an hmac is
	
	public $errors = array();
	
	public function __construct(
			$debug, $hash_algorithm, $iterations, $salt_bytes, $hash_bytes,
			$domain, $enable_ssl, $path, $expiration_date, $secret, $cookie_mode){
		
		$this->debug = $debug;
		$this->_o['hashAlgo'] = $this->hash_algorithm = $hash_algorithm;
		$this->iterations = $iterations;
		$this->salt_bytes = $salt_bytes;
		$this->hash_bytes = $hash_bytes;
		
		$this->_o['domain'] = $this->_domain = $domain;
		$this->_o['secure'] = $this->_enable_ssl = $enable_ssl;
		$this->_o['path'] = $this->_path = $path;
		$this->_expiration_period = $expiration_date;
		$this->_o['expire'] = strtotime($expiration_date);
		
		$this->_o['mode'] = $this->_cookie_mode = $cookie_mode;
		$this->_o['secret'] = $this->_secret = $secret;
		$this->_o['encryptFunc'] = array('SecureCookie', 'encrypt');
		$this->_o['decryptFunc'] = array('SecureCookie', 'decrypt');

		if (empty($this->_o['secret'])) {
			throw new Exception('secret must be set in $options.');
		}
		
		$this->_hmac = new MrClay_Hmac($this->_o['secret'], $this->_o['hashAlgo']);
		
	}
	
	public static function encrypt($key, $string){
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);  
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);  
		$data = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_ECB, $iv);
		return base64_encode($data);
	}
	
	public static function decrypt($key, $data){
		if (false === ($data = base64_decode($data))) {
			return false;
		}
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);  
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);  
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB, $iv);
	}

	public function setOption($name, $value)
	{
		$this->_o[$name] = $value;
	}

	/**
	 * @return bool success
	 */
	public function store($name, $string)
	{
		return ($this->_o['mode'] === self::MODE_ENCRYPT)
			? $this->_storeEncrypted($name, $string)
			: $this->_store($name, $string);
	}
	
	private function _store($name, $string)
	{
		$time = base_convert($_SERVER['REQUEST_TIME'], 10, 36); // pack time
		// tie sig to this cookie name and timestamp
		list($val, $salt, $hash) = $this->_hmac->sign($name . $time . $string);
		
		$raw = $salt . '|' . $hash . '|' . $time . '|' . $string;
		if (strlen($name . $raw) > self::LENGTH_LIMIT) {
			$this->errors[] = 'Cookie is likely too large to store.';
			return false;
		}
		$res = setcookie($name, $raw, $this->_o['expire'], $this->_o['path'], 
						 $this->_o['domain'], $this->_o['secure']);
		if ($res) {
			return true;
		} else {
			$this->errors[] = 'Setcookie() returned false. Headers may have been sent.';
			return false;
		}
	}
	
	private function _storeEncrypted($name, $string)
	{
		if (! is_callable($this->_o['encryptFunc'])) {
			$this->errors[] = 'Encrypt function not callable';
			return false;
		}
		$time = base_convert($_SERVER['REQUEST_TIME'], 10, 36); // pack time
		
		// tie sig to this cookie name and timestamp
		list($val, $salt, $hash) = $this->_hmac->sign($name . $time . $string);
		
		$cryptKey = hash('ripemd160', $this->_o['secret'], true);
		$encrypted = call_user_func($this->_o['encryptFunc'], $cryptKey, '1' . $string);
		
		$raw = $salt . '|' . $hash . '|' . $time . '|' . $encrypted;
		if (strlen($name . $raw) > self::LENGTH_LIMIT) {
			$this->errors[] = 'Cookie is likely too large to store.';
			return false;
		}
		$res = setcookie($name, $raw, $this->_o['expire'], $this->_o['path'], 
						 $this->_o['domain'], $this->_o['secure']);
		if ($res) {
			return true;
		} else {
			$this->errors[] = 'Setcookie() returned false. Headers may have been sent.';
			return false;
		}
	}

	/**
	 * @return string null if cookie not set, false if tampering occured
	 */
	public function fetch($name)
	{
		if (!isset($_COOKIE[$name])) {
			return null;
		}
		return ($this->_o['mode'] === self::MODE_ENCRYPT)
			? $this->_fetchEncrypted($name)
			: $this->_fetch($name);
	}
	
	private function _fetch($name)
	{
		if (isset($this->_returns[self::MODE_VISIBLE][$name])) {
			return $this->_returns[self::MODE_VISIBLE][$name][0];
		}
		$cookie = get_magic_quotes_gpc()
			? stripslashes($_COOKIE[$name])
			: $_COOKIE[$name];
		$parts = explode('|', $cookie, 4);
		if (4 !== count($parts)) {
			$this->errors[] = 'Cookie was tampered with.';
			return false;
		}
		list($salt, $hash, $time, $string) = $parts;
		
		if (! $this->_hmac->isValid(array($name . $time . $string, $salt, $hash))) {
			$this->errors[] = 'Cookie was tampered with.';
			return false;
		}
		$time = base_convert($time, 36, 10); // unpack time
		$this->_returns[self::MODE_VISIBLE][$name] = array($string, $time);
		return $string;
	}
	
	private function _fetchEncrypted($name)
	{
		if (isset($this->_returns[self::MODE_ENCRYPT][$name])) {
			return $this->_returns[self::MODE_ENCRYPT][$name][0];
		}
		if (! is_callable($this->_o['decryptFunc'])) {
			$this->errors[] = 'Decrypt function not callable';
			return false;
		}
		$cookie = get_magic_quotes_gpc()
			? stripslashes($_COOKIE[$name])
			: $_COOKIE[$name];
		$parts = explode('|', $cookie, 4);
		if (4 !== count($parts)) {
			$this->errors[] = 'Cookie was tampered with.';
			return false;
		}
		list($salt, $hash, $time, $encrypted) = $parts;
		
		$cryptKey = hash('ripemd160', $this->_o['secret'], true);
		$string = call_user_func($this->_o['decryptFunc'], $cryptKey, $encrypted);
		if (! $string) {
			$this->errors[] = 'Cookie was tampered with.';
			return false;
		}
		$string = substr($string, 1); // remove leading "1"
		$string = rtrim($string, "\x00"); // remove trailing null bytes
		
		if (! $this->_hmac->isValid(array($name . $time . $string, $salt, $hash))) {
			$this->errors[] = 'Cookie was tampered with.';
			return false;
		}
		$time = base_convert($time, 36, 10); // unpack time
		$this->_returns[self::MODE_ENCRYPT][$name] = array($string, $time);
		return $string;
	}

	public function getTimestamp($name)
	{
		if (is_string($this->fetch($name))) {
			return $this->_returns[$this->_o['mode']][$name][1];
		}
		return false;
	}

	public function delete($name)
	{
		setcookie($name, '', time() - 3600, $this->_o['path'], $this->_o['domain'], $this->_o['secure']);
	}
	
	/**
	 * @var array options
	 */
	private $_o;

	private $_returns = array();

}




/**
 * Store tamper-proof strings in an HTTP cookie
 * 
 * Requires MrClay_Hmac (and MrClay_Rand)
 *
 * <code>
 * $storage = new MrClay_CookieStorage(array(
 *	 'secret' => '67676kmcuiekihbfyhbtfitfytrdo=op-p-=[hH8'
 * ));
 * if ($storage->store('user', 'id:62572,email:bob@yahoo.com,name:Bob')) {
 *	// cookie OK length and no complaints from setcookie()
 * } else {
 *	// check $storage->errors
 * }
 * 
 * // later request
 * $user = $storage->fetch('user');
 * if (is_string($user)) {
 *	// valid cookie
 *	$age = time() - $storage->getTimestamp('user');
 * } else {
 *	 if (false === $user) {
 *		 // data was altered!
 *	 } else {
 *		 // cookie not present
 *	 }
 * }
 * 
 * // encrypt cookie contents
 * $storage = new MrClay_CookieStorage(array(
 *	 'secret' => '67676kmcuiekihbfyhbtfitfytrdo=op-p-=[hH8'
 *	 ,'mode' => MrClay_CookieStorage::MODE_ENCRYPT
 * ));
 * </code>
 * 
 * @author Steve Clay <steve@mrclay.org>
 * @license http://www.opensource.org/licenses/mit-license.php  MIT License
 */



class MrClay_Hmac {

    protected $_rand;
    protected $_secret;
    protected $_hashAlgo = 'sha256';

    /**
     * iterations to perform during key derivation
     *
     * @var int
     */
    protected $_iterations = 5000;

    /**
     * @param string $secretKey
     * 
     * @param string $hashAlgo 
     * 
     * @param MrClay_Rand $rand
     */
    public function __construct($secretKey, $hashAlgo = 'sha256', MrClay_Rand $rand = null) 
    {
        if (! $rand) {
            $rand = new MrClay_Rand();
        }
        $this->_rand = $rand;
        $this->setSecret($secretKey);
        $this->setHashAlgo($hashAlgo);
    }

    /**
     * Create an array containing the value given, a salt, and a hash created with the key.
     * 
     * @param mixed $val
     * 
     * @param int $saltLength
     * 
     * @return array [value, salt, hash]
     */
    public function sign($val, $saltLength = 16)
    {
        $origVal = $val;
        if (! is_string($val)) {
            $val = serialize($val);
        }
        $salt = $this->createSalt($saltLength);
        $hash = $this->_digest($val, $salt);
        return array($origVal, $salt, $hash);
    }
    
    /**
     * Was the first value in the array likely passed to sign()?
     * 
     * Caveat: If your value's serialization is not deterministic, validation may fail.
     * 
     * @param array $valueSaltHash [value, salt, hash]
     * 
     * @return bool 
     */
    public function isValid(array $valueSaltHash)
    {
        list($val, $salt, $hash) = $valueSaltHash;
        if (! is_string($val)) {
            $val = serialize($val);
        }
        return ($hash === $this->_digest($val, $salt));
    }
    
    /**
     * Create a string of random chars within [a-z A-z - _]
     * 
     * @param int $length length of output string
     * 
     * @return string
     */
    public function createSalt($length)
    {
        return $this->_rand->getUrlSafeChars($length);
    }
    
    /**
     * Set the secret from which a key will be derived
     * 
     * @param type $secret
     *
     * @return self
     */
    public function setSecret($secret)
    {
        $this->_secret = $secret;
        return $this;
    }

    public function setIterations($numIterations = 5000)
    {
        $this->_iterations = $numIterations;
    }

    /**
     * @param string $algo
     *
     * @return MrClay_Hmac
     *
     * @throw InvalidArgumentException
     */
    public function setHashAlgo($algo)
    {
        $algo = strtolower($algo);
        if (! in_array($algo, hash_algos())) {
            throw new InvalidArgumentException("Hash algorithm '$algo' unsupported.");
        }
        $this->_hashAlgo = $algo;
        return $this;
    }

    /**
     * @return string
     */
    public function getHashAlgo()
    {
        return $this->_hashAlgo;
    }

    /**
     * Derive a key via PBKDF2 (described in RFC 2898)
     *
     * @param string $p password
     * @param string $s salt
     * @param int $c iteration count (use 1000 or higher)
     * @param int $kl derived key length
     * @param string $a hash algorithm
     *
     * @return string derived key
     *
     * @author Andrew Johnson
     * @link http://www.itnewb.com/v/Encrypting-Passwords-with-PHP-for-Storage-Using-the-RSA-PBKDF2-Standard
    */
    function pbkdf2($p, $s, $c, $kl, $a = 'sha256') {
        $hl = strlen(hash($a, null, true)); // Hash length
        $kb = ceil($kl / $hl);              // Key blocks to compute
        $dk = '';                           // Derived key
        // Create key
        for ($block = 1; $block <= $kb; $block++) {
            // Initial hash for this block
            $ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);
            // Perform block iterations
            for ($i = 1; $i < $c; $i++) {
                // XOR each iterate
                $ib ^= ($b = hash_hmac($a, $b, $p, true));
            }
            $dk .= $ib; // Append iterated block
        }
        // Return derived key of correct length
        return substr($dk, 0, $kl);
    }
    
    /**
     * base 64 encoding with URL-safe chars and no padding (=)
     * 
     * @link http://en.wikipedia.org/wiki/Base64#URL_applications
     * 
     * @param string $data
     * 
     * @return string
     */
    public static function base64urlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * base 64 decoding with URL-safe chars and no padding (=)
     *
     * @link http://en.wikipedia.org/wiki/Base64#URL_applications
     *
     * @param string $str
     *
     * @return string
     */
    public static function base64urlDecode($str)
    {
        return base64_decode(strtr($str, '-_', '+/'));
    }
    
    /**
     * Get a hash of a string with the key and salt
     * 
     * @param string $val
     *
     * @param string $salt
     * 
     * @return string 
     */
    protected function _digest($val, $salt)
    {
        $key = $this->pbkdf2($this->_secret, $salt, $this->_iterations, 32);
        $hash = hash_hmac($this->_hashAlgo, $val, $key, true);
        return rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    }
}

/**
 * @deprecated use MrClay\Crypt\ByteString::rand()
 *
 *
 *
 * Random char/byte generator based on PHPass
 * 
 * @link http://www.openwall.com/phpass/
 * 
 * @author Steve Clay <steve@mrclay.org>
 * @license http://www.opensource.org/licenses/mit-license.php  MIT License
 */
class MrClay_Rand {
    
    /**
     * Create string of random bytes (from PHPass)
     * 
     * @param int $numBytes
     * 
     * @return string
     */
    public function getBytes($numBytes)
    {
        // generate random bytes (adapted from phpass)
        $randomState = microtime();
        if (function_exists('getmypid')) {
            $randomState .= getmypid();
        }
        $bytes = '';
        if (@is_readable('/dev/urandom') && ($fh = @fopen('/dev/urandom', 'rb'))) {
            $bytes = fread($fh, $numBytes);
            fclose($fh);
        }
        if (strlen($bytes) < $numBytes) {
            $bytes = '';
            for ($i = 0; $i < $numBytes; $i += 16) {
                $randomState = md5(microtime() . $randomState . mt_rand(0, mt_getrandmax()));
                $bytes .= pack('H*', md5($randomState));
            }
            $bytes = substr($bytes, 0, $numBytes);
        }
        
        return $bytes;
    }
    
    /**
     * Create string of random URL-safe chars
     * 
     * @link http://en.wikipedia.org/wiki/Base64#URL_applications
     * 
     * @param int $numChars
     * 
     * @return string
     */
    public function getUrlSafeChars($numChars)
    {
        $bytes = $this->getBytes($numChars);
        return substr(rtrim(strtr(base64_encode($bytes), '+/', '-_'), '='), 0, $numChars);
    }
}
