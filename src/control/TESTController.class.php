<?php


namespace php_base\Control;


use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

//***********************************************************************************************
//***********************************************************************************************
class TESTController extends Controller {
	//-----------------------------------------------------------------------------------------------
	public function __construct($payload = null) {
		//$this->model = new \php_base\model\HeaderModel($this);
		//$this->data = new \php_base\data\HeaderData($this);
		//$this->view = new \php_base\view\HeaderView($this);

		$this->payload = $payload;
	}

	//-----------------------------------------------------------------------------------------------
	public function doWork(){

Dump::dump('!!!!!!!!!!!!! at TestController');

		//$this->view->doWork(  $this  );
	}

	//-----------------------------------------------------------------------------------------------
	public static function controllerRequiredVars(){
		return [];
	}
}