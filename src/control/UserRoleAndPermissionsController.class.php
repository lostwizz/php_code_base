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

	public $action;
	public $payload;

	//-----------------------------------------------------------------------------------------------
	public function __construct($action='', $payload=null) {
		$this->model = new \php_base\model\UserRoleAndPermissionsModel($this);
		$this->data = new \php_base\data\UserRoleAndPermissionsData($this);
		//$this->view = new \php_base\data\UserRoleandPermissionsView($this);

		$this->action = $action;
		$this->payload = $payload;
	}

	//-----------------------------------------------------------------------------------------------
	public function doWork() : Response {
		echo 'should never get here';
	}

	//public function init($passedData)

	//-----------------------------------------------------------------------------------------------
	public function Setup( $action='', $payload= null) : Response{

		$u = Settings::GetRunTime( 'Currently Logged In User');
		$response = $this->Load($u);

		//$this->model->modelSetUser(  );


		Settings::SetRunTime('userPermissions', $this->model);
		return $response;
	}

	//-----------------------------------------------------------------------------------------------
	public function Load($username) : Response {
		if ( empty($username)){
			return new Response('Username not supplied to LoadPermissions', -6, false);
		}
		Settings::GetRunTimeObject('MessageLog')->addInfo('starting load');

		$this->username = $username;
		$this->usersInfo =  new \php_base\data\UserInfoData ($username);
		$this->usersRoles = new \php_base\data\UserRoleData( $username);
		$this->usersAttributes = new \php_base\data\UserAttributeData($username);
		$this->userPermissions =  new \php_base\data\UserPermissionData($username);

		return Response::NoError();
	}

	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------


}

