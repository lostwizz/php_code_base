<?php

/** * ********************************************************************************************
 * UserAttributeData.class.php
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
 * @package ModelViewController - UserRoleAndPermissions\UserAttributes
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
 * @see UserRoleData.class.php
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\data;

use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Utils as Utils;
use \php_base\Utils\DBUtils as DBUtils;
use \php_base\Utils\Response as Response;
use \php_base\Utils\Settings as Settings;

/** * **********************************************************************************************
 * This any of the reads or writes to the UserAttributes table
 */
class UserAttributeData extends data {

	public $UserAttributes = [];
	public $roleNames = [];

	/** -----------------------------------------------------------------------------------------------
	 * constructor - starts of the reading of data for that User Id
	 * @param type $userID
	 */
	public function __construct($userID) {
		$this->doReadFromDatabaseByUserID($userID);
	}

	/** -----------------------------------------------------------------------------------------------
	 * returns the basic list of Roles (used for the processsing of roles to eliminate duplicates
	 *
	 * @return type
	 */
	public function getArrayOfRoleNames() {
		return $this->roleNames;
	}

	/** -----------------------------------------------------------------------------------------------
	 * take the primary roll and add it to the list of roles
	 *
	 * @param type $roleName
	 */
	public function AddPrimaryRole($roleName) {
		if (!in_array($roleName, $this->roleNames) and ! empty($roleName)) {
			$this->roleNames[] = $roleName;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * take the raw data from the database and make an associative array for the attributes
	 *    - handle duplicates (dont add 2nd  copy)
	 *
	 * @param type $data
	 */
	public function ProcessAttributes($data) {
		foreach ($data as $record) {
			if (($record['ATTRIBUTENAME'] == 'PrimaryRole' )
					or ( $record['ATTRIBUTENAME'] == 'SecondaryRole' )
					or ( $record['ATTRIBUTENAME'] == 'Role' )) {

				if (!empty($record['ATTRIBUTEVALUE'])
						and ! in_array($record['ATTRIBUTEVALUE'], $this->roleNames)) {

					$this->roleNames[] = $record['ATTRIBUTEVALUE'];
				}
			} else {
				$this->UserAttributes[$record['ATTRIBUTENAME']] = $record['ATTRIBUTEVALUE'];
			}
		}
	}

	//-----------------------------------------------------------------------------------------------
////	public function AddPrimaryRole($roleName) {
////		if ( array_key_exists ( $roleName, $this->UserAttributes ) ) {
////			$this->UserAttributes;
////		}
////	}
	//-----------------------------------------------------------------------------------------------
	////  SELECT TOP (1000) [id]
////      ,[UserId]
////      ,[AttributeName]
////      ,[AttributeValue]
////  FROM [Mikes_Application_Store].[dbo].
////       [UserAttributes]
////  id	UserId	AttributeName	AttributeValue
////1	1	GivenName	Mike
////2	1	SurName	Merrett
////3	1	eMailAddress	mike.merrett@whitehorse.ca
////4	1	PrimaryRole	DBA
////5	1	SecondaryRole	Clerk
////6	1	SecondaryRole	ViewOnly

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $userID
	 * @return bool
	 */
	protected function doReadFromDatabaseByUserID($userID): bool {

		$sql = 'SELECT Id
						,UserId
						,AttributeName
						,AttributeValue
					FROM ' . Settings::GetProtected('DB_Table_UserAttributes')
				. ' WHERE  UserId = :userid'
		;

		$params = array(':userid' => $userID);
		$data = DBUtils::doDBSelectMulti($sql, $params);

		self::ProcessAttributes($data);
		return true;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userID
	 * @param string $attribName
	 * @param string $attribValue
	 * @return bool
	 */
	public static function doAddAttribute(int $userID, string $attribName, string $attribValue): bool {
		$sql = 'INSERT into ' . Settings::GetProtected('DB_Table_UserAttributes')
				. ' ( userid, AttributeName, AttributeValue )'
				. ' VALUES '
				. '( :userid, :attrib_name, :attrib_value )'
		;
		$params = array(':userid' => ['val' => $userID, 'type' => \PDO::PARAM_STR],
			':attrib_name' => ['val' => $attribName, 'type' => \PDO::PARAM_STR],
			':attrib_value' => ['val' => $attribValue, 'type' => \PDO::PARAM_STR]
		);

		$data = DBUtils::doDBInsertReturnID($sql, $params);
		return true;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userid
	 * @param string $attribName
	 * @return bool
	 */
	public static  function doRemoveAttribute(int $userid, string $attribName): bool {
		$sql = 'DELETE FROM ' . Settings::GetProtected('DB_Table_UserAttributes')
				. ' WHERE userid = :userid AND attributename = :attrib_name'
		;
		$params = array(':userid' => ['val' => $userID, 'type' => \PDO::PARAM_STR],
			':attrib_name' => ['val' => $attribName, 'type' => \PDO::PARAM_STR]
		);

		$data = DBUtils::doDBDelete($sql, $params);
		return ($data == 1);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userid
	 * @return bool
	 */
	public static function doRemoveAllAttributesForUserID(int $userid): bool {
		$sql = 'DELETE FROM ' . Settings::GetProtected('DB_Table_UserAttributes')
				. ' WHERE userid = :userid'
		;
		$params = array(':userid' => ['val' => $userID, 'type' => \PDO::PARAM_STR]);

		$data = DBUtils::doDBDelete($sql, $params);
		return ($data == 1);
	}

}
