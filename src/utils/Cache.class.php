<?php
/** * ********************************************************************************************
 * Cache.class.php
 *
 * Summary: static class wrapper for the html code
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description: static wrapper for the html code
 *
 * @Note:   some items will not be cacheable over a session start and stop (such as a PDO instance )
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

	public const DEFAULTTIMEOUTSECONDS = 600;

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
	 * @param string $itemName
	 * @param type $data
	 * @param int $secondToTimeout
	 * @return bool
	 */
	public static function add(string $itemName, $data, int $secondToTimeout = self::DEFAULTTIMEOUTSECONDS): bool {
		if (!Settings::GetPublic('CACHE_IS_ON')) {
			return false;
		}
		if (defined("IS_PHPUNIT_TESTING")) {
			$now = 1575920000;
		} else {
			$now = (new \DateTime('now'))->getTimestamp();
		}
		$timeoutStamp = $now + $secondToTimeout;
		$value = array('Data' => $data,
			'Expires' => $timeoutStamp
		);
		$_SESSION['CACHE'][$itemName] = $value;
		if (Settings::GetPublic('IS_DETAILED_CACHE_DEBUGGING')) {
			Settings::GetRunTimeObject('MessageLog')->addNotice('Cache added: ' . $itemName );
		}
		return true;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $itemName
	 * @param type $data
	 * @param int $secondToTimeout
	 * @return bool
	 */
	public static function addOrUpdate( string $itemName, $data, int $secondToTimeout = self::DEFAULTTIMEOUTSECONDS): bool {
		if (!Settings::GetPublic('CACHE_IS_ON')) {
			return false;
		}
		if (defined("IS_PHPUNIT_TESTING")) {
			$now = 1575940000;
		} else {
			$now = (new \DateTime('now'))->getTimestamp();
		}
		$timeoutStamp = $now + $secondToTimeout;

		if ( self::hasExpired( $itemName)) {
			return false;             /* expired before you got here */
		}
		$_SESSION['CACHE'][$itemName]['Data'] = $data;
		$_SESSION['CACHE'][$itemName]['Expires'] = $timeoutStamp;
		if (Settings::GetPublic('IS_DETAILED_	CACHE_DEBUGGING')) {
			Settings::GetRunTimeObject('MessageLog')->addNotice('Cache addOrUpdate: ' . $itemName );
		}

		return true;
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $itemName
	 * @return type
	 */
	public static function pull($itemName) {
		if (!Settings::GetPublic('CACHE_IS_ON')) {
			return null;
		}
		if ( ! self::exists($itemName)){
			return null;
		}


		if (defined("IS_PHPUNIT_TESTING")) {
			$now = 1575920000;
		} else {
			$now = (new \DateTime('now'))->getTimestamp();
		}

		if ( self::hasExpired( $itemName)) {
			return false;
		} else {
			if (Settings::GetPublic('IS_DETAILED_	CACHE_DEBUGGING')) {
				Settings::GetRunTimeObject('MessageLog')->addNotice('Cache pull: ' . $itemName );
			}

			return $_SESSION['CACHE'][$itemName]['Data'];
		}
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $secondsFromNow
	 * @return bool
	 */
	public static function changeExpires(string $itemName, int $secondsFromNow = DEFAULTTIMEOUTSECONDS): bool{
		if (!Settings::GetPublic('CACHE_IS_ON')) {
			return false;
		}
		if (defined("IS_PHPUNIT_TESTING")) {
			$now = 1575950000;
		} else {
			$now = (new \DateTime('now'))->getTimestamp();
		}

		$timeoutStamp = $now + $secondsFromNow;
		if ( !empty( $_SESSION['CACHE'][$itemName])) {
			$_SESSION['CACHE'][$itemName]['Expires'] = $timeoutStamp;
			return true;
		}
		return false;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $itemName
	 * @return bool
	 */
	public static function exists( string $itemName) : bool {
		if ( self::hasExpired( $itemName)) {
			return false;
		}
		if (empty( $_SESSION['CACHE'][$itemName]) ){
			return false;
		}
		if ( !empty( $_SESSION['CACHE'][$itemName]['Data'] ) and !empty(  $_SESSION['CACHE'][$itemName]['Expires'])){
			return true;
		}
		return false;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $itemName
	 * @return bool
	 */
	public static function hasExpired(string $itemName): bool{
		if (!Settings::GetPublic('CACHE_IS_ON')) {
			return false;
		}

		if (empty( $_SESSION['CACHE'][$itemName]['Expires'])){
			self::delete( $itemName, true);   /* not expires time so no timehout which is not allowed */
			return true;
		}

		if (defined("IS_PHPUNIT_TESTING")) {
			$now = 1575920000;
		} else {
			$now = (new \DateTime('now'))->getTimestamp();
		}

		if ( $_SESSION['CACHE'][$itemName]['Expires'] <= $now) {
			self::delete( $itemName, true);
			return true;
		} else {
			return false;
		}
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param \php_base\Utils\sting $itemName
	 * @return boolean
	 */
	public static function delete(string $itemName, bool $fromExpired = false){
		if (!Settings::GetPublic('CACHE_IS_ON')) {
			return true;
		}

		if ( !empty( $_SESSION['CACHE'][$itemName])) {
			if (Settings::GetPublic('IS_DETAILED_	CACHE_DEBUGGING')) {
				Settings::GetRunTimeObject('MessageLog')->addNotice('Cache deleted' . ($fromExpired ? 'Expired':'') .': ' . $itemName );
			}

			unset ($_SESSION['CACHE'][$itemName]);
			return true;
		}
		return false;
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $itemName
	 * @return int
	 */
	public static function secondsUntilExpire(string $itemName): int{
		if (!Settings::GetPublic('CACHE_IS_ON')) {
			return -1;
		}

		if ( self::hasExpired($itemName)){
			return -1;
		}

		if (defined("IS_PHPUNIT_TESTING")) {
			$now = 1575930000;
		} else {
			$now = (new \DateTime('now'))->getTimestamp();
		}


		if ( !empty( $_SESSION['CACHE'][$itemName])) {
			$then = $_SESSION['CACHE'][$itemName]['Expires'];
			return ($now - $then);

		}
		return 0;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @return bool
	 */
	public static function doesSerializeWorkOnThisObject($data) :bool {
		if (!Settings::GetPublic('CACHE_IS_ON')) {
			return false;
		}

		if ( empty($data)) {
			return true;
		}
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
		//dump::dumpLong( $_SESSION);
		if ( !empty( $_SESSION) and !empty($_SESSION['CACHE'] )) {
			foreach ($_SESSION['CACHE'] as $itemName => $value) {
				if ( !self::doesSerializeWorkOnThisObject($value['Data'])){
					if (Settings::GetPublic('IS_DETAILED_	CACHE_DEBUGGING')) {
						Settings::GetRunTimeObject('MessageLog')->addNotice('Cache Cleanup : ' . $itemName );
					}
					unset ( $_SESSION['CACHE'][$itemName]);
				}
			}
		}
		//dump::dumpLong( $_SESSION);
	}



}


