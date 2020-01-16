<?php

/** * ********************************************************************************************
 * AuthenticateController.class.php
 *
 * Summary user authentication and verification
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description.`
 * this handles the initial logn on screen and its results - also resonsible for vwerifying the user is still "Logged on"
 *
 *
 *
 * @link URL
 *
 * @package  AuthenticateController
 * @subpackage Controller
 * @since 0.3.0
 *
 * @example
 *
 * @see AuthenticateModel
 * @see AuthenticateView
 *
 * @todo add forgot password
 * @todo add change password
 * @todo add signup (add new account)
 *
 */
//**********************************************************************************************

namespace php_base\Control;

use \php_base\Model\AuthenticateModel;
use \php_base\View\AuthenticateView;
use \php_base\data\UserInfoData as UserInfoData;

use \php_base\Resolver;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\Settings as Settings;


/** * **********************************************************************************************
 * handles authentication and logon processes
 *
 * Description.
 *
 * @since 0.0.2
 */
class AuthenticateController extends \php_base\Control\Controller {

	protected $UserInfoData = null;
	public $process;
	public $task;
	public $action;
	public $payload;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';


	/**  -----------------------------------------------------------------------------------------------
	 *
	 * @param string $passedAction
	 * @param type $passedPayload
	 */
	public function __construct(string $passedProcess, string $passedTask, string $passedAction = '', $passedPayload = null) {

		if (Settings::GetPublic('IS_DETAILED_AUTHENTICATION_DEBUGGING' ) != false) {
			Settings::SetRuntime ('AUTHENTICATION_DEBUGGING' , Settings::GetRunTimeObject('MessageLog') );
		}

		$this->model = new \php_base\model\AuthenticateModel($this);
		$this->data = new \php_base\data\UserInfoData($this);
		$this->view = new \php_base\view\AuthenticateView($this);

		$this->process = $passedProcess;
		$this->task = $passedTask;
		$this->action = $passedAction;
		$this->payload = $passedPayload;
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice('AuthenticateController construct' .$this->process. '.' .$this->task .  '.' .$this->action);

//
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}

//	/**  -----------------------------------------------------------------------------------------------
//	 *
//	 * @param type $process
//	 * @param type $task
//	 */
//	public function setProcessAndTask($process, $task): void {
//		$this->process = $process;
//		$this->task = $task;
//	}

	/**  -----------------------------------------------------------------------------------------------
	 *
	 * @return Response
	 */
//	public function doWork(): Response {
//		echo 'authenticationController doWork hi - i am here!!';
//		echo 'should never get here';
//		return Response::TODO_Error();
//	}

	/**  -----------------------------------------------------------------------------------------------
	 * get the username and password then call the appropriate method
	 * @param type $parent
	 * @return Response
	 */
	public function checkAuthentication($parent): Response {
		//dump::dumpLong($this);
		$isAlreadyLoggedOn = $this->model->isGoodAuthentication();
		if ($isAlreadyLoggedOn) {
			return Response::NoError();
		}

		$password = (!empty($this->payload['entered_password'])) ? $this->payload['entered_password'] : null;
		$username = (!empty($this->payload['entered_username'])) ? $this->payload['entered_username'] : null;

		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice('AuthenticateController-check:'.$username . $password );

		if (empty($this->payload[Resolver::REQUEST_ACTION])) {
			$this->payload[Resolver::REQUEST_ACTION] = 'Need_Login';
		}
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice('AuthenticateController-check2' . $this->payload[Resolver::REQUEST_ACTION]);
		// not yet logged on
		//    yes we could just change the spaces to underscores - but this i think is easier to read
		switch ($this->payload[Resolver::REQUEST_ACTION]) {
			case 'Submit Logon':
				$action = 'Submit_Logon';
				break;
			case 'Change Password':
				$action = 'Change_Password';
				break;
			case 'Submit Username for Password Change':
				$action = 'Submit_Username_for_Password_Change';
				break;
			case 'Add New Account':
				$action = 'Add_New_Account';
				break;
			case 'Submit New Account Info':
				$action = 'Submit_New_Account_Info';
				break;
			case 'Forgot Password':
				$action = 'Forgot_Password';
				break;
			case 'Submit Username for Forgot Password':
				$action = 'Submit_Username_for_Forgot_Password';
				break;
			case '':
			default:
				$action = 'Need_Login';
				break;
		}

		// now do the action
		$r = $this->$action($parent, $username, $password);
		return $r;
	}

	/**  -----------------------------------------------------------------------------------------------
	 * handle no logon yet -i.e. show the login page
	 *
	 * @see AuthenticateView
	 * @param type $parent
	 * @param type $username
	 * @param type $password
	 * @return Response
	 */
	protected function Need_login($parent, $username = null, $password = null): Response {
		 $this->view->showLoginPage();
		 return Response::NoError();
	}

