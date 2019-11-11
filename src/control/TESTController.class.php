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

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\model\UserRoleAndPermissionsModel as UserRoleAndPermissionsModel;

//***********************************************************************************************
//***********************************************************************************************
class TESTController extends Controller {

	public $model;
	public $view;
	public $data;
	public $process;
	public $task;
	public $action;
	public $payload;

	//-----------------------------------------------------------------------------------------------
	public function __construct($action = '', $payload = null) {
		//$this->model = new \php_base\model\HeaderModel($this);
		//$this->data = new \php_base\data\HeaderData($this);
		//$this->view = new \php_base\view\HeaderView($this);

		$this->payload = $payload;
	}

	//-----------------------------------------------------------------------------------------------
	public function setProcessAndTask($process, $task) {
		$this->process = $process;
		$this->task = $task;
	}

	//-----------------------------------------------------------------------------------------------
	public function doWork(): Response {

		Dump::dump('!!!!!!!!!!!!! at TestController');

		//$perms=Settings::GetRunTime('userPermissions');
		//$perms= UserRoleAndPermissionsModel
//Dump::dump(Settings::GetRunTime('userPermissions'));

		$perms = Settings::GetRunTime('userPermissions');
//Dump::dumpLong( $perms);
		//$p1 = \php_base\model\UserRoleAndPermissionsModel::WRITE_RIGHT;
		$p1 = UserRoleAndPermissionsModel::WRITE_RIGHT;
//Dump::dump($p1);
		//$process = 'test';
		//$task = 'testRead';
		//$action = \php_base\model\UserRoleAndPermissionsModel::WRITE_RIGHT;
		$action = UserRoleAndPermissionsModel::WRITE_RIGHT;

//::WILDCARD_RIGHT
		$x = $perms->isAllowed($p1, $this->process, $this->task, $action);
//Dump::dump($x);


		$x = $perms->isAllowed(UserRoleAndPermissionsModel::DBA_RIGHT, $this->process, $this->task, $action);
//Dump::dump($x);

		$x = $perms->isAllowed(UserRoleAndPermissionsModel::READ_RIGHT, $this->process, $this->task, 'actionfred');
//Dump::dump($x);

		$x = Settings::GetRunTime('userPermissions')->isAllowed(UserRoleAndPermissionsModel::READ_RIGHT, '*', 'READ_OLD_PASSWORD', 'CHANGE_PASSWORD', 'PASSWORD'
		);

		$x = Settings::GetRunTime('userPermissions')->isAllowed(UserRoleAndPermissionsModel::READ_RIGHT, 'FRED', '*', 'CHANGE_PASSWORD', '*'
		);
		$x = Settings::GetRunTime('userPermissions')->isAllowed(UserRoleAndPermissionsModel::DBA_RIGHT, 'FRED', '*', 'CHANGE_PASSWORD', '*'
		);

		$x = Settings::GetRunTime('userPermissions')->hasRole('Clerk');

		$x = Settings::GetRunTime('userPermissions')->hasRole('DBA');
		$x = Settings::GetRunTime('userPermissions')->hasRole('GOD');
		$x = Settings::GetRunTime('userPermissions')->hasRole('billybob');


		Settings::GetRunTimeObject('MessageLog')->addINFO('----------------');

		//$this->view->doWork(  $this  );
		//return new Resonse( 'ok', 0, true);
		return Response::NoError();
	}

//	//-----------------------------------------------------------------------------------------------
//	public static function controllerRequiredVars(){
//		return [];
//	}
}
