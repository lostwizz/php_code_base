<?php
//**********************************************************************************************
//* UserRoleData.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-08-30 11:58:13 -0700 (Fri, 30 Aug 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 30-Aug-19 M.Merrett - Created
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
Class UserRoleData  extends Data {
//	public $action;
//	public $payload;

	public $RoleIDData =[];    // array with the keys begin the name and the values being the roleID #
										//		- only needed this way because of the RolePermissions needing the the list of ids
	public $RoleIDnames =[];	// array with the keys being the roleID # and the values being the name

	//-----------------------------------------------------------------------------------------------
	public function __construct($ArrayOfNames){

		$this->doReadFromDatabase($ArrayOfNames);
//dUMP::dump($this->RoleIDData);
	}


	//-----------------------------------------------------------------------------------------------
			//[ROLEID] => 3
            //[NAME] => ViewOnly
	public function ProcessRoleIDs($data){
		foreach( $data as $record) {
			if ( !empty($record['NAME']) and !empty($record['ROLEID']) ) {
				$this->RoleIDData[ $record['NAME']] = $record['ROLEID'];
				$this->RoleIDnames[$record['ROLEID']] = $record['NAME'];
			}
		}
	}

	//-----------------------------------------------------------------------------------------------
	protected function doReadFromDatabase($ArrayOfNames) {
		$names = "'" . implode( "', '", $ArrayOfNames) ."'";
		try {
			$sql = 'SELECT RoleId
						,Name
					FROM ' .  Settings::GetProtected( 'DB_Table_RoleManager')
					. ' WHERE  Name in ('
					. $names
					. ')';

			$params = null; ///array(Settings::GetPublic( 'RoleId') );
			$data = myDBUtils::doDBSelectMulti($sql, $params);

	  		$this->ProcessRoleIDs($data);

		} catch (\PDOException $e)				{
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		} catch (\Exception $e){
			throw new \Exception($e->getMessage(), (int)$e->getCode());
		}
	}

}