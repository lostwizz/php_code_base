<?php

/** * ********************************************************************************************
 * myUtils.class.php
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
abstract Class myUtils {

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

	//-----------------------------------------------------------------------------------------------
}
