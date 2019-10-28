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


namespace php_base\Control;


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;


//***********************************************************************************************
//***********************************************************************************************
class AuthenticateController extends Controller {

	public $process;
	public $task;

	public $action;
	public $payload;

	//-----------------------------------------------------------------------------------------------
	public function __construct($action='', $payload = null) {
		$this->model = new \php_base\model\AuthenticateModel($this);
		$this->data = new \php_base\data\AuthenticateData($this);
		$this->view = new \php_base\view\AuthenticateView($this);

		$this->action = $action;
		$this->payload = $payload;
//Dump::dump($payload);

		//Settings::SetRunTime( 'Currently Logged In User', false);            //after a successful logon this will be set to the userid
	}

	//-----------------------------------------------------------------------------------------------
	public function setProcessAndTask( $process, $task){
		$this->process = $process;
		$this->task = $task;
	}

	//-----------------------------------------------------------------------------------------------
	public function doWork() : Response {
		echo 'authenticationController doWork hi - i am here!!';
		echo 'should never get here';
	}

	//-----------------------------------------------------------------------------------------------
	public function CheckLogin($parent) : Response {
//Dump::dumpLong($this);
		//Settings::GetRunTimeObject('MessageLog')->addNotice('at checkLogin');
//Dump::dump($this->payload);

		$username = (!empty( $this->payload['entered_username'])) ? $this->payload['entered_username'] : null;
		$password = (!empty( $this->payload['entered_password'])) ? $this->payload['entered_password'] : null;

		//if ( !empty($this->action) and !empty($this->))
		switch ( $this->action){
			case 'no_Credentials':
			case 'Need_Login':
				//Settings::GetRunTimeObject('MessageLog')->addNotice('about to showLoginPage');
				return $this->view->showLoginPage();

			case 'do_the_logon_attempt':
				//Settings::GetRunTimeObject('MessageLog')->addNotice('about to tryToLogin');
				return $this->model->tryToLogin($username, $password);

			case 'Check_Ongoing_Connection':
				//Settings::GetRunTimeObject('MessageLog')->addNotice('about to isLoggedIn');
				return $this->model->isLoggedIn($username, $password);

			default:
				//return false;
				return new Response('Invalid Login' , -3, false);
		}

	}

}
