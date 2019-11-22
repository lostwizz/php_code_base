<?php

/** * ********************************************************************************************
 * SetypSytemDetail.php
 *
 * Summary do additinal setup functions
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description.
 * this file does the additional setup activities - prior to starting the resolver/dispatcher
 *
 *
 * @link URL
 *
 * @package setup
 * @subpackage initial setup
 * @since 0.7.0
 *
 * @example
 *
 * @see index.php
 *
 *
 * @todo Description
 *
 */
//**********************************************************************************************
//


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\MessageLog as MessageLog;


//************************************************************************************
// setup everything
//************************************************************************************
include_once( DIR . 'autoload.php');
date_default_timezone_set('Canada/Yukon');
//include_once( DIR . 'utils' . DS . 'dump.class.php');


require_once( DIR . '..' . DS . 'vendor' . DS . 'autoload.php');

include_once( DIR . 'utils' . DS . 'settings.class.php');

require_once( DIR . '_config' . DS . '_Settings-General.php');
require_once( DIR . '_config' . DS . '_Settings-Database.php');
require_once( DIR . '_config' . DS . '_Settings-protected.php');

require_once( 'P:\Projects\_Private_Settings.php');

include_once( DIR . 'utils' . DS . 'ErrorHandler.php');		   // has to be after the settings are initialized

Settings::SetPublic('Log_file', DIR . 'logs' . DS . Settings::GetPublic('App Name') . '_app.log');
include_once( DIR . 'utils' . DS . 'Setup_Logging.php');



//echo serialize(array('fred'=>'johnny', 'bob'=> 'house'));


Settings::GetRunTimeObject('MessageLog')->addNotice('Starting read from db settings');
Settings::dbReadAndApplySettings();

Settings::SetPublic('TEST that All is well', 'YES');


// check that setup.php worked properly
if (Settings::GetPublic('TEST that All is well') != 'YES') {
	throw new exception('it seems that setup (or settings.class.php did not run properly');
}
if (Settings::GetRuntimeObject('FileLog') == null) {
	throw new exception('it seems that setup (or settings.class.php did not run properly');
}





//phpinfo();
if (false) {
	//https://libmemcached.org/libMemcached.html
	//https://www.php.net/manual/en/book.memcached.php


	$mc = new Memcached();
	$mc->addServer("localhost", 11211);

	$mc->set("foo", "Hello!");
	$mc->set("bar", "Memcached...");
}

if (false) {
	session_name('SESSID_' . str_replace(' ', '_', Settings::GetPublic('App Name')));
	session_start();

	//think about how to use   session_regenerate_id(true);
	//    and/or session_destroy()
	// https://www.php.net/manual/en/function.session-destroy.php
}

//$tt = new \php_base\UtilsmyNullAbsorber();
//$h = new \php_base\View\HeaderView($tt );
//$h->doWork($tt);

if (Settings::GetPublic('Use_MessageLog')) {
	$mLog = new MessageLog();
	Settings::SetRunTime('MessageLog', $mLog);
}


//echo '<BR>';
//echo  '\php_code_base\src\static\css\message_stack_style.css';
//echo '<br>';
//Dump::dumpLong($_SERVER);
//echo $_SERVER['HTTP_HOST'] . '\php_code_base\src\static\css\message_stack_style.css';
//echo '<BR>';


if (Settings::GetPublic('Use_MessageLog')) {
	$mLog = new MessageLog();
	Settings::SetRunTime('MessageLog', $mLog);
}
//************************************************************************************
// done the setup
//************************************************************************************
//************************************************************************************
//************************************************************************************
//************************************************************************************
//Dump::dump( \filter_input_array(\INPUT_SERVER, \FILTER_SANITIZE_STRING, true));

Settings::SetRunTime('Benchmarks.start.executionTime', microtime(true));


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
//Dump::dump(Settings::GetProtected('database_extension_needed'));
if (extension_loaded(Settings::GetProtected('database_extension_needed'))) {
	Settings::GetRuntimeObject('DBLog')->addInfo('Starting....');
} else {
	echo '<font color=white style="background-color:red;font-size:160%;" >', 'PDO_SQLSRV is not available!!- exiting ', '</font>';
	throw new exception(Settings::GetProtected('database_extension_needed') . ' is not available!!', 256);
}


// this is for testing the crash email
if (false) {
	Settings::GetRuntimeObject('EmailLog')->addCritical(' it blew up!', \filter_input_array(\INPUT_SERVER, \FILTER_SANITIZE_STRING));
	Settings::GetRuntimeObject('EmailLog')->addCritical('Hey, a critical log entry!', array('bt' => debug_backtrace(true), 'server' => \filter_input_array(\INPUT_SERVER, \FILTER_SANITIZE_STRING)));
}

if (Settings::GetPublic('IS_DEBUGGING')) {
	Settings::GetRunTimeObject('MessageLog')->addNotice('Starting ....');
	Settings::GetRuntimeObject('FileLog')->addInfo('Staring...');
	Settings::GetRuntimeObject('SecurityLog')->addInfo('Starting...');
}


if (Settings::GetPublic('IS_DEBUGGING')) {
	Settings::GetRunTimeObject('MessageLog')->addNotice('Starting ..session..');
}