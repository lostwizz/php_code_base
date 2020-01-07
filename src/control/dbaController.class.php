<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace php_base\control;


use \php_base\Resolver;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\Settings as Settings;

use \php_base\Utils\DatabaseHandlers\SimpleTableEditor as SimpleTableEditor;



/**
 * Description of dbaController
 *
 * @author merrem
 */
class dbaController extends Controller {

	public $model;
	public $view;
	public $data;

	public $process;
	public $task;
	public $action;
	public $payload;



	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 * basic form of the constructor
	 */
	public function __construct(string $passedAction = '', $passedPayload = null){
		$this->action = $passedAction;
		$this->payload = $passedPayload;
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}


	/** -----------------------------------------------------------------------------------------------
	 * this is the default method called to do anyhing (if another method is not used)
	 *
	 * @return Response
	 */
	public function doWork(): Response {
		return Response::NoError() ;
	}

	/** -----------------------------------------------------------------------------------------------
	 *  setup the process and task names so that it can be used for Permission queries
	 *
	 * @param type $process
	 * @param type $task
	 */
	public function setProcessAndTask($process =null, $task=null) {
		$this->process = $process;
		$this->task = $task;
	}

	public function one( $parent=null) {

//dump::dump(	AuthenticateController::isAuthenticated());
//dump::dumpLong( $this);

//dump::dumpLong($_SESSION);


	//	$ste = new SimpleTableEditor( 'php_base\data\UserInfoData');
	//	$DataUserInfo = new \php_base\data\UserInfoData();

//		dump::dumpLong( $DataUserInfo);
	//	$y = \php_base\data\UserInfoData::$Table;
	//	dump::dumpLong( $y);

		$x = \php_base\data\UserInfoData::$Table ;
	//	dump::dumpLong($x);

		$action =
		$editorSession = new SimpleTableEditor($x, 'DBA', 'one',$this->action, $this->payload);
		$result = $editorSession->runTableDisplayAndEdit( true );
//
//

/*





		return $result;


*/











		return Response::NoError();
	}

	public function two( $parent=null) {

		echo 'at dba two ';

		return Response::NoError();
	}

	public function three( $parent=null) {

		echo 'at dba three ';
		return Response::NoError();
	}
}
