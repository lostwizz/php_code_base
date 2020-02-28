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

use \php_base\data\UserAttributesData;
use \php_base\data\UserInfoData;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Utils as Utils;
//////////////////////////////use \Swoole\MySQL\Exception;

use \php_base\Utils\SubSystemMessage as SubSystemMessage;


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
	public function __construct($controller) {
		Settings::getRunTimeObject('AUTHENTICATION_DEBUGGING')->addInfo('constructor for AuthenticateModel');

		$this->controller = $controller;

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
	public function tryToLogin(?string $username, ?string $password ): Response {
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('@@AuthenticateModel-tryToLogin');

		if (empty( $username)) {
			Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addInfo('Username Missing!');
			return Response::GenericError();
		}
		if( empty( $password)) {
			Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addInfo('Password Missing!');
			return Response::GenericError();
		}

		if ( $this->isGoodAuthentication()) {
			Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addInfo( 'AuthenticateModel-tryToLogin had isGoodAuthentication');
			return Response::NoError();
		}

		$method = 'LogonMethod_' . $this->controller->data->UserInfo['METHOD'];

		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addInfo('Authenticateion-tryToLogin-Trying: ' . $method);
		if (method_exists($this, $method)) {

			$response = $this->$method($username, $password);
			if ($response->giveErrorCode() == 0) {
				Settings::SetRunTime('Currently Logged In User', $username);
				$_SESSION['Authenticated_username'] = $username;
				$exp = (new \DateTime('now'))->getTimestamp();
				$_SESSION['Authenticated_ExpireTime'] =	(new \DateTime('now'))->getTimestamp();
			}
		} else {
			Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addInfo('Authentication Method: ' . $method . 'Does NOT exist for:' . $username);
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
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('@@AuthenticateModel-isGoodAuthentication');

		if (empty( $_SESSION['Authenticated_username'])) {
			Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addDebug_9( 'no username so returning false');
			return false;
		}
		$now = (new \DateTime('now'))->getTimestamp();
		$then = $_SESSION['Authenticated_ExpireTime'] ;
		$diff = $now - $then; //ends up with seconds until time out

		if ($diff > 900) {
			Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addInfo( ' currently logged on - NOT Timeout');
			return false;
		}

		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addInfo( 'is user currently logged on -> YES  as: ' . Settings::GetRunTime('Currently Logged In User'));

		Settings::SetRunTime('Currently Logged In User', $_SESSION['Authenticated_username']);
		Settings::SetRuntime ('isAuthenticated', true );

		//Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addInfo( Settings::dump(false, false, true));

		//update the timeout -  so that it reflects inactivity
		$exp = (new \DateTime('now'))->getTimestamp();
		$_SESSION['Authenticated_ExpireTime'] =	(new \DateTime('now'))->getTimestamp();
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
	 * @return Response
	 */
	public function doChangePassword($username, $old_password, $new_password): Response {
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
		if (password_verify($old_password, $this->controller->data->UserInfo['PASSWORD'])) {

			//encode the new password
			$pwd = password_hash($new_password, PASSWORD_DEFAULT);

			// save the new password
			$rUpdate = $this->controller->data->doUpdatePassword($this->controller->data->UserInfo['USERID'], $pwd);
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
	public function LogonMethod_DB_Table(string $username, string $password ): Response {
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('@@AuthenticateModel-LogonMethod_DB_Table');

		if (\password_verify($password, $this->controller->data->UserInfo['PASSWORD'])) {
			Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('AuthenticateModel-LogonMethod_DB_Table- good password for:'.$username);

			$this->DoFinishLoginUpdatebyName( $username);

			return Response::NoError();
		}
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addAlert('UNSuccessful logon DB_TABLE password for: ' . $username);

		return new Response('db_table password failed', -11);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $username
	 * @param string $password
	 * @param type $userInfoData
	 * @return Response
	 */
	public function LogonMethod_HardCoded(string $username, string $password): Response {
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('@@AuthenticateModel-LogonMethod_HardCoded');
		//$pwd = \password_hash($password, PASSWORD_DEFAULT);
//dump::dump($pwd);

		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addTODO('test this to make sure it is working properly');

		if (\password_verify($password, Settings::GetProtected('Password_for_' . $username))) {
			Settings::GetRunTimeObject('MessageLog')->addInfo('Successful Hardcoded password for: ' . $username);

			$this->DoFinishLoginUpdatebyName( $username);

			return Response::NoError();
		}
		Settings::GetRunTimeObject('MessageLog')->addInfo('UNSuccessful Hardcoded password for: ' . $username);
		return new Response('HardCoded password failed', -9);
	}



	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $username
	 * @param string $password
	 * @param type $userInfoData
	 * @return Response
	 */
	public function LogonMethod_LDAP(string $username, string $password): Response {
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('@@AuthenticateModel-LogonMethod_LDAP');
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



//dump::dump( $this->controller->data);
				if (!empty( $this->controller->data) ){
					$userid = $this->controller->data->getUserID();
				}

				Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addTODO('add these attributes somewhere usefull - at this point the attrib table has not be read - should it Now??');

/*
 *
				$this->controller->data->doInserOrUpdateAttributeForUserID($userid, 'eMailAddress', $email );
				$this->controller->data->doInserOrUpdateAttributeForUserID($userid, 'GivenName', $givenname );
				$this->controller->data->doInserOrUpdateAttributeForUserID($userid, 'Surname', $surname );
				$this->controller->data->doInserOrUpdateAttributeForUserID($userid, 'PhoneNum', $telephonenumber );
				$this->controller->data->doInserOrUpdateAttributeForUserID($userid, 'Title', $title );
				$this->controller->data->doInserOrUpdateAttributeForUserID($userid, 'Department', $department );
				$this->controller->data->doInserOrUpdateAttributeForUserID($userid, 'CellNum', $mobile );
*/
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
	 * @param string $username
	 * @param UserInfoData|null $userInfoData
	 */
	public function DoFinishLoginUpdatebyName( string $username ){
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('@@AuthenticateModel-doFinishLoginUpdatebyName');

		if (!empty( $this->controller->data) ){
			$userid = $this->controller->data->getUserID();
			if ( !empty( $userid)) {
				$this->DoFinishLoginUpdate( $userid );
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userid
	 * @return void
	 */
	public  function DoFinishLoginUpdate( int $userid) : void{
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('@@AuthenticateModel-DoFinishLoginUpdate - call data to update last logon');
		$prettyNow = (new \DateTime('now'))->format( 'Y-m-d G:i:s');

		$ip = \filter_input(INPUT_SERVER, 'REMOTE_ADDR');
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('Auth Model-doFinshLoginUpdate:'. $userid);

		$this->controller->data->doUpdateLastLoginAndIP( $userid, $prettyNow, $ip, \session_id() );


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
