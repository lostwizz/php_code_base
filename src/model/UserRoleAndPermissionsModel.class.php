<?php
//**********************************************************************************************
//* UserRoleAndPermissionsModel.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-08-30 12:00:20 -0700 (Fri, 30 Aug 2019) $
//*  $WCNOW$
//*  $WCDATE$
//*  $WCUNVER?DATE_NOW:DATE_COMMIT$
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
//Dump::dump($controller);
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
	public  function isAllowed( $wantedPermission= self::NO_RIGHT,
								$process=self::NO_RIGHT,
								$task=self::NO_RIGHT,
								$action=self::NO_RIGHT,
								$field=self::WILDCARD_RIGHT
							){
		if (empty($wantedPermission) or $wantedPermission== self::NO_RIGHT){
			return false;
		}
		if ( empty($process) or $process==self::NO_RIGHT) {
			return false;
		}
		if ( empty($task) or $task==self::NO_RIGHT) {
			return false;
		}
		if ( empty($action) or $action ==self::NO_RIGHT){
			return false;
		}
		$s = $wantedPermission . '<=' . $process . '.' . $task . '.' . $action . '.' . $field;

		$arPermissions = $this->controller->userPermissions->permissionList;

		$process = strtoupper($process);
		$task = strtoupper($task);
		$action = strtoupper($action);
		$field = strtoupper($field);
//Dump::dump( $arwantedPermissions);

		foreach( $arPermissions as $value){
			if (  $this->checkRight( $value, $wantedPermission, $process, $task, $action, $field )) {
				Settings::GetRunTimeObject('MessageLog')->addAlert('has permission wanted: ' . $s);
				Settings::GetRuntimeObject( 'SecurityLog')->addNotice( Settings::GetRunTime( 'Currently Logged In User') . ' has permission: '. $s);

				return true;
			}
		}
		Settings::GetRunTimeObject('MessageLog')->addAlert('Does NOT have permission wanted: ' . $s);
				Settings::GetRuntimeObject( 'SecurityLog')->addAlert( Settings::GetRunTime( 'Currently Logged In User') . ' Does NOT have permission: '. $s);
		return false;
	}

	//-----------------------------------------------------------------------------------------------
	protected function checkRight( $singleOfPermissions, $wantedPermission, $process, $task, $action, $field ) {

//Dump::dumpLong( array( $singleOfPermissions, $wantedPermission, $process, $task, $action, $field));

		if ( ! $this->checkProcess($singleOfPermissions, $process)) { return false;}
		if ( ! $this->checkTask( $singleOfPermissions, $task)) 		{ return false;}
		if ( ! $this->checkAction( $singleOfPermissions, $action)) 	{ return false;}
		if ( ! $this->checkField( $singleOfPermissions, $field)) 	{ return false;}
		return $this->checkPermission($singleOfPermissions, $wantedPermission);
	}

	//-----------------------------------------------------------------------------------------------
	protected function checkProcess( $singleOfPermissions, $process){
		$r = (($process == self::WILDCARD_RIGHT)
		   or ($process == $singleOfPermissions['PROCESS'])
		   or ($singleOfPermissions['PROCESS'] ==self::WILDCARD_RIGHT));

//		$s =$r ? '+true+':'+false+';
//		Settings::GetRunTimeObject('MessageLog')->addNotice('checkProcess:' .  $s);
		return $r;
	}

	//-----------------------------------------------------------------------------------------------
	protected function checkTask( $singleOfPermissions, $task){
		$r  = (($task == self::WILDCARD_RIGHT)
		    or ($task == $singleOfPermissions['TASK'])
		    or ($singleOfPermissions['TASK'] ==self::WILDCARD_RIGHT));

//		$s =$r ? '+true+':'+false+';
//		Settings::GetRunTimeObject('MessageLog')->addNotice('checkTask:' .  $s);
		return $r;
	}

	//-----------------------------------------------------------------------------------------------
	protected function checkAction( $singleOfPermissions, $action){
		$r = (($action == self::WILDCARD_RIGHT)
		   or ($action == $singleOfPermissions['ACTION'])
		   or ( $singleOfPermissions['ACTION'] == self::WILDCARD_RIGHT));

//		$s =$r ? '+true+':'+false+';
//		Settings::GetRunTimeObject('MessageLog')->addNotice('checkAction:' .  $s);
		return $r;
	}

	//-----------------------------------------------------------------------------------------------
	protected function checkField( $singleOfPermissions, $field){
		$r = (($field == self::WILDCARD_RIGHT)
		   or ($field == $singleOfPermissions['FIELD'])
		   or ($singleOfPermissions['FIELD'] ==self::WILDCARD_RIGHT));

//		$s =$r ? '+true+':'+false+';
//		Settings::GetRunTimeObject('MessageLog')->addNotice('checkField:' .  $s);
		return $r;
	}

	//-----------------------------------------------------------------------------------------------
	protected function checkPermission($singleOfPermissions, $wantedPermission){
		switch ($wantedPermission ){
			case self::GOD_RIGHT:
				$r = (( $singleOfPermissions['PERMISSION'] == self::GOD_RIGHT )
				  or  ( $singleOfPermissions['PERMISSION'] == self::WILDCARD_RIGHT));
				break;
			case self::DBA_RIGHT:
				$r = (( $singleOfPermissions['PERMISSION'] == self::GOD_RIGHT)
				  or  ( $singleOfPermissions['PERMISSION'] == self::DBA_RIGHT)
				  or  ( $singleOfPermissions['PERMISSION'] == self::WILDCARD_RIGHT));
				  break;
			case self::WRITE_RIGHT:
				$r = (( $singleOfPermissions['PERMISSION'] == self::GOD_RIGHT)
				  or  ( $singleOfPermissions['PERMISSION'] == self::DBA_RIGHT)
				  or  ( $singleOfPermissions['PERMISSION'] == self::WRITE_RIGHT)
				  or  ( $singleOfPermissions['PERMISSION'] == self::WILDCARD_RIGHT));
				  break;
			case self::READ_RIGHT:
				$r = (( $singleOfPermissions['PERMISSION'] == self::GOD_RIGHT)
				  or  ( $singleOfPermissions['PERMISSION'] == self::DBA_RIGHT)
				  or  ( $singleOfPermissions['PERMISSION'] == self::WRITE_RIGHT)
				  or  ( $singleOfPermissions['PERMISSION'] == self::READ_RIGHT)
				  or  ( $singleOfPermissions['PERMISSION'] == self::WILDCARD_RIGHT));
				  break;
			case self::NO_RIGHT:
				$r =  false;
				break;
			case self::WILDCARD_RIGHT;
				$r = false;
		}
//		$s = 'wanted: ' . $wantedPermission;
//		$s .= ' --> ' . $singleOfPermissions['PERMISSION'];
//		$s .= '_';
//		$s .= $r ? '^true^' : '^false^';
//		Settings::GetRunTimeObject('MessageLog')->addNotice('checkPerm:' .  $s);
		return $r;
	}

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