	/**  -----------------------------------------------------------------------------------------------
	 *  verify password is good and therefor the user has logged on
	 *
	 * @see AuthenticateModel
	 *
	 * @param type $parent
	 * @param type $username
	 * @param type $password
	 * @return Response
	 */
	protected function Submit_Logon($parent, $username = null, $password = null): Response {
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice('AuthenticateController-submitLogon:'. $username);

		$this->UserInfoData = new UserInfoData($this, $username);

		if (!empty($this->UserInfoData->UserInfo) and ! empty($this->UserInfoData->UserInfo['USERID'])) {
			Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice('AuthenticateController-submitLogon1:'. $username);
			$r = $this->model->tryToLogin($username, $password, $this->UserInfoData);
		} else {
			$r = new Response('Username does not exist', -10);
		}
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice('AuthenticateController-submitLogon2:'. $username);


		if ($r->hadError()) {
			Settings::GetRunTimeObject('MessageLog')->addAlert('User could not be Logged onto the application');
			$this->Need_login($parent, null,null);
		} else {
			Settings::SetRuntime ('isAuthenticated', true );
		}
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice('AuthenticateController-submitLogon3:'. $username);

		return $r;
	}

	/**  -----------------------------------------------------------------------------------------------
	 * ask the user for the old password and 2 repeats of the new one
	 *
	 * @todo !!!!!!!!!!!!build this
	 *
	 * @param type $parent
	 * @param type $username
	 * @param type $password
	 * @return Response
	 */
	protected function Change_Password($parent, $username = null, $password = null): Response {
		// ask for the user id and the old password
		$this->view->showChangePassword();
		return Response::TODO_Error();
	}

	/**  -----------------------------------------------------------------------------------------------
	 * user has entered the old passwords and two identical (?) new  ones - so change it
	 *
	 * @param type $parent
	 * @param type $username
	 * @param type $password
	 * @return Response
	 */
//	protected function password_change_submit($parent, $username = null, $password = null): Response {
//		dump::dump('tony was here!!!!!!!');
//		return Response::TODO_Error();
//	}

	/**  -----------------------------------------------------------------------------------------------
	 * add a new user to the authentication system
	 * @todo !!!!!!!!!!!!build this
	 *
	 * @param type $parent
	 * @param type $username
	 * @param type $password
	 * @return Response
	 */
	protected function add_New_Account($parent, $username = null, $password = null): Response {
		// show the page asking all the questions
		$this->view->showAddNewAccount();
		return Response::NoError();
	}

	/**  -----------------------------------------------------------------------------------------------
	 * reset the password and email the user with the new one
	 *
	 * @todo !!!!!!!!!!!!build this
	 *
	 * @param type $parent
	 * @param type $username
	 * @param type $password
	 * @return Response
	 */
	protected function forgot_Password($parent, $username = null, $password = null): Response {
		$this->view->showForgotPassword();
		return Response::NoError();
	}

	/**  -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @param type $username
	 * @return Response
	 */
	public function Submit_Username_for_Forgot_Password($parent, $username): Response {
		$this->UserInfoData = new \php_base\data\UserInfoData($username);
		$r = $this->model->doPasswordForgot($username, $this->UserInfoData);
		return $r;
	}

	/**  -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @param type $username
	 * @return Response
	 */
	public function Submit_Username_for_Password_Change($parent, $username): Response {

		$this->UserInfoData = new \php_base\data\UserInfoData($username);
		$r = $this->model->doChangePassword(
				$username,
				$this->payload['old_password'],
				$this->payload['new_password'],
				$this->UserInfoData
		);
		return $r;
	}

	/**  -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @param type $username
	 * @return Response
	 */
	public function Submit_New_Account_Info($parent, $username): Response {

		$uRAPController = new \php_base\control\UserRoleAndPermissionsController();

		$r = $uRAPController->doInsertNewAccount($this->payload['entered_username'],
												$this->payload['entered_password'],
												$this->payload['entered_email']
												);
		if ( $r ) {
			return Response::NoError();
		} else {
			return Response::TODO_Error( ' do something here and create a proper error');
		}

		return $r;
	}

	/**  -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @return Response
	 */
	public function Logoff($parent): Response {

		$this->model->DoLogoff();


		if ( !empty($this->UserInfoData )){
			$this->UserInfoData->doLogoff();
		}

		Settings::GetRunTimeObject('userPermissionsController')->doLogoff();

		Settings::unSetRunTime('userPermissionsController');
		Settings::unSetRunTime('Currently Logged In User');

		$this->Need_login($parent, null,null);

		return Response::NoError();
	}


	/**  -----------------------------------------------------------------------------------------------
	 *
	 * @return type
	 */
	public  function isAuthenticated(){
		$r = $this->model->isGoodAuthentication();
		Settings::SetRuntime ('isAuthenticated', $r );
		return $r;
	}

}
