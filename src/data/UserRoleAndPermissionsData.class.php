<?php

namespace php_base\Data;


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

//***********************************************************************************************
//***********************************************************************************************
Class UserRoleAndPermissionsData extends Data{

	public $action;
	public $payload;

	//-----------------------------------------------------------------------------------------------
	public function __construct($action ='', $payload = null){
		$this->action = $action;
		$this->payload = $payload;
		Settings::GetRunTimeObject('MessageLog')->addInfo('User: created model');
	}




	//-----------------------------------------------------------------------------------------------




	//-----------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------
	//////////////////////////////////////////////////////////////////////////////////////////
	// setup pdo connection to the database
	//////////////////////////////////////////////////////////////////////////////////////////
	function setup_PDO(){
		if ( ! extension_loaded(Settings::GetProtected('database_extension_needed'))) {
			throw new Exception ('NOT loaded');
		}
		if ( empty(Settings::GetProtected( 'DB_Username'))) {
			throw new Exception('Missing Config Data from Settings- DB_Username');
		}
		if ( empty(Settings::GetProtected( 'DB_Password'))) {
			throw new Exception('Missing Config Data from Settings- DB_Password');
		}

		$dsn = Settings::GetProtected( 'DB_DSN');
		$options= Settings::GetProtected( 'DB_DSN_OPTIONS');
		try {
			$conn = new \PDO($dsn,
							Settings::GetProtected('DB_Username'),
							Settings::GetProtected('DB_Password'),
							$options
							);
		} catch (\PDOException $e)				{
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
		return $conn;
	}

}