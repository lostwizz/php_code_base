<?php
//**********************************************************************************************
//* UserInfoData.class
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
	protected function doReadFromDatabase($username) {
		try {
			$sql = 'SELECT  UserId
						,app
						,method
						,username
						,password
						,PrimaryRoleName
						,ip
						,last_logon_time
					FROM ' .  Settings::GetProtected( 'DB_Table_UserManager')
					. ' WHERE  app =? AND username = ?';

			$conn = myUtils::setup_PDO();
	  		$stmt = $conn->prepare($sql);

			$app = Settings::GetPublic( 'App Name');
	  		$stmt->bindParam(1, $app, \PDO::PARAM_STR);

	  		$username = strtolower($username);
	  		$stmt->bindParam(2, $username, \PDO::PARAM_STR);
	  		$stmt->execute();
	  		$data = $stmt->fetchAll();

			if (count($data) ==1 ) {
				$this->UserInfo =  $data[0];
			} else {
				throw new \Exception('Bad Data Rows Returned', -4);
			}

		} catch (\PDOException $e)				{
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		} catch (\Exception $e)				{
			throw new \Exception($e->getMessage(), (int)$e->getCode());
		}
	}

}