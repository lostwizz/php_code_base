<?php

namespace whitehorse\MikesCommandAndControl2\View;


//***********************************************************************************************
//***********************************************************************************************
abstract class View{

    public $controller;

		//-----------------------------------------------------------------------------------------------
	public function __construct($controller) {
		$this->controller = $controller;
	}

	abstract public function doWork();

}