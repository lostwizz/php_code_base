<?php

/** * ********************************************************************************************
 * CacheHandler.class.php
 *
 * Summary: static class wrapper for the html code
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description: static wrapper for the html code
 *
 *
 *
 * @package utils
 * @subpackage Cache
 * @since 0.3.0
 *
 * @see  https://github.com/queued/HTML-Helper/blob/master/class.html.php
 *
 * @example
 *
 *
 * @todo Description
 *
 */
//**********************************************************************************************
//***********************************************************************************************************

namespace php_base\Utils;

use \php_base\Utils\Dump\Dump as Dump;


/** * ********************************************************************************************

 * Description of CacheHandler
 *
 * @author merrem
 */
Abstract class Cache {

	const DEFAULTTIMEOUTSECONDS = 600;

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $itemName
	 * @param type $data
	 * @param int $secondToTimeout
	 * @return bool
	 */
	public static function add(string $itemName, $data, int $secondToTimeout = DEFAULTTIMEOUTSECONDS): bool {

//		if ( ! self::doesSerializeWorkOnThisObject($data)) {
//			echo 'Sorry this object does not serialize';
//			return false;
//		}

		$now = (new \DateTime('now'))->getTimestamp();
		$timeoutStamp = $now + $secondToTimeout;
		$value = array('Data' => $data,
			'Expires' => $timeoutStamp
		);
		$_SESSION['CACHE'][$itemName] = $value;

		return true;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $itemName
	 * @param type $data
	 * @param int $secondToTimeout
	 * @return bool
	 */
	public static function addOrUpdate( string $itemName, $data, int $secondToTimeout = DEFAULTTIMEOUTSECONDS): bool {
		$now = (new \DateTime('now'))->getTimestamp();
		$timeoutStamp = $now + $secondToTimeout;
//		if ( ! self::doesSerializeWorkOnThisObject($data)) {
//			echo 'Sorry this object does not serialize';
//			return false;
//		}

		$_SESSION['CACHE'][$itemName]['Data'] = $data;
		$_SESSION['CACHE'][$itemName]['Expires'] = $timeoutStamp;
		return true;
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $itemName
	 * @return type
	 */
	public static function pull($itemName) {
		$now = (new \DateTime('now'))->getTimestamp();

		if ( !empty( $_SESSION['CACHE'][$itemName])  and $_SESSION['CACHE'][$itemName]['Expires'] > $now) {
			return $_SESSION['CACHE'][$itemName]['Data'];
		} elseif ( !empty( $_SESSION['CACHE'][$itemName])  and $_SESSION['CACHE'][$itemName]['Expires'] < $now) {
			unset ($_SESSION['CACHE'][$itemName]);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param \php_base\Utils\sting $itemName
	 * @return boolean
	 */
	public static function delete(sting $itemName){
		if ( !empty( $_SESSION['CACHE'][$itemName])) {
			unset ($_SESSION['CACHE'][$itemName]);
			return true;
		}
		return false;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $secondsFromNow
	 * @return bool
	 */
	public static function changeExpires( int $secondsFromNow = DEFAULTTIMEOUTSECONDS): bool{
		$now = (new \DateTime('now'))->getTimestamp();
		$timeoutStamp = $now + $secondsFromNow;
		if ( !empty( $_SESSION['CACHE'][$itemName])) {
			$_SESSION['CACHE'][$itemName]['Expires'] = $timeoutStamp;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $itemName
	 * @return bool
	 */
	public static function exists( string $itemName) : bool {
		return  ( !empty( $_SESSION['CACHE'][$itemName]));
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $itemName
	 * @return int
	 */
	public static function secondUntilExpire(string $itemName): int{
		if ( !empty( $_SESSION['CACHE'][$itemName])) {
			$then = $_SESSION['CACHE'][$itemName]['Expires'];
			$now = (new \DateTime('now'))->getTimestamp();
			return ($then - $now);
		}
		return 0;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @return bool
	 */
	public static function doesSerializeWorkOnThisObject($data) :bool {

		if ( $data instanceof \PDO ) {
			return false;
		}
		try {
			$s = \serialize( $data);

		} catch (Exception $ex) {
			echo 'NNOPPEE!!!!!!!!!!!!!!!!!!';
			return false;
		}
		return true;
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	public static function CleanupBeforSessionWrite(): void{
		dump::dumpLong( $_SESSION);
		if ( !empty( $_SESSION) and !empty($_SESSION['CACHE'] )) {
			foreach ($_SESSION['CACHE'] as $key => $value) {
				if ( !self::doesSerializeWorkOnThisObject($value['Data'])){
					unset ( $_SESSION['CACHE'][$key]);
				}
			}
		}
		dump::dumpLong( $_SESSION);
	}



}


