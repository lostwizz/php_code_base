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
use \php_base\Utils\Utils as Utils;
use \php_base\Utils\SubSystemMessage as SubSystemMessage;

//************************************************************************************
// setup everything
//************************************************************************************
include_once( DIR . 'autoload.php');
date_default_timezone_set('Canada/Yukon');
//include_once( DIR . 'utils' . DS . 'dump.class.php');



if (true) {
	if ( session_status() == \PHP_SESSION_NONE AND !headers_sent()){
		session_name('SESSID_' . str_replace(' ', '_', Settings::GetPublic('App Name')));
		session_start();
		//Settings::GetRunTimeObject('MessageLog')->addNotice('Starting ..session..'); // cant really do a messageLog because it isnt loaded yet
	}

	//think about how to use   session_regenerate_id(true);
	//    and/or session_destroy()
	// https://www.php.net/manual/en/function.session-destroy.php
}




require_once( DIR . '..' . DSZ . 'vendor' . DSZ . 'autoload.php');

include_once( DIR . 'utils' . DSZ . 'settings.class.php');

require_once( DIR . '_config' . DSZ . '_Settings-General.php');
require_once( DIR . '_config' . DSZ . '_Settings-Database.php');
require_once( DIR . '_config' . DSZ . '_Settings-protected.php');

require_once( 'P:\Projects\_Private_Settings.php');

include_once( DIR . 'utils' . DSZ . 'ErrorHandler.php');	 // has to be after the settings are initialized


Settings::SetPublic('Log_file', DIR . 'logs' . DSZ . Settings::GetPublic('App Name') . '_app.log');


if (Settings::GetPublic('Use_MessageLog')) {
	$mLog = new MessageLog();
	Settings::SetRunTime('MessageLog', $mLog);
}

include_once( DIR . 'utils' . DSZ . 'Setup_Logging.php');



///////////////////////////////////////////////////////////////////////////////
// verify versions of a few key items
if (Utils::Version() != '0.3.0'){
	die;
}
if (Utils::isVersionGood( '0.3.0', Dump::Version()) ) {
	//echo 'good dump version';
} else {
	echo 'bad dump version';
}

///////////////////////////////////////////////////////////////////////////////


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






if (false) {
	//https://libmemcached.org/libMemcached.html
	//https://www.php.net/manual/en/book.memcached.php


	$mc = new Memcached();
	$mc->addServer("localhost", 11211);

	$mc->set("foo", "Hello!");
	$mc->set("bar", "Memcached...");
}



//************************************************************************************
// done the setup
//************************************************************************************
//************************************************************************************
//************************************************************************************
//************************************************************************************
//Dump::dump( \filter_input_array(\INPUT_SERVER, \FILTER_SANITIZE_STRING, true));


Settings::SetRunTime('Benchmarks.start.executionTime', microtime(true));


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


