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
use \php_base\data\UserData as UserData;

use \php_base\Resolver;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\Settings as Settings;

use \php_base\Utils\SubSystemMessage as SubSystemMessage;


/** * **********************************************************************************************
 * handles authentication and logon processes
 *
 * Description.
 *
 * @since 0.0.2
 */
class AuthenticateController extends \php_base\Control\Controller {

	public $UserData = null;
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

		$this->model = new \php_base\model\AuthenticateModel($this);
		//$this->data = new \php_base\data\UserData($this);
		$this->data =null;  //not created yet - wait until i have a username to load
		$this->view = new \php_base\view\AuthenticateView($this);

		$this->process = $passedProcess;
		$this->task = $passedTask;
		$this->action = $passedAction;
		$this->payload = $passedPayload;
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('AuthenticateController construct' .$this->process. '.' .$this->task .  '.' .$this->action);

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
	 * @param type $dispatcher
	 * @return Response
	 */
	public function checkAuthentication($dispatcher): Response {

		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('@@auth controllercheckAuthentication');

		$isAlreadyLoggedOn = $this->model->isGoodAuthentication();
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('after isGoodAuth: '. ($isAlreadyLoggedOn ? 'yes':'no'));
		if ($isAlreadyLoggedOn) {
			Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addInfo('User is already logged on  so continue');
			return Response::NoError();
		}

		$password = (!empty($this->payload['entered_password'])) ? $this->payload['entered_password'] : null;
		$username = (!empty($this->payload['entered_username'])) ? $this->payload['entered_username'] : null;

		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('AuthenticateController-check: ' . $username . '/' . $password );

		//if (empty($this->payload[Resolver::REQUEST_ACTION])) {
		//	$this->payload[Resolver::REQUEST_ACTION] = 'Need_Login';
		//}

		if (empty($this->payload[Resolver::REQUEST_ACTION])){
			$this->payload[Resolver::REQUEST_ACTION] ='';
		}

//dump::dump($this->payload)		;

		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('AuthenticateController-check2'
							. (empty( $this->payload[Resolver::REQUEST_ACTION])  ? '' : $this->payload[Resolver::REQUEST_ACTION] ));
		// not yet logged on
		//    yes we could just change the spaces to underscores - but this i think is easier to read (and it shows all the possibilities)
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

		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addInfo('Authenticate is starting:' . $action);
		// now do the action
		$r = $this->$action($dispatcher, $username, $password);

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
	protected function Need_login($dispatcher, $username = null, $password = null): Response {
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('@@AuthenticateController-Need_login:');
		 $this->view->showLoginPage();
		 return Response::NoError();
	}

	/**  -----------------------------------------------------------------------------------------------
	 *  verify password is good and therefor the user has logged on
	 *
	 * @see AuthenticateModel
	 *
	 * @param type $dispatcher
	 * @param type $username
	 * @param type $password
	 * @return Response
	 */
	protected function Submit_Logon($dispatcher, $username = null, $password = null): Response {
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('@@AuthenticateController-submitLogon:'. $username);

		//$this->UserData = new UserData($this, $username);
		$this->data = new UserData($this, $username);
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_8( $this->UserData);

		if (!empty($this->data->UserInfo) and ! empty($this->data->UserInfo['USERID'])) {
			Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('AuthenticateController-submitLogon1:'. $username);
			$r = $this->model->tryToLogin($username, $password);
		} else {
			Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addWarning('User Name does not exist');
			$r = new Response('Username does not exist', -10);
		}
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('AuthenticateController-submitLogon2:'. $username);


		if ($r->hadError()) {
			Settings::GetRunTimeObject('MessageLog')->addInfo('User could not be Logged onto the application');
			$this->Need_login($dispatcher, null,null);
		} else {
			Settings::SetRuntime ('isAuthenticated', true );
		}
		Settings::GetRuntimeObject ('AUTHENTICATION_DEBUGGING')->addNotice_7('AuthenticateController-submitLogon3:'. $username);

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
		$this->data = new \php_base\data\UserData($username);
		$r = $this->model->doPasswordForgot($username, $this->data);
		return $r;
	}

	/**  -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @param type $username
	 * @return Response
	 */
	public function Submit_Username_for_Password_Change($parent, $username): Response {

		$this->data = new UserData($username);
		$r = $this->model->doChangePassword(
				$username,
				$this->payload['old_password'],
				$this->payload['new_password']
				//,$this->UserData
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


		if ( !empty($this->data )){
			$this->data->doLogoff();
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
