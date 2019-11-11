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

//***********************************************************************************************
//***********************************************************************************************
/**
 * handles the logic for user roles and permissions
 */
Class UserRoleAndPermissionsModel extends Model {
	/*
	 * theses the the constants for ALL the possible permissions i.e. none, read,  write, dba and god (in hirachial order)
	 */

	const GOD_RIGHT = 'GOD';
	const DBA_RIGHT = 'DBA';
	const WRITE_RIGHT = 'Write';
	const READ_RIGHT = 'Read';
	const WILDCARD_RIGHT = '*';
	const NO_RIGHT = '__NO__RIGHT__';

	public $action;
	public $payload;
	public $controller;

	/** -----------------------------------------------------------------------------------------------
	 *  constructor - basically keeps track of the controller
	 * @param type $controller
	 */
	public function __construct($controller) {   //$action ='', $payload = null){
		if (!empty($controller)) {
			$this->controller = $controller;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $roleWanted - string with the role wanted
	 * @return type
	 */
	public function hasRole(string $roleWanted): bool {
		$roleWanted = trim($roleWanted);
		if (in_array($roleWanted, $this->controller->ArrayOfRoleNames)) {
			Settings::GetRunTimeObject('MessageLog')->addAlert('has role wanted: ' . $roleWanted);
			Settings::GetRuntimeObject('SecurityLog')->addNotice(Settings::GetRunTime('Currently Logged In User') . ' has role: ' . $roleWanted);
			return true;
		} else {
			Settings::GetRunTimeObject('MessageLog')->addAlert('Does NOT have role wanted: ' . $roleWanted);
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
	public function isAllowed($wantedPermission = self::NO_RIGHT, $process = self::NO_RIGHT, $task = self::NO_RIGHT, $action = self::NO_RIGHT, $field = self::WILDCARD_RIGHT
	) {
		if (empty($wantedPermission) or $wantedPermission == self::NO_RIGHT) {
			return false;
		}
		if (empty($process) or $process == self::NO_RIGHT) {
			return false;
		}
		if (empty($task) or $task == self::NO_RIGHT) {
			return false;
		}
		if (empty($action) or $action == self::NO_RIGHT) {
			return false;
		}
		$s = $wantedPermission . '<=' . $process . '.' . $task . '.' . $action . '.' . $field;

		$arPermissions = $this->controller->userPermissions;

		$process = strtoupper($process);
		$task = strtoupper($task);
		$action = strtoupper($action);
		$field = strtoupper($field);

		foreach ($arPermissions as $value) {
			if ($this->checkRight($value, $wantedPermission, $process, $task, $action, $field)) {
				Settings::GetRunTimeObject('MessageLog')->addAlert('has permission wanted: ' . $s);
				Settings::GetRuntimeObject('SecurityLog')->addNotice(Settings::GetRunTime('Currently Logged In User') . ' has permission: ' . $s);

				return true;
			}
		}
		Settings::GetRunTimeObject('MessageLog')->addAlert('Does NOT have permission wanted: ' . $s);
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
		$r = (($process == self::WILDCARD_RIGHT)
				or ( $process == $singleOfPermissions['PROCESS'])
				or ( $singleOfPermissions['PROCESS'] == self::WILDCARD_RIGHT));
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *  check if the task allows it
	 * @param type $singleOfPermissions
	 * @param type $task
	 * @return type
	 */
	protected function checkTask($singleOfPermissions, $task) {
		$r = (($task == self::WILDCARD_RIGHT)
				or ( $task == $singleOfPermissions['TASK'])
				or ( $singleOfPermissions['TASK'] == self::WILDCARD_RIGHT));
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 * check if the action allows it
	 * @param type $singleOfPermissions
	 * @param type $action
	 * @return type
	 */
	protected function checkAction($singleOfPermissions, $action) {
		$r = (($action == self::WILDCARD_RIGHT)
				or ( $action == $singleOfPermissions['ACTION'])
				or ( $singleOfPermissions['ACTION'] == self::WILDCARD_RIGHT));

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
		$r = (($field == self::WILDCARD_RIGHT)
				or ( $field == $singleOfPermissions['FIELD'])
				or ( $singleOfPermissions['FIELD'] == self::WILDCARD_RIGHT));

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
			case self::GOD_RIGHT:
				$r = (( $singleOfPermissions['PERMISSION'] == self::GOD_RIGHT )
						or ( $singleOfPermissions['PERMISSION'] == self::WILDCARD_RIGHT));
				break;
			case self::DBA_RIGHT:
				$r = (( $singleOfPermissions['PERMISSION'] == self::GOD_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == self::DBA_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == self::WILDCARD_RIGHT));
				break;
			case self::WRITE_RIGHT:
				$r = (( $singleOfPermissions['PERMISSION'] == self::GOD_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == self::DBA_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == self::WRITE_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == self::WILDCARD_RIGHT));
				break;
			case self::READ_RIGHT:
				$r = (( $singleOfPermissions['PERMISSION'] == self::GOD_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == self::DBA_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == self::WRITE_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == self::READ_RIGHT)
						or ( $singleOfPermissions['PERMISSION'] == self::WILDCARD_RIGHT));
				break;
			case self::NO_RIGHT:
				$r = false;
				break;
			case self::WILDCARD_RIGHT;
				$r = false;
		}
//		$s = 'wanted: ' . $wantedPermission;
//		$s .= ' --> ' . $singleOfPermissions['PERMISSION'];
//		$s .= '_';
//		$s .= $r ? '^true^' : '^false^';
//		Settings::GetRunTimeObject('MessageLog')->addNotice('checkPerm:' .  $s);
		return $r;
	}

}
