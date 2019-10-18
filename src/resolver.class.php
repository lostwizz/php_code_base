<?php

//namespace whitehorse\MikesCommandAndControl2\Resolver;
namespace php_base;

//use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

//include_once( DIR . 'dispatcher.class.php');



//***********************************************************************************************
//***********************************************************************************************
class Resolver {

	const DESTINATION ='DEST';
	const SUB_DESTINATION = 'SUB_DEST';

	public $dispatcher;
	public $requestInfo;

	public $process;
	//public $payload;

	public $possibleProcesses = array('Login',
									   'Logout',
									   'DBA_Menu'
									);

	//-----------------------------------------------------------------------------------------------
	public function __construct(){
		$this->dispatcher  = new Dispatcher();


	}


	//-----------------------------------------------------------------------------------------------
	public function doWork() {
		$this->doLoginWork();            // always start with login checks

		$this->decodeRequestinfo();

		if ( $this->dispatcher->do_work() === false ){
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'resolver got a false' );
			Settings::GetRuntimeObject('FileLog')->addNotice( 'resolver got a false');
		} else {
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'resolver got a true' );
			Settings::GetRuntimeObject('FileLog')->addNotice( 'resolver got a true');

		}

	}

	//-----------------------------------------------------------------------------------------------
	protected function doLoginWork(){
				// always start with logged on check

		$arRequiredPayload = \php_base\Control\AuthenticateController::getLoginRequiredVarsToCheck();
		$requiredVars = $this->getRequiredPayload( $arRequiredPayload);

		$isLoggedOn = $this->dispatcher->addProcess( 'control.AuthenticateController.CheckLogin', $requiredVars);

		if (! $isLoggedOn) {
			$this->dispatcher->addProcess( 'control.AuthenticateController.ForceLogin');

		}
		$this->dispatcher->addProcessPayload('control.HeaderController', $requiredVars);
		$this->dispatcher->addProcessPayload('control.FooterController', $requiredVars);

//Dump::dumplong( $this->dispatcher);
//
//$ar = $this->dispatcher;
//$s = Dump::arrayDisplayCompactor( $ar, array('payloads'));
//Dump::dumplong($s);

	}


	//-----------------------------------------------------------------------------------------------
	protected function getRequiredPayload( $arRequiredVars){
		$arOfVars = array();
		foreach( $arRequiredVars as $varName){
			$arOfVars[$varName] = $this->isVarInRequestOrSession( $varName);
		}
		return $arOfVars;
	}


	//-----------------------------------------------------------------------------------------------
	protected function isVarInRequestOrSession( $varName){
		if ( !empty($_REQUEST) and !empty( $_REQUEST[ $varName])) {
			return  $_REQUEST[ $varName];
		} else if (!empty($_SESSION) and !empty($_SESSION[$varName])) {
			return $_SESSION[$varName];
		} else {
			return false;
		}
	}


	//-----------------------------------------------------------------------------------------------
	public function decodeRequestinfo(){
		if ($this->hasNoPassedInfo()){
			$this->process = 'Login';
			return;
		}

		$this->determineProcess();

	}

	//-----------------------------------------------------------------------------------------------
	function hasNoPassedInfo(){
		if ( !empty( $_REQUEST) and  !empty($_REQUEST[DESTINATION])){
			return true;
		}
		return false;
	}

	//-----------------------------------------------------------------------------------------------
	function determineProcess() {

	}

	//-----------------------------------------------------------------------------------------------


}

