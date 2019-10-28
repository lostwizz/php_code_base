<?php
//**********************************************************************************************
//* setup_Logging.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-12 09:46:20 -0700 (Thu, 12 Sep 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 12-Sep-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************
/**
 * Setup_Logging.php
 *
 */

namespace php_base\Utils;

//echo DIR . 'vendor/autoload.php';
require_once(DIR . 'vendor/autoload.php');
require_once(DIR . 'utils' . DS . 'PDOHandler.php');
require_once(DIR . 'utils' . DS . 'PDOdataHandler.php');
require_once(DIR . 'utils' . DS . 'EmailHtmlFormatter.php');

use \PDO;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\PDOHandler;
use Monolog\Handler\PDODataHandler;

use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Formatter\EmailHtmlFormatter;

use \php_base\Utils\myUtils as myUtils;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

// get the log file name
$log_fn = Settings::GetPublic('Log_file' );


//Dump::dumpClasses('Monolog');
//Dump::dump($log_fn);
//-- alternate syntax --> //$log_fn = Settings::GetProtected('Log_file' );
//-- alternate syntax --> //$log_fn =  \whitehorse\MikesCommandAndControl2\Settings\Settings::GetProtected('Log_file' );


//////////////////////////////////////////////////////////////////////////////////////////
//create database logging


if (extension_loaded(Settings::GetProtected('database_extension_needed') )) {

	/// setup database link
	//$conn  = setup_PDO();
	$conn = myUtils::setup_PDO();
//Dump::dump( $pdo);

	if (Settings::GetPublic( 'Use_DBLog')) {

		$dbLog = new Logger('DBLog');
	//Dump::dump($dbLog);
		$dbTable = Settings::GetProtected('Logging_DB_Table');
		//$dbLog->setTableName( $dbTable);
		$dbLog->pushHandler (new \Monolog\Handler\PDOHandler( $conn, Logger::INFO, true, $dbTable));
		$dbLog->pushProcessor( new IntrospectionProcessor());
		$dbLog->pushProcessor( new \Monolog\Processor\ProcessIdProcessor());
		$dbLog->pushProcessor( new WebProcessor());
		Settings::SetRuntime('DBLog' , $dbLog);

					//$dbLog->addRecord( Logger::ALERT, '-------------Starting Logging------------', ['username'=>'fred was here', 'super'=> 'sam was not here']);

		Settings::GetRuntime('DBLog')->addRecord( Logger::ALERT, '-------------Starting Logging------------', ['username'=>'fred was here', 'super'=> 'sam was not here']);
		//Settings::GetRuntime('DBLog')->addInfo("hellow world");
		//////////////////////////////////////////////////////////////////////////////////////////

	}
	if (Settings::GetPublic( 'Use_DBDataLog')) {
		//////////////////////////////////////////////////////////////////////////////////////////
		//create database logging
		$dbDataLog = new Logger('DBData');
		//$pdoData  = setup_PDO();
		$dbTable = Settings::GetProtected('Data_Logging_DB_Table');
		$dbDataLog->pushHandler( new \Monolog\Handler\PDODataHandler( $conn, Logger::INFO, true, $dbTable));
		$dbDataLog->pushProcessor( new IntrospectionProcessor());
		$dbDataLog->pushProcessor( new \Monolog\Processor\ProcessIdProcessor());
		$dbDataLog->pushProcessor( new WebProcessor());

		Settings::SetRuntime('DBdataLog' , $dbDataLog);
	//Dump::dump('++++++++++++++++++++++ herre');
	//Dump::dump($dbDataLog);

		$dbDataLog->addRecord( Logger::ALERT, '-------------Starting Logging------------', ['XXusername'=>'fred was here', 'super'=> 'sam was not here']);

		//Settings::GetRuntime('DBdataLog')->addRecord( Logger::ALERT, '-------------Starting Logging------------', ['CCCCusername'=>'fred was here', 'super'=> 'sam was not here']);
		//////////////////////////////////////////////////////////////////////////////////////////
	}
}


