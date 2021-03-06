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
 * @see UserData.class.php
 * @see UserAttributesdata.class.php
 * @see UserPermissionData.class.php
 * @see UserRolesData.class.php
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

use \php_base\data\UserData as UserData;
use \php_base\data\UserAttributesData as UserAttributesData;
use \php_base\data\UserRolesData as UserRolesData;
use \php_base\data\RolePermissionsData as RolePermissionsData;

use \php_base\Utils\SubSystemMessage as SubSystemMessage;




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
	public function __construct($controller) {   //$action ='', $payload = null){


		Settings::getRunTimeObject('PERMISSION_DEBUGGING')->addInfo('constructor for UserRoleAndPermissionsModel');


		$this->controller = $controller;
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
	 * @return array|null
	 */
	public function getListOfAllUsers() : ?array {
		$UD = new \php_base\data\UserData(null, null);
		$userList = $UD->readAllData();
		return $userList;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array|null
	 */
	public function getListOfAllAttributes() : ?array {
		$UA = new \php_base\data\UserAttributesData($this);
		$attribs = $UA->readAllData();
		return $attribs;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array|null
	 */
	public function getListOfAllRoles() : ?array {
		$UR = new \php_base\data\UserRolesData( $this);
		$userRoles = $UR->readAllData();
		return $userRoles;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array|null
	 */
	public function getListOfAllRolePermissions() : ?array {
		//$PL = $RP->permissionList;
		$RP = new \php_base\data\RolePermissionsData($this);
		$rolePermsList = $RP->readAllData();

		return $rolePermsList;
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $username
	 * @return Response
	 */
	public function LoadALLUser( $username) : Response {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_4('@@LoadALLUser: '.  $username);

		try {

			// setup the user with the extra data in the users table and then get the attributes for that user
			$this->controller->username = $username;

			$this->GetUSERinfo($username);
			Settings::GetRunTimeObject( 'PERMISSION_DEBUGGING')->addNotice_5( $this->controller->userInfo);

			$this->GetUSERAttributes();
			Settings::GetRunTimeObject( 'PERMISSION_DEBUGGING')->addNotice_5( $this->controller->userAttributes);

			$this->GetRolePermissionsData();
			Settings::GetRunTimeObject( 'PERMISSION_DEBUGGING')->addNotice_5( $this->controller->rolePermissions);

			// clean up things not needed
			unset($this->arOfRoleIDs);

			Settings::GetRunTimeObject( 'PERMISSION_DEBUGGING')->addInfo(
				$this->controller->view->dumpPermissions()
					);


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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_4('@@GetUSERinfo: ' . $username);

		$DataUserInfo = new UserData($this->controller, $username);

//dump::dumpLong($DataUserInfo);
		$this->controller->userID = $DataUserInfo->getUserID();

		$this->controller->userInfo = $DataUserInfo->UserInfo;
		return (!empty($this->controller->userInfo));
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return bool
	 */
	protected function GetUSERAttributes(): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_4('@@GetUSERAttributes');

		$DataUserAttributes = new UserAttributesData($this->controller, $this->controller->userID);

		// take the primary role from the user info and addit to the array of roles in the user attributes
		$primaryRole = $this->controller->userInfo['PRIMARYROLENAME'];

		$DataUserAttributes->AddPrimaryRole($primaryRole);  // add the userInfo PrimaryRole

		$this->controller->userAttributes = $DataUserAttributes->UserAttributes;

		// get the array of all the roles this user has   (words i.e. Clerk)
		$this->controller->ArrayOfRoleNames = $DataUserAttributes->getArrayOfRoleNames();

		return (!empty($this->controller->userAttributes));
	}



	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return bool
	 */
	protected function GetRolePermissionsData(): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_4('@@GetRolepermissions');


		// take the list of roles (words i.e. Clerk) and get the role IDs
		$DataUserRoles = new UserRolesData($this->controller, $this->controller->ArrayOfRoleNames);

		// now we have an array of Role ids
		$this->controller->arOfRoleIDs = $DataUserRoles->RoleIDData;

		// now with roleid go and get the permissions related to those role ids
		$DataRolePermissions = new RolePermissionsData($this->controller, $this->controller->arOfRoleIDs);

		$this->controller->ArrayOfRoleNames = $DataUserRoles->RoleIDnames;

		$this->controller->rolePermissions = $DataRolePermissions->permissionList;

		//Settings::GetRunTimeObject( 'PERMISSION_DEBUGGING')->addInfo(	$this->controller->view->dumpPermissions());

		return (!empty($this->controller->rolePermissions));
	}



	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $roleWanted - string with the role wanted
	 * @return type
	 */
	public function hasRolePermissions(string $roleWanted): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_4('@@hasRolePermissions: ' .     $roleWanted);

		$roleWanted = trim($roleWanted);
		if (\in_array($roleWanted, $this->controller->ArrayOfRoleNames)) {
			Settings::GetRunTimeObject('PERMISSION_DEBUGGING')->addInfo('has role wanted: ' . $roleWanted);

			Settings::GetRuntimeObject('SecurityLog')->addNotice(Settings::GetRunTime('Currently Logged In User') . ' has role: ' . $roleWanted);
			return true;
		} else {
			Settings::GetRunTimeObject('PERMISSION_DEBUGGING')->addInfo('Does NOT have role wanted: ' . $roleWanted);
			Settings::GetRuntimeObject('SecurityLog')->addInfo(Settings::GetRunTime('Currently Logged In User') . ' Does NOT have role: ' . $roleWanted);
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
	public function isAllowed(
			$wantedPermission = Permissions::NO_RIGHT,
			$process = Permissions::NO_RIGHT,
			$task = Permissions::NO_RIGHT,
			$action = Permissions::NO_RIGHT,
			$field = Permissions::WILDCARD_RIGHT
			) {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_4('@@isAllowed');
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

		if (!empty($this->controller->rolePermissions)) {
			foreach ($this->controller->rolePermissions as $value) {
				if ($this->checkRight($value, $wantedPermission, $process, $task, $action, $field)) {
					Settings::GetRunTimeObject('PERMISSION_DEBUGGING')->addInfo('has permission wanted: ' . $s);

					Settings::GetRuntimeObject('SecurityLog')->addNotice(Settings::GetRunTime('Currently Logged In User') . ' has permission: ' . $s);

					return true;
				}
			}
		} else {
			return true;
		}

		Settings::GetRunTimeObject('PERMISSION_DEBUGGING')->addAlert('Does NOT have permission wanted: ' . $s);

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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_4('@@checkRight');

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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_4('@@checkProcess');
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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_4('@@checkTask');
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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_4('@@checkAction');
		$r = (($action == Permissions::WILDCARD_RIGHT)
				or ( $action == $singleOfPermissions['ACTION'])
				or ( $singleOfPermissions['ACTION'] == Permissions::WILDCARD_RIGHT));

//		$s =$r ? '+true+':'+false+';
//		Settings::GetRunTimeObject('MessageLog')->addNotice_4('checkAction:' .  $s);
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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_4('@@checkField');
		$r = (($field == Permissions::WILDCARD_RIGHT)
				or ( $field == $singleOfPermissions['FIELD'])
				or ( $singleOfPermissions['FIELD'] == Permissions::WILDCARD_RIGHT));

//		$s =$r ? '+true+':'+false+';
//		Settings::GetRunTimeObject('MessageLog')->addNotice_4('checkField:' .  $s);
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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_4('@@checkPermission');
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
//		Settings::GetRunTimeObject('MessageLog')->addNotice_4('checkPerm:' .  $s);
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param string|null $primaryRole
	 * @return bool
	 */
	public function doInsertIfNotExists(string $username, string $password, string $email, ?string $primaryRole= null) :bool{
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_4('@@doInsertIfNotExists');
		$userInfoData = new UserData();
		if( empty($username)){
			return false;
		}
		$exists  = $userInfoData->doReadFromDatabaseByUserNameAndApp($username);

		if (! $exists) {
			Settings::GetRunTimeObject('MessageLog')->addNotice_4('adding user');
			$pwd = password_hash($password, PASSWORD_DEFAULT);
			UserData::doInsertNewAccount( $username, $pwd, $email, $primaryRole);
			return true;
		}
		Settings::GetRunTimeObject('MessageLog')->addNotice_4('NOT adding user');
		return false;
	}

}
