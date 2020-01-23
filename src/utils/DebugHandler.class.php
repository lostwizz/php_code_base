<?php

namespace php_base\Utils;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

abstract class DebugHandler {

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';
	const DEBUG = 100;
	const INFO = 200;
	const NOTICE = 250;
	const TODO = 275;
	const WARNING = 300;
	const ERROR = 400;
	const CRITICAL = 500;
	const ALERT = 550;
	const EMERGENCY = 600;
	const STYLE_DUMP = 'StyleDump';
	const STYLE_ECHO = 'StyleEcho';
	const STYLE_MESSAGE_LOG = 'MessageLog';

	/**
	 *
	 * @var array $levels - gives a text description of the error type
	 */
	protected static $levels = array(
		self::DEBUG => 'DEBUG',
		self::TODO => 'TODO',
		self::INFO => 'INFO',
		self::NOTICE => 'NOTICE',
		self::WARNING => 'WARNING',
		self::ERROR => 'ERROR',
		self::CRITICAL => 'CRITICAL',
		self::ALERT => 'ALERT',
		self::EMERGENCY => 'EMERGENCY',
	);

//	protected $text; // the messageText message
//	protected $timeStamp;  // time stamp for the message (for displaying the time)
//	protected $level; // level of the message (see defines at top)
//
//	abstract function Show();
//
//	abstract function Set($value = null);
//
//	abstract function Get();

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}

	static protected $CurrentLevel = self::NOTICE;

	public static function setCurrentLevel($lvl = self::INFO): void {
		self::$CurrentLevel = $lvl;
	}

	public static function getCurrentLevel(): string {
		return self::$levels[self::$CurrentLevel];
	}

	public static function getLevel(int $lvl): string {
		return self::$levels[$lvl];
	}

	public static function isShow($lvl = self::INFO): bool {
		if (self::$CurrentLevel == self::DEBUG) {
			return true;
		}
		if (self::$CurrentLevel <= $lvl) {
			return true;
		}
		return false;
	}

	public static function doShow(int $lvl, string $msg, $var, $style = self::STYLE_DUMP) :void{
		if (self::isShow($lvl)) {
			$method = 'show' . $style;
			self::$method($msg, $var, $lvl);
		}
	}

	public static function showStyleDump(string $msg, $var, $dummy = null):void {
		dump::dump($var, $msg);
	}

	public static function showStyleEcho(string $msg, $var, $lvl = null):void {
		echo '<pre>';
		echo self::getLevel($lvl);
		echo ': ';
		//echo '<br>'. PHP_EOL;
		echo $msg;
		echo PHP_EOL;
		print_r($var);
		echo '</pre><br>';
	}

	public static function showMessageLog(string $msg, $var, $lvl) :void{
		$outVar = print_r($var, true);
		Settings::GetRunTimeObject('MessageLog')->add($msg . ': ' . $outVar, NULL, $lvl);
	}




}
