<?php

namespace php_base\Control;

use php_base\Model;
use php_base\Data;
use php_base\View;

use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;


//***********************************************************************************************
//***********************************************************************************************
abstract class Controller{
 	public $model;
	public $view;
	public $data;

	public $payload;


	//-----------------------------------------------------------------------------------------------
	abstract public function __construct($payload = null);


	//-----------------------------------------------------------------------------------------------
	abstract public static function controllerRequiredVars();

	//-----------------------------------------------------------------------------------------------
	public function doWork(){
		return false;
	}


}