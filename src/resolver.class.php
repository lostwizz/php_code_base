<?php

//namespace whitehorse\MikesCommandAndControl2\Resolver;
namespace php_base;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;


//***********************************************************************************************
//***********************************************************************************************
class Resolver {

	const REQUEST_PROCESS = 'ACTION_PROCESS';
	const REQUEST_TASK = 'ACTION_TASK';
	const REQUEST_ACTION = 'ACTION_ACTION';
	const REQUEST_PAYLOAD = 'ACTION_PAYLOAD';

	public $dispatcher;
	public $requestInfo;

	public $process = null;
	public $task = null;
	public $action = null;
	public $payload = null;

	//-----------------------------------------------------------------------------------------------
	public function __construct(){
		$this->dispatcher  = new Dispatcher();
	}


	//-----------------------------------------------------------------------------------------------
	public function doWork() {

Dump::dump($_REQUEST);
		//$this->doLoginWork();


		$this->AddHeader();
		$this->doAuthenticateUserHasLoggedIn();		// always start with login checks

		$this->AddFooter();

		$this->decodeRequestinfo();

		$r = $this->doDispatchCurrent();
		return $r;
	}

	//-----------------------------------------------------------------------------------------------
	protected function doDispatchCurrent(){

//Dump::dumpLong($this);

		if ( $this->dispatcher->do_work( $this) === false ){

			Settings::GetRunTimeObject('MessageLog')->addNotice( 'resolver got a false' );
			Settings::GetRuntimeObject('FileLog')->addNotice( 'resolver got a false');
			return false;
		} else {
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'resolver got a true' );
			Settings::GetRuntimeObject('FileLog')->addNotice( 'resolver got a true');
			return true;
		}
	}

	//-----------------------------------------------------------------------------------------------
	public function decodeRequestinfo(){

		if ($this->hasNoPassedInfo()){
			$this->doAuthenticateMustLogOn();
			return;
		}

		$this->determineProcessFromRequest();
	}

	//-----------------------------------------------------------------------------------------------
	function hasNoPassedInfo(){
		if ( !empty( $_REQUEST) and  !empty($_REQUEST[self::REQUEST_PROCESS])){
			return true;
		}
		return false;
	}

	//-----------------------------------------------------------------------------------------------
	function determineProcessFromRequest() {
		$process = ( ! empty($_REQUEST[self::REQUEST_PROCESS] )) ? $_REQUEST[self::REQUEST_PROCESS] : null;
		$task =    ( ! empty($_REQUEST[self::REQUEST_TASK]    )) ? $_REQUEST[self::REQUEST_TASK]    : null;
		$action =  ( ! empty($_REQUEST[self::REQUEST_ACTION]  )) ? $_REQUEST[self::REQUEST_ACTION]  : null;
		$payload = ( ! empty($_REQUEST[self::REQUEST_PAYLOAD] )) ? $_REQUEST[self::REQUEST_PAYLOAD] : null;
		$this->dispatcher->addProcess( $process, $task, $action, $payload);
	}

	//-----------------------------------------------------------------------------------------------
	protected function AddHeader(){
		$process ='Header';
		$task = 'doWork';
		$action = '';
		$payload =null;

		$this->dispatcher->addPREProcess( $process, $task, $action, $payload);

	}

	//-----------------------------------------------------------------------------------------------
	protected function doAuthenticateUserHasLoggedIn(){
				// always start with logged on check
		$process ='Authenticate';
		$task = 'CheckLogin';
		$action = 'isAuthenticated';
		$payload =null;

		$this->dispatcher->addPREProcess($process, $task, $action, $payload);
	}

	//-----------------------------------------------------------------------------------------------
	protected function doAuthenticateMustLogOn(){
				// always start with logged on check
		$process ='Authenticate';
		$task = 'forceLogin';
		$action = 'isNOTAuthenticated';
		$payload =null;

		$this->dispatcher->addPREProcess($process, $task, $action, $payload);
	}

	//-----------------------------------------------------------------------------------------------
	protected function AddFooter(){
		$process ='Footer';
		$task = 'doWork';
		$action = '';
		$payload =null;

		$this->dispatcher->addPOSTProcess( $process, $task, $action, $payload);

	}


//
//		$arRequiredPayload = \php_base\Control\AuthenticateController::getLoginRequiredVarsToCheck();
//		$requiredVars = $this->getRequiredPayload( $arRequiredPayload);
//
//		$isLoggedOn = $this->dispatcher->addProcess( 'control.AuthenticateController.CheckLogin', $requiredVars);
//
//		if (! $isLoggedOn) {
//			$this->dispatcher->addProcess( 'control.AuthenticateController.ForceLogin');
//
//		}
//		$this->dispatcher->addProcessPayload('control.HeaderController', $requiredVars);
//		$this->dispatcher->addProcessPayload('control.FooterController', $requiredVars);

//Dump::dumplong( $this->dispatcher);
//
//$ar = $this->dispatcher;
//$s = Dump::arrayDisplayCompactor( $ar, array('payloads'));
//Dump::dumplong($s);



	//-----------------------------------------------------------------------------------------------
//	protected function getRequiredPayload( $arRequiredVars){
//		$arOfVars = array();
//		foreach( $arRequiredVars as $varName){
//			$arOfVars[$varName] = $this->isVarInRequestOrSession( $varName);
//		}
//		return $arOfVars;
//	}


	//-----------------------------------------------------------------------------------------------
//	protected function isVarInRequestOrSession( $varName){
//		if ( !empty($_REQUEST) and !empty( $_REQUEST[ $varName])) {
//			return  $_REQUEST[ $varName];
//		} else if (!empty($_SESSION) and !empty($_SESSION[$varName])) {
//			return $_SESSION[$varName];
//		} else {
//			return false;
//		}
//	}



	//-----------------------------------------------------------------------------------------------


}

