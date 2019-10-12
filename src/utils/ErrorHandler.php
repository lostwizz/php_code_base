<?php
//////////////////////////////////////////////////////////////
// ErrorHandler.php
//
//
//   https://www.php.net/manual/en/class.error.php
//
//////////////////////////////////////////////////////////////

use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;


if ( Settings::GetPublic('IS_DEBUGGING')) {
	ini_set( "display_startup_errors", 1);
	ini_set('display_errors', 1);
	$x = error_reporting( ~0 );              /////////0xFFFFFFF  );
} else {
	ini_set( "display_startup_errors", false);
	$x = error_reporting( E_ALL ^ E_NOTICE );          ///0xFFFFFFF ^ E_NOTICE );
}


if ( ! Settings::GetPublic('IS_DEBUGGING')) {
	set_error_handler('UserErrorHandler');
	set_error_handler('UserErrorHandler');
	set_exception_handler('exception_handler');
}

//***********************************************************************************************
//***********************************************************************************************
function exception_handler($e) {

	UserErrorHandler( $e->getCode(),
							$e->getMessage() . '(Error Code:' . $e->getCode . ')',
							$e->getFile(),
							$e->getLine(),
							$e->getTrace()
						);
}


//***********************************************************************************************
//***********************************************************************************************
function UserErrorHandler($errno, $errstr, $errfile, $errline, $alternate_bt=null) {

	if (! Settings::GetPublic('IS_DEBUGGING')) {
		echo '</span>';
		echo '<script>document.getElementById("please_wait").style.display ="none";</script>';
		echo '<script>document.getElementById("screen").style.display ="inline";</script>';
	}

	// define an assoc array of error string
	// in reality the only entries we should
	// consider are 2,8,256,512 and 1024
	$errortype = array (
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

	$logMsg1 = '-----------ERROR-----------';
	$logMsg2  = 'ErrorNo:';
	$logMsg2 .= empty($errno) ? '' : $errno;
	$logMsg2 .= ' - ';
	$logMsg2 .= empty( $errortype[$errno]) ? '' : $errortype[$errno];
	$logMsg3  = 'ErrorStr:'. $errstr;
	$logMsg3 .= '(Line:' . $errline . ') ' . $errfile;

	if ( Settings::GetPublic('DBLog')) {
		Settings::GetPublic('DBLog')->addCritical($logMsg1);
		Settings::GetPublic('DBLog')->addCritical($logMsg2);
		Settings::GetPublic('DBLog')->addCritical($logMsg3);
	}

	if (Settings::GetPublic('FileLog')){
		Settings::GetPublic('FileLog')->addCritical($logMsg1);
		Settings::GetPublic('FileLog')->addCritical($logMsg2);
		Settings::GetPublic('FileLog')->addCritical($logMsg3);
	}




}