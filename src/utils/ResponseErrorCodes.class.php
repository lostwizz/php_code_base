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


//- http response codes
//
//https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
//
//https://www.tutorialspoint.com/http/http_status_codes.htm

/*
 * S.N.
Code and Description
1
1xx: Informational
This means request received and continuing process.
2
2xx: Success
This means the action was successfully received, understood, and accepted.
3
3xx: Redirection
This means further action must be taken in order to complete the request.
4
4xx: Client Error
This means the request contains bad syntax or cannot be fulfilled
5
5xx: Server Error
The server failed to fulfill an apparently valid request
 *
 *
 * 1×× Informational
100 Continue
101 Switching Protocols
102 Processing
2×× Success
200 OK
201 Created
202 Accepted
203 Non-authoritative Information
204 No Content
205 Reset Content
206 Partial Content
207 Multi-Status
208 Already Reported
226 IM Used
3×× Redirection
300 Multiple Choices
301 Moved Permanently
302 Found
303 See Other
304 Not Modified
305 Use Proxy
307 Temporary Redirect
308 Permanent Redirect
4×× Client Error
400 Bad Request
401 Unauthorized
402 Payment Required
403 Forbidden
404 Not Found
405 Method Not Allowed
406 Not Acceptable
407 Proxy Authentication Required
408 Request Timeout
409 Conflict
410 Gone
411 Length Required
412 Precondition Failed
413 Payload Too Large
414 Request-URI Too Long
415 Unsupported Media Type
416 Requested Range Not Satisfiable
417 Expectation Failed
418 I'm a teapot
421 Misdirected Request
422 Unprocessable Entity
423 Locked
424 Failed Dependency
426 Upgrade Required
428 Precondition Required
429 Too Many Requests
431 Request Header Fields Too Large
444 Connection Closed Without Response
451 Unavailable For Legal Reasons
499 Client Closed Request
5×× Server Error
500 Internal Server Error
501 Not Implemented
502 Bad Gateway
503 Service Unavailable
504 Gateway Timeout
505 HTTP Version Not Supported
506 Variant Also Negotiates
507 Insufficient Storage
508 Loop Detected
510 Not Extended
511 Network Authentication Required
599 Network Connect Timeout Error
 */


namespace php_base\Utils;

//use \php_base\Utils\Settings as Settings;
//use \php_base\Utils\Dump\Dump as Dump;

/** * ********************************************************************************************
 * list of all possible errors in the application
 */
abstract class ResponseErrorCodes {

	const TODO = -999999;
	const TestingError = -9999;

	const PermissionsError = -9000;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';
	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}

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
		-8 => 'The Authentication Method doesnt exist',
		-9 => 'HardCoded password failed',
		-10 => 'Username does not exist',
		-11 => 'db_table password failed',
		-12 => 'Unable to email the new password to the user',
		-13 => 'Missing username for Change password',
		-14 => 'Missing old password for Change password',
		-15 => 'Missing new password for Change password',
		-16 => 'Old password did not match for Change Password',
		-17 => 'New password was NOT saved for Change Password',
		-18 => 'username has not been set',
		-19 => 'missing username for new account',
		-20 => 'Missing password for new account',
		-21 => 'Missing email address for new account',
		-22 => 'LDAP not loaded in PHP - cant login' ,
		-23 => '',

		-9000 => 'Permissions Error',
		-9999 => 'testing error',
		-999999 => 'TODO'
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
