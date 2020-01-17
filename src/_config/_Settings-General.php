<?php

/** * ********************************************************************************************
 * _Settings-General.php
 *
 * Summary: General settings
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description.
 *   this holds and sets the settings related to database functions
 *         - note that there is a _private_settings file that holds the username and passwords - and DSN
 *       - note that these settings  are not comprehensive - the app can create  and remove settings as it pleases *
 *
 *
 * @link URL
 *
 * @package  AuthenticateController
 * @subpackage Controller
 * @since 0.3.0
 *
 * @example
 *
 * @see p:\projects\_Private_Settings.php
 * @see _Settings-Database.php
 * @see _Settings-protected.php
 * @see utils\settings.class.php
 *

 *
 * @todo
 *
 */
//**********************************************************************************************

namespace php_base\Utils;

/**
 * debugging type settings
 */

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// you can use a flag file in the c:\city directory to turn on debugging for only this PC

Settings::SetPublic('IS_DEBUGGING', checkLocalEnvIfDebugging());

//Settings::SetPublic('IS_DEBUGGING', false);
Settings::SetPublic('IS_DEBUGGING', true);

if (Settings::GetPublic('IS_DEBUGGING')) {
	DebugHandler::setCurrentLevel(DebugHandler::DEBUG);

	Settings::SetPublic('IS_DETAILED_SQL_DEBUGGING', false);

	Settings::SetPublic('IS_DETAILED_RESOLVER_DEBUGGING', false);

	Settings::SetPublic('IS_DETAILED_DISPATCH_QUEUE_DEBUGGING', false);

	Settings::SetPublic('IS_DETAILED_AUTHENTICATION_DEBUGGING', false);
	Settings::SetPublic('IS_DETAILED_USERROLEANDPERMISSIONS_DEBUGGING', true);
	Settings::SetPublic('IS_DETAILED_PERMISSIONS_DEBUGGING', true);
	Settings::SetPublic('IS_DETAILED_CACHE_DEBUGGING', false);

	Settings::SetPublic('IS_DETAILED_MENU_DEBUGGING', true);



	Settings::SetPublic('Show MessageLog Display Mode Short Color', false);
	Settings::SetPublic('Show MessageLog Adds', true);
	Settings::SetPublic('Show MessageLog Adds_FileAndLine', true);
} else {
	DebugHandler::setCurrentLevel(DebugHandler::NOTICE);

	Settings::SetPublic('Show MessageLog Display Mode Short Color', true);
}

//Settings::SetPublic('THE_DEBUGGING_LEVEL', 0); // debugging level is off
Settings::SetPublic('THE_DEBUGGING_LEVEL', 100);  // 100 = MessageLog::DEBUG;
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 200);  // 200 = MessageLog::INFO;
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 250);  //                   Notice = 250;
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 300);  //                   WARNING = 300;
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 400);  //                   ERROR = 400;
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 500);  //                   CRITICAL = 500;
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 550);  //                   ALERT = 550;
//Settings::SetPublic('THE_DEBUGGING_LEVEL', 600);  //                   EMERGENCY = 600;




Settings::SetPublic('CACHE_IS_ON', false);
Settings::SetPublic('CACHE Allow_Menu to be Cached', true);
Settings::SetPublic('CACHE_Allow_Tables to be Cached', true);     //true


/**--------------------------------------------------------
 * details on the app
 */
//Settings::SetPublic( 'App Name', 'MikesCommandAndControl2');
Settings::SetPublic('App Name', 'TestApp');

Settings::SetPublic('App Version', '0.3.0');
Settings::SetPublic('App Server', empty($_SERVER['SERVER_NAME']) ? 'unknown' : $_SERVER['SERVER_NAME'] );

Settings::SetPublic('Email_From', 'no-reply@whitehorse.ca');



/**--------------------------------------------------------
 * initialize the Runtime settings for the logging to DB/Files/Email
 */
// these will be set in the "/Setup.php/SetupLogging.php"
Settings::SetRuntime('DBLog', null);
Settings::SetRuntime('DBdataLog', null);
Settings::SetRuntime('FileLog', null);
Settings::SetRuntime('SecurityLog', null);
Settings::SetRuntime('EmailLog', null);

/**--------------------------------------------------------
 * the file names for the Lot Files
 */
Settings::SetPublic('Log_file', DIR . 'logs' . DSZ . Settings::GetPublic('App Name') . '_app.log');
Settings::SetPublic('Security_Log_file', DIR . 'logs' . DSZ . Settings::GetPublic('App Name') . '_security.log');

/**--------------------------------------------------------
 * this indicates which of the logging is active (true) and which are basically left turned off
 */
Settings::SetPublic('Use_MessageLog', true);  //true
Settings::SetPublic('Use_DBLog', false);
Settings::SetPublic('Use_DBdataLog', false);
Settings::SetPublic('Use_FileLog', false);  // true
Settings::SetPublic('Use_SecurityLog', false);
Settings::SetPublic('Use_EmailLog', false);	  // true


/**--------------------------------------------------------
 * initialize a setting (basically a reminder that this setting exists outside of the loggin
 *      - used for the email log event (usually Critical, Emergency and Errors events
 */
//////////Settings::SetPublic( 'CRITICAL_EMAIL_PAYLOAD', 'CRITICAL_EMAIL_PAYLOAD');
Settings::SetPublic('CRITICAL_EMAIL_PAYLOAD_CONTEXT', false);  // could be string or array
Settings::SetPublic('CRITICAL_EMAIL_PAYLOAD_EXTRA', false);  // could be string or array


function checkLocalEnvIfDebugging(){

	$get = filter_input_array(\INPUT_GET, \FILTER_SANITIZE_STRING);
	if ( !empty( $get) and in_array('DEBUG', $get) and $get['DEBUG']== 78 ){
		//echo '-debug true-'	;
		return true;
	}
	$cookie = filter_input_array(\INPUT_COOKIE,\FILTER_SANITIZE_STRING);

//	echo '<pre>';
//	print_r ($cookie);
//	echo '</pre>';

	if ( !empty( $cookie['DEBUG']) and ($cookie['DEBUG'] == '78') ){
		echo '-debug true by cookie-'	;
		return true;
	}

	//echo '-debug false (by cookie or get)-'	;
	return false;
}