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
	 * makes sure we can find the class file for the a lookup
	 *     - should be in one of Control, Data, View, Model, Utils or DatabaseHandlers
	 *  - usually need when using a table - may cal controller model or direct to the data class
	 *     this function tries to figure out where it actually is for the instaniation     (i.e. "new $x($y)" -- tries to fully qualify $x
	 *
	 * @param type $class
	 * @return type
	 */
	public static function checkClass($class) : ?string{
		Settings::GetRunTimeObject('MessageLog')->addNotice('@@checkClass: ' . $class);

		$prefixes= ['',
			'php_base\Control\\',
			'php_base\Data\\',
			'php_base\View\\',
			'php_base\Model\\',
			'php_base\Utils\\',
			'php_base\Utils\DatabaseHandlers\\'
			];
		$suffixes= ['',
			'Data',
			'Controller',
			'Model',
			'View'
			];
		foreach( $prefixes as $prefix){
			if ( self::tryNameSpaceClass($prefix, $class)) {
				return $prefix  . $class;
			}
			foreach( $suffixes as $suffix){
				if ( self::tryNameSpaceClass($prefix, $class, $suffix)) {
					return $prefix  . $class . $suffix;
				}
			}
		}

		Settings::GetRunTimeObject('DISPATCHER_DEBUGGING')->addAlert( 'class doesnt exist: ' . $class);
		return null;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $prefix
	 * @param type $class
	 * @return type
	 */
	protected static function tryNameSpaceClass($prefix, $class, $suffix='') : string {
		Settings::GetRunTimeObject('MessageLog')->addNotice('@@TRYING:' . $prefix . ' - ' . $class);
		//echo '--Trying: ', $prefix . '\\' .  $class, '<BR>';


		/////////////$r = ( class_exists($prefix .  $class, true) ) ;
		$r =  ( class_exists($prefix .  $class .  $suffix, true) ) ;
		Settings::GetRunTimeObject('MessageLog')->addNotice('@@TRYING - result:'. ( $r ? 'exists' : 'doesnt exist'));
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	public static function makePTAPpretty($process, $task = '', $action = '', $payload = '') {

		$r = $process
				. ' . '
				. $task
				. ' . '
				. $action
				. ' . '
		;
		if (!empty($payload)) {
			$data = @unserialize($payload);
			if ($data === false) {
				$r .= @serialize($payload);
			} else {
				$r .= $payload;
			}
		}
		$r .= "<==";
		return $r;
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
			return (substr(strtolower($string), 0, $len) == strtolower($startString));
		} else {
			return (substr($string, 0, $len) == $startString);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $string
	 * @param string $endString
	 * @param bool $ignoreCase
	 * @return bool
	 */
	public static function endsWith( string $string, string $endString, bool $ignoreCase=false) :bool{
		$len = strlen($endString);
		if ( $len <1) {
			return false;
		}
		if ($ignoreCase){
			return ( substr( strtolower($string, (-1*$len) )) == strtolower($endString) );
		} else {
			return ( substr( $string, (-1*$len) ) == $endString );
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


		/** -----------------------------------------------------------------------------------------------
	 * set the timestamp - it does not have to be a time it can be any string
	 *      - if timestamp it must be formatted properly before getting here
	 * @param string $timeStamp
	 */
	public static function setTimeStamp(string $timeStamp = null) : string {
		if (defined("IS_PHPUNIT_TESTING")) {
			$this->timeStamp = '23:55:30';
			if (empty($timeStamp)) {
				return '23:55:30';
			} else {
				return $timeStamp;
			}
		} else {
			if (empty($timeStamp)) {
				return  date('g:i:s'); // current timestamp
			} else {
				return $timeStamp;
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * take an array and when you do a print_r it takes a lot of lines to show it all
	 * this function will attempt to shrink the contents of the subarrays down to a one line string
	 *     (if the key is in the $compress_elements array -- if not in that array then it keeps it and does nothing to it)
	 *     using implode (if simple strings)or using serialize if the sub array is more complicated
	 *  - you can also just ignore the sub array if the key exists in the $eliminate_elements array
	 *
	 * @param type $the_array
	 * @param array $compress_elements
	 * @param array $eliminate_elements
	 * @return type
	 */
	public static function array_display_compactor($the_array) : string {
		return wordwrap( serialize($the_array), 100, "<br />\n", true);
	}



//			, $compress_elements = array(), $eliminate_elements = array()) : string {
//		// make sure the two parameters are arrays - attempt to make them arrays if they are null -- give up if still not arrays
//		if (is_null($compress_elements)) {
//			$compress_elements = array();
//		}
//		if (is_null($eliminate_elements)) {
//			$eliminate_elements = array();
//		}
//		if (!is_array($compress_elements) or ! is_array($eliminate_elements)) {
//			return print_r($the_array, true);
//		}
//
//
//		// run thru the array and check if to include the sub element or just ignore it -- then if to compress the contents or not
//		$s = array();
//		if (is_array($the_array)) {
//			foreach ($the_array as $k => &$a) {
//				if (!!in_array($k, $eliminate_elements)) {
//
//					if (in_array($k, $compress_elements) and is_array($a)) {
//
//						// make sure the sub elements are simple strings with integer keys -- otherwise use serialize to show keys and such
//						$all_str = true;
//						foreach ($a as $sub_k => &$sub_a) {
//							if (!is_string($sub_a) or ! is_int($sub_k)) {
//								$all_str = false;
//								break;
//							}
//						}
//						if ($all_str) {
//							$s[$k] = implode(',', $a);
//						} else {
//							$s[$k] = serialize($a);
//						}
//					} else {
//						$s[$k] = $a; // not compressing it so just include it
//					}
//				}
//			}
//			// return the array as the printable expansion using print_r
//			return print_r($s, true);
//		} else {
//			return $the_array;
//		}
//	}

}


