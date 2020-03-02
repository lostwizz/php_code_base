<?php

//**********************************************************************************************
//* TESTController.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-08-30 11:58:13 -0700 (Fri, 30 Aug 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 30-Aug-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************

namespace php_base\Control;

use \php_base\Control;
use \php_base\data;
use \php_base\data\UserData;
use \php_base\model\Permissions as Permissions;
use \php_base\model\UserRoleAndPermissionsModel as UserRoleAndPermissionsModel;
use \php_base\Resolver;
use \php_base\Utils\DatabaseHandlers\Field as Field;
use \php_base\Utils\DatabaseHandlers\Table as Table;
use \php_base\Utils\TableFun as TableFun;

use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Utils as Utils;
use \php_base\Utils\HTML\HTML as HTML;
use \php_base\Utils\Response as Response;
use \php_base\Utils\Settings as Settings;
use \php_code\utils\Cache as Cache;

//***********************************************************************************************
//***********************************************************************************************
class TESTController extends \php_base\Control\Controller {

	public $model;
	public $view;
	public $data;
	public $process;
	public $task;
	public $action;
	public $payload;

	//-----------------------------------------------------------------------------------------------
	public function __construct(string $process, string $task, string $action = '', $payload = null) {
		//$this->model = new \php_base\model\HeaderModel($this);
		//$this->data = new \php_base\data\HeaderData($this);
		//$this->view = new \php_base\view\HeaderView($this);
		$this->process = $process;
		$this->task = $task;
		$this->action= $action;

		$this->payload = $payload;
	}

//	//-----------------------------------------------------------------------------------------------
//	public function setProcessAndTask($process, $task) {
//		$this->process = $process;
//		$this->task = $task;
//	}

	//-----------------------------------------------------------------------------------------------
	public function doWork(): Response {

		Dump::dump('!!!!!!!!!!!!! at TestController', 'now at', array('Show BackTrace Num Lines' => 5,'Beautify_BackgroundColor' => '#FFAA55') );


		dump::Dump('array("1"=>4)',null,array('Show BackTrace Num Lines' => 5,'Beautify_BackgroundColor' => '#FFAA55') );

		//$perms=Settings::GetRunTime('userPermissions');
		//$perms= UserRoleAndPermissionsModel
//Dump::dump(Settings::GetRunTime('userPermissions'));

		$perms = Settings::GetRunTime('userPermissionsController');

//Dump::dumpLong( $perms);
		//$p1 = \php_base\model\UserRoleAndPermissionsModel::WRITE_RIGHT;
		$p1 = Permissions::WRITE_RIGHT;
//Dump::dump($p1);
		//$process = 'test';
		//$task = 'testRead';
		//$action = \php_base\model\UserRoleAndPermissionsModel::WRITE_RIGHT;
		$action = Permissions::WRITE_RIGHT;

//::WILDCARD_RIGHT
		$x = $perms->isAllowed($p1, $this->process, $this->task, $action);
//Dump::dump($x);


		$x = $perms->isAllowed(Permissions::DBA_RIGHT, $this->process, $this->task, $action);
//Dump::dump($x);

		$x = $perms->isAllowed(Permissions::READ_RIGHT, $this->process, $this->task, 'actionfred');
//Dump::dump($x);

		$x = Settings::GetRunTime('userPermissionsController')->isAllowed(Permissions::READ_RIGHT, '*', 'READ_OLD_PASSWORD', 'CHANGE_PASSWORD', 'PASSWORD'
		);

		$x = Settings::GetRunTime('userPermissionsController')->isAllowed(Permissions::READ_RIGHT, 'FRED', '*', 'CHANGE_PASSWORD', '*'
		);
		$x = Settings::GetRunTime('userPermissionsController')->isAllowed(Permissions::DBA_RIGHT, 'FRED', '*', 'CHANGE_PASSWORD', '*'
		);

		$x = Settings::GetRunTime('userPermissionsController')->hasRole('Clerk');

		$x = Settings::GetRunTime('userPermissionsController')->hasRole('DBA');
		$x = Settings::GetRunTime('userPermissionsController')->hasRole('GOD');
		$x = Settings::GetRunTime('userPermissionsController')->hasRole('billybob');


		//Settings::GetRunTimeObject('MessageLog')->addINFO('----------------');

		\php_base\Utils\Cache::add( 'test1', array(1=>2, 3=>4), 10);
//dump::dump( $_SESSION);

$x =\php_base\Utils\Cache::pull('test1');


$x =\php_base\Utils\Cache::pull('test1');
//dump::dump($x);

////dump::dump( $_SESSION);
//
//
//dump::dumpLong(UserData::$Table->fields['userid']);
//UserData::$Table->fields['userid']->giveBinding('x');
//UserData::$Table->fields['username']->giveBinding('c');


//		$flds[0]->giveBinding();
//		$flds[1]->giveBinding();
//		$flds[2]->giveBinding();
//		$flds[3]->giveBinding();




		//$this->demoTableFun();

///Dump::dumpClasses();
//$x = new TableFun();
//$x->demoTableFun();


		//$this->view->doWork(  $this  );
		//return new Resonse( 'ok', 0, true);
		return Response::NoError();
	}

//	//-----------------------------------------------------------------------------------------------
//	public static function controllerRequiredVars(){
//		return [];
//	}

}
