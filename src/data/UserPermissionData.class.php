<?php
//**********************************************************************************************
//* UserPermissionData.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-11 16:07:53 -0700 (Wed, 11 Sep 2019) $
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

//***********************************************************************************************
//***********************************************************************************************
class UserPermissionData {

	public $permissionList;

	//-----------------------------------------------------------------------------------------------
	public function __construct($listOfRoleIDs) {
		$this->doReadFromDatabase($listOfRoleIDs);

//dump::dump($this->permissionList);
	}

	//-----------------------------------------------------------------------------------------------
	public function ProcessAttributes( $data){

		$this->permissionList = $data;
	}

	//-----------------------------------------------------------------------------------------------
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

			$conn = myUtils::setup_PDO();
	  		$stmt = $conn->prepare($sql);

			$app = Settings::GetPublic( 'UserId');
	  		$stmt->bindParam(1, $userID, \PDO::PARAM_INT);

	  		$stmt->execute();
	  		$data = $stmt->fetchAll();
//Dump::dump($sql);
//Dump::dump($data);

			$this->ProcessAttributes( $data);

		} catch (\PDOException $e)				{
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		} catch (\Exception $e){
			throw new \Exception($e->getMessage(), (int)$e->getCode());
		}
	}


}