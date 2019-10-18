<?php

namespace php_base\View;


//***********************************************************************************************
//***********************************************************************************************
abstract class View{

    public $controller;

		//-----------------------------------------------------------------------------------------------
	public function __construct($controller) {
		$this->controller = $controller;
	}

	abstract public function doWork( $data =null);

}