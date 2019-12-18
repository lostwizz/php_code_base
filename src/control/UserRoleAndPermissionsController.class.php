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
 * the output of this controller is a link back in to do any of the tasks
 * Settings::GetRunTime('userPermissionsController');      // $this
 *
 *
 * @link URL
 *
 * @package ModelViewController - UserRoleAndPermissions
 * @subpackage UserRoleAndPermissions
 * @since 0.3.0
 *
 * @example
 *  after things are setup the code is used to query if it has permissions or not
 * Settings::GetRunTime('userPermissionsController')->is
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

use \php_base\model\Permissions as Permissions;
use \php_base\Utils\Cache;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\Settings as Settings;


/** * **********************************************************************************************
 * controller for the user roles and permissions
 */
Class UserRoleAndPermissionsController {

	/**
	 * the username
	 *
	 * @var string
	 */
	public $username;
	public $userID;
	public $userInfo = null;
	public $userAttributes = null;
	public $userPermissions = null;
	public $ArrayOfRoleNames = null;
// temp things
	public $arOfRoleIDs = null;
	public $view;
	public $model;
	public $process;
	public $task;
	public $action;
	public $payload;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $action
	 * @param type $payload
	 */
	public function __construct($action = '', $payload = null) {
		$u = Settings::GetRunTime('Currently Logged In User');
		if (!empty($u)) {

			$this->model = new \php_base\model\UserRoleAndPermissionsModel($this);
			$this->view = new \php_base\view\UserRoleAndPermissionsView($this);

			$this->action = $action;
			$this->payload = $payload;
		}
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
//dump::dumpLong(Settings::dump(false,false, true));
//echo Settings::dump(false, false, true);
		$u = Settings::GetRunTime('Currently Logged In User');

		if (!empty($u)) {
			$response = $this->LoadAllUserInformation($u);

			// use this setting to check permissions
			Settings::SetRunTime('userPermissionsController', $this);
		} else {
			$response = new Response('no username', -18, false, true);
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

		if (Cache::exists('UserRoleAndPermissions')) {
			$this->getCached();
			//$this->view->dumpState(null, null, true);
			//$this->view->dumpPermissions();

			return Response::NoError();
		} else {
			try {

				// setup the user with the extra data in the users table and then get the attributes for that user
				$this->username = $username;

				$this->GetUSERinfo();

				$this->GetUSERAttributes();

				$this->GetUSERpermissions();

				// clean up things not needed
				unset($this->arOfRoleIDs);

				$this->setCached();

				//$this->view->dumpState(null, null, true);
				//$this->view->dumpPermissions();
			} catch (\Exception $e) {
				return new Response('something happended when trying to load all permissions' . $e->getMessage(), -7);
			}

			return Response::NoError();
		}
	}

	protected function getCached() {
		$cacheVal = Cache::pull('UserRoleAndPermissions');
//dump::dump($cacheVal);
		$this->username = $cacheVal['username'];
		$this->userID = $cacheVal['userID'];
		$this->userInfo = $cacheVal['userInfo'];
		$this->userAttributes = $cacheVal['userAttributes'];
		$this->userPermissions = $cacheVal['userPermissions'];
		$this->ArrayOfRoleNames = $cacheVal['ArrayOfRoleNames'];
	}

	protected function setCached() {
		$cacheVal = array();
		$cacheVal['username'] = $this->username;
		$cacheVal['userID'] = $this->userID;
		$cacheVal['userInfo'] = $this->userInfo;
		$cacheVal['userAttributes'] = $this->userAttributes;
		$cacheVal['userPermissions'] = $this->userPermissions;
		$cacheVal['ArrayOfRoleNames'] = $this->ArrayOfRoleNames;

		Cache::addOrUpdate('UserRoleAndPermissions', $cacheVal, 900);
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
		$primaryRole = $this->userInfo['PRIMARYROLENAME'];

		$DataUserAttribute->AddPrimaryRole($primaryRole);  // add the userInfo PrimaryRole

		$this->userAttributes = $DataUserAttribute->UserAttributes;

		// get the array of all the roles this user has   (words i.e. Clerk)
		$this->ArrayOfRoleNames = $DataUserAttribute->getArrayOfRoleNames();

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

		if ( Settings::GetPublic('Show_Debug_UserRoleAndPermissiosn')){
			/** DEBUG - dump the permissions prettily */
			$this->view->dumpPermissions();
		}

		return (!empty($this->userPermissions));
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $roleWanted
	 * @return bool
	 */
	public function hasRole(string $roleWanted): bool {
		return $this->model->hasRolePermission($roleWanted);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $permissionWanted
	 * @param string $process
	 * @param string $task
	 * @param string $action
	 * @param string $field
	 * @return bool
	 */
	public function isAllowed($permissionWanted = Permissions::NO_RIGHT,
			string $process = Permissions::NO_RIGHT,
			string $task = Permissions::NO_RIGHT,
			string $action = Permissions::NO_RIGHT,
			string $field = Permissions::NO_RIGHT
	): bool {

		return $this->model->isAllowed($permissionWanted, $process, $task, $action, $field);
	}

//	public static function tryToLogin(string $username, string $password) {
//
//	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param string $primaryRole
	 * @return bool
	 */
	public function doInsertNewAccount(string $username, string $password, string $email, string $primaryRole =null): bool {
		$model = new \php_base\model\UserRoleAndPermissionsModel();

		$r = $model->doInsertIfNotExists($username, $password, $email, $primaryRole);

		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $roleName
	 */
	public function doRemoveRoleByName(string $roleName){
		//$DataUserRoles = new \php_base\data\UserRoleData();

		$roleID = \php_base\data\UserRoleData::getRoleIDByName($roleName);

		//$DataPermissions = new \php_base\data\UserPermissionData();
		\php_base\data\UserPermissionData::doDeletePermissionByRoleID( $roleID);

		$DataUserRoles->doRemoveRoleByID($roleID);
	}

}
