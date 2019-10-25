<?php

namespace php_base\Data;





use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;


//***********************************************************************************************
//***********************************************************************************************
Class UserRoleData  extends Data {
	public $action;
	public $payload;

	//-----------------------------------------------------------------------------------------------
	public function __construct($action ='', $payload = null){
		$this->action = $action;
		$this->payload = $payload;
		Settings::GetRunTimeObject('MessageLog')->addInfo('User: created model');
	}


//	public function doWork() : Response {
//		return Response::GenericError();
//	}
}
