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


use \php_base\Utils\SubSystemMessage as SubSystemMessage;

/**
 * debugging type settings
 * the various debugging levels - it is in numberical order so you can set LVL_ALL so evrerything is MessageLog output
 *       if you set LVL_DEBUG then the LVL_DEBUG_1-9 will not output
 *         this should allow fine levels of output --
 *       eg i set to LVL_DEBUG_5 then debug_4,3,2,1 wont be outputed but 5,6,7,8,9 and LVL_DEBUG_ will be output
 *			these here are only used to set the "DETAILED" MessageLog output
 *            to make these really work you have to change MessageBase  (in the MessageLog.class.php file)
 * all these come from AMessage::xxx -so try to keep them matchedup
 *
 */
define('LVL_ALL', 1);

define('LVL_DEBUG_1', 101);
define('LVL_DEBUG_2', 102);
define('LVL_DEBUG_3', 103);
define('LVL_DEBUG_4', 104);
define('LVL_DEBUG_5', 105);
define('LVL_DEBUG_6', 106);
define('LVL_DEBUG_7', 107);
define('LVL_DEBUG_8', 108);
define('LVL_DEBUG_9', 109);
define('LVL_DEBUG', 110);

define('LVL_INFO_1', 201);
define('LVL_INFO_2', 202);
define('LVL_INFO_3', 203);
define('LVL_INFO_4', 204);
define('LVL_INFO_5', 205);
define('LVL_INFO_6', 206);
define('LVL_INFO_7', 207);
define('LVL_INFO_8', 208);
define('LVL_INFO_9', 209);
define('LVL_INFO', 210);

define('LVL_Notice_1', 251);
define('LVL_Notice_2', 252);
define('LVL_Notice_3', 253);
define('LVL_Notice_4', 254);
define('LVL_Notice_5', 255);
define('LVL_Notice_6', 256);
define('LVL_Notice_7', 257);
define('LVL_Notice_8', 258);
define('LVL_Notice_9', 259);
define('LVL_Notice', 260);
/////////////define('LVL_Notice', AMessage::NOTICE);

								// -it will show Notices but not the lower levels i.e. Notice_9..1

define('LVL_TODO', 1275);

define('LVL_WARNING', 300);

define('LVL_NORMAL', LVL_WARNING);    // this the noraml level for not debugging i.e. production

define('LVL_ERROR', 400);
define('LVL_CRITICAL', 500);
define('LVL_ALERT', 550);
define('LVL_EMERGENCY', 600);



////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ( !empty($_SESSION['LOCAL_DEBUG_SETTING']) AND $_SESSION['LOCAL_DEBUG_SETTING'] == 99 ) {
	Settings::SetPublic('IS_DEBUGGING', true );
}


//Settings::SetPublic('IS_DEBUGGING', false);
//Settings::SetPublic('IS_DEBUGGING', true);

