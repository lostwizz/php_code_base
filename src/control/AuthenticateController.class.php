<?php
//**********************************************************************************************
//* AuthenticateController.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-12 09:46:20 -0700 (Thu, 12 Sep 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 12-Sep-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************


namespace whitehorse\MikesCommandAndControl2\Control;


use \whitehorse\MikesCommandAndControl2\Settings\Settings as Settings;
use \whitehorse\MikesCommandAndControl2\Utils\Dump\Dump as Dump;

//***********************************************************************************************
//***********************************************************************************************
class AuthenticateController extends Controller {

	//-----------------------------------------------------------------------------------------------
	public function __construct($payload = null) {
		$this->model = new \whitehorse\MikesCommandAndControl2\model\AuthenticateModel($this);
		$this->data = new \whitehorse\MikesCommandAndControl2\data\AuthenticateData($this);
		$this->view = new \whitehorse\MikesCommandAndControl2\view\AuthenticateView($this);

		$this->payload = $payload;
	}

	//-----------------------------------------------------------------------------------------------
	public function doWork(){
		echo 'authenticationController doWork hi - i am here!!';
	}

	//-----------------------------------------------------------------------------------------------
	public static function controllerRequiredVars(){
		return [];
	}

	//-----------------------------------------------------------------------------------------------
	public static function checkLoginRequiredVars() {
		return array_merge(self::controllerRequiredVars(),
				[
					'username',
					'password'
				]);
	}

	//-----------------------------------------------------------------------------------------------
	public function checkLogin(){
		$this->model->isLoggedIn();
	}

	//-----------------------------------------------------------------------------------------------
	public function forceLoginRequiredVars(){
		return array_merge(self::controllerRequiredVars(),
				 []);
	}

	//-----------------------------------------------------------------------------------------------
	public function forceLogin(){
		$this->model->doLogin();
	}

}
