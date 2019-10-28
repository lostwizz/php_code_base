<?php
//**********************************************************************************************
//* myCryption.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-12 09:46:20 -0700 (Thu, 12 Sep 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 12-Sep-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************


// mostly from https://www.the-art-of-web.com/php/two-way-encryption/


// other interesting sites:
//			https://github.com/defuse/php-encryption
//			https://deliciousbrains.com/php-encryption-methods/



namespace php_base\utils\myCryption;


Class myCryption{


	protected $method = 'aes-128-ctr'; // default cipher method if none supplied
	protected $key;

//	function __construct() {
//		if(defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
//			//echo "CRYPT_BLOWFISH is enabled!";
//		} else {
//			echo "CRYPT_BLOWFISH is NOT enabled!";
//			throw new \BadMethodCallException("Blowfish is not available");
//		}
//	}



	//-----------------------------------------------------------------------------------------------
	public function __construct($key = FALSE, $method = FALSE) {
		if(!$key) {
			$key = php_uname(); // default encryption key if none supplied
		}
		if(ctype_print($key)) {
	  		// convert ASCII keys to binary format
			$this->key = openssl_digest($key, 'SHA256', TRUE);
		} else {
			$this->key = $key;
		}
		if($method) {
			if(in_array(strtolower($method), openssl_get_cipher_methods())) {
				$this->method = $method;
	  		} else {
				die(__METHOD__ . ": unrecognised cipher method: {$method}");
			}
		}
  	}

	//-----------------------------------------------------------------------------------------------
	protected function iv_bytes() {
		return openssl_cipher_iv_length($this->method);
	}

	//-----------------------------------------------------------------------------------------------
	public function encrypt($data) {
		$iv = openssl_random_pseudo_bytes($this->iv_bytes());
		return bin2hex($iv) . openssl_encrypt($data, $this->method, $this->key, 0, $iv);
	}

	//-----------------------------------------------------------------------------------------------
	// decrypt encrypted string
	public function decrypt($data) {
		$iv_strlen = 2  * $this->iv_bytes();
		if(preg_match("/^(.{" . $iv_strlen . "})(.+)$/", $data, $regs)) {
			list(, $iv, $crypted_string) = $regs;
			if(ctype_xdigit($iv) && strlen($iv) % 2 == 0) {
				return openssl_decrypt($crypted_string, $this->method, $this->key, 0, hex2bin($iv));
			}
		}
		return FALSE; // failed to decrypt
	}



	//-----------------------------------------------------------------------------------------------
	// FROM https://gist.github.com/niczak/2501891
    public  function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

	//-----------------------------------------------------------------------------------------------
    public function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }





	protected $nonce;
	protected $secret_key;
	protected $block_size;
	protected $messageAuthenticationCode;


	public function sodium_GetMessageAuthenticationCode() {
		return $this->messageAuthenticationCode;
	}

	// $secret_key needs to be 32 bytes long
	public function sodium_init( $secret_key=null){
		if ( empty($secret_key)){
			$secret_key = sodium_crypto_secretbox_keygen();
		}
		$this->block_size = 16;
		$this->secret_key = $secret_key;
		$this->nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
		return $secret_key;
	}

	public function sodium_encrypt( $message, $secret_key=null){

		$secret = empty($secret_key) ? $this->secret_key : $secret_key;

		$padded_message = sodium_pad($message, $this->block_size);

		$this->messageAuthenticationCode  = sodium_crypto_auth($message, $secret);
		$encrypted_message = sodium_crypto_secretbox($padded_message, $this->nonce, $secret);
		return $encrypted_message;
	}

	public function sodium_decrypt( $encrypted_message, $secret_key=null){
		$secret = empty($secret_key) ? $this->secret_key : $secret_key;

		$decrypted_padded_message = sodium_crypto_secretbox_open($encrypted_message, $this->nonce, $secret);

		$decrypted_message = sodium_unpad($decrypted_padded_message, $this->block_size);
		return $decrypted_message;
	}

	public function sodium_authenticate($sodium_mac, $message, $key ){
		return sodium_crypto_auth_verify( $sodium_mac, $message, $key);
	}


//	function crypt($input, $rounds = 7) {
//		$salt = "";
//		$salt_chars = array_merge(range('A','Z'), range('a','z'), range(0,9));
//		for($i=0; $i < 22; $i++) {
//			$salt .= $salt_chars[array_rand($salt_chars)];
//		}
//		return $this->crypt($input, sprintf('$2y$%02y$', $rounds) . $salt);
//	}

////
////	//-----------------------------------------------------------------------------------------------
////	function basicEnCrypt( $token){
////		//$token = "The quick brown fox jumps over the lazy dog.";
////
////		$cipher_method = 'aes-128-ctr';
////		$enc_key = openssl_digest(php_uname(), 'SHA256', TRUE);
////		$enc_iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_method));
////		$crypted_token = openssl_encrypt($token, $cipher_method, $enc_key, 0, $enc_iv) . "::" . bin2hex($enc_iv);
////		unset($token, $cipher_method, $enc_key, $enc_iv);
////		return $crypted_token;
////	}
////
////	//-----------------------------------------------------------------------------------------------
////	function basicDeCrypt($crypted_token){
////		list($crypted_token, $enc_iv) = explode("::", $crypted_token);;
////		$cipher_method = 'aes-128-ctr';
////		$enc_key = openssl_digest(php_uname(), 'SHA256', TRUE);
////		$token = openssl_decrypt($crypted_token, $cipher_method, $enc_key, 0, hex2bin($enc_iv));
////		unset($crypted_token, $cipher_method, $enc_key, $enc_iv);
////		return $token;
////	}
////
////	//-----------------------------------------------------------------------------------------------
////    //protected
////    function mySimpleCrypt( string $s , $action = 'e', $secret_key = 'my_simple_secret_key', $secret_iv = 'my_simple_secret_iv' ){
////
////		//$secret_key = 'my_simple_secret_key';
////		//$secret_iv = 'my_simple_secret_iv';
////
////		$output = false;
////		$encrypt_method = "AES-256-CBC";
////		$key = hash( 'sha256', $secret_key );
////		$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
////
////		if( $action == 'e' ) {
////			$output = base64_encode( openssl_encrypt( $s, $encrypt_method, $key, 0, $iv ) );
////		}
////		else if( $action == 'd' ){
////			$output = openssl_decrypt( base64_decode( $s ), $encrypt_method, $key, 0, $iv );
////		}
////
////		return $output;
////	}
}
