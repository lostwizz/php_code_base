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
	public function __construct($username) {
		$this->doReadFromDatabase($username);
	}

	/** -----------------------------------------------------------------------------------------------
	 *  give the user id (assuming the database has be read
	 * @return int
	 */
	public function getUserID() : int {
		if ( !empty( $this->UserInfo) and !empty( $this->UserInfo['USERID'])) {
			return  $this->UserInfo['USERID'];
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
		if ( !empty( $this->UserInfo) and !empty( $this->UserInfo['PRIMARYROLENAME'])) {
			return  $this->UserInfo['PRIMARYROLENAME'];
		} else {
			return false;
		}
	}

	//-----------------------------------------------------------------------------------------------
	////						,app
	////						,method
	////						,username
	////						,password

	/* UserId
						,method
						,username
						,password
						,PrimaryRoleName
						,ip
						,last_logon_time
	 *
	 */



	/** -----------------------------------------------------------------------------------------------
	 *  read from the database table
	 *
	 * @param type $username
	 * @throws \PDOException
	 * @throws \Exception
	 */
	protected function doReadFromDatabase( string $username) :bool {
		try {
			$sql = 'SELECT * '
					. ' FROM ' . Settings::GetProtected( 'DB_Table_UserManager')
					. ' WHERE username = :uname AND  app = :app ;';
//dump::dump($sql);
			$app = Settings::GetPublic( 'App Name');
	  		$username = strtolower($username);

			$params = array(  ':app' => ['val' =>$app,'type'=>\PDO::PARAM_STR],
									':uname' => ['val'=> $username, 'type'=>\PDO::PARAM_STR]
				 );

			$data = DBUtils::doDBSelectSingle($sql, $params);
			$this->UserInfo =  $data;

		} catch (\PDOException $e)				{
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		} catch (\Exception $e)				{
			throw new \Exception($e->getMessage(), (int)$e->getCode());
		}
		return true;
	}

	/**
	 *
	 * @param int $userid
	 * @param string $newPW
	 */
	public function doUpdatePassword(int $userid, string $newPW ) : bool {
		$sql = 'UPDATE ' . Settings::GetProtected( 'DB_Table_UserManager')
				. ' SET password = :password'
				. ' WHERE userid = :userid'
				;
  		$userid = strtolower($userid);

		$params = array( ':password' => ['val' =>$newPW,'type'=>\PDO::PARAM_STR],
						':userid' => ['val'=> $userid, 'type'=>\PDO::PARAM_INT]
				 );
		$data = DBUtils::doDBUpdate($sql, $params);
dump::dump( $data);
		return ($data != null);
	}

	public function doUpdateLastLoginAndIP( int $userid, string $newTime, string $newIP) {

	}

}