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

use \php_base\Utils\HTML\HTML as HTML;

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
	public function __construct(string $passedProcess, string $passedTask, string $passedAction = '', $passedPayload = null){
		$this->process = $passedProcess;
		$this->task = $passedTask;
		$this->action = $passedAction;
		$this->payload = $passedPayload;
//dump::dumpLong($this);
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

//	/** -----------------------------------------------------------------------------------------------
//	 *  setup the process and task names so that it can be used for Permission queries
//	 *
//	 * @param type $process
//	 * @param type $task
//	 */
//	public function setProcessAndTask($process =null, $task=null) {
//		$this->process = $process;
//		$this->task = $task;
//	}


//	public function Route_UserInfoData($parent=null) : Response{
//		Settings::GetRuntimeObject('DISPATCHqueue' )->enqueue('UserRoleAndPermissionsController.prepareToEditUserInfoData');
//
//		Settings::GetRuntimeObject('DISPATCHqueue' )->enqueue('DBAcontroller.Edit_UserInfoData');
//
////dump::dumpLong(Settings::GetRuntimeObject('DISPATCHqueue' )	);
//		return Response::NoError();
//	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @return type
	 */
	public function Edit_UserInfoData( $parent=null) {

		if( ! Settings::GetRunTime('userPermissionsController')->hasRole('DBA')) {
			return Response::PermissionsError('Required DBA - Edit UserInfoData');
		}

		$t = new \php_base\data\UserInfoData();

		//$x = \php_base\data\UserInfoData::$Table ;
		$x = $t::$Table;

//dump::dumpLong($this);

		//Settings::GetRuntimeObject('POSTqueue' )->enqueue('FooterController.doWork.two');

		$this->payload['Table'] = 'UserInfoData';

		//Settings::GetRuntimeObject('DISPATCHqueue' )->enqueue ('SimpleTableEditor');

//dump::dumpLong(Settings::GetRuntimeObject('DISPATCHqueue' )	);
//dump::dumpLong(Settings::GetRuntimeObject('PREqueue' )	);
//dump::dumpLong(Settings::GetRuntimeObject('POSTqueue' )	);

		$editorSession = new SimpleTableEditor($x, $this->process, $this->task, $this->action, $this->payload);

		//$result = $editorSession->runTableDisplayAndEdit( true );

		return Response::NoError();
	}




	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @return type
	 */
	public function two( $parent=null) {

		echo 'at dba two ';

		return Response::NoError();
	}




	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @return type
	 */
	public function three( $parent=null) {

		echo 'at dba three ';

//$fldName = 'xxxxxxxxxx';

		$x =   HTML::Image(Resolver::REQUEST_PAYLOAD . '[sortAsc][' . $fldName . ']', '\static\images\A_to_Z_icon.png', 'xx', ['width'=>18] );
		dump::dump($x);

		echo  $x;

		return Response::NoError();
	}
}
