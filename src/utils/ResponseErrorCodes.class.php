<?php

/** * ********************************************************************************************
 * ResponseErrorCodes.class.php
 *
 * Summary: make up a list of the errors that may happen and assign them an error code
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description.
 *   list of possible errors and warnings in the application
 *
 * @package utils
 * @subpackage Response
 * @since 0.3.0
 *
 * @example
 *
 * @see Response
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\Utils;

//use \php_base\Utils\Settings as Settings;
//use \php_base\Utils\Dump\Dump as Dump;

/** * ********************************************************************************************
 * list of all possible errors in the application
 */
abstract class ResponseErrorCodes {

	const TODO = -999999;
	const TestingError = -9999;

	/**
	 *
	 * @var array of possible errors
	 */
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
		-9999 => 'testing error',
		-999999 => 'DODO'
	);

	/** -----------------------------------------------------------------------------------------------
	 * for a code give the message
	 * @param type $errNo
	 * @return string
	 */
	public static function giveErrorMessage($errNo) {
		if (array_key_exists($errNo, self::$errors)) {
			return self::$errors[$errNo];
		} else {
			return '-Unknown Error Code-';
		}
	}

}
