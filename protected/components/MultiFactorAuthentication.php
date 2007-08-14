<?php
/** 
 * Class for multi-factor authentication
 *
 * @author _uJJwAL_
 * @copyright 2014
 *
 *
 */

class MultiFactorAuthentication {
	/**
	 * Key Length 
	 * @var int $keyLength
	 */
	private $keyLength = 11;

	/**
	 * Code length
	 * @var int $codeLength
	 */
	private $codeLength = 6;

	/**
	 * Iteration
	 * @var int $iteration
	 */
	private $iteration = 90;

	/**
	 * Generate the key for multi-factor authentication
	 * @param string $length
	 * @return string $key
	 */
	private function generateKey($length) {
		$key = "";
	    $codeAlphabet  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	    $codeAlphabet .= "0123456789";
	   
	    for($i=0; $i<$length; $i++){
	        $key .= $codeAlphabet[$this -> crypto_rand_secure(0,strlen($codeAlphabet))];
	    }

	    return $key;
	}

	/** 
	 * @link http://us1.php.net/manual/en/function.openssl-random-pseudo-bytes.php#104322
	 * 
	 */
	private function crypto_rand_secure($min, $max) {
        $range = $max - $min;

        if ($range < 0) return $min; // not so random...
        
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        
        return $min + $rnd;
	}

	/**
	 * Generate the pseudo random Key
	 * @return string $key
	 */
	public function getMultiFactorKey() {
		$key = $this -> generateKey($this -> keyLength);

		return $key;
	}

	/**
	 * Generates the hotp value for the key
	 * @param string $key
	 * @return Array $hotpValue
	 */
	public function getHotpByKey($key) {
		$hotp = new HOTP;
		$hotpValue = array();

		for($i = 0; $i < $this -> iteration; $i++) {
			array_push($hotpValue, $hotp -> generateByCounter($key, $i) -> toHotp($this -> codeLength));
		}

		return $hotpValue;
	}

	/**
	 * Get hotp value based on the key and index
	 * @return string $hotpvalue
	 */
	public function getHotpByKeyIndex($key, $index) {
		$hotp = new HOTP;

		$hotpValue = $hotp -> generateByCounter($key, $index) -> toHotp($this -> codeLength);

		return $hotpValue;
	}
}
