<?php

namespace Tests\Test;
use PHPUnit\Framework\TestCase;



//use \php_base\Settings\Settings as Settings;
//use \php_base\Utils\Dump\Dump as Dump;
//use \php_base\Utils\Dump\DumpExtendedClass as DumpExtendedClass;

use \php_base\utils\Cryption\Cryption as Cryption;







//$key = random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
//echo $key;
//$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
//$ciphertext = sodium_crypto_secretbox('This is a secret!', $nonce, $key);
//


//$sodium = new Cryption();
//
//$key = $sodium->sodium_init();
//Dump::dump(  $sodium->safe_b64encode( $key));
//
//$s = 'This is some message to be encrypted and then decrypted for testng';
//$ex = $sodium->sodium_encrypt( $s);
//
//$macauth = $sodium->sodium_GetMessageAuthenticationCode();
//
//
//
//Dump::dump(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
////Dump::dump($key);
////echo SODIUM_CRYPTO_SECRETBOX_KEYBYTES, '-' ,$key, '<Br>';
//echo $s, '<Br>';
////echo $ex, '<Br>';
//$ex64 = $sodium->safe_b64encode( $ex);
//echo $ex64, '<Br>';
//
//$x =  $sodium->safe_b64decode( $ex64);
//$orig = $sodium->sodium_decrypt( $x);
//echo $orig , '<Br>';
//
//
//$auth= $sodium->sodium_authenticate( $macauth, $s, $key);
//
//dump::dump($auth ?'t':'F');
//$s .= 'FRED WAS HERE';
//$auth= $sodium->sodium_authenticate( $macauth, $s, $key);
//dump::dump($auth ?'t':'F');
//
//
//Dump::dump(bin2hex($macauth));
//
//$newS = random_bytes(SODIUM_CRYPTO_AUTH_BYTES);
//Dump::dump(bin2hex($newS));
//


//$en = Settings::mySimpleCrypt( "This is some random string" ,'e');
//Dump::dump( $en);
//
//$un = Settings::mySimpleCrypt( $en ,'d');
//Dump::dump($un);





class Cryption_Test extends TestCase{

	function test_encrypt():void {
		$encryptionClass = new Cryption();
		$str = 'This is some message to be encrypted and then decrypted for testng';
		$ex  = $encryptionClass->encrypt($str);
		$out = $encryptionClass->decrypt($ex);

		$this->assertEquals($out, $str);
	}

	function test_safe_b64():void{
		$encryptionClass = new Cryption();
		$str = 'This is some message to be encrypted and then decrypted for testng';

		$b64 = $encryptionClass->safe_b64encode( $str);
		$out = $encryptionClass->safe_b64decode($b64);
		$this->assertEquals($out, $str);
	}

	function test_sodium():void {
		$sodium = new Cryption();
		$key = $sodium->sodium_init();

		$s = 'This is some message to be encrypted and then decrypted for testng';
		$ex = $sodium->sodium_encrypt( $s);
		$out = $sodium->sodium_decrypt( $ex);
		$this->assertEquals( $out, $s);

		$macauth = $sodium->sodium_GetMessageAuthenticationCode();

		$out= $sodium->sodium_authenticate( $macauth, $s, $key);
		$this->assertTrue($out);

		$newS = $s . 'FRED WAS HERE';
		$out= $sodium->sodium_authenticate( $macauth, $newS, $key);
		$this->assertFalse($out);

		$newMacauth =  random_bytes(SODIUM_CRYPTO_AUTH_BYTES);
		$out= $sodium->sodium_authenticate( $newMacauth, $s, $key);
		$this->assertFalse($out);


	}
}


//$sodium = new Cryption();
//
//$key = $sodium->sodium_init();
//Dump::dump(  $sodium->safe_b64encode( $key));
//
//$s = 'This is some message to be encrypted and then decrypted for testng';
//$ex = $sodium->sodium_encrypt( $s);
//
//$macauth = $sodium->sodium_GetMessageAuthenticationCode();
//
//
//
//Dump::dump(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
////Dump::dump($key);
////echo SODIUM_CRYPTO_SECRETBOX_KEYBYTES, '-' ,$key, '<Br>';
//echo $s, '<Br>';
////echo $ex, '<Br>';
//$ex64 = $sodium->safe_b64encode( $ex);
//echo $ex64, '<Br>';
//
//$x =  $sodium->safe_b64decode( $ex64);
//$orig = $sodium->sodium_decrypt( $x);
//echo $orig , '<Br>';
//
//
//$auth= $sodium->sodium_authenticate( $macauth, $s, $key);
//
//dump::dump($auth ?'t':'F');
//$s .= 'FRED WAS HERE';
//$auth= $sodium->sodium_authenticate( $macauth, $s, $key);
//dump::dump($auth ?'t':'F');
//
