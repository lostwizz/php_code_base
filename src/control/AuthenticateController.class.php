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
 * @see AuthenticateData
 *
 * @todo add forgot password
 * @todo add change password
 * @todo add signup (add new account)
 *
 */
//**********************************************************************************************

namespace php_base\Control;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

use \php_base\data\UserInfoData as UserInfoData;


/** * **********************************************************************************************
 * handles authentication and logon processes
 *
 * Description.
 *
 * @since 0.0.2
 */
class AuthenticateController extends Controller {

	protected $UserInfoData = null;
	public $process;
	public $task;
	public $action;
	public $payload;

	/**  -----------------------------------------------------------------------------------------------
	 *
	 * @param string $passedAction
	 * @param type $passedPayload
	 */
	public function __construct(string $passedAction = '', $passedPayload = null) {
		$this->model = new \php_base\model\AuthenticateModel($this);
		$this->data = new \php_base\data\AuthenticateData($this);
		$this->view = new \php_base\view\AuthenticateView($this);

		$this->action = $passedAction;
		$this->payload = $passedPayload;
	}

	/**  -----------------------------------------------------------------------------------------------
	 *
	 * @param type $process
	 * @param type $task
	 */
	public function setProcessAndTask($process, $task): void {
		$this->process = $process;
		$this->task = $task;
	}

	/**  -----------------------------------------------------------------------------------------------
	 *
	 * @return Response
	 */
	public function doWork(): Response {
		echo 'authenticationController doWork hi - i am here!!';
		echo 'should never get here';
		return Response::TODO_Error();
	}

	/**  -----------------------------------------------------------------------------------------------
	 * get the username and password then call the appropriate method
	 * @param type $parent
	 * @return Response
	 */
	public function checkLogin($parent): Response {
		$username = (!empty($this->payload['entered_username'])) ? $this->payload['entered_username'] : null;
		$password = (!empty($this->payload['entered_password'])) ? $this->payload['entered_password'] : null;

		if (!empty($this->action)) {
			$action = $this->action;
		} else {
			$action = 'Need_login';
		}

		return $this->$action($parent, $username, $password);
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
		return $this->view->showLoginPage();
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
		$this->UserInfoData = new \php_base\data\UserInfoData($username);
////dump::dump($this->UserInfoData);

		if (!empty($this->UserInfoData->UserInfo) and ! empty($this->UserInfoData->UserInfo['USERID'])) {
			return $this->model->tryToLogin($username, $password, $this->UserInfoData);
		}
		return new Response('Username does not exist', -10);

/////		\php_base\control\UserRoleAndPermissionsController::tryToLogin( $username, $password);
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
		dump::dump('hi ho');
		// ask for the user id and the old password
		$r = $this->view->showChangePassword();

		// check the old password matches
		// save the new password
		// send an email saying changed the password?

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
		$r = $this->view->showAddNewAccount();
		return $r;
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
		$r = $this->view->showForgotPassword();
		return $r;
	}

	public function Submit_Username_for_Forgot_Password($parent, $username): Response {
		$this->UserInfoData = new \php_base\data\UserInfoData($username);
		$r = $this->model->doPasswordForgot($username, $this->UserInfoData);
		return $r;
	}

	public function Submit_Username_for_Password_Change($parent, $username): Response {
//		dump::dump('Submit_Username_for_Password_Change');

		$this->UserInfoData = new \php_base\data\UserInfoData($username);
//dump::dump($this);
		$r = $this->model->doChangePassword(
				$username,
				$this->payload['old_password'],
				$this->payload['new_password'],
				$this->UserInfoData
		);
		return $r;
	}

	public function Submit_New_Account_Info($parent, $username): Response {
		dump::dump($this);

		$r = $this->model->doNewAccountInfo($this->payload['entered_username'], $this->payload['entered_password'], $this->payload['entered_email']);

		return $r;
	}

}
