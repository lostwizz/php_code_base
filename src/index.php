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
use \php_base\Utils\History as History;


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




//dump::Dump('array("1"=>4)',null,array('Show BackTrace Num Lines' => 5,'Beautify_BackgroundColor' => '#FFAA55') );
//Dump::dump('hello world', 'the world');
//
//dump::dump('hi');
//dump::dump('one',null, array('Beautify is On'=>false));
//
//$s =dump::dump('hi',null, array('Only Return Output String' => true, 'Beautify is On'=>false));
//echo $s;

//debug_zval_dump(Settings::GetPublic('IS_DEBUGGING'));
//
//dump::dumpLong( get_defined_vars());
//
//dump::dumpClasses(null, array('Beautify_BackgroundColor' => '#FFAA55') );

//dump::dumpClasses( );

//$a1 =3432;
//$a2 = 'fred was here';
//$a3 = 'bob';
//$a4= -21;
//
//dump::dumpA($a1,$a2,$a3, $a4);

History::clear();
//History::addMarker();



//	dump::dump(Settings::GetRunTimeObject('MessageLog'));

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
