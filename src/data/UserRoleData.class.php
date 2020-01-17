<?php

//**********************************************************************************************
//* UserRoleData.class.php
/** * ********************************************************************************************
 * UserRoleData.class.php
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
 * @package ModelViewController - UserRoleAndPermissions\UserRoleData
 * @subpackage UserRoleAndPermissions
 * @since 0.3.0
 *
 * @example
 *
 * @see UserRoleAndPermissionsController.class.php
 * @see UserRoleAndPermissionsModel.class.php
 * @see UserRoleAndPermissionsView.class.php
 * @see UserInfoData.class.php
 * @see UserPermissionData.class.php
 * @see UserAttributeData.class.php
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\data;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\Utils as Utils;
use \php_base\Utils\DBUtils as DBUtils;
use \php_base\Utils\Cache as CACHE;
use \php_base\Utils\DatabaseHandlers\Table as Table;
use \php_base\Utils\DatabaseHandlers\Field as Field;

/** * **********************************************************************************************
 * read and write the roles (by id) for the user
 */
Class UserRoleData extends Data {

//	public $action;
//	public $payload;

	public $RoleIDData = []; // array with the keys begin the name and the values being the roleID #
	//		- only needed this way because of the RolePermissions needing the the list of ids
	public $RoleIDnames = []; // array with the keys being the roleID # and the values being the name
	public $Table;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 *  constructor that initiates the reading of the database
	 * @param type $ArrayOfNames
	 */
	public function __construct($controller, ?array $ArrayOfNames = null) {
		if ( Settings::GetPublic('IS_DETAILED_USERROLEANDPERMISSIONS_DEBUGGING')){
			Settings::setRunTime( 'PERMISSION_DEBUGGING',  Settings::GetRunTimeObject('MessageLog')) ;
		}
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@constructor: ' . print_r($ArrayOfNames, true));

		$this->controller = $controller;

		$this->defineTable();

		if (!empty($ArrayOfNames)) {
			$this->doReadFromDatabase($ArrayOfNames);
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
	 * @return void
	 */
	public function defineTable(): void {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@defineTable');

		$this->Table = new Table(Settings::GetProtected('DB_Table_RoleManager'), ['className' => __NAMESPACE__ . '\UserRoleData']);
		$this->Table->setPrimaryKey('roleId', ['prettyName' => 'Role Id']);
		$this->Table->addFieldInt('roleid', ['prettyName' => 'Role Id',
			'alignment' => 'right']);
		$this->Table->addFieldText('name', ['prettyName' => 'Name']);
	}

	/** -----------------------------------------------------------------------------------------------
	 * process the roles into arrays - one with keys being the name of the role and the other having the key the role's ID
	 * 			- the first is needed to read the role's permissions form the permission table - then it is dumped as not needed
	 * @param type $data
	 */
	public function ProcessRoleIDs($data) {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addInfo('@@ProcessRoleIDs: '. print_r($data, true));
		foreach ($data as $record) {
			if (!empty($record['NAME']) and ! empty($record['ROLEID'])) {
				$this->RoleIDData[$record['NAME']] = $record['ROLEID'];
				$this->RoleIDnames[$record['ROLEID']] = $record['NAME'];
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *  read the data from the database
	 * @param type $ArrayOfNames
	 * @return bool
	 */
	protected function doReadFromDatabase($ArrayOfNames): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doReadFromDatabase' . print_r($ArrayOfNames, true));

		$names = "'" . implode("', '", $ArrayOfNames) . "'";
		$sql = 'SELECT RoleId
						,Name
					FROM ' . Settings::GetProtected('DB_Table_RoleManager')
				. ' WHERE  Name in ('
				. $names
				. ')'
		;

		$params = null; ///array(Settings::GetPublic( 'RoleId') );
		$data = DBUtils::doDBSelectMulti($sql, $params);

		$this->ProcessRoleIDs($data);
		return true;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $roleName
	 * @return int
	 */
	public function doAddNewRole(string $roleName): int {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doAddNewRole: '. $roleName);

		$sql = 'INSERT INTO ' . Settings::GetProtected('DB_Table_RoleManager')
				. ' { name)'
				. ' VALUES '
				. '( :name )'
		;
		$params = array(':name' => ['val' => $roleName, 'type' => \PDO::PARAM_STR]);

		$data = DBUtils::doDBInsertReturnID($sql, $params);
		return $data;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $roleName
	 * @return bool
	 */
	public function doRemoveRoleByName(string $roleName): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doRemveRoleByName:' . $roleName);

		$sql = 'DELETE FROM ' . Settings::GetProtected('DB_Table_RoleManager')
				. ' WHERE name = :name'
		;
		$params = array(':name' => ['val' => $roleName, 'type' => \PDO::PARAM_STR]);

		$data = DBUtils::doDBDelete($sql, $params);
		return ($data == 1);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $roleID
	 * @return bool
	 */
	public function doRemoveRoleByID(int $roleID): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doRemoveRoleByID: '. $roleID);
		$sql = 'DELETE FROM ' . Settings::GetProtected('DB_Table_RoleManager')
				. ' WHERE roleid = :roleid'
		;
		$params = array(':roleid' => ['val' => $roleID, 'type' => \PDO::PARAM_INT]);

		$data = DBUtils::doDBDelete($sql, $params);
		return ($data == 1);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $roleName
	 * @return int
	 */
	public function getRoleIDByName(string $roleName): int {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@getRoleIDbyName: ' . $roleName );

		$sql = 'SELECT roleid '
				. ' FROM ' . Settings::GetProtected('DB_Table_RoleManager')
				. ' WHERE roleid = :roleid'
		;
		$params = array(':name' => ['val' => $roleName, 'type' => \PDO::PARAM_STR]);
		$data = DBUtils::doDBUpdateSingle($sql, $params);
		if ($data > 0) {
			return $data;
		} else {
			return -1;
		}
	}

}
