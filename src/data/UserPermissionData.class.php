<?php

/** * ********************************************************************************************
 * UserPermissionData.class.php
 *
 * Summary: reads the role's permissions from the database
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
 * @package ModelViewController - UserRoleAndPermissions\UserPermissions
 * @subpackage UserRoleAndPermissions
 * @since 0.3.0
 *
 * @example
 *
 * @see UserRoleAndPermissionsController.class.php
 * @see UserRoleAndPermissionsModel.class.php
 * @see UserRoleAndPermissionsView.class.php
 * @see UserInfoData.class.php
 * @see UserAttributeData.class.php
 * @see UserRoleData.class.php
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

////SELECT TOP (1000) [id]
////      ,[roleId]
////      ,[model]
////      ,[task]
////      ,[field]
////      ,[Permission]
////  FROM [Mikes_Application_Store].[dbo].
////  [RolePermissions]
////
////
////  id	roleId	model				task				field		right
////	1	2		Change_Password		Read_Old_Password	Password	Write
////	2	2		Add_Something		doSomething			SomeField	Write
////	3	4		Change_Something	*					*			DBA
////	4	2		Read_Something		*					*			Read

/** * **********************************************************************************************
 * read and write the role's permissions from the database
 */
class UserPermissionData {

	public $permissionList;

	/** -----------------------------------------------------------------------------------------------
	 *  basic constructor that initiates the reading from the database
	 * @param type $listOfRoleIDs
	 */
	public function __construct($listOfRoleIDs) {
		self::doReadFromDatabaseForRoles($listOfRoleIDs);

//dump::dump($this->permissionList);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $listOfRolesIDs
	 */
	protected function doReadFromDatabaseForRoles($listOfRolesIDs) {
		$ids = implode(', ', $listOfRolesIDs);

			$sql = 'SELECT id
						,roleId
						,UPPER(process) as process
						,UPPER(task) as task
						,UPPER(action) as action
						,UPPER(field) as field
						,Permission
					FROM ' . Settings::GetProtected('DB_Table_PermissionsManager')
					. ' WHERE  RoleId in ('
					. $ids
					. ')';

			//$paramas = array();
			$data = DBUtils::doDBSelectMulti($sql);

			$this->permissionList = $data;
//dump::dump($sql)	;
//dump::dump($data);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $roleID
	 * @param string $process
	 * @param string $task
	 * @param string $action
	 * @param string $field
	 * @param \php_base\data\Permission $permission
	 * @return int
	 */
	public static function doAddNewPermission( int $roleID, string $process, string $task, string $action, string $field, Permission $permission): int {
		$sql = 'INSERT INTO ' . Settings::GetProtected('DB_Table_PermissionsManager')
				. '( roleid, process, task, action, field, permission )'
				. ' VALUES '
				. '( :roleid, :process, :task, :action, :field, :permission )'
				;
		$params  = array(  ':roleid' =>  [ 'val' =>  $roleID   ,'type'=> \PDO::PARAM_INT],
			':process' =>  [ 'val' =>  strtoupper($process)     ,'type'=> \PDO::PARAM_STR],
			':task' =>  [ 'val' =>  strtoupper($task)     ,'type'=> \PDO::PARAM_str],
			':action' =>  [ 'val' =>  strtoupper($action)     ,'type'=> \PDO::PARAM_STR],
			':field' =>  [ 'val' =>   strtoupper($field)    ,'type'=> \PDO::PARAM_STR],
			':permission' =>  [ 'val' =>  $permission     ,'type'=> \PDO::PARAM_STR],

			);
		$data = DBUtils::doDBInsertReturnID( $sql, $params);
		return $data;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function doDeletePermissionByID(int $id): bool {
		$sql = 'DELETE FROM ' . Settings::GetProtected('DB_Table_PermissionsManager')
				. ' WHERE id = :id'
			;
		$params = array( ':id' => ['val' => $id, 'type'=> \PDO::PARAM_INT]);
		$data = DBUtils::doDBDelete($sql, $params);
		return ($data == 1);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $roleid
	 * @return bool
	 */
	public static function doDeletePermissionByRoleID( int $roleid ) :bool {

		$sql = 'DELETE FROM ' . Settings::GetProtected('DB_Table_PermissionsManager')
				. 'WHERE roleid = :roleid'
				;
		$params = array( ':roleid' => ['val' => $roleid, 'type'=> \PDO::PARAM_INT]);
		$data = DBUtils::doDBDelete($sql, $params);
		return ($data >= 1);
	}

}
