<?php
//**********************************************************************************************
//* settings.class.php
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
//echo '<pre> getPublic-->';
//print_r($key);
//print_r(self::$public);
//echo '</pre>';



namespace php_base\Utils;

use \php_base\Utils\myCryption\myCryption as myCryption;

use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\myNullAbsorber as myNullAbsorber;


//Dump::dump(require_once(DIR . 'utils' . DS . 'myNullAbsorber.class.php'));



///echo 'xxxxx"', __NAMESPACE__, '"'; // outputs "MyProject"

//***********************************************************************************************
//***********************************************************************************************
abstract class Settings
{
    static protected $protected = array(); // For DB / passwords etc
    static protected $public = array(); // For all public strings such as meta stuff for site
    static protected $runTime = array();  // for runtime vars that will realyonly exist when running

	public const INI_RESTORE_OVERWRITE =1;
	//static public INI_RESTORE_ONLYNEW = 'n';


	//-----------------------------------------------------------------------------------------------
	public static function hasProtectedKey($key){
		return array_key_exists( $key, $protected);
	}

	//-----------------------------------------------------------------------------------------------
	public static function hasRunTimeKey($key){
		return array_key_exists( $key, $runTime);
	}

	//-----------------------------------------------------------------------------------------------
	public static function hasPublicKey($key){
		return array_key_exists( $key, $public);
	}

	//-----------------------------------------------------------------------------------------------
	public static function getProtectedObject($key){
		return isset(self::$protected[$key]) ? self::$protected[$key] : new myNullAbsorber();
	}

	public static function getPublicObject($key){
		return isset(self::$public[$key]) ? self::$public[$key] : new myNullAbsorber();
	}

	//-----------------------------------------------------------------------------------------------
	public static function getRunTimeObject($key){
//dump::dump(self::$runTime[$key]);
		return isset(self::$runTime[$key]) ? self::$runTime[$key] : new myNullAbsorber();
	}

	//-----------------------------------------------------------------------------------------------
    public static function getProtected($key) {
        return isset(self::$protected[$key]) ? self::$protected[$key] : false;
    }

	//-----------------------------------------------------------------------------------------------
    public static function getPublic($key) {
        return isset(self::$public[$key]) ? self::$public[$key] : false;
    }

	//-----------------------------------------------------------------------------------------------
    public static function getRunTime($key) {
        return isset(self::$runTime[$key]) ? self::$runTime[$key] : false;
    }


	//-----------------------------------------------------------------------------------------------
    public static function setProtected($key,$value) {
        self::$protected[$key] = $value;
    }

	//-----------------------------------------------------------------------------------------------
    public static function setPublic($key,$value) {
        self::$public[$key] = $value;
    }

	//-----------------------------------------------------------------------------------------------
    public static function setRunTime($key,$value) {
        self::$runTime[$key] = $value;
    }

	//-----------------------------------------------------------------------------------------------
    public function __get($key){
    	//$this->key // returns public->key
        return isset(self::$public[$key]) ? self::$public[$key] : false;
    }

	//-----------------------------------------------------------------------------------------------
    public function __isset($key) {
        return isset(self::$public[$key]);
    }


	//-----------------------------------------------------------------------------------------------
    public static function dumpCR(bool $show_protected = false, bool $show_runtime = false) : string {
    	$s = self::dump( $show_protected, $show_runtime);
    	$r  = str_replace('<BR>', "\r\n", $s);
    	return $r;
	}

	//-----------------------------------------------------------------------------------------------
    public static function dumpBR(bool $show_protected = false, bool $show_runtime = false) : string {
    	$s = self::dump( $show_protected, $show_runtime);
    	$r  = str_replace("\n", '<BR>', $s);
    	return $r;
	}

	//-----------------------------------------------------------------------------------------------
	public static function dump(bool $show_protected = false, bool $show_runtime = false) : string {
		$s = 'Settings::dump:<BR>';
		if ( $show_runtime) {
			foreach( self::$runTime as $key => $val){
				$s .= 'RunTime: ' .  var_export( $key, true) . ' ==> ' . print_r( $val, true);
				$s .= '<Br>';
			}
		}
		if ( $show_protected) {
			foreach( self::$protected as $key => $val){
				$s .= 'Protected: ' .  var_export( $key, true) . ' ==> ' . print_r( $val, true);
				$s .= '<Br>';
			}
		}
		foreach( self::$public as $key => $val){
			$s .= 'Public: '  . var_export( $key, true) . ' ==> ' . print_r( $val, true);
			$s .= '<Br>';
		}
		//$s .= '<Br>';
		return $s;
	}



	//-----------------------------------------------------------------------------------------------
	protected static function giveINISetting($value) {
		switch (gettype($value)){
			case 'boolean':
				return $value ? '-True-' : '-False-';
			case 'NULL':
				return '-Null-';
			case 'object':
			case 'array':
				return serialize($value);
			default:
				return urlencode($value);

		}
	}

