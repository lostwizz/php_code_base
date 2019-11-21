<?php

/** * ********************************************************************************************
 * UserInfoData.class.php
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
 * @package ModelViewController - UserRoleAndPermissions\UserInfoData
 * @subpackage UserRoleAndPermissions
 * @since 0.3.0
 *
 * @example
 *
 * @see UserRoleAndPermissionsController.class.php
 * @see UserRoleAndPermissionsModel.class.php
 * @see UserRoleAndPermissionsView.class.php
 * @see UserAttributeData.class.php
 * @see UserPermissionData.class.php
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

/** * **********************************************************************************************
 * reads the info on a user - the id, password and last time they logged in and any other basic data
 */
class UserInfoData extends data {

	public $UserInfo;

	/** -----------------------------------------------------------------------------------------------
	 *  constructor - initiate the read from the database
	 * @param type $username
	 */
	public function __construct($username = null) {
		if (!empty($username)) {
			self::doReadFromDatabaseByUserNameAndApp($username);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *  give the user id (assuming the database has be read
	 * @return int
	 */
	public function getUserID(): int {
		if (!empty($this->UserInfo) and ! empty($this->UserInfo['USERID'])) {
			return $this->UserInfo['USERID'];
		} else {
			return false;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * return the primary role from the user info table (assuming database has been read
	 *
	 * @return boolean
	 */
	public function getPrimaryRole() {
		if (!empty($this->UserInfo) and ! empty($this->UserInfo['PRIMARYROLENAME'])) {
			return $this->UserInfo['PRIMARYROLENAME'];
		} else {
			return false;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *  read from the database table
	 *
	 * @param type $username
	 * @throws \PDOException
	 * @throws \Exception
	 */
	public function doReadFromDatabaseByUserNameAndApp(string $username): bool {
		$sql = 'SELECT * '
				. ' FROM ' . Settings::GetProtected('DB_Table_UserManager')
				. ' WHERE username = :uname AND  app = :app ;'
				;

		$app = Settings::GetPublic('App Name');
		$username = strtolower($username);

		$params = array(':app' => ['val' => $app, 'type' => \PDO::PARAM_STR],
			':uname' => ['val' => $username, 'type' => \PDO::PARAM_STR]
		);

		$data = DBUtils::doDBSelectSingle($sql, $params);
		$this->UserInfo = $data;

		if ($data ==false){
			//Settings::GetRunTimeObject('MessageLog')->addNotice('user does not exist');
			return false;
		} else {
			//Settings::GetRunTimeObject('MessageLog')->addNotice('user was successfully read');
			return true;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userid
	 * @param string $newPW
	 */
	public static function doUpdatePassword(int $userid, string $newPW): bool {
		$sql = 'UPDATE ' . Settings::GetProtected('DB_Table_UserManager')
				. ' SET password = :password'
				. ' WHERE userid = :userid'
		;
		$userid = strtolower($userid);

		$params = array(':password' => ['val' => $newPW, 'type' => \PDO::PARAM_STR],
			':userid' => ['val' => $userid, 'type' => \PDO::PARAM_INT]
		);
		$data = DBUtils::doDBUpdateSingle($sql, $params);
		return ($data != null);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userid
	 * @param string $newTime
	 * @param string $newIP
	 */
	public static function doUpdateLastLoginAndIP(int $userid, string $newTime, string $newIP): bool {
		$sql = 'UPDATE ' . Settings::GetProtected('DB_Table_UserManager')
				. ' SET ip = :ip'
				. ' , last_logon_time = :last_logon'
				. '	WHERE userid = :userid'
		;

		$now = date('DD-MMM-YY g:i:s');

		Settings::GetRunTimeObject('MessageLog')->addInfo('DT=' . $now);

		$params = array(':ip' => ['val' => $newIP, 'type' => \PDO::PARAM_STR],
			':last_logon' => ['val' => $now, 'type' => \PDO::PARAM_STR],
			':userid' => ['val' => $userid, 'type' => \PDO::PARAM_INT]
		);
		$data = DBUtils::doDBUpdateSingle($sql, $params);
		return ($data ==1);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $username
	 * @param type $password
	 * @param type $email
	 * @param type $primaryRole
	 * @return int
	 */
	public static function doInsertNewAccount($username, $password, $email, $primaryRole = null): int {
		$sql = 'INSERT INTO ' . Settings::GetProtected('DB_Table_UserManager')
				. ' ( app, method, username, password, primaryRoleName)'
				. ' VALUES '
				. ' ( :app, :method, :username, :password, :primary_role_name )'
		;

		$app = Settings::GetPublic('App Name');
		$username = strtolower($username);

		$params = array(':app' => ['val' => $app, 'type' => \PDO::PARAM_STR],
			':method' => ['val' => 'DB_Table', 'type' => \PDO::PARAM_STR],
			':username' => ['val' => $username, 'type' => \PDO::PARAM_STR],
			':password' => ['val' => $password, 'type' => \PDO::PARAM_STR],
			':primary_role_name' => ['val' => $primaryRole, 'type' => \PDO::PARAM_STR]
		);

		$data = DBUtils::doDBInsertReturnID($sql, $params);
		return $data;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $username
	 * @return bool
	 */
	public static function doDeleteAccountByUserNameAndApp(string $username): bool {
		$sql = 'DELETE FROM ' . Settings::GetProtected('DB_Table_UserManager')
				. ' WHERE username = :username AND app = :app'
		;
		$app =  Settings::GetPublic('App Name');
		$params = array(':app' => ['val' => $app, 'type' => \PDO::PARAM_STR],
			':username' => ['val' => $username, 'type' => \PDO::PARAM_STR]
		);
		$data = DBUtils::doDBDelete($sql, $params);
		return ($data == 1);
	}

}
