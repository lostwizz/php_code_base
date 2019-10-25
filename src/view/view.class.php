<?php

namespace php_base\View;

use \php_base\Utils\Response as Response;

//***********************************************************************************************
//***********************************************************************************************
abstract class View{

    public $controller;

		//-----------------------------------------------------------------------------------------------
	public function __construct($controller) {
		$this->controller = $controller;
	}

	abstract public function doWork( $parent = null) : Response ;

}