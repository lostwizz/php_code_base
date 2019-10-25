<?php


namespace php_base\Data;



use \php_base\Utils\Response as Response;

//***********************************************************************************************
//***********************************************************************************************
class Data {
	public $action;
	public $payload;

	//-----------------------------------------------------------------------------------------------
	public function __construct($action ='', $payload = null){
		$this->action = $action;
		$this->payload = $payload;
	}

	public function doWork() : Response {
		return Response::GenericError();
	}
}
