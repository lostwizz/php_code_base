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

// set some usefull usings

//use \php_base\Authenticate as Authenticate;


use \php_base\Resolver as Resolver;
use \php_base\Utils\Cache;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\MessageLog as MessageLog;
use \php_base\Utils\Settings as Settings;
use \php_base\Utils\DebugHandler as DebugHandler;



////
//
////////!!!!!!!!!!!!!this line #5 must alwaygs be this for the unit tests to work
//Dump::dump(__LINE__, '-This is a Title-',true);
////////!!!!!!!!!!!!!this line #55 must alwaygs be this for the unit tests to work
//************************************************************************************
//

include_once( \DIR . 'SetupSystemDetail.php');


////DebugHandler::setCurrentLevel(DebugHandler::NOTICE);
//echo 'a<br>';
//echo DebugHandler::getCurrentLevel();
//echo 'b<br>';
//
//echo DebugHandler::isShow( DebugHandler::DEBUG) ? 'DEBUG Show YES' : 'DEBUG Show NO' , '<br>';
//echo DebugHandler::isShow( DebugHandler::INFO) ? 'DEBUG Show YES' : 'DEBUG Show NO' , '<br>';
//echo DebugHandler::isShow( DebugHandler::ERROR) ? 'DEBUG Show YES' : 'DEBUG Show NO' , '<br>';
//echo DebugHandler::isShow( DebugHandler::ALERT) ? 'DEBUG Show YES' : 'DEBUG Show NO' , '<br>';
//echo DebugHandler::isShow( DebugHandler::EMERGENCY) ? 'DEBUG Show YES' : 'DEBUG Show NO' , '<br>';
//
//DebugHandler::doShow(DebugHandler::TODO, 'test msg', 44 );
//DebugHandler::doShow(DebugHandler::TODO, 'test msg', 45, DebugHandler::STYLE_ECHO );
//
//DebugHandler::doShow(DebugHandler::TODO, 'test msg', 46, DebugHandler::STYLE_MESSAGE_LOG );




dump::dump('hi');

//dump::dumpClasses(null, array('Beautify_BackgroundColor' => '#FFAA55') );


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


Cache::CleanupBeforSessionWrite();

session_write_close();
//Dump::dump( $_SESSION); //dumpLong

//Dump::dump( $_SESSION); //dumpLong
Settings::GetRunTimeObject('MessageLog')->addNotice('... Closing Session..');


if (Settings::GetPublic('IS_DEBUGGING')) {
	echo '<br>--...The End.--<Br>';
}
