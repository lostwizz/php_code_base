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
define('DS', DIRECTORY_SEPARATOR);
//define ('DIR', 'p:' . DS . 'Projects' . DS . 'MikesCommandAndControl2' . DS . 'src' . DS );
if (strripos(realpath('.'), 'src') < 1) {
	define('DIR', realpath('..') . DS . 'src' . DS);
} else {
	define('DIR', realpath('.') . DS);
}

include_once( DIR . 'autoload.php');

// set some usefull usings
use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\MessageLog as MessageLog;
//use \php_base\Authenticate as Authenticate;
use \php_base\Resolver as Resolver;

//use \php_base\utils\myCryption\myCryption as myCryption;
//use \php_base\Utils\HTML\HTML as HTML;
//use \php_base\Utils\myUtils\myUtils as myUtils;
//
//
//
//
//
////////!!!!!!!!!!!!!this line #5 must alwaygs be this for the unit tests to work
//Dump::dump(__LINE__, '-This is a Title-',true);
////////!!!!!!!!!!!!!this line #55 must alwaygs be this for the unit tests to work
//************************************************************************************
//


include_once( DIR . 'SetupSystemDetail.php');

//-------------------------------------------------------------------------------------------------------------------------------------
// now start everything running
$resolver = new Resolver();
$response = $resolver->doWork();

//-------------------------------------------------------------------------------------------------------------------------------------





/**
 * handle the after effects
 */
if ($response->hadFatalError() and $response->failNoisily()) {
	echo '<h2 class="responseError" >' . PHP_EOL;
	echo 'Fatal Error: ' . $response->toString();
	echo '</h2>' . PHP_EOL;
	echo '<BR><BR>Exiting!';
}


session_write_close();
//Dump::dump( $_SESSION); //dumpLong

//Dump::dump( $_SESSION); //dumpLong
Settings::GetRunTimeObject('MessageLog')->addNotice('... Closing Session..');


if (Settings::GetPublic('IS_DEBUGGING')) {
	echo '<br>--...The End.--<Br>';
}