//////////////////////////////////////////////////////////////////////////////////////////
if (Settings::GetPublic( 'Use_FileLog')) {
	// create a log channel
	$log = new Logger( 'default');
	$log->pushHandler( new StreamHandler( $log_fn, Logger::INFO));
	Settings::SetRuntime('FileLog' , $log);
	//$log->addRecord( Logger::ALERT, '-------------Starting Logging------------');
	Settings::GetRuntime('FileLog')->addRecord( Logger::ALERT, '-------------Starting Logging------------');
	//////////////////////////////////////////////////////////////////////////////////////////

	//Dump::dump(get_class($log));
}


//////////////////////////////////////////////////////////////////////////////////////////
if (Settings::GetPublic( 'Use_SecurityLog') ) {
	// create the Security Log Channel
	$securityLog = new Logger('Security');
	$security_log_fn =Settings::GetPublic('Security_Log_file');

	//Dump::dump($security_log_fn);

	$securityLog->pushHandler( new StreamHandler( $security_log_fn, Logger::DEBUG));
	Settings::SetRuntime('SecurityLog' , $securityLog);
	//$securityLog->addRecord( Logger::ALERT, '-------------Starting Logging------------');
	//Settings::GetPublic('SecurityLog')->addRecord( Logger::ALERT, '-------------Starting Logging------------');
	//////////////////////////////////////////////////////////////////////////////////////////
}

//////////////////////////////////////////////////////////////////////////////////////////
if (Settings::GetPublic( 'Use_EmailLog')) {
// setup an Email Logger (for critical errors)
	$emailLog = new Logger( 'email');

	//$handler = new Monolog\Handler\NativeMailerHandler(
	$handler = new NativeMailerHandler(
	                'mike.merrett@whitehorse.ca',
	                'System Error in ' . Settings::GetPublic('App Name'),
	                Settings::GetPublic('App Name') . '@'. $_SERVER['SERVER_NAME'] ,
			);

	$handler->setContentType('text/html' );
	$handler->addHeader( "MIME-Version: 1.0");

	// gather as much detail as possible for the email
	$emailLog->pushProcessor( new IntrospectionProcessor());
	$emailLog->pushProcessor( new \Monolog\Processor\ProcessIdProcessor());
	$emailLog->pushProcessor( new WebProcessor());

	// make the email look pretty
	$handler->setFormatter( new EmailHtmlFormatter());
	$emailLog->pushHandler( $handler);
	$emailLog->pushHandler( new StreamHandler( $log_fn, Logger::WARNING));
	Settings::SetRuntime('EmailLog' , $emailLog);

}

//////////////////////////////////////////////////////////////////////////////////////////


////////////////
// cleanup vars not needed anymore
unset($dbTable);
unset($log_fn);
unset($pdo);

////////////////
// example or for testing

//   $emailLog->addCritical('Hey, a critical log entry!', [['key'=>'value'], ['second key'=> 'second value']]);

// this wont send as it is not critical
//$emailLog->info( 'hi');




//////////////////////////////////////////////////////////////////////////////////////////
// setup pdo connection to the database
//////////////////////////////////////////////////////////////////////////////////////////
//function setup_PDO(){
//	if ( ! extension_loaded(Settings::GetProtected('database_extension_needed'))) {
//		throw new Exception ('NOT loaded');
//	}
//	if ( empty(Settings::GetProtected( 'DB_Username'))) {
//		throw new Exception('Missing Config Data from Settings- DB_Username');
//	}
//	if ( empty(Settings::GetProtected( 'DB_Password'))) {
//		throw new Exception('Missing Config Data from Settings- DB_Password');
//	}
//
//	$dsn = Settings::GetProtected( 'DB_DSN');
//	$options= Settings::GetProtected( 'DB_DSN_OPTIONS');
//	try {
//		$conn = new \PDO($dsn,
//						Settings::GetProtected('DB_Username'),
//						Settings::GetProtected('DB_Password'),
//						$options
//						);
//	} catch (\PDOException $e)				{
//		throw new \PDOException($e->getMessage(), (int)$e->getCode());
//	}
//	return $conn;
//}
//////////////////////////////////////////////////////////////////////////////////////////
/////zzzzzzzzzzz