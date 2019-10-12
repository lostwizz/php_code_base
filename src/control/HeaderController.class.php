<?php
//**********************************************************************************************
//* HeaderController.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-12 13:12:55 -0700 (Thu, 12 Sep 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 12-Sep-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************


namespace php_base\Control;


use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

//***********************************************************************************************
//***********************************************************************************************
class HeaderController extends Controller {

	//-----------------------------------------------------------------------------------------------
	public function __construct($payload = null) {
		//$this->model = new \whitehorse\MikesCommandAndControl2\model\HeaderModel($this);
		//$this->data = new \whitehorse\MikesCommandAndControl2\data\HeaderData($this);
		$this->view = new \php_base\view\HeaderView($this);

		$this->payload = $payload;
	}

	//-----------------------------------------------------------------------------------------------
	public function doWork(){
		$this->view->doWork();
	}

	//-----------------------------------------------------------------------------------------------
	public static function controllerRequiredVars(){
		return [];
	}
}