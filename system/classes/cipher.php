<?php

class cipher {

	private $securekey;
	private $iv;

	function __construct() {
		$this->securekey = hash('sha256', Configure::read("security.cipher"), TRUE);
		$this->iv = mcrypt_create_iv(32, MCRYPT_DEV_URANDOM);
	}

	function encrypt($input) {
		return "cipher:".base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->securekey, $input, MCRYPT_MODE_ECB, $this->iv));
	}

	function decrypt($input) {
		if (substr($input,0,7) == "cipher:") {
			$input = substr($input, 7);
			return str_replace('\\\\', '\\', trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->securekey, base64_decode($input), MCRYPT_MODE_ECB, $this->iv)));
		} else { 
			return $input;
		}
	}

}

?>