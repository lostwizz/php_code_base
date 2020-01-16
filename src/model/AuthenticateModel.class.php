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

use \php_base\data\UserAttributeData;
use \php_base\data\UserInfoData;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Utils as Utils;
use \Swoole\MySQL\Exception;
//use \Swoole\MySQL\Exception;

/** * **********************************************************************************************
 * business logic for the authentication
 */
class AuthenticateModel extends \php_base\Model\Model {
	/*
	 * the current user
	 */

	protected static $User;

	public $controller = null;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 * constructor - the parent has the data
	 * @param type $parentObj
	 */
	public function __construct($parentObj) {
		$this->controller = $parentObj;

//dump::dumpLong($this);
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}

	/** -----------------------------------------------------------------------------------------------
	 * default method called if something goes wrong - should never get here
	 * @return Response
	 */
	public function doWork(): Response {
		// should never get here
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 * given a username and a password see if the password is valid for that username
	 * @param type $username
	 * @param type $password
	 * @return Response
	 */
	public function tryToLogin(?string $username, ?string $password, ?UserInfoData $userInfoData): Response {
Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice('AuthenticateModel@@@@');
		if ( $this->isGoodAuthentication()) {
			if (Settings::GetPublic('Show_Debug_Authenticate')) {
				Settings::SetRunTime('Currently Logged In User', $_SESSION['Authenticated_username']);
			}
			return Response::NoError();
		}
		if (empty( $username)) {
			return Response::GenericError();
		}
		if( empty( $password)) {
			return Response::GenericError();
		}

		$method = 'LogonMethod_' . $userInfoData->UserInfo['METHOD'];

		//Settings::GetRunTimeObject('MessageLog')->addInfo('Trying ' . $method );

		if (method_exists($this, $method)) {
			if (Settings::GetPublic('Show_Debug_Authenticate')) {
				Settings::GetRunTimeObject('MessageLog')->addInfo('Trying ' . $method);
			}
			$response = $this->$method($username, $password, $userInfoData);
			if ($response->giveErrorCode() == 0) {
				Settings::SetRunTime('Currently Logged In User', $username);
				$_SESSION['Authenticated_username'] = $username;
				$exp = (new \DateTime('now'))->getTimestamp();
				$_SESSION['Authenticated_ExpireTime'] =	(new \DateTime('now'))->getTimestamp();
			}
		} else {
			if (Settings::GetPublic('Show_Debug_Authenticate')) {
				Settings::GetRunTimeObject('MessageLog')->addInfo('Authentication Method: ' . $method . 'Does NOT exist for:' . $username);
			}
			$response = new Response('Authentication Method doenst exist', -8);
		}
		return $response;
	}

	/** -----------------------------------------------------------------------------------------------
	 * test if the username and password are good
	 * @param type $passedUsername
	 * @return boolean
	 */
	public  function isGoodAuthentication() {

		if (empty( $_SESSION['Authenticated_username'])) {
			if (Settings::GetPublic('IS_DETAILED_AUTHENTICATION_DEBUGGING')) {
				Settings::GetRunTimeObject('MessageLog')->addCritical( ' currently logged on -  as: ' . Settings::GetRunTime('Currently Logged In User'));
			}
			return false;
		}
		$now = (new \DateTime('now'))->getTimestamp();
		$then = $_SESSION['Authenticated_ExpireTime'] ;
		$diff = $now - $then; //ends up with seconds until time out

		if ($diff > 900) {
			if (Settings::GetPublic('IS_DETAILED_AUTHENTICATION_DEBUGGING')) {
				Settings::GetRunTimeObject('MessageLog')->addCritical( ' currently logged on - NO Timeout');
			}
			return false;
		}

		if (Settings::GetPublic('IS_DETAILED_AUTHENTICATION_DEBUGGING')) {
			Settings::GetRunTimeObject('MessageLog')->addCritical( 'is user currently logged on -> YES  as: ' . Settings::GetRunTime('Currently Logged In User'));
		}
		Settings::SetRunTime('Currently Logged In User', $_SESSION['Authenticated_username']);
		Settings::SetRuntime ('isAuthenticated', true );
		return true;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $passedUsername
	 * @param type $UserInfoData
	 * @return Response
	 */
	public function doPasswordForgot($passedUsername, $UserInfoData): Response {

		if ( !empty( $UserInfoData->UserInfo['EMAIL'] )){

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
		} else {

			$this->controller->view->showNoEmailAddressError();
			return new Response('Unable to email the new password to the user', -12);
		}
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
	 * @param type $userInfoData
	 * @return Response
	 */
	public function LogonMethod_DB_Table(string $username, string $password, $userInfoData): Response {
		if (\password_verify($password, $userInfoData->UserInfo['PASSWORD'])) {
			if (Settings::GetPublic('Show_Debug_Authenticate')) {
				Settings::GetRunTimeObject('MessageLog')->addAlert('Successful LOGON of ' . $username . ' using DB_TABLE password');
			}

			$this->DoFinishLoginUpdatebyName( $username, $userInfoData);

			return Response::NoError();
		}
		if (Settings::GetPublic('Show_Debug_Authenticate')) {
			Settings::GetRunTimeObject('MessageLog')->addAlert('UNSuccessful logon DB_TABLE password for: ' . $username);
		}
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

			$this->DoFinishLoginUpdatebyName( $username, $userInfoData);

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
	public function LogonMethod_LDAP(string $username, string $password, $userInfoData): Response {
		if (!extension_loaded('LDAP')) {
			return new Response('LDAP not loaded in PHP - cant login ', -22);
		}
		try {
			$ldap_conn = @\ldap_connect("city.local");
			if (!$ldap_conn){
				return Response::GenericError();
			}

			$user_connect = @\ldap_bind($ldap_conn, 'city\\' . $username, $password);
			if (!$user_connect) {
				return Response::GenericError();
			}

			if (Utils::startsWith( strtolower($username), 'admin')){
				$dn = "OU=Admins,DC=CITY,DC=local";
			} else {
				$dn = "OU=City Users,DC=CITY,DC=local";
			}
			$attributes = array("sn", "givenname",  "mail", "telephonenumber", "cn", "title", "department", "mobile");
			///////, "memberOf");

			$result = @\ldap_search($ldap_conn, $dn, "sAMAccountname=" . $username, $attributes);
			if (!$result) {
				return Response::GenericError();
			}

			$entries = @\ldap_get_entries($ldap_conn, $result);
			if (!$entries or  empty( $entries['count']) or  $entries['count'] !==1) {
				return Response::GenericError();
			}

			////dump::dump($entries);

			@\ldap_close( $ldap_conn);
			if ( !empty( $entries) and !empty( $entries[0])) {
				$who = (empty($entries[0]['cn'][0]) ? '' : $entries[0]['cn'][0]);
				$surname = (empty($entries[0]['sn'][0]) ? '' : $entries[0]['sn'][0]);
				$telephonenumber = (empty($entries[0]['telephonenumber'][0]) ? '' : $entries[0]['telephonenumber'][0]);
				$givenname = (empty($entries[0]['givenname'][0]) ? '' : $entries[0]['givenname'][0]);
				$email = (empty($entries[0]['mail'][0]) ? '' : $entries[0]['mail'][0]);
				$title = (empty($entries[0]['title'][0]) ? '' : $entries[0]['title'][0]);
				$department = (empty($entries[0]['department'][0]) ? '' : $entries[0]['department'][0]);
				$mobile = (empty($entries[0]['mobile'][0]) ? '' : $entries[0]['mobile'][0]);
				$groups = (empty($entries[0]['memberOf'])  ? '' : $entries[0]['memberOf']);



				$userid = $userInfoData->getUserID();

				UserAttributeData::doInserOrUpdateAttributeForUserID( $userid, 'eMailAddress', $email );
				UserAttributeData::doInserOrUpdateAttributeForUserID($userid, 'GivenName', $givenname );
				UserAttributeData::doInserOrUpdateAttributeForUserID($userid, 'Surname', $surname );
				UserAttributeData::doInserOrUpdateAttributeForUserID($userid, 'PhoneNum', $telephonenumber );
				UserAttributeData::doInserOrUpdateAttributeForUserID($userid, 'Title', $title );
				UserAttributeData::doInserOrUpdateAttributeForUserID($userid, 'Department', $department );
				UserAttributeData::doInserOrUpdateAttributeForUserID($userid, 'CellNum', $mobile );

			}
			$this->DoFinishLoginUpdate( $userid);

//dump::dumpLong( $entries)			;
		} catch (Exception $ex) {
			throw new Exception('unable to connect to ldap');
		}
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userid
	 * @return void
	 */
	public  function DoFinishLoginUpdate( int $userid) : void{
		$prettyNow = (new \DateTime('now'))->format( 'Y-m-d G:i:s');

		$ip = \filter_input(INPUT_SERVER, 'REMOTE_ADDR');
		//UserInfoData::doUpdateLastLoginAndIP( $userid, $prettyNow, $ip);

Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice('Auth Model-doFinshLoginUpdate:'. $userid);

		$this->controller->data->doUpdateLastLoginAndIP( $userid, $prettyNow, $ip);

	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $username
	 * @param UserInfoData|null $userInfoData
	 */
	public function DoFinishLoginUpdatebyName( string $username, ?UserInfoData $userInfoData){
		if (!empty( $userInfoData) ){
			$userid = $userInfoData->getUserID();
			if ( !empty( $userid)) {
				$this->DoFinishLoginUpdate( $userid );
			}
		}
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	public function DoLogoff() {
		unset($_SESSION['Authenticated_username']);
		unset( $_SESSION['Authenticated_ExpireTime'] );

		Settings::unSetRunTime('Currently Logged In User');

	}

}
