<?php
//**********************************************************************************************
//* authenticateModel.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-08-30 12:00:20 -0700 (Fri, 30 Aug 2019) $
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


//***********************************************************************************************
//***********************************************************************************************
class AuthenticateModel extends Model{

	const GOD_RIGHT = 'GOD';
	const DBA_RIGHT ='DBA';
	const WRITE_RIGHT = 'Write';
	const READ_RIGHT ='Read';

	const WILDCARD_RIGHT = '*';

	const NO_RIGHT = '__NO__RIGHT__';
	///////////const NOBODY = '__NO__BODY__';

	//var $controller;

	protected static $User;

//	protected static $curUserID = 0;  // number (id)
//	protected static $curUserName;  // name
//	protected static $curRoleID;  // number (id)
//	protected static $curRoleName;  // desc
//	protected static $curPermissions; //array( id, roleid, model, task, field, right (read, write, dba)

	//-----------------------------------------------------------------------------------------------
//	public function __construct($controller) {
//		$this->controller = $controller;
//		//dummy Settings
////		self::$curUserID =1000;
////		self::$curUserName= 'Mike';
////		self::$curRoleID=  99;
////		self::$curRoleName = self::DBA_RIGHT;
////		self::$curPermissions = array (
////									array('model'=>	'UserManagement', 'task'=> '*', 'field'=> 'RoleID', 'right'=> self::WRITE_RIGHT),
////									array('model'=>	'*', 'task'=> '*', 'field'=> '*', 'right'=> self::READ_RIGHT)
////								);
//	}

	//-----------------------------------------------------------------------------------------------
	public function isLoggedIn(){
		$uname = $this->controller->payload['username'];
		echo 'Checking login:',  $uname;
		echo '<br>';
		Settings::GetRunTimeObject('MessageLog')->addNotice( 'username='. $uname . (empty($uname) ? 'NOT-logged in': 'Seems to be Loggedin'));
		return (! empty($uname));
	}
	//-----------------------------------------------------------------------------------------------
	public function doLogin(){
		$this->controller->view->showLoginPage();
	}

	//-----------------------------------------------------------------------------------------------
	public function doWork(){

		//for testing only
		return true;
	}

	//-----------------------------------------------------------------------------------------------
	public function checkRights( $model = self::NO_RIGHT, $task = self::NO_RIGHT, $field = self::NO_RIGHT, $rightWanted = self::NO_RIGHT){
//		$s =   'User: ' . self::$curUserName . ' (' . self::$curUserID . ') '
//			. ' Role: ' . self::$curRoleName . ' (' . self::$curRoleID . ') '
//			. ' wanted Model:' . $model
//			. ' task:' . $task
//			. ' field' . $field
//			. ' rightWanted: ' . $rightWanted
//			;
		//Settings::GetPublic('SecurityLog')->addInfo( $s);
		$this->loadCache('RoleManagement');
		$this->loadCache('PermissionManagement');

		$r = $this->checkIfHasRight($model, $task, $field, $rightWanted) ;
		return $r;
	}

	//-----------------------------------------------------------------------------------------------
	protected function checkIfHasRight($model = self::NO_RIGHT, $task = self::NO_RIGHT, $field = self::NO_RIGHT, $rightWanted = self::NO_RIGHT){
		foreach( self::$curPermissions as $row) {
			if ( ! $this->checkModel($model, $row)){
				continue;
			}
			if ( ! $this->checkTask( $task, $row) ) {
				continue;
			}
			if ( ! $this->checkField($field, $row)){
				continue;
			}
			if (! $this->checkWantedRight($rightWanted, $row)){
				continue;
			}
			return true;
		}
		return false;
	}


	//-----------------------------------------------------------------------------------------------
	protected function checkModel($model, $aRow){
//echo 'm>'. $model,'<>', $aRow['model'] , '<>';
		$x = ( $aRow['model'] == $model or $aRow['model'] == self::WILDCARD_RIGHT);
//echo ($x?'Y':'N'), '< <br>';
		return $x;
	}

	//-----------------------------------------------------------------------------------------------
	protected function checkTask($task, $aRow){
//echo 't>'. $task,'<>', $aRow['task'] , '<>';
		$x = ( $aRow['task'] == $task or $aRow['task'] == self::WILDCARD_RIGHT);
//echo ($x?'Y':'N'), '< <br>';
		return $x;
	}

	//-----------------------------------------------------------------------------------------------
	protected function checkField($field, $aRow){
		return ( $aRow['field'] == $field or $aRow['field'] == self::WILDCARD_RIGHT);
	}

	//-----------------------------------------------------------------------------------------------
	protected function checkWantedRight($rightWanted, $aRow){
//echo 'R>', $rightWanted, '<>' , $aRow['right'], '<>';
		if ( $aRow['right'] == $rightWanted){
//echo '-y-';
			return true;
		}
		if ( $aRow['right'] == self::WILDCARD_RIGHT ){
			return true;
		}
//echo '#';
		if ( $rightWanted == self::READ_RIGHT and ( $aRow['right'] == self::WRITE_RIGHT
													or $aRow['right'] == self::DBA_RIGHT
													or $aRow['right'] == self::GOD_RIGHT)) {
//echo '@@'	;
			return true;
		}
		if ( $rightWanted == self::WRITE_RIGHT and ( $aRow['right'] == self::DBA_RIGHT
													or $aRow['right'] == self::GOD_RIGHT)){
			return true;
		}
		if ($rightWanted == self::DBA_RIGHT and ($aRow['right']== self::GOD_RIGHT)){
			return true;
		}
		return false;
	}

	//-----------------------------------------------------------------------------------------------
	protected function loadCache( $tbl){

	}

}
