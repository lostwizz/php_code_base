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

//use \php_base\Utils\SubSystemMessage as SubSystemMessage;

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
		Settings::getRunTimeObject('DBA_DEBUGGING')->addInfo('constructor for dbaController');


		$this->process = $passedProcess;
		$this->task = $passedTask;
		$this->action = $passedAction;
		$this->payload = $passedPayload;
dump::dumpLong($this, 'constructor');
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
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('@@doWork');
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
	 * @param type $dispatcher
	 * @return Response
	 */
	public function showAllMessageLogLevels($dispatcher) : Response {

		Settings::GetRunTimeObject('MessageLog')->TestAllLevels();

		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $dispatcher
	 * @return Response
	 */
	public function ShowAllSettings($dispatcher): Response {

			//dump::dump(Settings::GetRunTimeObject('MessageLog'));

		echo Settings::dump(true,true,true);
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @return type
	 */
	public function Edit_UserInfoData( $dispatcher=null) : Response  {
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('@@Edit_UserInfoData');

	Settings::GetRunTimeObject('MessageLog')->setSubSystemLoggingLevel(\php_base\Utils\MessageLog::DEFAULT_SUBSYSTEM , LVL_Notice_5);


		if( ! Settings::GetRunTime('userPermissionsController')->hasRole('DBA')) {
			return Response::PermissionsError('Required DBA - Edit UserInfoData');
		}

		$this->payload['Table'] = '\php_base\data\UserInfoData';
		//$editorSession = new SimpleTableEditor('\php_base\data\UserInfoData', $this->process, $this->task, $this->action, $this->payload);
		$dispatcher->addProcess( 'SimpleTableEditorController', 'runTableDisplayAndEdit', $this->action, $this->payload );
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------	*/
	public function Edit_Roles( $dispatcher=null) : Response {
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('@@Edit_Roles');

		if( ! Settings::GetRunTime('userPermissionsController')->hasRole('DBA')) {
			return Response::PermissionsError('Required DBA - Edit Roles');
		}

		$this->payload['Table'] = 'UserRoleData';

		//$editorSession = new SimpleTableEditor('\php_base\data\UserRoleData', $this->process, $this->task, $this->action, $this->payload);

//dump::dump($this);

	//	$dispatcher->addProcess( 'UserRoleAndPermissionsController', 'readAllData', 'UserRoleData', $this->payload );
		$dispatcher->addProcess( 'SimpleTableEditorController', 'runTableDisplayAndEdit', $this->action, $this->payload );

//dump::dump($dispatcher->dumpQueue());
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------	*/
	public function Edit_Attributes( $dispatcher=null) : Response {
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('@@Edit_attributes');

		if( ! Settings::GetRunTime('userPermissionsController')->hasRole('DBA')) {
			return Response::PermissionsError('Required DBA - Edit Attributes');
		}

		$this->payload['Table'] = '\php_base\data\UserAttributeData';
		//$editorSession = new SimpleTableEditor('\php_base\data\UserAttributeData', $this->process, $this->task, $this->action, $this->payload);
		$dispatcher->addProcess( 'SimpleTableEditor', 'runTableDisplayAndEdit', $this->action, $this->payload );

		return Response::NoError();
	}


	/** -----------------------------------------------------------------------------------------------	*/
	public function Edit_Permissions( $dispatcher=null) : Response {
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('Edit_Permissions');

		if( ! Settings::GetRunTime('userPermissionsController')->hasRole('DBA')) {
			return Response::PermissionsError('Required DBA - Edit_Permissions');
		}

		$this->payload['Table'] = 'UserPermissionData';
		$editorSession = new SimpleTableEditor('\php_base\data\UserPermissionData', $this->process, $this->task, $this->action, $this->payload);
		return Response::NoError();
	}




	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @return type
	 */
	public function two( $parent=null) {
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('@@two');

		echo 'at dba two ';
dump::dump('at two');

	//Settings::GetRuntimeObject('POSTqueue')->push('dbaController.four');
		//Settings::GetRuntimeObject('Dispatcher')->addPOSTProcess('dba', 'four');


		return Response::NoError();
	}




	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @return type
	 */
	public function three( $parent=null) {
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('@@three');

		echo 'at dba three ';

//$fldName = 'xxxxxxxxxx';

		//$x =   HTML::Image(Resolver::REQUEST_PAYLOAD . '[sortAsc][' . $fldName . ']', '\static\images\A_to_Z_icon.png', 'xx', ['width'=>18] );
		dump::dump('at three');

		//echo  $x;
		//Settings::GetRuntimeObject('POSTqueue')->push('dbaController.five');
		Settings::GetRuntimeObject('Dispatcher')->addPOSTProcess('dba', 'five');

		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------*/
	public function four( $parent=null) {
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('@@four');

		echo 'at dba four ';
dump::dump('at four');
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------*/
	public function five( $parent=null) {
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('@@five');

		echo 'at dba five ';
dump::dump('at five');
		return Response::NoError();
	}

}