	//-----------------------------------------------------------------------------------------------
	public static function saveAsINI( $fn) {
		$s  = ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . "\n";
		$s .= ';;Written: ' . date( DATE_RFC822) . "\n";
		$s .= ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . "\n";

		$s .= self::getINIPublic();
		$s .= self::getINIProtected();

		$handle = fopen( $fn, 'w');
		fwrite( $handle, $s);
		fclose( $handle);
	}

	//-----------------------------------------------------------------------------------------------
	protected static function getINIPublic(){
		$s = "\n";
		$s .= '[PUBLIC]' . "\n";
    	foreach( self::$public as $key => $val){
    		//$s .= 'Public: '  . var_export( $key, true) . ' ==> ' . print_r( $val, true);
			$s .= $key . '=';
			$s .= self::giveINISetting( $val );

			$s .= "\n";
			$s .= "\n";
    	}
		return $s;
	}

	//-----------------------------------------------------------------------------------------------
	protected static function getINIProtected(){
		$encryptionClass = new myCryption();
		$s = "\n";
		$s .= '[PROTECTED]' . "\n";
		foreach( self::$protected as $key => $val){
			$s .=  $key . '=';

			$basicValue = self::giveINISetting( $val );
			$s_encrypted = $encryptionClass->encrypt( '_E_' . $basicValue);

			$s .= $s_encrypted;
			$s .= "\n";
			$s .= "\n";
		}
		return $s;
	}

	//-----------------------------------------------------------------------------------------------
	protected static function readINI( $fn){
		$encryptionClass = new myCryption();

		if ( file_exists( $fn)) {
			$a = parse_ini_file( $fn, true,   INI_SCANNER_RAW );
			//$b = self::decode_encrypted();
			$b = array();
			foreach( $a['PROTECTED'] as $key=> $val){
				$x = $encryptionClass->decrypt(	$val);
				if( substr($x, 0, 3) == '_E_') {          /// handle when string is not encrypted in the ini
					$s = substr($x, 3);
				} else {
					$s = $val;
				}
				$b[$key] = $s;
			}
			$a['PROTECTED'] = $b;
			return $a;
		}
		return false;
	}

//	protected static function decode_encrypted(){
//
//	}
	//-----------------------------------------------------------------------------------------------
	public static function destructiveINIRestore($fn) {
		$fromINI = self::readINI($fn);
		if ( is_array($fromINI)){
			self::$public = $fromINI['PUBLIC'];
			self::$protected = $fromINI['PROTECTED'];
		}
	}


	//-----------------------------------------------------------------------------------------------
	public static function nonDestructiveINIRestore($fn, $method =self::INI_RESTORE_OVERWRITE){
		$fromINI = self::readINI($fn);
		if ( is_array($fromINI)){
			foreach( $fromINI['PUBLIC'] as $key => $val){
				if( array_key_exists($key, self::$public))	{
					if ($method == self::INI_RESTORE_OVERWRITE){
						self::$public[$key] = $val;
					}
				} else {
					self::$public[$key] = $val;
				}
			}
			foreach( $fromINI['PROTECTED'] as $key => $val){
				if( array_key_exists($key, self::$protected))	{
					if ($method == self::INI_RESTORE_OVERWRITE){
						self::$protected[$key] = $val;
					}
				} else {
					self::$protected[$key] = $val;
				}

			}
		}
	}

//////	//-----------------------------------------------------------------------------------------------
//////	public static function  onlyKeysINIRestore($fn, $listOfKeys,  $method =self::INI_RESTORE_OVERWRITE){
//////		if (is_array($listOfKeys)) {
//////			$fromINI = self::readINI($fn);
//////				if ( is_array($fromINI)){
//////					foreach( $listOfKeys as $typeAndKey){
//////						list( $ty, $key) = $typeAndKey;
//////						switch (ty){
//////							case 'Protected':
//////								//if ()
//////								break;
//////							case 'Public':
//////							default:
//////
//////								break;
//////						}
//////					}
//////			}
//////		}
//////	}

}






////////
////////Class SettingValue{
////////	public $key;
////////	public $value;
////////	public $isSaveable;
////////
////////	private function __construct(){
////////		$this->key =null;
////////		$this->value = null;
////////		$this->isSavable = false;
////////	}
////////
////////	public function __set($value, $)
////////
////////	    public function __get($key){
////////    	//$this->key // returns public->key
////////        return isset(self::$public[$key]) ? self::$public[$key] : false;
////////    }
////////
////////	//-----------------------------------------------------------------------------------------------
////////    public function __isset($key) {
////////        return isset(self::$public[$key]);
////////    }
////////
////////}
