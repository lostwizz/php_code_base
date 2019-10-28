<?php
//**********************************************************************************************
//* UserAttributeData.class
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-11 16:08:56 -0700 (Wed, 11 Sep 2019) $
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
class UserAttributeData  extends data{

	public $UserAttributes;
	public $roleNames =[];

	//-----------------------------------------------------------------------------------------------
	public function __construct($userID) {

		$this->doReadFromDatabase($userID);
//Dump::dump($this->UserAttributes);
//Dump::dump($this->roleNames);
	}

	//-----------------------------------------------------------------------------------------------
	public function getArrayOfRoleNames() {
		return $this->roleNames;
	}

	//-----------------------------------------------------------------------------------------------
	public function AddPrimaryRole($roleName){
		if ( !in_array( $roleName, $this->roleNames ) and !empty($roleName)) {
			$this->roleNames[] = $roleName;
		}
	}

	//-----------------------------------------------------------------------------------------------
	public function ProcessAttributes( $data) {

//Dump::dump(	$this->roleNames);
		foreach ($data as $record){
			if (($record['ATTRIBUTENAME'] =='PrimaryRole' )
			 or ($record['ATTRIBUTENAME'] =='SecondaryRole' )
			 or ($record['ATTRIBUTENAME'] =='Role' )) {

			 	if (!empty($record['ATTRIBUTEVALUE'])
			 	and !in_array($record['ATTRIBUTEVALUE'] , $this->roleNames)){

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
	protected function doReadFromDatabase($userID) {
		try {
			$sql = 'SELECT Id
						,UserId
						,AttributeName
						,AttributeValue
					FROM ' .  Settings::GetProtected( 'DB_Table_UserAttributes')
					. ' WHERE  UserId = ?';

			$conn = myUtils::setup_PDO();
	  		$stmt = $conn->prepare($sql);

			$app = Settings::GetPublic( 'UserId');
	  		$stmt->bindParam(1, $userID, \PDO::PARAM_INT);

	  		$stmt->execute();
	  		$data = $stmt->fetchAll();

			$this->ProcessAttributes( $data);

		} catch (\PDOException $e)				{
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		} catch (\Exception $e){
			throw new \Exception($e->getMessage(), (int)$e->getCode());
		}
	}


}