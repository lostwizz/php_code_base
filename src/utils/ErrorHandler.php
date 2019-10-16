<?php
//////////////////////////////////////////////////////////////
// ErrorHandler.php
//
//
//   https://www.php.net/manual/en/class.error.php
//
//////////////////////////////////////////////////////////////

////////
////////use Monolog\Handler\HandlerInterface;
////////use Monolog\Handler\StreamHandler;
////////use Psr\Log\LoggerInterface;
////////use Psr\Log\InvalidArgumentException;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;


if ( Settings::GetPublic('IS_DEBUGGING')) {
	ini_set( "display_startup_errors", 1);
	ini_set('display_errors', 1);
	$x = error_reporting( ~0 );              /////////0xFFFFFFF  );
	Settings::SetProtected('SurpressErrorHandlerDetails', 'NO');
} else {
	// not debugging
	ini_set( "display_startup_errors", false);
	$x = error_reporting( E_ALL ^ E_NOTICE );          ///0xFFFFFFF ^ E_NOTICE );
	Settings::SetProtected('SurpressErrorHandlerDetails', 'YES');

	Dump::dump(set_error_handler('UserErrorHandler', E_ALL));
	Dump::dump(set_error_handler('UserErrorHandler', E_ALL));

	Dump::dump(set_exception_handler( 'myException_handler'));
	Dump::dump(set_exception_handler( 'myException_handler'));
}




//***********************************************************************************************
//***********************************************************************************************
function myException_handler($exception)  {

	UserErrorHandler( $exception->getCode(),
							$exception->getMessage() ,
								//. '(Error Code:' . $exception->getCode . ')',
							$exception->getFile(),
							$exception->getLine(),
							$exception->getTrace()
						);
}


//***********************************************************************************************
//***********************************************************************************************
//$alternate_bt=null
function UserErrorHandler($errno, $errstr, $errfile, $errline, $alternate_bt) {

	$errLines = getTextAboutError($errno, $errstr, $errfile, $errline, $alternate_bt);
	$bt = debug_backtrace();

	$backTraceLines = getBackTraceLines($bt);

	if ( Settings::GetRuntime('DBLog')) {
		saveLog(Settings::GetRuntime('DBLog'), $errLines, $backTraceLines);
	}

	if (Settings::GetRuntime('FileLog')){
		saveLog(Settings::GetRuntime('FileLog'), $errLines, $backTraceLines);
	}

	if (Settings::GetRuntime('EmailLog') and Settings::GetProtected('SurpressErrorHandlerDetails') =='YES'){
		sendEmailLog(Settings::GetRuntime('EmailLog'), $errLines, $backTraceLines,$errno);
	}

	if (Settings::GetProtected('SurpressErrorHandlerDetails') =='NO'){
		$error_text = $errLines[0] . PHP_EOL;
		$error_text .= $errLines[1] . PHP_EOL;
		$error_text .= $errLines[2] . PHP_EOL;
		$error_text .= '<hr color=red size=9><font color=red><Br>';

		$error_text .= " - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - \n\n" ;
		$error_text .= "\n\n[[Back Trace==>\n" ;
		$error_text .= $backTraceLines . PHP_EOL;

		$error_text .= " - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - \n\n" ;

	 	echo "\n" . '<fieldset style="background-color: LightGray; border-style: dashed; border-width: 1px; border-color: #950095;">' . "\n";
	 	echo '<legend><font color=yellow style="background-color:red;font-size:160%;">Error BackTrace</font></legend>';
		//echo '<font color=navyblue style="background-color:LightGray;" >';
		echo nl2br($error_text);
		echo '</fieldset></font>';
	} else {
		$error_text = '<hr color=red size=9><font color=red style="font-size:160%;"><Br>';
		$error_text .= 'An Error has occured.' . PHP_EOL;
		$error_text .= Settings::GetProtected('Critical_email_TO_ADDR') . ' has been notified by email ';

		echo nl2br($error_text);
		die;
	}
}


//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
function getTextAboutError($errno, $errstr, $errfile, $errline, $alternate_bt) {

	// define an assoc array of error string
	// in reality the only entries we should
	// consider are 2,8,256,512 and 1024
	$errortype = array (0    =>  "Unknown",
						1    =>  "Error",
						2    =>  "Warning",
						4    =>  "Parsing Error",
						8    =>  "Notice",
						16   =>  "Core Error",
						32   =>  "Core Warning",
						64   =>  "Compile Error",
						128  =>  "Compile Warning",
						256  =>  "User Error",
						512  =>  "User Warning",
						1024 =>  "User Notice",
						2048 =>  "Strict",
						4096 =>  "Recoverable Error",
						8192 =>  "Depreciated",
						16384=> "User Deprecated",
						32767=>  "ALL",
						);

	$line1 = " -*-*-*- A error has been caught. -*-*-*-\n" . "Date: " . date("F j, Y, g:ia") ;
	$line2 = 'Error #:' . $errno . ' - ';
	if ( ! empty( $errortype[$errno])){
		$line2 .= $errortype[$errno];
	}
	$line3 = 'ErrorStr:'. $errstr . ' File:' . $errfile . '(Line:' . $errline . ') ';
	return array ( $line1, $line2, $line3);
}


//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
function getBackTraceLines($bt) {
	$error_text = '';
	foreach( $bt as $bt_func) {
		if ( ! empty(  $bt_func['file'] )) {
			$error_text  .=  "<b>" . $bt_func['file'] . "</b>"
								. ":" . $bt_func['line']
								. "&nbsp;&nbsp;&nbsp;("
								;
		}
		$error_text  .= $bt_func['function']
							. ')'
							. "\n"
							;
	}
	$to_be_exported= print_r( $bt, true);
	$x = str_replace( ' ',  "&nbsp;",$to_be_exported);
	$error_text .= $x . "]]\n";
	return $error_text;
}


//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
function saveLog( $logObject, $errLines, $backTraceLines) {
	$logObject->addCritical($errLines[0]);
	$logObject->addCritical($errLines[1]);
	$logObject->addCritical($errLines[2]);

	$btLines = str_replace(   "&nbsp;", ' ',$backTraceLines);
	$logObject->addCritical($btLines);
}


//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
function sendEmailLog($emailLogObject, $errLines, $backTraceLines, $errno) {

	$btLines = str_replace(   "&nbsp;", ' ',$backTraceLines);
	$details = array(
					'details' => $errLines,
					'bt' => $btLines,
					'server' =>$_SERVER,
					'request' => $_REQUEST,
	);

	if ($errno >= 256){  //  user error and above in the $errortype
		Settings::SetPublic( 'CRITICAL_EMAIL_PAYLOAD_EXTRA', Settings::dump(true, false));
		/*
		//     you can pass more info with these two Settings
			Settings::SetPublic( 'CRITICAL_EMAIL_PAYLOAD_CONTEXT', array('hi'=>'mike'));
		*/
		$emailLogObject->addError($errLines[2], $details);
	} else {
		Settings::SetPublic( 'CRITICAL_EMAIL_PAYLOAD_EXTRA', Settings::dump(true, true));
		/*
		//     you can pass more info with these two Settings
			Settings::SetPublic( 'CRITICAL_EMAIL_PAYLOAD_CONTEXT', array('hi'=>'mike'));
		*/
		$emailLogObject->addCritical($errLines[2], $details);
	}
}

//-----------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------
