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
use \php_base\Utils\SubSystemMessage as SubSystemMessage;
use \php_base\Utils\Cache as CACHE;



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
	public function __construct($controller, $userID = null) {

		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_6('@@constructor: ' . $userID);

		$this->controller = $controller;

		$this->defineTable();
		if ( ! is_null($userID)){
			$this->doReadFromDatabaseByUserID($userID);
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
	 * [id]
	  ,[UserId]
	  ,[AttributeName]
	  ,[AttributeValue]
	 *
	 *
	 * @return void
	 */
	public function defineTable(): void {
		Settings::GetRuntimeObject('PERMISSION_DEBUGGING')->addNotice_6('@@defineTable');
		Settings::GetRuntimeObject('PERMISSION_DEBUGGING')->Suspend();

		$this->Table = new Table(
				Settings::GetProtected('DB_Table_UserAttributes'),
				[
			'className' => __NAMESPACE__ . '\UserAttributeData',
			'isAdding' => true,
			'isEditing' => true,
			'isDeleting' => true,
			'isSpecial' => true
		]);
		$this->Table->setPrimaryKey('id');//, ['prettyName' => 'Id']);

		$this->Table->addFieldInt('id', ['prettyName' => 'Id',
			'text-align' => 'right',
			'isEditable' => false
			]);
		$this->Table->addFieldInt(
				'userid',
				['prettyName' => 'User Id',
					'text-align' => 'right',
					'subType' => Field::SUBTYPE_SELECTLIST,
					'selectFrom' => ['method' => 'getUserInfoDataForSelect',
									'class' => '\php_base\data\UserInfoData',
									'id' => 'userid',
									'data' => 'username']
					]);
		$this->Table->addFieldInt('attributename', ['prettyName' => 'Attribute Name']);
		$this->Table->addFieldInt('attributevalue', ['prettyName' => 'Attribute Value']);

		Settings::GetRuntimeObject('PERMISSION_DEBUGGING')->Resume();
	}

	/** -----------------------------------------------------------------------------------------------
	 * if any updates or inserts are done on the table than all of the caches may be invalid
	 *    - so clear them out so no mixups
	 * @return void
	 */
	public function clearAllCaches() :void {
		CACHE::deleteForPrefix(Settings::GetProtected('DB_Table_UserAttributes') . '_for_userid_' );
		CACHE::deleteForPrefix(Settings::GetProtected('DB_Table_UserAttributes') . '_for_userid_');
		CACHE::deleteForPrefix( Settings::GetProtected('DB_Table_UserAttributes')   . '_userAndAttrib_');
		CACHE::delete(Settings::GetProtected('DB_Table_UserAttributes') .'_ReadAll');
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array
	 */
	public function readAllData(): array {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_6('@@readAllData');

		if ( CACHE::exists( Settings::GetProtected('DB_Table_UserAttributes') .'_ReadAll' )){
			$data = CACHE::pull( Settings::GetProtected('DB_Table_UserAttributes') .'_ReadAll' );
		} else  {
			$sql = 'SELECT * '
					. ' FROM ' . Settings::GetProtected('DB_Table_UserAttributes')
					. ' ORDER BY Userid' ;

			$data = DBUtils::doDBSelectMulti($sql);

			if (Settings::GetPublic('CACHE_Allow_Tables to be Cached')) {
				CACHE::add(Settings::GetProtected('DB_Table_UserAttributes') .'_ReadAll', $data);
			}
		}
		return $data;
	}

	/** -----------------------------------------------------------------------------------------------
	 * returns the basic list of Roles (used for the processsing of roles to eliminate duplicates
	 *
	 * @return type
	 */
	public function getArrayOfRoleNames() {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_6('@@getArrayOfRoleNames');

		return $this->roleNames;
	}

	/** -----------------------------------------------------------------------------------------------
	 * take the primary roll and add it to the list of roles
	 *
	 * @param type $roleName
	 */
	public function AddPrimaryRole($roleName) {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_6('@@AddPrimaryRole');
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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_6('@@ProcessAttributes');
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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_6('@@doReadFromDatabaseByUserID: ' . $userID);

		if (CACHE::exists( Settings::GetProtected('DB_Table_UserAttributes') . '_for_userid_' . $userID)) {
			$data = CACHE::pull( Settings::GetProtected('DB_Table_UserAttributes') . '_for_userid_' . $userID)	;
		} else {
			$sql = 'SELECT Id
							,UserId
							,AttributeName
							,AttributeValue
						FROM ' . Settings::GetProtected('DB_Table_UserAttributes')
					. ' WHERE  UserId = :userid'
			;

			$params = array(':userid' => $userID);
			$data = DBUtils::doDBSelectMulti($sql, $params);
			if (Settings::GetPublic('CACHE_Allow_Tables to be Cached') ) {
				CACHE::add( Settings::GetProtected('DB_Table_UserAttributes') . '_for_userid_' . $userID, $data);
			}
			Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_6($data);
		}
		$this->ProcessAttributes($data);

		$this->clearAllCaches();
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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_6('@@doAddAttribute:' . $userID . '/'. $attribName . '/'. $attribValue);

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
		$this->clearAllCaches();
		return true;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userid
	 * @param string $attribName
	 * @return bool
	 */
	public function doRemoveAttribute(int $userid, string $attribName): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_6('@@doRemoveAttribute: ' . $userid. $attribName);

		$sql = 'DELETE FROM ' . Settings::GetProtected('DB_Table_UserAttributes')
				. ' WHERE userid = :userid AND attributename = :attrib_name'
		;
		$params = array(':userid' => ['val' => $userID, 'type' => \PDO::PARAM_STR],
			':attrib_name' => ['val' => $attribName, 'type' => \PDO::PARAM_STR]
		);

		$data = DBUtils::doDBDelete($sql, $params);
		$this->clearAllCaches();
		return ($data == 1);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userid
	 * @return bool
	 */
	public function doRemoveAllAttributesForUserID(int $userid): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_6('@@doRemoveAllAttributesForUserID: ' . $userid);

		$sql = 'DELETE FROM ' . Settings::GetProtected('DB_Table_UserAttributes')
				. ' WHERE userid = :userid'
		;
		$params = array(':userid' => ['val' => $userID, 'type' => \PDO::PARAM_STR]);

		$data = DBUtils::doDBDelete($sql, $params);
		$this->clearAllCaches();
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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_6('@@doInsertOrUpdateAttributeForUserID:'. $userid);

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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_6('@@getByUseridAndAttributeName:'  . $userid);

		if ( CACHE::exists( Settings::GetProtected('DB_Table_UserAttributes')   . '_userAndAttrib_'  . $userid .'_' . $attribName )) {
			$data = CACHE::exists(Settings::GetProtected('DB_Table_UserAttributes')   . '_userAndAttrib_'  . $userid .'_' . $attribName);
		}
		$sql = 'SELECT Id
						,UserId
						,AttributeName
						,AttributeValue
					FROM ' . Settings::GetProtected('DB_Table_UserAttributes')
				. ' WHERE  UserId = :userid'
				. ' AND AttributeName = :attribName'
		;
		$params = array(
			':userid' => ['val' => $userid, 'type' => \PDO::PARAM_STR],
			':attribName' => ['val' => $attribName, 'type' => \PDO::PARAM_STR]);

		$data = DBUtils::doDBSelectSingle($sql, $params);
		if (Settings::GetPublic('CACHE_Allow_Tables to be Cached') ) {
			CACHE::add( Settings::GetProtected('DB_Table_UserAttributes')   . '_userAndAttrib_'  . $userid .'_' . $attribName, $data);
		}

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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_6('@@updateByUseridAndAttributeName:' . $userid);

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
		$this->clearAllCaches();
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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice_6('@@insertUseridAndAttriubuteName: '. $userid);

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

		$this->clearAllCaches();
		return ($data > 0);
	}

}
