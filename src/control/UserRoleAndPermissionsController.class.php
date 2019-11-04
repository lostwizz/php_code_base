<?php
//**********************************************************************************************
//* UserRoleAndPermissionsController.class.php
/**
 * sets up the users permissions from the database
 *
 * @author  mike.merrett@whitehorse.ca
 * @license City of Whitehorse
 */
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


namespace php_base\control;


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\HTML\HTML as HTML;



//***********************************************************************************************
//***********************************************************************************************
Class UserRoleAndPermissionsController {

	/**
	 * the usersname
	 *
	 * @var string
	 */
	public $username;
	public $userID;

	public $userInfo =null;
	public $userAttributes = null;
	public $roleNames = null;
	public $userPermissions = null;


	//public $data;
	//public $view;
	public $model;

	public $process;
	public $task;

	public $action;
	public $payload;

	//-----------------------------------------------------------------------------------------------
	public function __construct($action='', $payload=null) {
		$u = Settings::GetRunTime( 'Currently Logged In User');
		if ( !empty($u)) {

			$this->model = new \php_base\model\UserRoleAndPermissionsModel($this);
			//$this->data = new \php_base\data\UserRoleAndPermissionsData($this);
			//$this->view = new \php_base\data\UserRoleandPermissionsView($this);

			$this->action = $action;
			$this->payload = $payload;
		}
	}

	//-----------------------------------------------------------------------------------------------
	public function setProcessAndTask( $process, $task){
		$this->process = $process;
		$this->task = $task;
	}

	//-----------------------------------------------------------------------------------------------
	public function doWork() : Response {
		echo 'should never get here';
	}


	//-----------------------------------------------------------------------------------------------
	public function Setup( $action='', $payload= null) : Response{

		$u = Settings::GetRunTime( 'Currently Logged In User');
		if ( !empty($u)) {
			$response = $this->LoadAllUserInformation($u);

			// use this setting to check permissions
			Settings::SetRunTime('userPermissions', $this->model);

		} else {
			$response = new Response('no username', -8, false, true);
		}

//Dump::dump(Settings::GetRunTime('userPermissions'));
		return $response;
	}

	//-----------------------------------------------------------------------------------------------
	public function LoadAllUserInformation($username): Response {
		if (empty($username)) {
			return new Response('Username not supplied to LoadPermissions', -6, false, true);
		}
		try {

			// setup the user with the extra data in the users table and then get the attributes for that user
			$this->username = $username;

			$DataUserInfo = new \php_base\data\UserInfoData($username);

			$this->userID = $DataUserInfo->getUserID();
			$DataUserAttribute = new \php_base\data\UserAttributeData($this->userID);

			// take the primary role from the user info and addit to the array of roles in the user attributes
			$primaryRole = $DataUserInfo->getPrimaryRole();
			$DataUserAttribute->AddPrimaryRole($primaryRole);  // add the userInfo PrimaryRole

			$this->userInfo = $DataUserInfo->UserInfo;

			$this->userAttributes = $DataUserAttribute->UserAttributes;

			// get the array of all the roles this user has   (words i.e. Clerk)
			$ArrayOfRoleNames = $DataUserAttribute->getArrayOfRoleNames();


			// take the list of roles (words i.e. Clerk) and get the role IDs
			$DataUserRoles = new \php_base\data\UserRoleData($ArrayOfRoleNames);

			$this->roleNames = $DataUserAttribute->roleNames;

			// now we have an array of Role ids
			$arOfRoleIDs = $DataUserRoles->RoleIDData;

			// now with roleid go and get the permissions related to those role ids
			$DataUserPermissions = new \php_base\data\UserPermissionData($arOfRoleIDs);

			$this->userPermissions = $DataUserPermissions->permissionList;

			//$this->dumpState(null, null, true);

		} catch (\Exception $e) {
			return new Response('something happended when trying to load all permissions', -7);
		}

		return Response::NoError();
	}

	//-----------------------------------------------------------------------------------------------
	public function dumpState( $arRoleNames = null, $arOfRoleIds=null, $forceShow=false){
		if ( !$forceShow){
			return null;
		}
		echo HTML::HR();
		echo '<pre class="UserStateDump" >';

//		print_r($arRoleNames);
//		print_r($arOfRoleIds);

		echo 'THIS=';
		print_r($this);
		echo HTML::BR();


		echo HTML::HR();
		echo '</pre>';
	}
	//
	//-----------------------------------------------------------------------------------------------


}


//
//		if ( empty(	$this->UserInfoData  )){
//			echo ' - Missing users id';
//		} else {
//			echo 'userInfoData';
//			print_r($this->UserInfoData);
//			echo 'userid= ' , $this->UserInfoData->getUserID();
//
//		}
//
//		echo HTML::BR();
//
//		print_r($this->userInfo);
//		 echo HTML::BR();
//
//		if ( empty($this->UserInfoData)){
//			echo 'this->UserInfoData  - is missing';
//		} else {
//			echo 'this->UserInfoData';
//			print_r($this->UserInfoData);
//		}
//		echo HTML::BR();
//
//
//		if (empty($this->userInfo )){
//			echo ' - Missing UsersInfo - ';
//		} else {
//			echo 'UsersInfo=';
//			print_r($this->userInfo);
//		}
//		echo HTML::BR();
//
//		if ( empty($this->data) ) {
//			echo '- missing this->data';
//		} else {
//			echo 'this->data';
//			print_r( $this->data);
//		}
//
//		echo HTML::BR();
//
//		if (empty( $this->data)){
//			echo '- missing UserAttributeData';
//		} else {
//			echo 'UsersAttributeData=';
//			print_r ($this->data);
//		}
//		echo HTML::BR();
//
//		if ( empty( $arRoleNames)){
//			echo ' - missing  role names -';
//		} else {
//			echo ' arRoleNames=';
//			print_r ($arRoleNames);
//		}
//		echo HTML::BR();
//
//		if ( empty($this->userRoles)) {
//			echo ' - missing user roles -';
//		} else{
//			echo 'user roles=';
//			print_r($this->userRoles);
//		}
//		echo HTML::BR();
//
//		if ( empty($arOfRoleIds)) {
//			echo ' missing $arOfRoleIds';
//		} else {
//			echo '$arOfRoleIds=';
//			print_r( $arOfRoleIds);
//		}
//		echo HTML::BR();
//
//		if ( empty($this->DataUserPermissions)) {
//			echo ' missing list of user permissions';
//		} else {
//			echo 'user permissions=';
//			print_r( $this->DataUserPermissions);
//		}
//		echo HTML::BR();
//
//
//
//		echo 'THIS=';
//		print_r($this);
//		echo HTML::BR();