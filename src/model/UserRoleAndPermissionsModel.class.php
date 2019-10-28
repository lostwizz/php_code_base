<?php

namespace php_base\Model;


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;




//***********************************************************************************************
//***********************************************************************************************
Class UserRoleAndPermissionsModel extends Model{

	const GOD_RIGHT = 'GOD';
	const DBA_RIGHT ='DBA';
	const WRITE_RIGHT = 'Write';
	const READ_RIGHT ='Read';

	const WILDCARD_RIGHT = '*';

	const NO_RIGHT = '__NO__RIGHT__';
	///////////const NOBODY = '__NO__BODY__';

	public $action;
	public $payload;

	public $controller;

	//-----------------------------------------------------------------------------------------------
	public function __construct( $controller){   //$action ='', $payload = null){
Dump::dump($controller)		;
		if (!empty( $controller)){
			$this->controller = $controller;
		}
	}


//	//-----------------------------------------------------------------------------------------------
//	public function modelSetUser($uname) {
//		if ( !empty($uname)){
//			$this->username = $uname;
//		}
//
//
//	}


	//-----------------------------------------------------------------------------------------------
	public  function isAllowed( $permission= self::NO_RIGHT, $process=self::NO_RIGHT, $task=self::NO_RIGHT, $action=null){
		if (empty($permission) or $permission== self::NO_RIGHT){
			return false;
		}
		if ( empty($process) or $process==self::NO_RIGHT) {
			return false;
		}
		if ( empty($task) or $task==self::NO_RIGHT) {
			return false;
		}

		$arPermissions = $this->controller->userPermissions->permissionList;
Dump::dump( $arPermissions);
		foreach( $arPermissions as $value){
			if ( $this->checkRight( $value, $permission, $process, $task, $action )) {
				return true;
			}
		}
		return false;

	}

	//-----------------------------------------------------------------------------------------------
	protected function checkRight( $value, $permission, $process, $task, $action ) {

Dump::dumpLong( array( $value, $permission, $process, $task, $action));

		//if ( ! $this->checkModel($))

	}

////	//-----------------------------------------------------------------------------------------------
////	protected function checkIfHasRight($model = self::NO_RIGHT, $task = self::NO_RIGHT, $field = self::NO_RIGHT, $rightWanted = self::NO_RIGHT){
////		foreach( self::$curPermissions as $row) {
////			if ( ! $this->checkModel($model, $row)){
////				continue;
////			}
////			if ( ! $this->checkTask( $task, $row) ) {
////				continue;
////			}
////			if ( ! $this->checkField($field, $row)){
////				continue;
////			}
////			if (! $this->checkWantedRight($rightWanted, $row)){
////				continue;
////			}
////			return true;
////		}
////		return false;
////	}
////
////
////	//-----------------------------------------------------------------------------------------------
////	protected function checkModel($model, $aRow){
//////echo 'm>'. $model,'<>', $aRow['model'] , '<>';
////		$x = ( $aRow['model'] == $model or $aRow['model'] == self::WILDCARD_RIGHT);
//////echo ($x?'Y':'N'), '< <br>';
////		return $x;
////	}
////
////	//-----------------------------------------------------------------------------------------------
////	protected function checkTask($task, $aRow){
//////echo 't>'. $task,'<>', $aRow['task'] , '<>';
////		$x = ( $aRow['task'] == $task or $aRow['task'] == self::WILDCARD_RIGHT);
//////echo ($x?'Y':'N'), '< <br>';
////		return $x;
////	}
////
////	//-----------------------------------------------------------------------------------------------
////	protected function checkField($field, $aRow){
////		return ( $aRow['field'] == $field or $aRow['field'] == self::WILDCARD_RIGHT);
////	}
////
////	//-----------------------------------------------------------------------------------------------
////	protected function checkWantedRight($rightWanted, $aRow){
//////echo 'R>', $rightWanted, '<>' , $aRow['right'], '<>';
////		if ( $aRow['right'] == $rightWanted){
//////echo '-y-';
////			return true;
////		}
////		if ( $aRow['right'] == self::WILDCARD_RIGHT ){
////			return true;
////		}
//////echo '#';
////		if ( $rightWanted == self::READ_RIGHT and ( $aRow['right'] == self::WRITE_RIGHT
////													or $aRow['right'] == self::DBA_RIGHT
////													or $aRow['right'] == self::GOD_RIGHT)) {
//////echo '@@'	;
////			return true;
////		}
////		if ( $rightWanted == self::WRITE_RIGHT and ( $aRow['right'] == self::DBA_RIGHT
////													or $aRow['right'] == self::GOD_RIGHT)){
////			return true;
////		}
////		if ($rightWanted == self::DBA_RIGHT and ($aRow['right']== self::GOD_RIGHT)){
////			return true;
////		}
////		return false;
////	}
////
////	//-----------------------------------------------------------------------------------------------
////	public function checkRights( $model = self::NO_RIGHT, $task = self::NO_RIGHT, $field = self::NO_RIGHT, $rightWanted = self::NO_RIGHT){
//////		$s =   'User: ' . self::$curUserName . ' (' . self::$curUserID . ') '
//////			. ' Role: ' . self::$curRoleName . ' (' . self::$curRoleID . ') '
//////			. ' wanted Model:' . $model
//////			. ' task:' . $task
//////			. ' field' . $field
//////			. ' rightWanted: ' . $rightWanted
//////			;
////		//Settings::GetPublic('SecurityLog')->addInfo( $s);
////		$this->loadCache('RoleManagement');
////		$this->loadCache('PermissionManagement');
////
////		$r = $this->checkIfHasRight($model, $task, $field, $rightWanted) ;
////		return $r;
////	}
////

}