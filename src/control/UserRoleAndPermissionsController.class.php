<?php

namespace php_base\control;



use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;


//***********************************************************************************************
//***********************************************************************************************
Class UserRoleAndPermissionsController {

	public $username;

	public $usersInfo;
	public $usersRoles;
	public $usersRights;
	public $data;
	public $view;
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

	//public function init($passedData)

	//-----------------------------------------------------------------------------------------------
	public function Setup( $action='', $payload= null) : Response{

		$u = Settings::GetRunTime( 'Currently Logged In User');
		if ( !empty($u)) {
			$response = $this->LoadAllPermissionData($u);

			// use this setting to check permissions
			Settings::SetRunTime('userPermissions', $this->model);

		} else {
			$response = new Response('no username', -8, false, true);
		}

//Dump::dump(Settings::GetRunTime('userPermissions'));
		return $response;
	}

	//-----------------------------------------------------------------------------------------------
	public function LoadAllPermissionData($username) : Response {
		if ( empty($username)){
			return new Response('Username not supplied to LoadPermissions', -6, false, true);
		}

		//Settings::GetRunTimeObject('MessageLog')->addInfo('starting load');

		try {
			// setup the user with the extra data in the users table and then get the attributes for that user
			$this->username = $username;
			$this->usersInfo =  new \php_base\data\UserInfoData ($username);
			$this->usersAttributes = new \php_base\data\UserAttributeData($this->usersInfo->getUserID());

			// take the primary role from the user info and addit to the array of roles in the user attributes
			$primaryRole = $this->usersInfo->getPrimaryRole();
			$this->usersAttributes->AddPrimaryRole($primaryRole);  // add the userInfo PrimaryRole

			// get the array of all the roles this user has   (words i.e. Clerk)
			$ArrayOfRoleNames = $this->usersAttributes->getArrayOfRoleNames();

			// take the list of roles (words i.e. Clerk) and get the role IDs
			$this->usersRoles = new \php_base\data\UserRoleData( $ArrayOfRoleNames);

			// now we have an array of Role ids
			$arOfRoleIDs = $this->usersRoles->RoleIDData;
	//Dump::dump($arOfRoleIDs);

			// now with roleid go and get the permissions related to those role ids
			$this->userPermissions =  new \php_base\data\UserPermissionData($arOfRoleIDs);
//Dump::dump($this->userPermissions );
			// now we are setup to check the permissions -- Model->hasRights


//Dump::dump(Settings::GetRunTime('userPermissions'));

			//////$this->model = new \php_base\model\UserRoleAndPermissionsModel($this);
//Dump::dump($this->model);
		} catch (\Exception $e){
			return new Response( 'something happended when trying to load all permissions' , -7);
		}

		return Response::NoError();
	}

	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------


}

