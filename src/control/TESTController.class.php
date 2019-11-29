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
use \php_base\data\UserInfoData;
use \php_base\model\Permissions as Permissions;
use \php_base\model\UserRoleAndPermissionsModel as UserRoleAndPermissionsModel;
use \php_base\Resolver;
use \php_base\Utils\DatabaseHandlers\Field as Field;
use \php_base\Utils\DatabaseHandlers\Table as Table;
use \php_base\Utils\Dump\Dump as Dump;
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

//dump::dump( $_SESSION);


//dump::dump(UserInfoData::$Table->fields['userid']);
//UserInfoData::$Table->fields['userid']->giveBinding('x');
//UserInfoData::$Table->fields['username']->giveBinding('c');


//		$flds[0]->giveBinding();
//		$flds[1]->giveBinding();
//		$flds[2]->giveBinding();
//		$flds[3]->giveBinding();




//		$this->demoTableFun();

		//$this->view->doWork(  $this  );
		//return new Resonse( 'ok', 0, true);
		return Response::NoError();
	}

//	//-----------------------------------------------------------------------------------------------
//	public static function controllerRequiredVars(){
//		return [];
//	}

	public function demoTableFun() {


		//
//
//		echo '<form method=post>';
//		dump::dump( self::$Table->giveHeaderRow(true, true));
//		echo '<table border=1>';
//		echo '<tr>';
//		echo self::$Table->giveHeaderRow(true, true);
//		echo '</tr>';
//		echo '</table>';
//		echo '</form>';

//dump::dump($this->payload);

		echo HTML::FormOpen('tableFun');
		echo HTML::Hidden(Resolver::REQUEST_PROCESS, 'test');

		$d = UserInfoData::$Table->readAllTableData();

		 $this->sortData($d);

		 $this->filterData($d);
		//dump::dumpLong($d);
		 $sortAr= $this->processPassedSort();
//dump::dumpLong($sortAr)	;
		 $filter = $this->processPassedFilter();
		echo UserInfoData::$Table->showTable( $d, $sortAr, $filter);
		echo HTML::FormClose();
	}

	public function processPassedSort() : ?array{
		$ar = array();
		$flds = UserInfoData::$Table->giveFields();


		if (!empty( $this->payload['sortAsc']) and  is_array($this->payload['sortAsc']) ) {
			foreach ($flds as $fld) {
				if ( !empty($this->payload['sortAsc'][$fld])) {
					$ar[$fld] = 'Asc';
				} else {
					$ar[$fld] =null;
				}
			}
		}
		if (!empty( $this->payload['sortDesc']) and  is_array($this->payload['sortDesc']) ) {
			foreach ($flds as $fld) {
				if ( !empty($this->payload['sortDesc'][$fld])) {
					$ar[$fld] = 'Desc';
				}
			}
		}
		return $ar;
	}

	public function processPassedFilter(): ?array {
		if ( !empty($this->payload['filter']) and is_array($this->payload['filter'])) {
			$filter = $this->payload['filter'];
		} else  {
			$filter = null;
		}
		return $filter;
	}

	public function filterData( array &$data) {
		if ( !empty($this->payload['filter']) and is_array($this->payload['filter'])) {
			foreach ($this->payload['filter'] as $fld => $value) {
				//dump::dump($value, $fld);
				if ( !empty($value )) {
					$this->filterOn($data, strtoupper($fld), $value);
				}
			}
		}
	}

	function startsWith ($string, $startString) {
		$len = strlen($startString);
		return (substr($string, 0, $len) === $startString);
	}


	public function filterOn( &$data, $fld, $filter) {
		foreach ($data as $key => $value) {
			if ( $this->startsWith ( $value[$fld] , $filter)) {
				unset( $data[$key]);
			}
		}
	}


	public $key='';


	public function sortData( array &$data){
		//figure out what to sort and in which direction

		if ( !empty( $this->payload['sortAsc']) ) {
			$this->key = strtoupper (array_keys( $this->payload['sortAsc'])[0]);
			uasort($data, function($a, $b) {
				return $a[ $this->key ] <=> $b[$this->key];}
				);
		}

		if ( !empty( $this->payload['sortDesc']) ) {
			$this->key = strtoupper(array_keys( $this->payload['sortDesc'])[0]);
			uasort($data, function($a, $b) {
				return $b[$this->key] <=> $a[$this->key];}
				);
		}

		return;
	}
}
