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

		//$this->model = new \php_base\model\MenuModel($this);
		//$this->data = new \php_base\data\MenuData($this);
		$this->view = new \php_base\view\dbaView($this);


		$this->process = $passedProcess;
		$this->task = $passedTask;
		$this->action = $passedAction;
		$this->payload = $passedPayload;
//dump::dumpLong($this, 'constructor');
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

		dump::dump(Settings::GetRunTimeObject('MessageLog'));

		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $dispatcher
	 * @return Response
	 */
	public function ShowAllSettings( $dispatcher): Response {

		echo Settings::dump(true,true,true);

		$menu = Settings::GetPublic('MenuController');
	//	$menu->changeMenuItemStatus( 20, false);

		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $dispatcher
	 * @return Response
	 */
	public function setLocalDebugModeToggle( $dispatcher) : Response {
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('@@setLocalDebugModeToggle' );
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addAlert( Settings::GetPublic('IS_DEBUGGING') );
		//Settings::GetRuntimeObject('DBA_DEBUGGING')->addAlert( $_SESSION );

		Settings::SetPublic('IS_DEBUGGING',  ( ! Settings::GetPublic('IS_DEBUGGING')));
		if ( Settings::GetPublic('IS_DEBUGGING')) {
			$_SESSION['LOCAL_DEBUG_SETTING']= ['IS_DEBUGGING' => 99];
			Settings::SetPublic('Show MessageLog Adds', true);
			Settings::SetPublic('Show MessageLog Adds_FileAndLine', true);

		} else {
			unset( $_SESSION['LOCAL_DEBUG_SETTING']['IS_DEBUGGING'] );
		}
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addAlert( Settings::GetPublic('IS_DEBUGGING') );
		//Settings::GetRuntimeObject('DBA_DEBUGGING')->addAlert( $_SESSION );
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $dispatcher
	 * @return Response
	 */
	public function EditAllDebugLevels($dispatcher) :Response {
		Settings::GetRunTimeObject('MessageLog')->addWarning( 'at EditAllDebugLevels' );

		$ar = Settings::giveAllSettingsThatStartWith('IS_DETAILED_');

		$this->view->showEditableIsDetailed( $ar);


		Settings::GetRunTimeObject('MessageLog')->addWarning( 'leaving EditAllDebugLevels' );

		return Response::NoError();
	}

	/** ----------------------------------------------------------------------------------------------- */
	public function setAllDebugLevels() {

		//dump::dump($this->payload);
		echo 'Applying: <BR>';
		foreach ($this->payload as $key => $value) {
			if ( $key =='IS_DEBUGGING'){
				Settings::SetPublic('IS_DEBUGGING', ($value !=0 ));
				$_SESSION['LOCAL_DEBUG_SETTING'][$key] = $value;
				echo 'Setting is_debugging' , (Settings::getPublic('IS_DEBUGGING')  ? '-ON-' : '_OFF_');
			} else {
				$_SESSION['LOCAL_DEBUG_SETTING'][$key] = $value;
				echo $key, ' with value=', $value, '<br>';
				Settings::setPublic($key, $value);
			}
		}
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @return type
	 */
	public function Edit_UserData( $dispatcher=null) : Response  {
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('@@Edit_UserData');

		if( ! Settings::GetRunTime('userPermissionsController')->hasRole('DBA')) {
			return Response::PermissionsError('Required DBA - Edit UserData');
		}
		$this->payload['Table'] = '\php_base\data\UserData';
		$dispatcher->addProcess( 'SimpleTableEditorController', 'runTableDisplayAndEdit', $this->action, $this->payload );
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------	*/
	public function OutputAllInfoByUser($dispatcher=null) :Response {

		$UD = new \php_base\data\UserData(null, null);
		$userList = $UD->readAllData();
//dump::dump( $userList);

		foreach($userList as $aUser){
			echo '---------------------------------------------', '<Br>';
			echo 'UserId=', $aUser['USERID'], "\t", 'User Name=', $aUser['USERNAME'], "\t", 'Application=', $aUser['APP'], '<Br>';
			echo "\t\t", 'Logon Method=', $aUser['METHOD'], 'Last IP=', $aUser['IP'], '<Br>';
			echo "\t\t", 'Last Logon Time=', $aUser['LAST_LOGON_TIME'], '<Br>';
			echo "\t\t", 'Primary Role Name=', $aUser['PRIMARYROLENAME'], '<Br>';

			echo '<pre>';

			$UA = new \php_base\data\UserAttributesData( null, $aUser['USERID']);
			if ( !empty( $UA)  and !empty( $UA->UserAttributes)) {
	//dump::dump($UA->UserAttributes);
				foreach( $UA->UserAttributes as $attribName => $attribValue) {
					echo "\t", $attribName, ' = ', $attribValue, '<BR>';
				}
			}

			echo 'Roles:<BR>';
			foreach($UA->roleNames as $roleId =>$aRoleName) {
				echo $aRoleName, ': <BR>';
				$RP = new \php_base\data\RolePermissionsData(null, [$roleId]);
//dump::dump($RP);
				$PL = $RP->permissionList;

//dump::dump($PL);
				if ( !empty($PL)){
					foreach( $PL as $RolePerm){
						echo "\t\t", ' PTAP=', $RolePerm['PROCESS'], ':', $RolePerm['TASK'], ':', $RolePerm['ACTION'],'=>', $RolePerm['FIELD'], ' -- ', $RolePerm['PERMISSION'], '<BR>';
					}
				} else  {
					echo '(role has no extra permissions)';
				}

				echo '<BR>';
			}
			echo '<BR>';
//dump::dump($UA);
echo '</pre>';

		}
		return Response::NoError();
	}


	/** -----------------------------------------------------------------------------------------------	*/
	public function Edit_Roles( $dispatcher=null) : Response {
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('@@Edit_Roles');

		if( ! Settings::GetRunTime('userPermissionsController')->hasRole('DBA')) {
			return Response::PermissionsError('Required DBA - Edit Roles');
		}
		$this->payload['Table'] = 'UserRolesData';
		$dispatcher->addProcess( 'SimpleTableEditorController', 'runTableDisplayAndEdit', $this->action, $this->payload );
		return Response::NoError();
	}






	/** -----------------------------------------------------------------------------------------------	*/
	public function Edit_Attributes( $dispatcher=null) : Response {
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('@@Edit_attributes');

		if( ! Settings::GetRunTime('userPermissionsController')->hasRole('DBA')) {
			return Response::PermissionsError('Required DBA - Edit Attributes');
		}
		$this->payload['Table'] = '\php_base\data\UserAttributesData';
		$dispatcher->addProcess( 'SimpleTableEditor', 'runTableDisplayAndEdit', $this->action, $this->payload );
		return Response::NoError();
	}


	/** -----------------------------------------------------------------------------------------------	*/
	public function Edit_Permissions( $dispatcher=null) : Response {
		Settings::GetRuntimeObject('DBA_DEBUGGING')->addNotice('Edit_Permissions');

		if( ! Settings::GetRunTime('userPermissionsController')->hasRole('DBA')) {
			return Response::PermissionsError('Required DBA - Edit_Permissions');
		}
		$this->payload['Table'] = '\php_base\data\RolePermissionsData';
		$dispatcher->addProcess( 'SimpleTableEditor', 'runTableDisplayAndEdit',  $this->action, $this->payload);
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

Settings::GetRunTimeObject('MessageLog')->addWarning('at  dba two');

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

