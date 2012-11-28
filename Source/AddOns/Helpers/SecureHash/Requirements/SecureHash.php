<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// class adapted from code here: https://defuse.ca/php-pbkdf2.htm

/*
 * Password hashing with PBKDF2.
 * Author: havoc AT defuse.ca
 * www: https://defuse.ca/php-pbkdf2.htm
 */

class SecureHash{
	
	private 
		$_hash_algorithm = 'sha256',
		$_iterations = 4000,
		$_salt_bytes = 32,
		$_hash_bytes = 32,
		
		$_sections = 4, 
		$_algorithm_index = 0, 
		$_iteration_index = 1, 
		$_salt_index = 2,
		$_makeHash_index = 3;
	
	
	public function __construct($debug, $hash_algorithm, $iterations, $salt_bytes, $hash_bytes){
		$this->debug = $debug;
		$this->hash_algorithm = $hash_algorithm;
		$this->iterations = $iterations;
		$this->salt_bytes = $salt_bytes;
		$this->hash_bytes = $hash_bytes;
	}
	
	public function generateSecureHash($password)
	{
		// format: algorithm:iterations:salt:hash
		$salt = base64_encode(mcrypt_create_iv($this->salt_bytes, MCRYPT_DEV_URANDOM));
		return $this->hash_algorithm . ':' . $this->iterations . ':' .  $salt . ':' . 
			base64_encode(self::makeHash(
				$this->hash_algorithm,
				$password,
				$salt,
				$this->iterations,
				$this->hash_bytes,
				true
			));
	}
	
	public function validatePassword($password, $good_hash)
	{
		$params = explode(':', $good_hash);
		if(count($params) < $this->_sections)
		   return false; 
		$makeHash = base64_decode($params[$this->_makeHash_index]);
		return self::checkHash(
			$makeHash,
			self::makeHash(
				$params[$this->_algorithm_index],
				$password,
				$params[$this->_salt_index],
				(int)$params[$this->_iteration_index],
				strlen($makeHash),
				true
			)
		);
	}
	
	// Compares two strings $a and $b in length-constant time.
	private function checkHash($a, $b)
	{
		$diff = strlen($a) ^ strlen($b);
		for($i = 0; $i < strlen($a) && $i < strlen($b); $i++)
		{
			$diff |= ord($a[$i]) ^ ord($b[$i]);
		}
		return $diff === 0; 
	}
	
	/*
	 * PBKDF2 key derivation public function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
	 * $algorithm - The hash algorithm to use. Recommended: SHA256
	 * $password - The password.
	 * $salt - A salt that is unique to the password.
	 * $count - Iteration count. Higher is better, but slower. Recommended: At least 1000.
	 * $key_length - The length of the derived key in bytes.
	 * $raw_output - If true, the key is returned in raw binary format. Hex encoded otherwise.
	 * Returns: A $key_length-byte key derived from the password and salt.
	 *
	 * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
	 *
	 * This implementation of PBKDF2 was originally created by https://defuse.ca
	 * With improvements by http://www.variations-of-shadow.com
	 */
	private function makeHash($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
	{
		$algorithm = strtolower($algorithm);
		if(!in_array($algorithm, hash_algos(), true))
			die('PBKDF2 ERROR: Invalid hash algorithm.');
		if($count <= 0 || $key_length <= 0)
			die('PBKDF2 ERROR: Invalid parameters.');
	
		$hash_length = strlen(hash($algorithm, '', true));
		$block_count = ceil($key_length / $hash_length);
	
		$output = '';
		for($i = 1; $i <= $block_count; $i++) {
			// $i encoded as 4 bytes, big endian.
			$last = $salt . pack('N', $i);
			// first iteration
			$last = $xorsum = hash_hmac($algorithm, $last, $password, true);
			// perform the other $count - 1 iterations
			for ($j = 1; $j < $count; $j++) {
				$xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
			}
			$output .= $xorsum;
		}
	
		if($raw_output)
			return substr($output, 0, $key_length);
		else
			return bin2hex(substr($output, 0, $key_length));
	}
	
}








