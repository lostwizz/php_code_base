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

	/** -----------------------------------------------------------------------------------------------
	 * format a number in a currency format (i.e with dollar sign)
	 * @param type $val
	 * @param type $leftPadding
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return type
	 */
	public static function ShowMoney($val, $leftPadding = 10, $arOptions = null, $arStyle = null) {
		//$mon = sprintf( '%10.2f', $val);
		$mon = '$' . number_format(round($val, 2), 2, '.', ',');
		$mon = str_pad($mon, $leftPadding, ' ', STR_PAD_LEFT);
		$mon = str_replace(' ', '&nbsp', $mon);
		return $mon;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $dir
	 * @param type $prefix
	 * @return type
	 */
	public static function GiveTempFileName($dir, $prefix = 'temp_') {
//		do{
//			$seed = substr(md5(microtime() ) , 0, 8);
//			$filename = $dir . $trailing_slash . $prefix . $seed . $postfix;
//		} while (file_exists($filename));
//		$fp = fopen($filename, "w");
//		fclose($fp);
		$filename = tempnam($dir, $prefix);
		return $filename;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $aTime
	 */
	public static function PrettyName($aTime) {

	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $withTime
	 * @return type
	 */
	public static function GiveCurrentDate($withTime = false) {
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
	public static function makeRandomPassword($length = 8) : string {
		$pass = "";
		$salt = "abchefghjkmnpqrstuvwxyz0123456789ABCHEFGHJKMNPQRSTUVWXYZ0123456789";
		srand((double)microtime()*1000314);
		$i = 0;
		while ($i <= $length) {
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
	public static function startsWith ($string, $startString) :bool {
		$len = strlen($startString);
		return (substr($string, 0, $len) === $startString);
	}

}
