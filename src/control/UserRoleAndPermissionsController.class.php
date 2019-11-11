<?php

//**********************************************************************************************
//* UserRoleAndPermissionsController.class.php
/**
 * sets up the users permissions from the database
 *
 * @author  mike.merrett@whitehorse.ca
 * @license City of Whitehorse
 *
 * Description.
 * this class handles the interaction between what the user enters and what the rest
 *    of the server does - it handles the POST/GET responses and passes the Dispatcher
 *    the Queue items. Process/Task/Action/Payload (PTAP)
 *
 *
 * @link URL
 *
 * @package ModelViewController - UserRoleAndPermissions
 * @subpackage UserRoleAndPermissions
 * @since 0.3.0
 *
 * @example
 *
 * @see UserRoleAndPermissionsModel.class.php
 * @see UserInfoData.class.php
 * @see UserAttributedata.class.php
 * @see UserPermissionData.class.php
 * @see UserRoleData.class.php
 *
 * @todo Description
 *
 *
 *
 * https://www.php-fig.org/psr/
 *
 *
 */
//**********************************************************************************************

namespace php_base\control;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

/** * **********************************************************************************************
 * controller for the user roles and permissions
 */
Class UserRoleAndPermissionsController {

	/**
	 * the usersname
	 *
	 * @var string
	 */
	public $username;
	public $userID;
	public $userInfo = null;
	public $userAttributes = null;
	//public $roleNames = null;
	public $userPermissions = null;
	public $ArrayOfRoleNames = null;
	// temp things
	public $arOfRoleIDs = null;
	//public $data;
	public $view;
	public $model;
	public $process;
	public $task;
	public $action;
	public $payload;

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $action
	 * @param type $payload
	 */
	public function __construct($action = '', $payload = null) {
		$u = Settings::GetRunTime('Currently Logged In User');
		if (!empty($u)) {

			$this->model = new \php_base\model\UserRoleAndPermissionsModel($this);
			//$this->data = new \php_base\data\UserRoleAndPermissionsData($this);
			$this->view = new \php_base\view\UserRoleAndPermissionsView($this);

			$this->action = $action;
			$this->payload = $payload;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $process
	 * @param type $task
	 */
	public function setProcessAndTask($process, $task) {
		$this->process = $process;
		$this->task = $task;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return Response
	 */
	public function doWork(): Response {
		echo 'should never get here';
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $action
	 * @param type $payload
	 * @return Response
	 */
	public function Setup($action = '', $payload = null): Response {

		$u = Settings::GetRunTime('Currently Logged In User');
		if (!empty($u)) {
			$response = $this->LoadAllUserInformation($u);

			// use this setting to check permissions
			Settings::SetRunTime('userPermissions', $this->model);
		} else {
			$response = new Response('no username', -8, false, true);
		}

//Dump::dump(Settings::GetRunTime('userPermissions'));
		return $response;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $username
	 * @return Response
	 */
	public function LoadAllUserInformation($username): Response {
		if (empty($username)) {
			return new Response('Username not supplied to LoadPermissions', -6, false, true);
		}
		try {

			// setup the user with the extra data in the users table and then get the attributes for that user
			$this->username = $username;

			$this->GetUSERinfo();

			$this->GetUSERAttributes();

			$this->GetUSERpermissions();

			// clean up things not needed
			unset($this->arOfRoleIDs);

			$this->view->dumpState(null, null, true);
			$this->view->dumpPermissions();
		} catch (\Exception $e) {
			return new Response('something happended when trying to load all permissions' . $e->getMessage(), -7);
		}

		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return bool
	 */
	protected function GetUSERinfo(): bool {
		$DataUserInfo = new \php_base\data\UserInfoData($this->username);

		$this->userID = $DataUserInfo->getUserID();

		$this->userInfo = $DataUserInfo->UserInfo;
		return (!empty($this->userInfo));
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return bool
	 */
	protected function GetUSERAttributes(): bool {

		$DataUserAttribute = new \php_base\data\UserAttributeData($this->userID);
		// take the primary role from the user info and addit to the array of roles in the user attributes
		//$primaryRole = $DataUserInfo->getPrimaryRole();
		$primaryRole = $this->userInfo['PRIMARYROLENAME'];

		$DataUserAttribute->AddPrimaryRole($primaryRole);  // add the userInfo PrimaryRole

		$this->userAttributes = $DataUserAttribute->UserAttributes;

		// get the array of all the roles this user has   (words i.e. Clerk)
		$this->ArrayOfRoleNames = $DataUserAttribute->getArrayOfRoleNames();

		//$this->roleNames = $DataUserAttribute->roleNames;


		return (!empty($this->userAttributes));
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return bool
	 */
	protected function GetUSERpermissions(): bool {

		// take the list of roles (words i.e. Clerk) and get the role IDs
		$DataUserRoles = new \php_base\data\UserRoleData($this->ArrayOfRoleNames);

		// now we have an array of Role ids
		$this->arOfRoleIDs = $DataUserRoles->RoleIDData;

		// now with roleid go and get the permissions related to those role ids
		$DataUserPermissions = new \php_base\data\UserPermissionData($this->arOfRoleIDs);

		$this->ArrayOfRoleNames = $DataUserRoles->RoleIDnames;

		$this->userPermissions = $DataUserPermissions->permissionList;

		usort($this->userPermissions,
				function ($a, $b) {
			$sA = $this->ArrayOfRoleNames[$a['ROLEID']] . $a['PROCESS'] . $a['TASK'] . $a['ACTION'] . $a['FIELD'] . $a['PERMISSION'];
			$sB = $this->ArrayOfRoleNames[$b['ROLEID']] . $b['PROCESS'] . $b['TASK'] . $b['ACTION'] . $b['FIELD'] . $b['PERMISSION'];
			return $sA <=> $sB;
		});

		return (!empty($this->userPermissions));
	}

}
