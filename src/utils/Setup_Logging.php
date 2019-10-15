<?php
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
//use Monolog\Formatter\HtmlFormatter;
use Monolog\Formatter\EmailHtmlFormatter;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump as Dump;

// get the log file name
$log_fn = Settings::GetPublic('Log_file' );



//Dump::dumpClasses('Monolog');
//Dump::dump($log_fn);
//-- alternate syntax --> //$log_fn = Settings::GetProtected('Log_file' );
//-- alternate syntax --> //$log_fn =  \whitehorse\MikesCommandAndControl2\Settings\Settings::GetProtected('Log_file' );

//echo 'WWWWWWWWWWWWWWWWWWWWWWWWWWWWWW Log_file=', $log_fn;


//////////////////////////////////////////////////////////////////////////////////////////
//create database logging


if (extension_loaded(Settings::GetProtected( 'Logging_Type') )) {

	/// setup database link
	$conn  = setup_PDO();
//Dump::dump( $pdo);

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
	//Settings::GetPublic('DBLog')->addInfo("hellow world");
	//////////////////////////////////////////////////////////////////////////////////////////


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
	//$dbDataLog->addRecord( Logger::ALERT, '-------------Starting Logging------------', ['username'=>'fred was here', 'super'=> 'sam was not here']);
	Settings::GetRuntime('DBdataLog')->addRecord( Logger::ALERT, '-------------Starting Logging------------', ['username'=>'fred was here', 'super'=> 'sam was not here']);
	//////////////////////////////////////////////////////////////////////////////////////////

}


//////////////////////////////////////////////////////////////////////////////////////////
// create a log channel
$log = new Logger( 'default');
$log->pushHandler( new StreamHandler( $log_fn, Logger::INFO));
Settings::SetRuntime('FileLog' , $log);
//$log->addRecord( Logger::ALERT, '-------------Starting Logging------------');
Settings::GetRuntime('FileLog')->addRecord( Logger::ALERT, '-------------Starting Logging------------');
//////////////////////////////////////////////////////////////////////////////////////////

//Dump::dump(get_class($log));



//////////////////////////////////////////////////////////////////////////////////////////
// create the Security Log Channel
$securityLog = new Logger('Security');
$security_log_fn =Settings::GetPublic('Security_Log_file');

//Dump::dump($security_log_fn);

$securityLog->pushHandler( new StreamHandler( $security_log_fn, Logger::DEBUG));
Settings::SetRuntime('SecurityLog' , $securityLog);
//$securityLog->addRecord( Logger::ALERT, '-------------Starting Logging------------');
//Settings::GetPublic('SecurityLog')->addRecord( Logger::ALERT, '-------------Starting Logging------------');
//////////////////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////////////////////////////
// setup an Email Logger (for critical errors)
$emailLog = new Logger( 'email');

//$handler = new Monolog\Handler\NativeMailerHandler(
$handler = new NativeMailerHandler(
                'mike.merrett@whitehorse.ca',
                'Critical error',
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
function setup_PDO(){
	if ( ! extension_loaded('pdo_sqlsrv')) {
		throw new Exception ('NOT loaded');
	}
	if ( empty( Settings::GetProtected( 'Logging_Server'))) {
		throw new Exception('Missing Config Data from Settings- Logging_Server');
	}
	if ( empty( Settings::GetProtected( 'Logging_Type'))) {
		throw new Exception('Missing Config Data from Settings- Logging_Type');
	}
	if ( empty(Settings::GetProtected( 'Logging_Database'))) {
		throw new Exception('Missing Config Data from Settings- Logging_Database');
	}
	if ( empty(Settings::GetProtected( 'Logging_DB_Username'))) {
		throw new Exception('Missing Config Data from Settings- Logging_DB_Username');
	}
	if ( empty(Settings::GetProtected( 'Logging_DB_Password'))) {
		throw new Exception('Missing Config Data from Settings- Logging_DB_Password');
	}

	$dsn =  Settings::GetProtected( 'Logging_Type')
			. ':server=' .  Settings::GetProtected( 'Logging_Server')
			. ';database=' .  Settings::GetProtected( 'Logging_Database');

	$options = 	array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_CASE=> PDO::CASE_LOWER,
						PDO::ATTR_ERRMODE =>PDO::ERRMODE_EXCEPTION
						//PDO::ATTR_PERSISTENT => true
						);
//Dump::dump($dsn)												;
	try {
		$conn = new \PDO($dsn,
						Settings::GetProtected('Logging_DB_Username'),
						Settings::GetProtected('Logging_DB_Password'),
						$options
						);
//Dump::dump($conn);
	} catch (\PDOException $e)				{
//Dump::dump($e->getMessage());
		throw new \PDOException($e->getMessage(), (int)$e->getCode());
	}
	return $conn;
}
//////////////////////////////////////////////////////////////////////////////////////////