if (Settings::GetPublic('IS_DEBUGGING')) {
	DebugHandler::setCurrentLevel(DebugHandler::DEBUG);

	Settings::GetRunTimeObject('MessageLog')->setSubSystemLoggingLevel(MessageLog::DEFAULT_SUBSYSTEM ,LVL_ALL); // LVL_Notice_5);

	MessageLog::$DEFAULTLoggingLevel = LVL_WARNING ;
	MessageLog::$LoggingLevels = array( MessageLog::$DEFAULTLoggingLevel => LVL_NORMAL);     //SubSystem= 'general'

		//==============
		// Note:  look in /utils/Setup_Logging for the initialization of the loggers at that level
	Settings::SetPublic('IS_DETAILED_RESOLVER_DEBUGGING',  LVL_NORMAL);

	Settings::SetPublic('IS_DETAILED_DISPATCH_QUEUE_DEBUGGING', LVL_NORMAL);

	Settings::SetPublic('IS_DETAILED_AUTHENTICATION_DEBUGGING', LVL_NORMAL);
	Settings::SetPublic('IS_DETAILED_USERROLEANDPERMISSIONS_DEBUGGING', LVL_NORMAL);
	//////////Settings::SetPublic('IS_DETAILED_PERMISSIONS_DEBUGGING', false);


	Settings::SetPublic('IS_DETAILED_DBA_DEBUGGING', LVL_ALL);

	Settings::SetPublic('IS_DETAILED_SIMPLE_TABLE_EDITOR_DEBUGGING', LVL_ALL ); //LVL_NORMAL);
	Settings::SetPublic('IS_DETAILED_DATABASEHANDLERS_DEBUGGING', LVL_NORMAL);
	Settings::SetPublic('IS_DETAILED_DATABASEHANDLERS_FLD_DEBUGGING', LVL_NORMAL);

	Settings::SetPublic('IS_DETAILED_MENU_DEBUGGING', LVL_NORMAL);

	Settings::SetPublic('IS_DETAILED_SQL_DEBUGGING',  LVL_NORMAL);
	Settings::SetPublic('IS_DETAILED_CACHE_DEBUGGING', LVL_NORMAL);
	Settings::SetPublic('IS_DETAILED_UTILS_DEBUGGING', LVL_NORMAL);
	Settings::SetPublic('Show MessageLog Display Mode Short Color', false);
	Settings::SetPublic('Show MessageLog Adds', true);
	Settings::SetPublic('Show MessageLog Adds_FileAndLine', true);
	Settings::SetPublic('Show MessageLog in Footer', false);
	Settings::SetPublic('Show History in Footer', true);
} else {
	DebugHandler::setCurrentLevel(DebugHandler::NOTICE);
	Settings::GetRunTimeObject('MessageLog')->setSubSystemLoggingLevel(MessageLog::DEFAULT_SUBSYSTEM ,LVL_NORMAL);
	MessageLog::$DEFAULTLoggingLevel = LVL_NORMAL ;
	MessageLog::$LoggingLevels = array( MessageLog::$DEFAULTLoggingLevel => LVL_NORMAL);     //SubSystem= 'general'

	Settings::SetPublic('Show MessageLog Adds', false );   // true);
	Settings::SetPublic('Show MessageLog Adds_FileAndLine', true);

		//Settings::SetPublic('IS_DETAILED_AUTHENTICATION_DEBUGGING', LVL_ALL ); //LVL_NORMAL);


	Settings::SetPublic('Show MessageLog Display Mode Short Color', false);
	Settings::SetPublic('Show MessageLog in Footer', true);
	Settings::SetPublic('Show History in Footer', false);
}



Settings::SetPublic('CACHE_IS_ON', false);
Settings::SetPublic('CACHE Allow_Menu to be Cached', true);
Settings::SetPublic('CACHE_Allow_Tables to be Cached', false);     //true


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
 * the file names for the Log Files
 */
Settings::SetPublic('Log_file', DIR . 'logs' . DSZ . Settings::GetPublic('App Name') . '_app.log');
Settings::SetPublic('Security_Log_file', DIR . 'logs' . DSZ . Settings::GetPublic('App Name') . '_security.log');

/**--------------------------------------------------------
 * this indicates which of the logging is active (true) and which are basically left turned off
 */
Settings::SetPublic('Use_MessageLog', true);  //true
Settings::SetPublic('Use_DBLog', true);
Settings::SetPublic('Use_DBdataLog', true);
Settings::SetPublic('Use_FileLog', true);  // true
Settings::SetPublic('Use_SecurityLog', true);
Settings::SetPublic('Use_EmailLog', true);	  // true


/**--------------------------------------------------------
 * initialize a setting (basically a reminder that this setting exists outside of the loggin
 *      - used for the email log event (usually Critical, Emergency and Errors events
 */
//////////Settings::SetPublic( 'CRITICAL_EMAIL_PAYLOAD', 'CRITICAL_EMAIL_PAYLOAD');
Settings::SetPublic('CRITICAL_EMAIL_PAYLOAD_CONTEXT', false);  // could be string or array
Settings::SetPublic('CRITICAL_EMAIL_PAYLOAD_EXTRA', false);  // could be string or array

//
//function checkLocalEnvIfDebugging(){
//
//	$get = filter_input_array(\INPUT_GET, \FILTER_SANITIZE_STRING);
//	if ( !empty( $get) and in_array('DEBUG', $get) and $get['DEBUG']== 78 ){
//		//echo '-debug true-'	;
//		return true;
//	}
//	$cookie = filter_input_array(\INPUT_COOKIE,\FILTER_SANITIZE_STRING);
//echo '<pre>';
//echo ' - cookie - ';
//print_r( $cookie);
//echo '</pre>';
//
////	echo '<pre>';
////	print_r ($cookie);
////	echo '</pre>';
//
//	if ( !empty( $cookie['DEBUG']) and ($cookie['DEBUG'] == '78') ){
//		echo '-debug true by cookie-'	;
//		return true;
//	}
//
//	//echo '-debug false (by cookie or get)-'	;
//	return false;
//}








