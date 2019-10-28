<?php


namespace php_base\data;


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

use \php_base\Utils\myUtils as myUtils;


//***********************************************************************************************
//***********************************************************************************************
Class UserRoleData  extends Data {
//	public $action;
//	public $payload;

	public $RoleIDData =[];

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
			}
		}
	}

	//-----------------------------------------------------------------------------------------------
	protected function doReadFromDatabase($ArrayOfNames) {
		$names = "'" . implode( "', '", $ArrayOfNames) ."'";

//dump::dump($names);

		try {
			$sql = 'SELECT RoleId
						,Name
					FROM ' .  Settings::GetProtected( 'DB_Table_RoleManager')
					. ' WHERE  Name in ('
					. $names
					. ')';

			$conn = myUtils::setup_PDO();
	  		$stmt = $conn->prepare($sql);

			$app = Settings::GetPublic( 'RoleId');
	  		$stmt->bindParam(1, $userID, \PDO::PARAM_INT);
	  		$stmt->execute();
	  		$data = $stmt->fetchAll();

	  		$this->ProcessRoleIDs($data);


		} catch (\PDOException $e)				{
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		} catch (\Exception $e){
			throw new \Exception($e->getMessage(), (int)$e->getCode());
		}
	}

}