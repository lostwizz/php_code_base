<?php

//**********************************************************************************************
//* UserRoleAndPermissionsModel.class.php
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
 * @see UserRoleAndPermissionsController.class.php
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

namespace php_base\Model;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

use \php_base\data\UserInfoData as UserInfoData;


/** * **********************************************************************************************
 *
 */
Class Permissions {
	/*
	 * theses the the constants for ALL the possible permissions i.e. none, read,  write, dba and god (in hirachial order)
	 */

	const GOD_RIGHT = 'GOD';
	const DBA_RIGHT = 'DBA';
	const WRITE_RIGHT = 'Write';
	const READ_RIGHT = 'Read';
	const WILDCARD_RIGHT = '*';
	const NO_RIGHT = '__NO__RIGHT__';

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';



	protected static $permissions = array(
		self::GOD_RIGHT,
		self::DBA_RIGHT,
		self::WRITE_RIGHT,
		self::READ_RIGHT,
		self::WILDCARD_RIGHT,
		self::NO_RIGHT
	);

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
	 * @param type $theRight
	 * @return bool
	 */
	public static function doesRightExists($theRight): bool {
		return \in_array($theRight, self::$permissions);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $arRights
	 * @return bool
	 */
	public static function areAllValidRights( ...$arRights) :bool {
		foreach ($arRights as $right) {
			if (!self::doesRightExists($right)) {
				return false;
			}
		}
		return true;
	}

}

/** * **********************************************************************************************
 * handles the logic for user roles and permissions
 */
Class UserRoleAndPermissionsModel extends Model {
	/*
	 * theses the the constants for ALL the possible permissions i.e. none, read,  write, dba and god (in hirachial order)
	 */

//	const GOD_RIGHT = 'GOD';
//	const DBA_RIGHT = 'DBA';
//	const WRITE_RIGHT = 'Write';
//	const READ_RIGHT = 'Read';
//	const WILDCARD_RIGHT = '*';
//	const NO_RIGHT = '__NO__RIGHT__';

	public $action;
	public $payload;
	public $controller;

		/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';


	/** -----------------------------------------------------------------------------------------------
	 *  constructor - basically keeps track of the controller
	 * @param type $controller
	 */
	public function __construct($controller =null) {   //$action ='', $payload = null){
		if (!empty($controller)) {
			$this->controller = $controller;
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
	 * @param type $username
	 * @return Response
	 */
	public function LoadALLUser( $username) : Response {
		try {

			// setup the user with the extra data in the users table and then get the attributes for that user
			$this->controller->username = $username;

			$this->GetUSERinfo($username);
			Settings::GetRunTimeObject( 'PERMISSION_DEBUGGING')->addNotice( $this->controller->userInfo);

			$this->GetUSERAttributes();
			Settings::GetRunTimeObject( 'PERMISSION_DEBUGGING')->addNotice( $this->controller->userAttributes);

			$this->GetUSERpermissions();
			Settings::GetRunTimeObject( 'PERMISSION_DEBUGGING')->addNotice( $this->controller->userPermissions);

			// clean up things not needed
			unset($this->arOfRoleIDs);

			//$this->view->dumpState(null, null, true);
			//$this->view->dumpPermissions();
		} catch (\Exception $e) {
			return new Response('something happended when trying to load all permissions' . $e->getMessage(), -7);
		}
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return bool
	 */
	protected function GetUSERinfo($username): bool {
		$DataUserInfo = new \php_base\data\UserInfoData($username);

		$this->controller->userID = $DataUserInfo->getUserID();

		$this->controller->userInfo = $DataUserInfo->UserInfo;
		return (!empty($this->controller->userInfo));
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return bool
	 */
	protected function GetUSERAttributes(): bool {

		$DataUserAttribute = new \php_base\data\UserAttributeData($this->controller->userID);

		// take the primary role from the user info and addit to the array of roles in the user attributes
		$primaryRole = $this->controller->userInfo['PRIMARYROLENAME'];

		$DataUserAttribute->AddPrimaryRole($primaryRole);  // add the userInfo PrimaryRole

		$this->controller->userAttributes = $DataUserAttribute->UserAttributes;

		// get the array of all the roles this user has   (words i.e. Clerk)
		$this->controller->ArrayOfRoleNames = $DataUserAttribute->getArrayOfRoleNames();

		return (!empty($this->controller->userAttributes));
	}



	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return bool
	 */
	protected function GetUSERpermissions(): bool {


		// take the list of roles (words i.e. Clerk) and get the role IDs
		$DataUserRoles = new \php_base\data\UserRoleData($this->controller->ArrayOfRoleNames);

		// now we have an array of Role ids
		$this->controller->arOfRoleIDs = $DataUserRoles->RoleIDData;
		// now with roleid go and get the permissions related to those role ids
		$DataUserPermissions = new \php_base\data\UserPermissionData($this->controller->arOfRoleIDs);
		$this->controller->ArrayOfRoleNames = $DataUserRoles->RoleIDnames;

		$this->controller->userPermissions = $DataUserPermissions->permissionList;

		if ( Settings::GetPublic('Show_Debug_UserRoleAndPermissiosn')){
			/** DEBUG - dump the permissions prettily */
			$this->controller->view->dumpPermissions();
		}

		return (!empty($this->controller->userPermissions));
	}



	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $roleWanted - string with the role wanted
	 * @return type
	 */
	public function hasRolePermission(string $roleWanted): bool {
		$roleWanted = trim($roleWanted);
		if (\in_array($roleWanted, $this->controller->ArrayOfRoleNames)) {
			if (Settings::GetPublic('IS_DETAILED_PERMISSIONS_DEBUGGING')) {
				Settings::GetRunTimeObject('MessageLog')->addAlert('has role wanted: ' . $roleWanted);
			}
			Settings::GetRuntimeObject('SecurityLog')->addNotice(Settings::GetRunTime('Currently Logged In User') . ' has role: ' . $roleWanted);
			return true;
		} else {
			if (Settings::GetPublic('IS_DETAILED_PERMISSIONS_DEBUGGING')) {
				Settings::GetRunTimeObject('MessageLog')->addAlert('Does NOT have role wanted: ' . $roleWanted);
			}
			Settings::GetRuntimeObject('SecurityLog')->addAlert(Settings::GetRunTime('Currently Logged In User') . ' Does NOT have role: ' . $roleWanted);
			return false;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * - do the test to see if the user has the permissions to do something
	 *
	 * @param type $wantedPermission
	 * @param type $process
	 * @param type $task
	 * @param type $action
	 * @param type $field
	 * @return boolean
	 */
	public function isAllowed($wantedPermission = Permissions::NO_RIGHT, $process = Permissions::NO_RIGHT, $task = Permissions::NO_RIGHT, $action = Permissions::NO_RIGHT, $field = Permissions::WILDCARD_RIGHT
	) {
		if (empty($wantedPermission) or $wantedPermission == Permissions::NO_RIGHT) {
			return false;
		}
		if (empty($process) or $process == Permissions::NO_RIGHT) {
			return false;
		}
		if (empty($task) or $task == Permissions::NO_RIGHT) {
			return false;
		}
		if (empty($action) or $action == Permissions::NO_RIGHT) {
			return false;
		}
		$s = $wantedPermission . '<=' . $process . '.' . $task . '.' . $action . '.' . $field;

		//$arPermissions = $this->controller->userPermissions;

		$process = strtoupper($process);
		$task = strtoupper($task);
		$action = strtoupper($action);
		$field = strtoupper($field);

		if (!empty($this->controller->userPermissions)) {
			foreach ($this->controller->userPermissions as $value) {
				if ($this->checkRight($value, $wantedPermission, $process, $task, $action, $field)) {
					if (Settings::GetPublic('IS_DETAILED_PERMISSIONS_DEBUGGING')) {
						Settings::GetRunTimeObject('MessageLog')->addAlert('has permission wanted: ' . $s);
					}
					Settings::GetRuntimeObject('SecurityLog')->addNotice(Settings::GetRunTime('Currently Logged In User') . ' has permission: ' . $s);

					return true;
				}
			}
		} else {
			return true;
		}
		if (Settings::GetPublic('IS_DETAILED_PERMISSIONS_DEBUGGING')) {
			Settings::GetRunTimeObject('MessageLog')->addAlert('Does NOT have permission wanted: ' . $s);
		}
		Settings::GetRuntimeObject('SecurityLog')->addAlert(Settings::GetRunTime('Currently Logged In User') . ' Does NOT have permission: ' . $s);
		return false;
	}

	/** -----------------------------------------------------------------------------------------------
	 * check if this right allows it
	 *
	 * @param type $singleOfPermissions
	 * @param type $wantedPermission
	 * @param type $process
	 * @param type $task
	 * @param type $action
	 * @param type $field
	 * @return boolean
	 */
	protected function checkRight($singleOfPermissions, $wantedPermission, $process, $task, $action, $field) {

//Dump::dumpLong( array( $singleOfPermissions, $wantedPermission, $process, $task, $action, $field));

		if (!$this->checkProcess($singleOfPermissions, $process)) {
			return false;
		}
		if (!$this->checkTask($singleOfPermissions, $task)) {
			return false;
		}
		if (!$this->checkAction($singleOfPermissions, $action)) {
			return false;
		}
		if (!$this->checkField($singleOfPermissions, $field)) {
			return false;
		}
		return $this->checkPermission($singleOfPermissions, $wantedPermission);
	}

	/** -----------------------------------------------------------------------------------------------
	 * check if the process allows it
	 * @param type $singleOfPermissions
	 * @param type $process
	 * @return type
	 */
	protected function checkProcess($singleOfPermissions, $process) {
		$r = (($process == Permissions::WILDCARD_RIGHT)
				or ( $process == $singleOfPermissions['PROCESS'])
				or ( $singleOfPermissions['PROCESS'] == Permissions::WILDCARD_RIGHT));
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *  check if the task allows it
	 * @param type $singleOfPermissions
	 * @param type $task
	 * @return type
	 */
	protected function checkTask($singleOfPermissions, $task) {
		$r = (($task == Permissions::WILDCARD_RIGHT)
				or ( $task == $singleOfPermissions['TASK'])
				or ( $singleOfPermissions['TASK'] == Permissions::WILDCARD_RIGHT));
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 * check if the action allows it
	 * @param type $singleOfPermissions
	 * @param type $action
	 * @return type
	 */
	protected function checkAction($singleOfPermissions, $action) {
		$r = (($action == Permissions::WILDCARD_RIGHT)
				or ( $action == $singleOfPermissions['ACTION'])
				or ( $singleOfPermissions['ACTION'] == Permissions::WILDCARD_RIGHT));

//		$s =$r ? '+true+':'+false+';
//		Settings::GetRunTimeObject('MessageLog')->addNotice('checkAction:' .  $s);
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 * check if the field allows it
	 *
	 * @param type $singleOfPermissions
	 * @param type $field
	 * @return type
	 */
	protected function checkField($singleOfPermissions, $field) {
		$r = (($field == Permissions::WILDCARD_RIGHT)
				or ( $field == $singleOfPermissions['FIELD'])
				or ( $singleOfPermissions['FIELD'] == Permissions::WILDCARD_RIGHT));

//		$s =$r ? '+true+':'+false+';
//		Settings::GetRunTimeObject('MessageLog')->addNotice('checkField:' .  $s);
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 * check if the permission hirachy allows it
	 * 			 i.e. god will allow everthing - dba will allow everything unless restricted by GOD rights
	 * 				or
	 * 	       i.e. -if you want read then you can get it with wildcard, read rights, write rights, dba rights, or god rights
	 * 								- otherwise you dont get to read
	 *
	 * @param type $singleOfPermissions
	 * @param type $wantedPermission
	 * @return boolean
	 */
	protected function checkPermission($singleOfPermissions, $wantedPermission) {
		switch ($wantedPermission) {
			case Permissions::GOD_RIGHT:
				$r = (( $singleOfPermissions['PERMISSION'] == Permissions::GOD_RIGHT )
						or ( $singleOfPermissions['PERMISSION'] == Permissions::WILDCARD_RIGHT));
				break;
			case Permissions::DBA_RIGHT:
				$r = (( $singleOfPermissions['PERMISSION'] == Permissions::GOD_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == Permissions::DBA_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == Permissions::WILDCARD_RIGHT));
				break;
			case Permissions::WRITE_RIGHT:
				$r = (( $singleOfPermissions['PERMISSION'] == Permissions::GOD_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == Permissions::DBA_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == Permissions::WRITE_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == Permissions::WILDCARD_RIGHT));
				break;
			case Permissions::READ_RIGHT:
				$r = (( $singleOfPermissions['PERMISSION'] == Permissions::GOD_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == Permissions::DBA_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == Permissions::WRITE_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == Permissions::READ_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == Permissions::WILDCARD_RIGHT));
				break;
			case Permissions::NO_RIGHT:
				$r = false;
				break;
			case Permissions::WILDCARD_RIGHT;
				$r = false;
		}
//		$s = 'wanted: ' . $wantedPermission;
//		$s .= ' --> ' . $singleOfPermissions['PERMISSION'];
//		$s .= '_';
//		$s .= $r ? '^true^' : '^false^';
//		Settings::GetRunTimeObject('MessageLog')->addNotice('checkPerm:' .  $s);
		return $r;
	}

	public function doInsertIfNotExists(string $username, string $password, string $email, ?string $primaryRole= null) :bool{
		$userInfoData = new \php_base\data\UserInfoData();

		$exists  = $userInfoData->doReadFromDatabaseByUserNameAndApp($username);

		if (! $exists) {
			Settings::GetRunTimeObject('MessageLog')->addNotice('adding user');
			$pwd = password_hash($password, PASSWORD_DEFAULT);
			UserInfoData::doInsertNewAccount( $username, $pwd, $email, $primaryRole);
			return true;
		}
		Settings::GetRunTimeObject('MessageLog')->addNotice('NOT adding user');
		return false;
	}

}