/** -----------------------------------------------------------------------------------------------
 *  this is the function (called after the message log has been created)
 *   that sets up the short way of calling the message log for a specific subclass msg
 * call as \php_base\utils\setup_loggy();
 */
function setup_Loggy() {

	/** ----------------------------------------------------------------------------------------------- */
		$loggy = new SubSystemMessage('CACHE_DEBUGGING', Settings::getPublic('IS_DETAILED_CACHE_DEBUGGING'));
		Settings::SetRuntime('CACHE_DEBUGGING', $loggy);
	//Settings::getRunTimeObject('CACHE_DEBUGGING')->addAlert('CACHE detail logging is turned on');


	/** ----------------------------------------------------------------------------------------------- */
		$loggy = new SubSystemMessage('SQL_DEBUGGING', Settings::getPublic('IS_DETAILED_SQL_DEBUGGING'));
		Settings::SetRuntime('SQL_DEBUGGING', $loggy);



	/** ----------------------------------------------------------------------------------------------- */
		$loggy = new SubSystemMessage('DBHANDLERS_DEBUGGING', Settings::getPublic('IS_DETAILED_DATABASEHANDLERS_DEBUGGING'));
		Settings::SetRuntime('DBHANDLERS_DEBUGGING', $loggy);

	/** ----------------------------------------------------------------------------------------------- */
		$loggy = new SubSystemMessage('MENU_DEBUGGING', Settings::getPublic('IS_DETAILED_MENU_DEBUGGING'));
		Settings::SetRuntime('MENU_DEBUGGING', $loggy);

	/** ----------------------------------------------------------------------------------------------- */
		$loggy = new SubSystemMessage('DBHANDLERS_FLD_DEBUGGING', Settings::getPublic('IS_DETAILED_DATABASEHANDLERS_FLD_DEBUGGING'));
		Settings::SetRuntime('DBHANDLERS_FLD_DEBUGGING', $loggy);

	/** ----------------------------------------------------------------------------------------------- */
		$loggy = new SubSystemMessage('SimpleTableEditor', Settings::getPublic('IS_DETAILED_SIMPLE_TABLE_EDITOR_DEBUGGING'));
		Settings::SetRuntime('SIMPLE_DEBUGGING', $loggy);


	/** ----------------------------------------------------------------------------------------------- */
		$loggy = new SubSystemMessage('DISPATCHER_DEBUGGING', Settings::getPublic('IS_DETAILED_DISPATCH_QUEUE_DEBUGGING'));
		Settings::SetRuntime('DISPATCHER_DEBUGGING', $loggy);

	/** ----------------------------------------------------------------------------------------------- */
		$loggy = new SubSystemMessage('RESOLVER_DEBUGGING', Settings::getPublic('IS_DETAILED_RESOLVER_DEBUGGING'));
		Settings::setRunTime('RESOLVER_DEBUGGING', $loggy);

	/** ----------------------------------------------------------------------------------------------- */
		$loggy = new SubSystemMessage('PERMISSION_DEBUGGING', Settings::getPublic('IS_DETAILED_USERROLEANDPERMISSIONS_DEBUGGING'));
		Settings::SetRuntime('PERMISSION_DEBUGGING', $loggy);

	/** ----------------------------------------------------------------------------------------------- */
		$loggy = new SubSystemMessage('AUTHENTICATION_DEBUGGING', Settings::getPublic('IS_DETAILED_AUTHENTICATION_DEBUGGING'));
		Settings::SetRuntime('AUTHENTICATION_DEBUGGING', $loggy);





	/** ----------------------------------------------------------------------------------------------- */
		$loggy = new SubSystemMessage('IS_DETAILED_DBA_DEBUGGING', Settings::getPublic('IS_DETAILED_DBA_DEBUGGING'));
		Settings::SetRuntime('DBA_DEBUGGING', $loggy);

	/** ----------------------------------------------------------------------------------------------- */
		$loggy = new SubSystemMessage('IS_DETAILED_UTILS_DEBUGGING', Settings::getPublic('IS_DETAILED_UTILS_DEBUGGING'));
		Settings::SetRuntime('UTILS_DEBUGGING', $loggy);


	//Settings::getRunTimeObject('DBA_DEBUGGING')->addAlert('dbadebugging detail logging is turned on');


	//$x =Settings::GetRunTimeObject('MessageLog')->__toString();

	//dump::dump(Settings::GetRunTimeObject('MessageLog')->__toString());


}