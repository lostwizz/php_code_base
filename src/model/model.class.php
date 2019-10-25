<?php


namespace php_base\Model;


use \php_base\Utils\Response as Response;

//***********************************************************************************************
//***********************************************************************************************
class Model {
   	public $controller;


	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------
	public function __construct($controller) {
		$this->controller = $controller;
	}

	public function doWork() : Response {
		return Response::GenericError();
	}

}
