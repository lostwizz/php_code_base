<?php


//***********************************************************************************
// setup the Directory Root
define ('DS', DIRECTORY_SEPARATOR);
               //define ('DIR', 'p:' . DS . 'Projects' . DS . 'MikesCommandAndControl2' . DS . 'src' . DS );
if ( strripos (realpath('.'), 'src' ) <1 ){
	define('DIR', realpath('..') . DS . 'src' . DS);
} else {
	define('DIR', realpath('.') . DS );
}



include_once( DIR . 'autoload.php');

// set some usefull usings
use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\MessageLog as MessageLog;
use \php_base\Authenticate as Authenticate;
use \php_base\Resolver as Resolver;

use \php_base\utils\myCryption\myCryption as myCryption;
use \php_base\Utils\HTML\HTML as HTML;
use \php_base\Utils\myUtils\myUtils as myUtils;















////////!!!!!!!!!!!!!this line #45 must alwaygs be this for the unit tests to work
//Dump::dump(__LINE__, '-This is a Title-',true);
////////!!!!!!!!!!!!!this line #45 must alwaygs be this for the unit tests to work


//************************************************************************************
//************************************************************************************
// setup everything
//************************************************************************************
include_once( DIR . 'autoload.php');
date_default_timezone_set('Canada/Yukon');
//include_once( DIR . 'utils' . DS . 'dump.class.php');
include_once( DIR . 'utils' . DS . 'settings.class.php');

require_once( DIR . '_config' . DS . '_Settings-General.php');
require_once( DIR . '_config' . DS . '_Settings-Database.php');
require_once( DIR . '_config' . DS . '_Settings-protected.php');

require_once( 'P:\Projects\_Private_Settings.php');

include_once( DIR . 'utils' . DS . 'ErrorHandler.php');           // has to be after the settings are initialized

Settings::SetPublic('Log_file', DIR . 'logs' . DS . Settings::GetPublic('App Name') . '_app.log' );
include_once( DIR . 'utils' . DS . 'Setup_Logging.php');

Settings::SetPublic( 'TEST that All is well', 'YES');


// check that setup.php worked properly
if ( Settings::GetPublic( 'TEST that All is well') != 'YES') {
	throw new exception('it seems that setup (or settings.class.php did not run properly');
}
if (Settings::GetRuntimeObject('FileLog') ==null) {
	throw new exception('it seems that setup (or settings.class.php did not run properly');
}


if (false) {
	session_name('SESSID_' . str_replace( ' ','_',Settings::GetPublic('App Name') ));
	session_start();
}

if ( Settings::GetPublic('Use_MessageLog')){
	$mLog = new MessageLog();
	Settings::SetRunTime('MessageLog', $mLog);
}
//************************************************************************************
// done the setup
//************************************************************************************
//************************************************************************************
//************************************************************************************
//************************************************************************************
//************************************************************************************
//************************************************************************************
//************************************************************************************
//************************************************************************************




Settings::SetRunTime('Benchmarks.start.executionTime',  microtime(true));



//if ( Settings::GetPublic('IS_DEBUGGING')) {
//	echo '<br>--Starting... :-) ...<br>';
//	Dump::dumpLong( $_REQUEST);
//	if ( !empty($_SESSION) ){
//		Dump::dump( $_SESSION); //dumpLong
//	}
//	if ( !empty( $GLOBALS)) {
////		Dump::dump($GLOBALS); //dumpLong
//	}
//	if ( ! empty($_COOKIES)){
//		Dump::dump( $_COOKIES);
//	}
//}

Settings::GetRunTimeObject('MessageLog')->addNotice( 'Starting ....');
Settings::GetRuntimeObject('FileLog')->addInfo('Staring...');
Settings::GetRuntimeObject('SecurityLog')->addInfo('Starting...');

//Dump::dump(Settings::GetProtected('database_extension_needed'));
if ( extension_loaded(Settings::GetProtected('database_extension_needed'))) {
	Settings::GetRuntimeObject('DBLog')->addInfo( 'Starting....');
} else {
	echo '<font color=white style="background-color:red;font-size:160%;" >', 'PDO_SQLSRV is not available!!- exiting ','</font>';
	throw new exception( Settings::GetProtected('database_extension_needed'). ' is not available!!', 256);
}


// this is for testing the crash email
if (false) {
	Settings::GetRuntimeObject('EmailLog')->addCritical( ' it blew up!', $_SERVER);
	Settings::GetRuntimeObject('EmailLog')->addCritical('Hey, a critical log entry!', array( 'bt' => debug_backtrace(true), 'server' =>$_SERVER));
}


Settings::GetRunTimeObject('MessageLog')->addNotice( 'Starting ..session..');




//
//$a = new Authenticate();
//echo 'does have right 1', $a->checkRights( 'fred', 'sam', 'john', Authenticate::DBA_RIGHT) ? 'yes':'no';
//echo 'does have right 2', $a->checkRights( 'fred', 'sam', 'john', Authenticate::READ_RIGHT)? 'yes':'no';


//$_SESSION['username'] = 'merrem';

// now start everything running
$resolver = new Resolver();
$resolver->doWork();


echo 'IIIIIIIIIII' . PHP_EOL;
echo HTML::Image(  '.\static\images\Whitehorse_RGB_200x140.jpg'  );
echo 'JJJJJJJJJJ' . PHP_EOL;


//Dump::dump( $_SESSION); //dumpLong
Settings::GetRunTimeObject('MessageLog')->addNotice( '... Closing Session..');

session_write_close();
//Dump::dump( $_SESSION); //dumpLong

if ( Settings::GetPublic('IS_DEBUGGING')) {
	echo '<br>--...The End.--<Br>';
}

//Settings::GetRunTimeObject('MessageLog')->addERROR('something happend here !');

$exec_time = microtime(true) - Settings::GetRunTime('Benchmarks.start.executionTime');
Settings::GetRunTimeObject('MessageLog')->addINFO('Execution Time was: '. $exec_time);

Settings::GetRunTimeObject('MessageLog')->showAllMessagesInBox();  // !! a!lways do this last so you get all the outstanding messages!!!!

