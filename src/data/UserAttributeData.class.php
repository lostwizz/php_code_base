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
use \php_base\Utils\DatabaseHandlers\Table as Table;
use \php_base\Utils\DatabaseHandlers\Field as Field;

/** * **********************************************************************************************
 * This any of the reads or writes to the UserAttributes table
 */
class UserAttributeData extends data {

	public $UserAttributes = [];
	public $roleNames = [];
	public $Table;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 * constructor - starts of the reading of data for that User Id
	 * @param type $userID
	 */
	public function __construct($controller, $userID) {
		if ( Settings::GetPublic('IS_DETAILED_USERROLEANDPERMISSIONS_DEBUGGING')){
			Settings::setRunTime( 'PERMISSION_DEBUGGING',  Settings::GetRunTimeObject('MessageLog')) ;
		}
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@constructor: ' . $userID);

		$this->controller = $controller;

		$this->defineTable();
		$this->doReadFromDatabaseByUserID($userID);
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
	 * [id]
	  ,[UserId]
	  ,[AttributeName]
	  ,[AttributeValue]
	 *
	 *
	 * @return void
	 */
	public function defineTable(): void {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@defineTable');
		$this->Table = new Table(Settings::GetProtected('DB_Table_UserAttributes'), ['className' => __NAMESPACE__ . '\UserAttributeData']);
		$this->Table->setPrimaryKey('id', ['prettyName' => 'Id']);

		$this->Table->addFieldInt('id', ['prettyName' => 'Id',
			'alignment' => 'right']);
		$this->Table->addFieldInt('userid', ['prettyName' => 'User Id']);
		$this->Table->addFieldInt('attributename', ['prettyName' => 'Attribute Name']);
		$this->Table->addFieldInt('attributevalue', ['prettyName' => 'Attribute Value']);
	}

	/** -----------------------------------------------------------------------------------------------
	 * returns the basic list of Roles (used for the processsing of roles to eliminate duplicates
	 *
	 * @return type
	 */
	public function getArrayOfRoleNames() {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@getArrayOfRoleNames');

		return $this->roleNames;
	}

	/** -----------------------------------------------------------------------------------------------
	 * take the primary roll and add it to the list of roles
	 *
	 * @param type $roleName
	 */
	public function AddPrimaryRole($roleName) {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@AddPrimaryRole');
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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@ProcessAttributes');
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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addInfo($this->UserAttributes);
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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doReadFromDatabaseByUserID: ' . $userID);

		$sql = 'SELECT Id
						,UserId
						,AttributeName
						,AttributeValue
					FROM ' . Settings::GetProtected('DB_Table_UserAttributes')
				. ' WHERE  UserId = :userid'
		;

		$params = array(':userid' => $userID);
		$data = DBUtils::doDBSelectMulti($sql, $params);
dump::dumpLong($data);
		$this->ProcessAttributes($data);
		return true;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userID
	 * @param string $attribName
	 * @param string $attribValue
	 * @return bool
	 */
	public function doAddAttribute(int $userID, string $attribName, string $attribValue): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doAddAttribute:' . $userID . '/'. $attribName . '/'. $attribValue);

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
	public function doRemoveAttribute(int $userid, string $attribName): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doRemoveAttribute: ' . $userid. $attribName);

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
	public function doRemoveAllAttributesForUserID(int $userid): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doRemoveAllAttributesForUserID: ' . $userid);

		$sql = 'DELETE FROM ' . Settings::GetProtected('DB_Table_UserAttributes')
				. ' WHERE userid = :userid'
		;
		$params = array(':userid' => ['val' => $userID, 'type' => \PDO::PARAM_STR]);

		$data = DBUtils::doDBDelete($sql, $params);
		return ($data == 1);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userid
	 * @param string $attribName
	 * @param string $attribValue
	 * @return bool
	 */
	public function doInserOrUpdateAttributeForUserID(int $userid, string $attribName, string $attribValue): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doInsertOrUpdateAttributeForUserID:'. $userid);

		$val = $this->getByUseridAndAttributeName($userid, $attribName, $attribValue);
		if (empty($val['ATTRIBUTEVALUE']) and empty($val['ATTRIBUTENAME'])) {  // the value might be '' so make it do an update if it is
			//insert
			$val2 = $this->insertUseridAndAttributeName($userid, $attribName, $attribValue);
		} else {
			//update
			$val2 = $this->updateByUseridAndAttributeName($userid, $attribName, $attribValue);
		}
		return $val2;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userid
	 * @param string $attribName
	 * @return type
	 */
	public function getByUseridAndAttributeName(int $userid, string $attribName) {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@getByUseridAndAttributeName:'  . $userid);

		$sql = 'SELECT Id
						,UserId
						,AttributeName
						,AttributeValue
					FROM ' . Settings::GetProtected('DB_Table_UserAttributes')
				. ' WHERE  UserId = :userid'
				. ' AND AttributeName = :attribName'
		;
		$params = array(':userid' => ['val' => $userid, 'type' => \PDO::PARAM_STR],
			':attribName' => ['val' => $attribName, 'type' => \PDO::PARAM_STR]);
		$data = DBUtils::doDBSelectSingle($sql, $params);
		return $data;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userid
	 * @param string $attribName
	 * @param string $attribValue
	 * @return bool
	 */
	public function updateByUseridAndAttributeName(int $userid, string $attribName, string $attribValue): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@updateByUseridAndAttributeName:' . $userid);

		$sql = 'UPDATE ' . Settings::GetProtected('DB_Table_UserAttributes')
				. ' SET AttributeValue = :attribValue'
				. ' WHERE userid = :userid'
				. ' AND AttributeName = :attribName'
		;
		$params = array(':userid' => ['val' => $userid, 'type' => \PDO::PARAM_STR],
			':attribName' => ['val' => $attribName, 'type' => \PDO::PARAM_STR],
			':attribValue' => ['val' => $attribValue, 'type' => \PDO::PARAM_STR]
		);

		$data = DBUtils::doDBUpdateSingle($sql, $params);
		return $data;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userid
	 * @param string $attribName
	 * @param string $attribValue
	 * @return bool
	 */
	public function insertUseridAndAttributeName(int $userid, string $attribName, string $attribValue): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@insertUseridAndAttriubuteName: '. $userid);

		$sql = 'INSERT INTO ' . Settings::GetProtected('DB_Table_UserAttributes')
				. ' (Userid, AttributeName, AttributeValue)'
				. ' VALUES '
				. ' (:userid, :attribName, :attribValue) '
		;
		$params = array(':userid' => ['val' => $userid, 'type' => \PDO::PARAM_STR],
			':attribName' => ['val' => $attribName, 'type' => \PDO::PARAM_STR],
			':attribValue' => ['val' => $attribValue, 'type' => \PDO::PARAM_STR]
		);
		$data = DBUtils::doDBInsertReturnID($sql, $params);
		Settings::GetRunTimeObject('MessageLog')->addNotice(' INsert by userid and attrib name' . $userid . '-' . $attribName);

		return ($data > 0);
	}

}
