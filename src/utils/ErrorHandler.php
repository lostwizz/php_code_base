<?php
//////////////////////////////////////////////////////////////
// ErrorHandler.php
//
//
//   https://www.php.net/manual/en/class.error.php
//
//////////////////////////////////////////////////////////////

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
//use \php_base\Utils\Dump\BackTraceProcessor as BackTraceProcessor;


if ( Settings::GetPublic('IS_DEBUGGING')) {
	ini_set( "display_startup_errors", 1);
	ini_set('display_errors', 1);
	$x = error_reporting( ~0 );              /////////0xFFFFFFF  );
} else {
	ini_set( "display_startup_errors", false);
	$x = error_reporting( E_ALL ^ E_NOTICE );          ///0xFFFFFFF ^ E_NOTICE );
}


echo 'HHII';
Dump::dump(set_error_handler('UserErrorHandler', E_ALL));
Dump::dump(set_error_handler('UserErrorHandler', E_ALL));

Dump::dump(set_exception_handler( 'myException_handler'));
Dump::dump(set_exception_handler( 'myException_handler'));


//***********************************************************************************************
//***********************************************************************************************
function myException_handler($exception)  {

echo '++here++';


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

	if (! Settings::GetPublic('IS_DEBUGGING')) {
		echo '</span>';
		echo '<script>document.getElementById("please_wait").style.display ="none";</script>';
		echo '<script>document.getElementById("screen").style.display ="inline";</script>';
	}


	$errLines = getTextAboutError($errno, $errstr, $errfile, $errline, $alternate_bt);




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


	$error_text = $errLines[0] . PHP_EOL;
	$error_text .= $errLines[1]. PHP_EOL;
	$error_text .= $errLines[2]. PHP_EOL;


	$error_text .= " - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - \n\n" ;
	$error_text .= '<hr size=3><Br>';
			$error_text .= "\n\n[[Back Trace==>\n" ;

	$bt = debug_backtrace();
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
	$error_text .= " - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - \n\n" ;

 	echo "\n" . '<fieldset style="background-color: LightGray; border-style: dashed; border-width: 1px; border-color: #950095;">' . "\n";
 	echo '<legend><font color=yellow style="background-color:red;font-size:160%;">Error BackTrace</font></legend>';
	//echo '<font color=navyblue style="background-color:LightGray;" >';
	echo nl2br($error_text);
	echo '</fieldset>';

}


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
