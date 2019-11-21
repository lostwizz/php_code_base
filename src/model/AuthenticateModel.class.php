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
use \php_base\Utils\Utils as Utils;

/** * **********************************************************************************************
 * business logic for the authentication
 */
class AuthenticateModel extends Model {
	/*
	 * the current user
	 */

	protected static $User;

	/** -----------------------------------------------------------------------------------------------
	 * default method called if something goes wrong - should never get here
	 * @return Response
	 */
	public function doWork(): Response {
		// should never get here
		return Response::NoError();
	}

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
	public function tryToLogin(string $username, string $password, $userInfoData): Response {

		$method = 'LogonMethod_' . $userInfoData->UserInfo['METHOD'];

		//Settings::GetRunTimeObject('MessageLog')->addInfo('Trying ' . $method );

		if (method_exists($this, $method)) {
			Settings::GetRunTimeObject('MessageLog')->addInfo('Trying ' . $method);
			$response = $this->$method($username, $password, $userInfoData);
			if ($response->giveErrorCode() == 0) {
				Settings::SetRunTime('Currently Logged In User', $username);
			}
		} else {
			Settings::GetRunTimeObject('MessageLog')->addInfo('Authentication Method: ' . $method . 'Does NOT exist for:' . $username);
			$response = new Response('Authentication Method doenst exist', -8);
		}
		return $response;
	}

	/** -----------------------------------------------------------------------------------------------
	 * test if the username and password are good
	 * @param type $passedUsername
	 * @return boolean
	 */
	public function isGoodAuthentication($passedUsername) {


		return false;
		///////////////////// DEBUG CODE
//		if ($passedUsername = self::Uname) {
//			return true;
//		} else {
//			return false;
//		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $passedUsername
	 * @param type $UserInfoData
	 * @return Response
	 */
	public function doPasswordForgot($passedUsername, $UserInfoData): Response {
		// generate new password
		$newPW = Utils::makeRandomPassword();
		dump::dump($newPW);
		$pwd = password_hash($newPW, PASSWORD_DEFAULT);

		// save the new password
		if (!empty($UserInfoData->UserInfo['USERID'])) {
			$r = $UserInfoData->doUpdatePassword($UserInfoData->UserInfo['USERID'], $pwd);
		}

		// send email with new password
		$r = $this->sendEmailForPasswordForgot($newPW, $UserInfoData->UserInfo['EMAIL']);

		if ($r === false) {
			Settings::GetRunTimeObject('MessageLog')->addError('Unable to email the new password');
			return new Response('Unable to email the new password to the user', -12);
		}
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return bool
	 */
	protected function sendEmailForPasswordForgot(string $newPW, string $emailAddr): bool {
		dump::dump(ini_get('SMTP'));
		dump::dump(ini_get('smtp_port'));
		//Settings::GetRunTimeObject('MessageLog')->addInfo( 'snmp: ' . ini_get('SMTP') );
		//Settings::GetRunTimeObject('MessageLog')->addInfo( 'snmp_port: ' . ini_get('smtp_port') );
		if (empty(ini_get('SMTP'))
				or empty(ini_get('smtp_port'))
				or ( ini_get('SMTP') == 'localhost')) {
			$r = false;
		} else {
			try {
				$message = "\n\r"
						. 'Application ' . Settings::GetPublic('App Name')
						. ' has requested a new password ' . "\n\r"
						. ' the new password is: ' . $newPW
						. "\n\r"
						. ' You can change it by using the "Change Password" button on the Logon Page'
						. "\n\r"
						. ' If you did not ask for this password change, please contact you application Administrator'
						. "\n\r";

				$r = mail($emailAddr,
						'Application: ' . Settings::GetPublic('App Name'),
						$message,
						array(
							'From' => Settings::GetPublic('Email_From')
						)
				);
			} catch (Exception $e) {
				Settings::GetRunTimeObject('MessageLog')->addError('Unable to email the new password');
				return false;
			}
		}
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $username
	 * @param type $old_password
	 * @param type $new_password
	 * @param type $UserInfoData
	 * @return Response
	 */
	public function doChangePassword($username, $old_password, $new_password, $UserInfoData): Response {
		if (empty($username)) {
			return new Response('missing usernamefor change password', -13);
		}
		if (empty($old_password)) {
			return new Response('Missing old password for Change password', -14);
		}
		if (empty($new_password)) {
			return new Response('Missing new password for Change password', -15);
		}
//dump::dump( $UserInfoData);
		// verify old password
		//if (password_verify($old_password, Settings::GetProtected('Password_for_' . $username))) {
		if (password_verify($old_password, $UserInfoData->UserInfo['PASSWORD'])) {

			//encode the new password
			$pwd = password_hash($new_password, PASSWORD_DEFAULT);

			// save the new password
			$rUpdate = $UserInfoData->doUpdatePassword($UserInfoData->UserInfo['USERID'], $pwd);
			if ($rUpdate) {
				$r = Response::NoError();
			} else {
				$r = new Response('New password was NOT saved for Change Password', -17);
			}
		} else {
			$r = new Response('Old password did not match for Change Password', -16);
			Settings::GetRunTimeObject('MessageLog')->addError('Old password did not match for Change Password');
		}

		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param string|null $primaryRole
	 * @return Response
	 */
	public function doNewAccountInfo( string $username, string $password, string $email, ?string $primaryRole =null): Response {
		if (empty($username)) {
			return new Response('missing username for new account', -19);
		}
		if (empty($password)) {
			return new Response('Missing password for new account', -20);
		}
		if (empty($email)) {
			return new Response('Missing email address for new account', -21);
		}

	//	$permController = new \php_base\control\UserRoleAndPermissionsController();
				// not set yet so not relevan even if set //Settings::GetRunTime('userPermissionsController');
//dump::dump( $permController);
		$UserInfoData = new \php_base\data\UserInfoData();
		//$rInsert = $UserInfoData->doInsertNewAccount($username, $password, $email);
		$pwd = password_hash($password, PASSWORD_DEFAULT);

		$UserInfoData->doInsertNewAccount($username, $pwd, $email, $primaryRole);
		return Response::NoError();
	}







	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $username
	 * @param string $password
	 * @param type $userInfoData
	 * @return Response
	 */
	public function LogonMethod_DB_Table(string $username, string $password, $userInfoData): Response {
		if (\password_verify($password, $userInfoData->UserInfo['PASSWORD'])) {
			Settings::GetRunTimeObject('MessageLog')->addAlert('Successful LOGON of ' . $username . ' using DB_TABLE password');
			return Response::NoError();
		}
		Settings::GetRunTimeObject('MessageLog')->addAlert('UNSuccessful logon DB_TABLE password for: ' . $username);
		return new Response('db_table password failed', -11);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $username
	 * @param string $password
	 * @param type $userInfoData
	 * @return Response
	 */
	public function LogonMethod_HardCoded(string $username, string $password, $userInfoData): Response {
		//$pwd = \password_hash($password, PASSWORD_DEFAULT);
//dump::dump($pwd);

		if (\password_verify($password, Settings::GetProtected('Password_for_' . $username))) {
			Settings::GetRunTimeObject('MessageLog')->addAlert('Successful Hardcoded password for: ' . $username);
			return Response::NoError();
		}
		Settings::GetRunTimeObject('MessageLog')->addAlert('UNSuccessful Hardcoded password for: ' . $username);
		return new Response('HardCoded password failed', -9);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $username
	 * @param string $password
	 * @param type $userInfoData
	 * @return Response
	 */
	public function LogonMethod_LDAP_CITY(string $username, string $password, $userInfoData): Response {

		return Response::TODO_Error();
	}



}
