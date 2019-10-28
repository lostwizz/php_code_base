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
use \php_base\Utils\Response as Response;


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
	public function isLoggedIn($username, $password) : Response{

		if ( !empty($username)) {

			echo 'Checking login:',  $username;
			echo '<br>';
			if ($this->isGoodAuthentication( $username, $password)) {
				Settings::GetRunTimeObject('MessageLog')->addInfo('User: '. $username . ' is logged on');
				// user and password are good so they is logged in
				self::$Uname - $password;
				Settings::SetRunTime( 'Currently Logged In User', $username );
			} else {
				$username = null;
			}
		} else {
			$username = null;
		}
		Settings::GetRunTimeObject('MessageLog')->addNotice( 'username='. $username . (empty($username) ? 'NOT-logged in': 'Seems to be Loggedin'));
		return (! empty($username));
	}


//	//-----------------------------------------------------------------------------------------------
	public function tryToLogin($username, $password) : Response{

		Settings::GetRunTimeObject('MessageLog')->addTODO('check the username against somthing -db or file or hard or whatever!!!');
		if (empty($username) or empty($password)){
			return new Response( 'Missing Username or Password trying to login', -5, false);
		}

		//////////////// -DEBUG CODE
		if ($username == $password){
			Settings::GetRunTimeObject('MessageLog')->addInfo('User: ' . $username . ' Sucessfully Logged in');
			self::$User = $username;
			Settings::SetRunTime( 'Currently Logged In User', $username );
			return Response::NoError();
		} else {
			Settings::GetRunTimeObject('MessageLog')->addNotice('User: ' . $username . ' UNSucessfully Logged in!!!!');
			self::$User = null;
			return new Response( 'Failed Trying to Login', -4, false);
		}
//		return false;
	}


	//-----------------------------------------------------------------------------------------------
	public function isGoodAuthentication($username){

		///////////////////// DEBUG CODE
		if ($username = self::Uname) {
			return true;
		} else {
			return false;
		}
	}

	//-----------------------------------------------------------------------------------------------
	public function doWork() : Response{
			// should never get here
		return Response::NoError();
	}

}
