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
 * 		$now = date('d-M-Y g:i:s');
 * 		$dt = new \DateTime( $now);
 *  	dump::dump($dt);
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
use \php_base\Utils\SubSystemMessage as SubSystemMessage;


/** * **********************************************************************************************
 * reads the info on a user - the id, password and last time they logged in and any other basic data
 */
class UserInfoData extends data {

	public $controller;

	public $UserInfo;
	public $Table;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 *  constructor - initiate the read from the database
	 * @param type $username
	 */
	public function __construct($controller, $username = null) {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@constructor: ' . $username);

		//Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addTODO('need somesort of verification when adding a column to the DB doesnt show here -instead of errors');

		$this->controller = $controller;

		$this->defineTable();

		if (!empty($username)) {
			$this->doReadFromDatabaseByUserNameAndApp($username);
			Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice( $this->UserInfo);
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
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->Suspend();

		$this->Table = new Table(Settings::GetProtected('DB_Table_UserManager'),
				['className' => __NAMESPACE__ . '\UserInfoData',
			'isAdding' => true,
			'isEditing' => true,
			'isDeleting' => true,
			'isSpecial' => true
		]);

		$this->Table->setPrimaryKey('UserId',
				['prettyName' => 'User Id',
					'isEditable' => false,
					'isShowable' => true,
		]);

		$this->Table->addFieldInt('UserId',
				['prettyName' => 'User Id',
					'text-align' => 'right',
					'isEditable' => false,
					'size' => 12
		]);

		$this->Table->addFieldText('app',
				['prettyName' => 'App',
					'isPassword' => false,
					'size' => 50,
					'maxlength' => 50,
					'subType' => Field::SUBTYPE_SELECTLIST,
					'selectFrom' => ['method' => 'getUserInfoDataForSelect',
									'class'=> 'UserInfoData',
									'id' => '!DISTINCT!',
									'data' => 'app'		]

		]);
		$this->Table->addFieldText('method',
				['prettyName' => 'Authentication Method',
					'size' => 10,
					'maxlength' => 10,
					'subType' => Field::SUBTYPE_SELECTLIST,
					'selectFrom' => [ 'method' => 'getUserInfoDataForSelect', //['LDAP'=>'LDAP','DB_Table'=>'DB_Table','HARDCoded' => 'HARDCoded' ]
									'class' => 'UserInfoData',
									'id' => '!DISTINCT!',
									'data' => 'method']
		]);
		$this->Table->addFieldText('username',
				[
					//'subType' =>  Field::SUBTYPE_TEXTAREA,
					'prettyName' => 'User Name',
					'isShowable' => true,
					'isEditable' => true,
					'size' => 35,
					'height' => true
		]);
		$this->Table->addFieldText('PrimaryRoleName',
				['prettyName' => 'Primary Role',
					'selectFrom' => ['method' => 'getRolesForSelect',
									'class' => 'UserRoleData',
									'id' => 'roleid',
									'data' => 'name']
					]);

		$this->Table->addFieldText('password',
				['prettyName' => 'Password',
					'isEditable' => false,
					'size' => 80,
					'isShowable' => false
		]);

		$this->Table->addFieldText('ip',
				['prettyName' => 'IP Address',
					'isShowable' => true,
					'isEditable' => false
		]);

		$this->Table->addFieldDateTime('last_logon_time',
				['prettyName' => 'Time/Date of Last Login',
					'isEditable' => false,
					'isShowable' => true
		]);

		$this->Table->addFieldText('session_id',
				['prettyName' => 'Session Id',
					//'size' => 50,
					'maxlength' => 50,
					'isEditable' => false,
		]);


		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->Resume();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	public function clearAllCaches() :void {
		CACHE::deleteForPrefix( Settings::GetProtected('DB_Table_UserManager') . '_onlyOneUSER' . '_' );
		CACHE::delete(Settings::GetProtected('DB_Table_UserManager') . '_SelectOnMethod');
		CACHE::delete(Settings::GetProtected('DB_Table_UserManager') . '_SelectOnApp');
		CACHE::delete(Settings::GetProtected('DB_Table_UserManager') .'_ReadAll');
		CACHE::delete(Settings::GetProtected('DB_Table_UserManager') . '_onlyOneUSER_ID' . '_');
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array
	 */
	public function readAllData(): array {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@readAllData');

		if ( CACHE::exists( Settings::GetProtected('DB_Table_UserManager') .'_ReadAll' )){
			$data = CACHE::pull( Settings::GetProtected('DB_Table_UserManager') .'_ReadAll' );
		} else  {
			$sql = 'SELECT * '
					. ' FROM ' . Settings::GetProtected('DB_Table_UserManager');
			$data = DBUtils::doDBSelectMulti($sql);

			if (Settings::GetPublic('CACHE_Allow_Tables to be Cached')) {
				CACHE::add(Settings::GetProtected('DB_Table_UserManager') .'_ReadAll', $data);
			}
		}
		return $data;
	}


	/** -----------------------------------------------------------------------------------------------
	 *  give the user id (assuming the database has be read
	 * @return int
	 */
	public function getUserID(): ?int {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@getUserID');

		if (!empty($this->UserInfo) and ! empty($this->UserInfo['USERID'])) {
			return $this->UserInfo['USERID'];
		} else {
			return null;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * return the primary role from the user info table (assuming database has been read
	 *
	 * @return boolean
	 */
	public function getPrimaryRole() {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@getPrimaryRole');
		if (!empty($this->UserInfo) and ! empty($this->UserInfo['PRIMARYROLENAME'])) {
			return $this->UserInfo['PRIMARYROLENAME'];
		} else {
			return false;
		}
	}

	/** -----------------------------------------------------------------------------------------------*/
	public function getUserInfoDataForSelect( $id, $data,  $idValue = null) {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@readAllData');

		if ( CACHE::exists( Settings::GetProtected('DB_Table_UserManager') .'_ReadAll' )){
			$data = CACHE::pull( Settings::GetProtected('DB_Table_UserManager') .'_ReadAll' );
		} else  {

			if ($id =='!DISTINCT!'){
				$sql = 'SELECT DISTINCT '. $data
					. ' FROM ' . Settings::GetProtected('DB_Table_UserManager');
			}

			$sql = 'SELECT  ' . $id . ', ' . $data
					. ' FROM ' . Settings::GetProtected('DB_Table_UserManager');

			if ( ! is_null( $idValue)){
				$sql .= ' WHERE ' . $id . ' = :data';
				$param = array(':data'=> $idValue );
			} else {
				$param = null;
			}

			$data = DBUtils::doDBSelectMulti($sql, $param);



			if (Settings::GetPublic('CACHE_Allow_Tables to be Cached')) {
				CACHE::add(Settings::GetProtected('DB_Table_UserManager') .'_ReadAll', $data);
			}
		}
		if ( ! is_null( $idValue)){
			return $data[0];
		}
		return $data;

	}




	/** -----------------------------------------------------------------------------------------------
	 *  read from the database table
	 *
	 * @param type $username
	 * @throws \PDOException
	 * @throws \Exception
	 */
	public function doReadFromDatabaseByUserNameAndApp(string $username): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doReadFromDatabaseByUserNameAndApp: '. $username);

		if (CACHE::exists(Settings::GetProtected('DB_Table_UserManager') . '_onlyOneUSER' . '_'. $username)) {

			$data = CACHE::pull(Settings::GetProtected('DB_Table_UserManager') . '_onlyOneUSER' . '_'. $username);
		} else {

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
		}
		$this->UserInfo = $data;

		if ($data == false) {
			//Settings::GetRunTimeObject('MessageLog')->addNotice('user does not exist');
			return false;
		} else {
			//Settings::GetRunTimeObject('MessageLog')->addNotice('user was successfully read');
			if (Settings::GetPublic('CACHE_Allow_Tables to be Cached')) {
				CACHE::add(Settings::GetProtected('DB_Table_UserManager') . '_onlyOneUSER' . '_'. $username, $data);
			}
			return true;
		}
	}

//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * @param string $userid
//	 * @return bool
//	 */
//	public function doReadFromDatabaseByUserID(string $userid, bool $allUserIds = true): ?array {
//		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doReadFromDatabaseByUserNameAndApp: '. $userid);
//
//		//if (CACHE::exists(Settings::GetProtected('DB_Table_UserManager') . '_onlyOneUSER_ID' . '_'. $userid)) {
//		//	$data = CACHE::pull( Settings::GetProtected('DB_Table_UserManager') . '_onlyOneUSER_ID' . '_'. $userid);
//		//} else {
//
//			$sql = 'SELECT  userid, username '
//					. ' FROM ' . Settings::GetProtected('DB_Table_UserManager');
//			if ( ! $allUserIds){
//				$sql .= ' WHERE userid = :uid ;';
//			}
//			$sql .= ' ORDER BY username';
//
//			$params = array(
//				':uid' => ['val' => $userid, 'type' => \PDO::PARAM_STR]
//			);
//
//			if ( ! $allUserIds){
//				$data = DBUtils::doDBSelectSingle($sql, $params);
//			} else {
//				$rawData = DBUtils::doDBSelectMulti($sql);
//				$data = array();
//				foreach ($rawData as $key => $value) {
//					$data[$value['USERID']] = $value['USERNAME'];
//				}
//			}
//			Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addInfo_5($data);
//		//}
//
//
//		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addInfo_5($data);
//		if ($data == false) {
//			return null;
//		} else {
//		//	if (Settings::GetPublic('CACHE_Allow_Tables to be Cached')) {
//		//		CACHE::add(Settings::GetProtected('DB_Table_UserManager') . '_onlyOneUSER_ID' . '_'. $userid, $data);
//		//	}
//			return $data;
//		}
//	}

//
//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * @return array|null
//	 */
//	public function giveSelectOnMethod(): ?array {
//		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@giveSelectOnMethod');
//		if (CACHE::exists(Settings::GetProtected('DB_Table_UserManager') . '_SelectOnMethod' )) {
//			$data = CACHE::pull(Settings::GetProtected('DB_Table_UserManager') . '_SelectOnMethod');
//		} else {
//			$sql = 'SELECT DISTINCT method FROM ' . Settings::GetProtected('DB_Table_UserManager');
//
//			$rawData = DBUtils::doDBSelectMulti($sql);
//
//			$data = array();
//			foreach ($rawData as $key => $value) {
//				$data[$value['METHOD']] = $value['METHOD'];
//			}
//
//			if (Settings::GetPublic('CACHE_Allow_Tables to be Cached')) {
//				CACHE::add(Settings::GetProtected('DB_Table_UserManager') . '_SelectOnMethod', $data);
//			}
//		}
//		return $data;
//	}
//
//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * @return array|null
//	 */
//	public function giveSelectOnApp(): ?array {
//		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@giveSelectOnApp');
//
//		if (CACHE::exists(Settings::GetProtected('DB_Table_UserManager') . '_SelectOnApp'  )) {
//			$data = CACHE::pull(Settings::GetProtected('DB_Table_UserManager') . '_SelectOnApp');
//		} else {
//			$sql = 'SELECT DISTINCT app FROM ' . Settings::GetProtected('DB_Table_UserManager');
//
//			$rawData = DBUtils::doDBSelectMulti($sql);
//
//			$data = array();
//			foreach ($rawData as $key => $value) {
//				$data[$value['APP']] = $value['APP'];
//			}
//
//			if (Settings::GetPublic('CACHE_Allow_Tables to be Cached')) {
//				CACHE::add(Settings::GetProtected('DB_Table_UserManager') . '_SelectOnApp', $data);
//			}
//		}
//		return $data;
//	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userid
	 * @param string $newPW
	 */
	public function doUpdatePassword(int $userid, string $newPW): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doUpdatePassword');
		$sql = 'UPDATE ' . Settings::GetProtected('DB_Table_UserManager')
				. ' SET password = :password'
				. ' WHERE userid = :userid'
		;
		$userid = strtolower($userid);

		$params = array(':password' => ['val' => $newPW, 'type' => \PDO::PARAM_STR],
			':userid' => ['val' => $userid, 'type' => \PDO::PARAM_INT]
		);

		$data = DBUtils::doDBUpdateSingle($sql, $params);
		$this->clearAllCaches();
		return ($data != null);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $userid
	 * @param string $newTime
	 * @param string $newIP
	 */
	public function doUpdateLastLoginAndIP(int $userid, string $newTime, string $newIP, string $sessionId) : bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doUpdateLastLoginAndIP');
		$sql = 'UPDATE ' . Settings::GetProtected('DB_Table_UserManager')
				. ' SET ip = :ip'
				. ' , last_logon_time = :last_logon'
				. ' , session_id = :session_id'
				. '	WHERE userid = :userid'
		;

		$now = date('d-M-Y g:i:s');
		//Settings::GetRunTimeObject('MessageLog')->addInfo('DT=' . $now);
		$params = array(':ip' => ['val' => $newIP, 'type' => \PDO::PARAM_STR],
			':last_logon' => ['val' => $now, 'type' => \PDO::PARAM_STR],
			':userid' => ['val' => $userid, 'type' => \PDO::PARAM_INT],
			':session_id' =>['val' => $sessionId, 'type' => \PDO::PARAM_STR]
		);

		$data = DBUtils::doDBUpdateSingle($sql, $params);

		$this->clearAllCaches();
		return ($data == 1);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $username
	 * @param type $password
	 * @param type $email
	 * @param type $primaryRole
	 * @return int
	 */
	public function doInsertNewAccount($username, $password, $email, $primaryRole = null): int {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doInsertNewAccount');
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
		$this->clearAllCaches();
		return $data;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $username
	 * @return bool
	 */
	public function doDeleteAccountByUserNameAndApp(string $username): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doDeleteAccountByUserNameAndApp');
		$sql = 'DELETE FROM ' . Settings::GetProtected('DB_Table_UserManager')
				. ' WHERE username = :username AND app = :app'
		;
		$app = Settings::GetPublic('App Name');
		$params = array(':app' => ['val' => $app, 'type' => \PDO::PARAM_STR],
			':username' => ['val' => $username, 'type' => \PDO::PARAM_STR]
		);

		$data = DBUtils::doDBDelete($sql, $params);
		$this->clearAllCaches();
		return ($data == 1);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @return bool
	 */
	public function doUpdateRecord($data): bool {
		Settings::GetRuntimeObject( 'PERMISSION_DEBUGGING')->addNotice('@@doUpdateRecord');
		///////////$data = array_change_key_case($data, CASE_UPPER);
		dump::dump($data);
		echo 'password editable=', ($this->table->password->isEditable) ? 'y' : 'no';
		/*
		  $sql = 'UPDATE ' .  Settings::GetProtected('DB_Table_UserManager')
		  . ' SET app = :app,'
		  . ' method = :method,'
		  . ' username = :username,'
		  . ' PrimaryRoleName = :PrimaryRoleName,'
		  . ($this->table->password->isEditable) ? ' password = :password,' : ''
		  . ($this)' ip = :,'
		  . ' last_logon_time = :last_logon_time,'
		 */
		$this->clearAllCaches();
	}

}
