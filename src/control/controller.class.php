<?php

namespace whitehorse\MikesCommandAndControl2\Control;

use whitehorse\MikesCommandAndControl2\Model;
use whitehorse\MikesCommandAndControl2\Data;
use whitehorse\MikesCommandAndControl2\View;

use \whitehorse\MikesCommandAndControl2\Settings\Settings as Settings;
use \whitehorse\MikesCommandAndControl2\Utils\Dump\Dump as Dump;


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