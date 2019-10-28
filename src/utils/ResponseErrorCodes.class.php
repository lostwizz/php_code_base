<?php
//**********************************************************************************************
//* ResponseErrorCodes.class.php
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

namespace php_base\Utils;


//use \php_base\Utils\Settings as Settings;
//use \php_base\Utils\Dump\Dump as Dump;

//***********************************************************************************************
//***********************************************************************************************
abstract class ResponseErrorCodes {

	protected static $errors = array(
			    2 => 'All is good',
			    1 => 'Generic Warning all is good',
			    0 => 'Not an Error',
			   -1 => 'Generic Warning something might be wrong',
			   -2 => 'Generic Error',
			   -3 => 'Invalid Login',
			   -4 => 'Failed login attempt',
			   -5 => 'Missing Username or Password trying to login',
			   -6 => 'Username not supplied to LoadPermissions',
			   -7 => 'something happended when trying to load all permissions',
			   -8 => ' ',
			   -9 => ' ',
			  -10 => ' ',
			  -11 => 'XXXXXXX',

			-9999 => 'testing error'
		);


	//-----------------------------------------------------------------------------------------------
	public static function giveErrorMessage( $errNo){
		if ( array_key_exists( $errNo, self::$errors )){
			return self::$errors[$errNo];
		} else {
			return '-Unknown Error Code-';
		}
	}
}
