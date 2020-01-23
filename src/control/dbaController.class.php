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

use \php_base\Utils\SubSystemMessage as SubSystemMessage;

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
	 * @param type $parent
	 * @return type
	 */
	public function Edit_UserInfoData( $dispatcher=null) {
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('@@edit_UserInfoData');

		if( ! Settings::GetRunTime('userPermissionsController')->hasRole('DBA')) {
			return Response::PermissionsError('Required DBA - Edit UserInfoData');
		}

//dump::dump($dispatcher);

//dump::dumpLong(Settings::GetRunTime('userPermissionsController'));


//dump::dumpClasses();

		//if ( )
		//$dispatcher->addPOSTProcess('AuthenticateController','')
		//$t = new \php_base\data\UserInfoData();

		//$x = \php_base\data\UserInfoData::$Table ;
		//$x = $t::$Table;

//dump::dumpLong($this);

		//Settings::GetRuntimeObject('POSTqueue' )->enqueue('FooterController.doWork.two');

		$this->payload['Table'] = 'UserInfoData';


//echo '"', __NAMESPACE__, '"';

		//Settings::GetRuntimeObject('DISPATCHqueue' )->enqueue ('SimpleTableEditor');

//dump::dumpLong(Settings::GetRuntimeObject('DISPATCHqueue' )	);
//dump::dumpLong(Settings::GetRuntimeObject('PREqueue' )	);
//dump::dumpLong(Settings::GetRuntimeObject('POSTqueue' )	);

		$editorSession = new SimpleTableEditor('\php_base\data\UserInfoData', $this->process, $this->task, $this->action, $this->payload);

		//$result = $editorSession->runTableDisplayAndEdit( true );

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


///////		$loggy = new \php_base\Utils\SubSystemMessage('SimpleTableEditor', \php_base\Utils\MessageBase::DEBUG);

//dump::dump($loggy);
		//Settings::SetRuntime ('DBA_DEBUGGING', $loggy);


	Settings::GetRunTimeObject('DBA_DEBUGGING')->addDebug_1('this is debug1 example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addDebug_2('this is debug2 example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addInfo_2('this is info2 example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addInfo_3('this is info3 example');

	Settings::GetRunTimeObject('DBA_DEBUGGING')->addNotice_2('this is notice2 example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addNotice_3('this is notice3 example');


	Settings::GetRunTimeObject('DBA_DEBUGGING')->addEmergency('this is emergency example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addAlert('this is alert example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addCritical('this is critical example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addError('this is error example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addWarning('this is warning example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addNotice('this is notice example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addInfo('this is info example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addTodo('this is todo example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addDebug('this is debug example');



		$loggy2 = new \php_base\Utils\SubSystemMessage('SimpleTableEditor', \php_base\Utils\MessageBase::NOTICE);

//dump::dump($loggy2);
		Settings::SetRuntime ('DBA_DEBUGGING', $loggy2);

	Settings::GetRunTimeObject('DBA_DEBUGGING')->addEmergency('this is emergency example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addAlert('this is alert example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addCritical('this is critical example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addError('this is error example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addWarning('this is warning example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addNotice('this is notice example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addInfo('this is info example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addTodo('this is todo example');
	Settings::GetRunTimeObject('DBA_DEBUGGING')->addDebug('this is debug example');


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
