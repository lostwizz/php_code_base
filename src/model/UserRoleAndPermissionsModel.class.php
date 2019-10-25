<?php

namespace php_base\Model;


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;




//***********************************************************************************************
//***********************************************************************************************
Class UserRoleAndPermissionsModel extends Model{

	const GOD_RIGHT = 'GOD';
	const DBA_RIGHT ='DBA';
	const WRITE_RIGHT = 'Write';
	const READ_RIGHT ='Read';

	const WILDCARD_RIGHT = '*';

	const NO_RIGHT = '__NO__RIGHT__';
	///////////const NOBODY = '__NO__BODY__';

	public $action;
	public $payload;


	//-----------------------------------------------------------------------------------------------
	public function __construct($action ='', $payload = null){

	}


//	//-----------------------------------------------------------------------------------------------
//	public function modelSetUser($uname) {
//		if ( !empty($uname)){
//			$this->username = $uname;
//		}
//
//
//	}


	public function isAllowed( $permission= self::NO_RIGHT, $process=self::NO_RIGHT, $task=self::NO_RIGHT, $action=null){
		if (empty($permission) or $permission== self::NO_RIGHT){
			return false;
		}
		if ( empty($process) or $process==self::NO_RIGHT) {
			return false;
		}
		if ( empty($task) or $task==self::NO_RIGHT) {
			return false;
		}



	}


}