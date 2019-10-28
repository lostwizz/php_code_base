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
use \php_base\Utils\Response as Response;


//***********************************************************************************************
//***********************************************************************************************
class HeaderController extends Controller {

	public $process;
	public $task;


	public $action;
	public $payload;

	//-----------------------------------------------------------------------------------------------
	public function __construct($action='', $payload = null) {
		//$this->model = new \php_base\model\HeaderModel($this);
		//$this->data = new \php_base\data\HeaderData($this);
		$this->view = new \php_base\view\HeaderView($this);

		$this->payload = $payload;
	}

	//-----------------------------------------------------------------------------------------------
	public function setProcessAndTask( $process, $task){
		$this->process = $process;
		$this->task = $task;
	}

	//-----------------------------------------------------------------------------------------------
	public function doWork() : Response {
		return $this->view->doWork(  $this  );
	}

	//-----------------------------------------------------------------------------------------------
	public static function controllerRequiredVars(){
		return [];
	}
}