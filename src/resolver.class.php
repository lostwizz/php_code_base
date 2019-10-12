<?php

//namespace whitehorse\MikesCommandAndControl2\Resolver;
namespace whitehorse\MikesCommandAndControl2;

use \whitehorse\MikesCommandAndControl2\Settings\Settings as Settings;
use \whitehorse\MikesCommandAndControl2\Utils\Dump\Dump as Dump;

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
			Settings::GetRunTime('MessageLog')->addNotice( 'resolver got a false' );
			Settings::GetPublic('FileLog')->addNotice( 'resolver got a false');
		}

	}

	//-----------------------------------------------------------------------------------------------
	protected function doLoginWork(){
				// always start with logged on check

		$arRequiredPayload = \whitehorse\MikesCommandAndControl2\Control\AuthenticateController::checkLoginRequiredVars();
		$requiredVars = $this->getRequiredPayload( $arRequiredPayload);
		$isLoggedOn = $this->dispatcher->addProcess( 'control.AuthenticateController.CheckLogin', $requiredVars);

		if (! $isLoggedOn) {
			$this->dispatcher->addProcess( 'control.AuthenticateController.ForceLogin');
		}
		$this->dispatcher->addProcessPayload('control.HeaderController', $requiredVars);
		$this->dispatcher->addProcessPayload('control.FooterController', $requiredVars);

Dump::dumplong( $this->dispatcher);

$ar = $this->dispatcher;
$s = Dump::arrayDisplayCompactor( $ar, array('payloads'));
Dump::dumplong($s);

	}

	protected function getRequiredPayload($arRequiredVars){
		$arOfVars = array();
		foreach( $arRequiredVars as $varName){
			if ( !empty($_REQUEST) and !empty( $_REQUEST[ $varName])) {
				$arOfVars[$varName] = $_REQUEST[ $varName];
			} else if (!empty($_SESSION) and !empty($_SESSION[$varName])) {
				$arOfVars[$varName] = $_SESSION[$varName];
			} else {
				$arOfVars[$varName] = false;
			}
		}
		return $arOfVars;
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

