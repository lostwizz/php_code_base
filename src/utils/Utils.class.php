<?php

/** * ********************************************************************************************
 * Utils.class.php
 *
 * Summary: static utility functions
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description:
 * 	static utility functions
 *
 *
 *
 * @package utils
 * @subpackage utils
 * @since 0.3.0
 *
 * @example
 *
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\Utils;

use \php_base\Utils\Dump\Dump as Dump;

/**
 *
 */
abstract Class Utils {

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * requried			actual		result
	 * 1.0.0			1.0.0		true
	 * 1.0.0			0.99.0		false
	 * 1.0.0			1.99.0		true
	 * 1.0.0
	 * @param string $requiredVersion
	 * @param string $actualVersion
	 * @return bool
	 */
	public static function isVersionGood( string $requiredVersion, string $actualVersion) : bool {
		$required = \explode('.', $requiredVersion);
		$actual = \explode('.', $actualVersion);
		if ( $required[0] > $actual[0]) {
			return false;
		}
		if ( $required[1] > $actual[1]) {
			return false;
		}
		return true;
	}





	/** -----------------------------------------------------------------------------------------------
	 * format a number in a currency format (i.e with dollar sign)
	 * @param type $val
	 * @param type $leftPadding
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return type
	 */
	public static function ShowMoney($val, int $leftPadding = 10 ) :string{
		//$mon = sprintf( '%10.2f', $val);
		$mon = '$' . number_format(round($val, 2), 2, '.', ',');
		$mon = str_pad($mon, $leftPadding, ' ', STR_PAD_LEFT);
		$mon = str_replace(' ', '&nbsp;', $mon);
		return $mon;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * Note: Windows uses only the first three characters of prefix.
	 *
	 * @param type $dir
	 * @param type $prefix
	 * @return type
	 */
	public static function GiveTempFileName(string $dir, string $prefix = 'tmp') :string {
//		do{
//			$seed = substr(md5(microtime() ) , 0, 8);
//			$filename = $dir . $trailing_slash . $prefix . $seed . $postfix;
//		} while (file_exists($filename));
//		$fp = fopen($filename, "w");
//		fclose($fp);
		$filename = \tempnam($dir, $prefix);
		return $filename;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $aTime
	 */
/*	public static function PrettyTime($aTime) {

	}
*/
	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $withTime
	 * @return type
	 */
	public static function GiveCurrentDate( bool $withTime = false) :string {
		if ($withTime) {
			$r = date('g:ia d-M-Y');
		} else {
			$r = date('d-M-Y');
		}
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
		//generate a new password
	 *
	 * @param type $length
	 * @return string
	 */
	public static function makeRandomPassword( int $length = 8) : string {
		$pass = "";
		$salt = "abchefghjkmnpqrstuvwxyz0123456789ABCHEFGHJKMNPQRSTUVWXYZ0123456789";
		srand((double)microtime()*1000314);
		$i = 0;
		while ($i < $length) {
			$num = rand() % strlen($salt);
			$tmp = substr($salt, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $string
	 * @param type $startString
	 * @return bool
	 */
	public static function startsWith (string $string, string $startString, bool $ignoreCase = false) :bool {
		$len = strlen($startString);
		if ($len <1) {
			return false;
		}
		if ( $ignoreCase){
			return (substr(strtolower($string), 0, $len) === strtolower($startString));
		} else {
			return (substr($string, 0, $len) === $startString);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $bt
	 * @param int $whichFrame
	 * @return string
	 */
	public static function backTraceHelper(array $bt, int $whichFrame = 1) :string {
		//$s = $bt[$whichFrame]['function']
		$s = '';
		$s .= 'Class: <B>'. $bt[$whichFrame]['class'] . '</B>';
		$s .= ' Called: <B>'. $bt[$whichFrame]['function']. '</B>';
		$s .= ' in: <B>' . basename($bt[$whichFrame]['file']) . '<B>';
		$s .= ' (line: <B>' . $bt[$whichFrame]['line'] .'</B>)';

		//$s .= '--'  . __METHOD__ .  '-- called from ' . $bt['file'] . '(line: '. $bt['line'] . ')' ;
		return $s;
	}




}


