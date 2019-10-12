<?php
//**********************************************************************************************
//* autoload.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-03 09:15:37 -0700 (Tue, 03 Sep 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 03-Sep-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************


if (!defined('DIR')){
	define ('DS', DIRECTORY_SEPARATOR);
	if ( strripos (realpath('.'), 'src' ) <1 ){
		define('DIR', realpath('.') . DS . 'src' . DS);
	} else {
		define('DIR', realpath('.') . DS );
	}
}
//echo '****************************' . DIR . '*****************************';

//***********************************************************************************************
//***********************************************************************************************
function myAutoLoader($class){
	$ex= explode('\\', $class );    // get the name of just the file - eg sam\fred gives fred
				//echo "<font color=magenta>(Looking for:" . $ex[ count($ex)-1] . ')</font>' . PHP_EOL;

	$base= $ex[ count($ex)-1] . '.class.php';
	if (runTheChecks( $base)) 	return true;

	$base= $ex[ count($ex)-1] . '.php';
	if (runTheChecks( $base)) 	return true;

	$base= strtolower($ex[ count($ex)-1]) . '.class.php';
	if (runTheChecks( $base)) 	return true;

	$base= strtolower($ex[ count($ex)-1]) . '.php';
	if (runTheChecks( $base)) 	return true;

	return false;
}



//********************************************************************************
function runTheChecks($base){
			//echo "Looking for:". $base . '<BR>' . PHP_EOL;
			//fwrite(STDERR, "\n\rLooking for:". print_r($base, TRUE));

	if (  tryFile( DIR . 'model'  . DS .  $base ))  				return true;
	if (  tryFile( DIR . 'view'   . DS .  $base )) 					return true;
	if (  tryFile( DIR . 'control'. DS .  $base ))					return true;
	if (  tryFile( DIR . 'data'   . DS .  $base ))					return true;
	if (  tryFile( DIR . 'utils'  . DS .  $base ))					return true;
	if (  tryFile( DIR . 'utils'  . DS . 'setup' . DS . $base ))	return true;
	if (  tryFile( DIR . 'utils'  . DS . 'log'   . DS . $base ))	return true;
	if (  tryFile( DIR . $base ))									return true;
	if (  tryFile( DIR . 'static' . DS .  $base ))					return true;
	return false;
}




//********************************************************************************
function tryFile( $fn){
	if ( file_exists( $fn) ) {
			//echo "<font color=magenta>FOUND:". $fn . ')</font><BR>' . PHP_EOL;
			//fwrite(STDERR, "\n\rFOUND:". print_r(  $fn , TRUE));
		$x = include_once( $fn);
		return $x;
	}  else {
			//echo "NotFOUND:". $fn . '<BR>' . PHP_EOL;
			//echo '.';
			//fwrite(STDERR, "\n\rNOTFOUND:". print_r(  $fn , TRUE));
		return false;
	}
}

//********************************************************************************
spl_autoload_register ('myAutoLoader');



//require_once __DIR__ . '/index.php';
//require_once __DIR__ . '/vendor/autoload.php';
