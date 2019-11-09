<?php

/** * ********************************************************************************************
 * AuthenticateModel.class.php
 *
 * Summary: reads the user's attributes from the database
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description:
 * Reads the userid and password from something - DB or file.
 *
 *
 * @link URL
 *
 * @package ModelViewController - AuthenticateModel
 * @subpackage Authenticate
 * @since 0.3.0
 *
 * @example
 *
 * @see AuthenticateController.class.php
 * @see AuthenticateView.class.php
 *
 * @todo Description
 *
 */
//**********************************************************************************************



//***********************************************************************************************************
/*  use something like this to hash the password
/*  $options = [    'cost' => 12,];
/* echo password_hash("rasmuslerdorf", PASSWORD_DEFAULT, $options);

or
sodium_crypto_pwhash_str( $pw, SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE , SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE);
*/

namespace php_base\Model;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

/** * **********************************************************************************************
 * business logic for the authentication
 */
class AuthenticateModel extends Model {


	/*
	 * the current user
	 */
	protected static $User;

	/** -----------------------------------------------------------------------------------------------
	 *  a check to see if the user is logged in (timeouts and bad passwords type situations may return basically  false
	 * @param type $username
	 * @param type $password
	 * @return Response
	 */
	public function isLoggedIn($username, $password): Response {

		if (!empty($username)) {
			//echo 'Checking login:',  $username;
			//echo '<br>';
			if ($this->isGoodAuthentication($username, $password)) {
				Settings::GetRunTimeObject('MessageLog')->addInfo('User: ' . $username . ' is logged on');
				// user and password are good so they is logged in
				self::$Uname - $password;
				Settings::SetRunTime('Currently Logged In User', $username);
			} else {
				$username = null;
			}
		} else {
			$username = null;
		}
		Settings::GetRunTimeObject('MessageLog')->addNotice('username=' . $username . (empty($username) ? 'NOT-logged in' : 'Seems to be Loggedin'));
		return (!empty($username));
	}

	/** -----------------------------------------------------------------------------------------------
	 * given a username and a password see if the password is valid for that username
	 * @param type $username
	 * @param type $password
	 * @return Response
	 */
	public function tryToLogin($username, $password): Response {

		Settings::GetRunTimeObject('MessageLog')->addTODO('check the username against somthing -db or file or hard or whatever!!!');
		if (empty($username) or empty($password)) {
			return new Response('Missing Username or Password trying to login', -5, false);
		}

		//////////////// -DEBUG CODE
		if ($username == $password) {
			Settings::GetRunTimeObject('MessageLog')->addInfo('User: ' . $username . ' Sucessfully Logged in');
			self::$User = $username;
			Settings::SetRunTime('Currently Logged In User', $username);
			return Response::NoError();
		} else {
			Settings::GetRunTimeObject('MessageLog')->addNotice('User: ' . $username . ' UNSucessfully Logged in!!!!');
			self::$User = null;
			return new Response('Failed Trying to Login', -4, false);
		}
//		return false;
	}

	/** -----------------------------------------------------------------------------------------------
	 * test if the username and password are good
	 * @param type $passedUsername
	 * @return boolean
	 */
	public function isGoodAuthentication($passedUsername) {

		///////////////////// DEBUG CODE
		if ($passedUsername = self::Uname) {
			return true;
		} else {
			return false;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * default method called if something goes wrong - should never get here
	 * @return Response
	 */
	public function doWork(): Response {
		// should never get here
		return Response::NoError();
	}

}
