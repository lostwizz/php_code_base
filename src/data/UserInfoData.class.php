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

	//-----------------------------------------------------------------------------------------------
	public function __construct($username) {

		$this->doReadFromDatabase($username);

	}

	protected function doReadFromDatabase($username) {

		$sql = 'SELECT  UserId
					,app
					,method
					,username
					,password
					,PrimaryRoleName
					,ip
					,last_logon_time
				FROM ' .  Settings::GetProtected( 'DB_Table_UserManager')
				. ' WHERE USERNAME = ?';

		$conn = myUtils::setup_PDO();
  		$stmt = $conn->prepare($sql);

  		$stmt->bindParam(1,  $username, \PDO::PARAM_STR);

  		$stmt->execute();


  		$data = $stmt->fetchAll();    //PDO::FETCH_ASSOC


dUMP::dump($data)  		;


	}

}