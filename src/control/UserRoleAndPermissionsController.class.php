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
	public function __construct(string $process, string $task, string $action = '', $payload = null) {
		$u = Settings::GetRunTime('Currently Logged In User');
		if (!empty($u)) {

			$this->model = new \php_base\model\UserRoleAndPermissionsModel($this);
			$this->view = new \php_base\view\UserRoleAndPermissionsView($this);

			$this->process = $process;
			$this->task = $task;
			$this->action = $action;
			$this->payload = $payload;
		}

		if ( Settings::GetPublic('IS_DETAILED_PERMISSIONS_DEBUGGING')){
			Settings::setRunTime( 'PERMISSION_DEBUGGING',  Settings::GetRunTimeObject('MessageLog')) ;
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

//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * @param type $process
//	 * @param type $task
//	 */
//	public function setProcessAndTask($process, $task) {
//		$this->process = $process;
//		$this->task = $task;
//	}

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
		Settings::GetRunTimeObject( 'PERMISSION_DEBUGGING')->addNotice( 'logged on user:' . $u);

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

//		/** -----------------------------------------------------------------------------------------------*/
//	public function prepareToEditUserInfoData(){
//dump::dump('CCCCCCCCCCCCCCCCCCC')		;
//		$x = \php_base\data\UserInfoData::$Table;
//		return Response::NoError();
//	}


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
			return Response::NoError();
		} else {

			Settings::GetRunTimeObject( 'PERMISSION_DEBUGGING')->addNotice( 'LoadAllUserInformation- about to loadall');
			$r = $this->model->LoadALLUser( $username);
			$this->setCached();

			return $r;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
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

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
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
		$r =  $this->model->isAllowed($permissionWanted, $process, $task, $action, $field);
		return $r;
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


//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * @param type $whichTbl
//	 * @return Response
//	 */
//	public function doEdit( $whichTbl) : Response{
//		$DataUserInfo = new \php_base\data\UserInfoData();
//dump::dumpLong( $DataUserInfo);
//
//$y = UserInfoData::$Table;
//dump::dumpLong( $y);
//
//		$x =  UserInfoData::$Table ;
//
//dump::dumpLong($x);
//		$editorSession = new \php_base\Utils\DatabaseHandlers\SimpleTableEditor($x);
//
//		$result = $editorSession->runTableDisplayAndEdit( true );
//		return $result;
//	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	public function doLogoff(){
		Settings::GetRunTimeObject( 'PERMISSION_DEBUGGING')->addNotice( 'LOGGING OFF!');
		unset($this->username );
		unset($this->userID);
		unset($this->userInfo);
		unset($this->userAttributes);
		unset($this->userPermissions);
		unset($this->ArrayOfRoleNames);
		unset($this->arOfRoleIDs);
	}


}
