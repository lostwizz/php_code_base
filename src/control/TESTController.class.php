<?php


namespace php_base\Control;


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;



//***********************************************************************************************
//***********************************************************************************************
class TESTController extends Controller {

	public $model;
	public $view;
	public $data;

	public $action;
	public $payload;


	//-----------------------------------------------------------------------------------------------
	public function __construct($action ='', $payload = null) {
		//$this->model = new \php_base\model\HeaderModel($this);
		//$this->data = new \php_base\data\HeaderData($this);
		//$this->view = new \php_base\view\HeaderView($this);

		$this->payload = $payload;
	}

	//-----------------------------------------------------------------------------------------------
	public function doWork() : Response{

Dump::dump('!!!!!!!!!!!!! at TestController');

		//$perms=Settings::GetRunTime('userPermissions');
		//$perms= UserRoleAndPermissionsModel

//Dump::dump(Settings::GetRunTime('userPermissions'));

		$perms = Settings::GetRunTime('userPermissions');
//Dump::dumpLong( $perms);

		$p1 = \php_base\model\UserRoleAndPermissionsModel::WRITE_RIGHT;
//Dump::dump($p1);

		$process = 'test';
		$task = 'testRead';
		$action = \php_base\model\UserRoleAndPermissionsModel::WRITE_RIGHT;

		$x = $perms->isAllowed( $p1, $process, $task, $action);
Dump::dump($x);

		//$this->view->doWork(  $this  );
		//return new Resonse( 'ok', 0, true);
		return Response::NoError();
	}

//	//-----------------------------------------------------------------------------------------------
//	public static function controllerRequiredVars(){
//		return [];
//	}
}