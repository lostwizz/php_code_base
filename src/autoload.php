<?php

//**********************************************************************************************
//* autoload.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-03 09:15:37 -0700 (Tue, 03 Sep 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 03-Sep-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************

if (defined("IS_PHPUNIT_TESTING")) {
	session_start();
}


use \php_base\Utils\Settings as Settings;
//use \php_base\Utils\Dump\Dump as Dump;

define('AUTOLOAD_LEVEL_NOTICE', 250);
define('AUTOLOAD_LEVEL_INFO', 200);
define('AUTOLOAD_LEVEL_DEBUG', 100);
define('AUTOLOAD_LEVEL_NONE', -1);

DEFINE('AUTOLOAD_OUTPUT_LEVEL', AUTOLOAD_LEVEL_NONE);
//DEFINE( 'AUTOLOAD_OUTPUT_LEVEL', AUTOLOAD_LEVEL_NOTICE);
//DEFINE( 'AUTOLOAD_OUTPUT_LEVEL', AUTOLOAD_LEVEL_INFO);
//DEFINE( 'AUTOLOAD_OUTPUT_LEVEL', AUTOLOAD_LEVEL_DEBUG);


//********************************************************************************
spl_autoload_register('myAutoLoader');


	/**
	 * @var version number
	 */
	const AUTOLOAD_VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	function Version() {
		return AUTOLOAD_VERSION;
	}


if (!defined('DIR')) {
	if ( !defined('DSZ')){
		define('DSZ', DIRECTORY_SEPARATOR);
	}
	if (strripos(realpath('.'), 'src') < 1) {
		define('DIR', realpath('.') . DSZ . 'src' . DSZ);
	} else {
		define('DIR', realpath('.') . DSZ);
	}
}

//***********************************************************************************************
//***********************************************************************************************
function myAutoLoader($class) {
	$r = doRunTheSearch($class);
	if (!empty(AUTOLOAD_OUTPUT_LEVEL) and ( AUTOLOAD_OUTPUT_LEVEL == AUTOLOAD_LEVEL_DEBUG )) {
		Dump::dumpClasses();
	}
	return $r;
}

//********************************************************************************
function doRunTheSearch($class) {
	$ex = explode('\\', $class);	 // get the name of just the file - eg sam\fred gives fred

	if (!empty(AUTOLOAD_OUTPUT_LEVEL) and ( AUTOLOAD_OUTPUT_LEVEL == AUTOLOAD_LEVEL_INFO )) {
		echo "<font color=magenta>(Looking for:" . $ex[count($ex) - 1] . ')</font>' . PHP_EOL;
	}

	$base = $ex[count($ex) - 1] . '.class.php';
	if (runTheChecks($base))
		return true;

	$base = $ex[count($ex) - 1] . '.php';
	if (runTheChecks($base))
		return true;

	$base = strtolower($ex[count($ex) - 1]) . '.class.php';
	if (runTheChecks($base))
		return true;

	$base = strtolower($ex[count($ex) - 1]) . '.php';
	if (runTheChecks($base))
		return true;


//fail silently
//	if ( !empty(AUTOLOAD_OUTPUT_LEVEL)  and (AUTOLOAD_OUTPUT_LEVEL != AUTOLOAD_LEVEL_NONE )) {
//		echo '<font color=white style="background-color:red;font-size:160%;" >', '(AutoLoad could not find!!! ' . $ex[ count($ex)-1] . ')','</font>' . PHP_EOL;
//	}
	return false;
}

//********************************************************************************
function runTheChecks($base) {
	if (!empty(AUTOLOAD_OUTPUT_LEVEL) and ( AUTOLOAD_OUTPUT_LEVEL == AUTOLOAD_LEVEL_DEBUG )) {
		echo "Looking for:" . $base . '<BR>' . PHP_EOL;
		//fwrite(STDERR, "\n\rLooking for:". print_r($base, TRUE));
	}
	if (tryFile(DIR . 'model' . DSZ . $base))
		return true;
	if (tryFile(DIR . 'view' . DSZ . $base))
		return true;
	if (tryFile(DIR . 'control' . DSZ . $base))
		return true;
	if (tryFile(DIR . 'data' . DSZ . $base))
		return true;
	if (tryFile(DIR . 'utils' . DSZ . $base))
		return true;
	////if (  tryFile( DIR . 'utils'  . DSZ . 'setup' . DSZ . $base ))	return true;
	////if (  tryFile( DIR . 'utils'  . DSZ . 'log'   . DSZ . $base ))	return true;

	if (tryFile(DIR . $base))
		return true;
	if (tryFile(DIR . 'static' . DSZ . $base))
		return true;
	if (tryFile(DIR . 'utils' . DSZ . 'DatabaseHandlers' . DSZ . $base))
		return true;
	if (tryFile(DIR . '..' . DSZ . 'vendor' . DSZ . 'monolog' . DSZ . 'monolog' . DSZ . 'src' . DSZ . $base))
		return true;
	return false;
}

//********************************************************************************
function tryFile($fn) {
	if (file_exists($fn)) {
		if (!empty(AUTOLOAD_OUTPUT_LEVEL) and ( AUTOLOAD_OUTPUT_LEVEL != AUTOLOAD_LEVEL_NONE )) {
			echo "<font color=maroon>FOUND:" . $fn . ')</font><BR>' . PHP_EOL;
			//fwrite(STDERR, "\n\rFOUND:". print_r(  $fn , TRUE));
		}
		$x = include_once( $fn);
		return $x;
	} else {
		if (!empty(AUTOLOAD_OUTPUT_LEVEL) and ( AUTOLOAD_OUTPUT_LEVEL == AUTOLOAD_LEVEL_DEBUG )) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-NotFOUND at:" . $fn . '<BR>' . PHP_EOL;
			//echo '.';
			//fwrite(STDERR, "\n\rNOTFOUND:". print_r(  $fn , TRUE));
		}
		return false;
	}
}

//require_once __DIR__ . '/index.php';
//require_once __DIR__ . '/vendor/autoload.php';
