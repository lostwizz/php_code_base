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

use \php_base\Utils\myUtils as myUtils;
use \php_base\Utils\myDBUtils as myDBUtils;


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
		$this->doReadFromDatabase($listOfRoleIDs);

//dump::dump($this->permissionList);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $listOfRolesIDs
	 * @throws \PDOException
	 * @throws \Exception
	 */
	protected function doReadFromDatabase($listOfRolesIDs) {
		$ids =  implode( ', ' , $listOfRolesIDs) ;
		try {
			$sql = 'SELECT id
						,roleId
						,UPPER(process) as process
						,UPPER(task) as task
						,UPPER(action) as action
						,UPPER(field) as field
						,Permission
					FROM ' .  Settings::GetProtected( 'DB_Table_PermissionsManager')
					. ' WHERE  RoleId in ('
											. $ids
											. ')';

			$paramas = array();
			$data = myDBUtils::doDBSelectMulti($sql);

			$this->permissionList = $data;

		} catch (\PDOException $e)	{
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		} catch (\Exception $e){
			throw new \Exception($e->getMessage(), (int)$e->getCode());
		}
	}

}