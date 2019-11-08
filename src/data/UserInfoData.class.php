<?php
//**********************************************************************************************
//* UserInfoData.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-11 16:08:38 -0700 (Wed, 11 Sep 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 11-Sep-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************


namespace php_base\data;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

use \php_base\Utils\myUtils as myUtils;
use \php_base\Utils\myDBUtils as myDBUtils;



//***********************************************************************************************
//***********************************************************************************************
class UserInfoData extends data {


	public $UserInfo;

	//-----------------------------------------------------------------------------------------------
	public function __construct($username) {
		$this->doReadFromDatabase($username);
//dump::dump($this->UserInfo);
	}

	//-----------------------------------------------------------------------------------------------
	public function getUserID() : int {
		if ( !empty( $this->UserInfo) and !empty( $this->UserInfo['USERID'])) {
			return  $this->UserInfo['USERID'];
		} else {
			return false;
		}
	}

	//-----------------------------------------------------------------------------------------------
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
	protected function doReadFromDatabase($username) {
		try {
			$sql = 'SELECT * '
					. ' FROM ' .  Settings::GetProtected( 'DB_Table_UserManager')
					. ' WHERE username = :uname AND  app = :app ;';
					//. ' WHERE  username = :uname ;';
					//. ' WHERE  app = :app ';

			$app = Settings::GetPublic( 'App Name');
	  		$username = strtolower($username);

			$params = array(  ':app' => $app,
									':uname' => $username
				 );

if (false ){
			$conn = myDBUtils::setupPDO();
	  		$stmt = $conn->prepare($sql);

	  		$stmt->bindParam(1, $app, \PDO::PARAM_STR);

	  		$stmt->bindParam(2, $username, \PDO::PARAM_STR);
	  		$stmt->execute();
	  		$data = $stmt->fetchAll();

			if (count($data) ==1 ) {
				$this->UserInfo =  $data[0];
			} else {
				throw new \Exception('Bad Data Rows Returned', -4);
			}

} else {


			$data = myDBUtils::doDBSelectMulti($sql, $params);
			dump::dump( $data);
			$this->UserInfo =  $data[0];
			die;
}
		} catch (\PDOException $e)				{
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		} catch (\Exception $e)				{
			throw new \Exception($e->getMessage(), (int)$e->getCode());
		}
	}

}