<?php

/** * ********************************************************************************************
 * index.php
 *
 * Summary (no period for file headers)
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.0.1
 * $Id$
 *
 * Description. (use period)
 *
 *
 * @link URL
 *
 * @package WordPress
 * @subpackage Component
 * @since x.x.x (when the file was introduced)
 *
 * @example path description
 * @see elementName
 *
 * @todo Description
 *
 */
//**********************************************************************************************

// setup the Directory Root
define('DSZ', DIRECTORY_SEPARATOR);
//define ('DIR', 'p:' . DSZ . 'Projects' . DSZ . 'MikesCommandAndControl2' . DSZ . 'src' . DSZ );
if (strripos(realpath('.'), 'src') < 1) {
	define('DIR', realpath('..') . \DSZ . 'src' . \DSZ);
} else {
	define('DIR', realpath('.') . \DSZ);
}

include_once( \DIR . 'autoload.php');


use \php_base\Resolver as Resolver;
use \php_base\Utils\Settings as Settings;

////
//
////////!!!!!!!!!!!!!this line #5 must alwaygs be this for the unit tests to work
//Dump::dump(__LINE__, '-This is a Title-',true);
////////!!!!!!!!!!!!!this line #55 must alwaygs be this for the unit tests to work
//************************************************************************************
//

//include_once( \DIR . 'SetupSystemDetail.php');
include_once( DIR . 'InitializeAndSetup.class.php');

$init = new \php_base\InitializeAndSetup();

Settings::SetRunTime('Benchmarks.start.executionTime',  microtime(true));

//echo '<pre>';
//print_r($_SESSION);
//print_r($_REQUEST);
//echo '</pre>';

//-------------------------------------------------------------------------------------------------------------------------------------
// now start everything running
$resolver = new Resolver();
$response = $resolver->doWork();

//-------------------------------------------------------------------------------------------------------------------------------------


/**
 * handle the after effects
 */
if ($response->hadError() and $response->failNoisily()) {
	echo '<h2 class="responseError" >' . PHP_EOL;
	echo 'Fatal Error: ' . $response->toString();
	echo '</h2>' . PHP_EOL;
	echo '<BR><BR>Exiting!';
}


unset($init);

//
//Cache::CleanupBeforSessionWrite();
//
//session_write_close();
////Dump::dump( $_SESSION); //dumpLong
//
////Dump::dump( $_SESSION); //dumpLong
//Settings::GetRunTimeObject('MessageLog')->addInfo('... Closing Session..');
//
//
//if (Settings::GetPublic('IS_DEBUGGING')) {
//	echo '<br>--...The End.--<Br>';
//}
