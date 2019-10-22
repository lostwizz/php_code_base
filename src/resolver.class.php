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

		if ( Settings::GetPublic('IS_DEBUGGING') ) {
			Dump::dump($_REQUEST);
		}

		$this->AddHeader();
		$this->AddFooter();

		$this->SetupAuthenticateCheck();		// always start with login checks

		$this->decodeRequestinfo();

		$this->SetupDefaultController();    // this would usually be the menu starter

		$r = $this->StartDispatch();
		return $r;
	}

	//-----------------------------------------------------------------------------------------------
	protected function StartDispatch(){

		$r = $this->dispatcher->do_work( $this);


		if ( $r[0] == false ){
			//echo 'Loggon failed';
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'resolver got a false on pre' );
			Settings::GetRuntimeObject('FileLog')->addNotice( 'resolver got a false on pre');
			return false;
		}
		if ($r[2]== false){
			//echo 'Post Queue failed? footer?';
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'resolver got a false on post' );
			Settings::GetRuntimeObject('FileLog')->addNotice( 'resolver got a false on post');
			return false;
		}

		if ( $r[1] == false){
			//echo 'the dispach queue got a false';
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'resolver got a false on dispatchQ' );
			Settings::GetRuntimeObject('FileLog')->addNotice( 'resolver got a false on dispatchQ');

		} else {
			//echo 'all seems good to the resolver';
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'resolver got a true' );
			Settings::GetRuntimeObject('FileLog')->addNotice( 'resolver got a true');
			return true;
		}

	}

	//-----------------------------------------------------------------------------------------------
	protected function SetupDefaultController(){
		$process = 'TEST';
		$task = 'doWork';
		$action = null;
		$payload = [ 'username'=> Settings::GetRunTime( 'Currently Logged In User' )];
		$this->dispatcher->addProcess( $process, $task, $action, $payload);

	}



	//-----------------------------------------------------------------------------------------------
	public function decodeRequestinfo(){

		$process = ( ! empty($_REQUEST[self::REQUEST_PROCESS] )) ? $_REQUEST[self::REQUEST_PROCESS] : null;
		$task =    ( ! empty($_REQUEST[self::REQUEST_TASK]    )) ? $_REQUEST[self::REQUEST_TASK]    : null;
		$action =  ( ! empty($_REQUEST[self::REQUEST_ACTION]  )) ? $_REQUEST[self::REQUEST_ACTION]  : null;
		$payload = ( ! empty($_REQUEST[self::REQUEST_PAYLOAD] )) ? $_REQUEST[self::REQUEST_PAYLOAD] : null;

		if ( ! ( $process == 'Authenticate' and $task =='CheckLogin' )){
			$this->dispatcher->addProcess( $process, $task, $action, $payload);
		} else {
			//already have an authenticate.checkLogin task - dont need another
		}
	}


	//-----------------------------------------------------------------------------------------------
		// always start with logged on check
		// now false cases -
				// nothing passed so show logon form
				//  loggin on for the first time
						// sucessfull
						// unsucsessful
				// have already logged on and just check session? still good
						// have already logged in and timed out OR some other reason they should login again
	protected function SetupAuthenticateCheck(){

		$payload = ( ! empty($_REQUEST[self::REQUEST_PAYLOAD] )) ? $_REQUEST[self::REQUEST_PAYLOAD] : array();
		$process ='Authenticate';
		$task = 'CheckLogin';

		if ($this->hasNoPassedInfo()) {
			$action = 'Need_Login';
			$payload = array_merge( $payload, array('authAction'=> $action));
		} else  if ( $this->passingFirstTimeCredentials()){
			$action = 'do_the_logon_attempt';
			$payload = array_merge( $payload, array('authAction'=> $action));
		} else if ( $this->passingOngoingDetails()){
			$action = 'Check_Ongoing_Connection';
			$payload = array_merge( $payload, array('authAction'=> $action));
		} else if ($this->isChangePasswordRequest()) {

		} else if ($this->isForgotPasswordRequest()){


		} else if($this->isSignupRequest()){

		} else {
			$action ='no_Credentials';
			$payload = array_merge( $payload, array('authAction'=> $action));
		}

		$this->dispatcher->addPREProcess($process, $task, $action, $payload);
	}

	//-----------------------------------------------------------------------------------------------
	function isChangePasswordRequest(){
		return false;
	}

	//-----------------------------------------------------------------------------------------------
	function isForgotPasswordRequest(){
		return false;

	}

	//-----------------------------------------------------------------------------------------------
	function isSignupRequest(){
		return false;

	}


	//-----------------------------------------------------------------------------------------------
	function hasNoPassedInfo(){
		if ( empty( $_REQUEST) or empty($_REQUEST[self::REQUEST_PROCESS])){
			return true;
		}
		return false;
	}

	//-----------------------------------------------------------------------------------------------
	protected function passingOngoingDetails(){
		if ( !empty( $_REQUEST)
		 and !empty( $_REQUEST['payload'])
		 and !empty($_REQUEST['payload']['credentials'])
		 and !empty($_REQUEST['payload']['credentials']['username'])){

			//////////!!!!!! and anything else that needs to be passes in an ongoin session - maybe session id?
			Settings::GetRunTimeObject('MessageLog')->addNotice('ongoing details true');
			return true;
		} else {
			Settings::GetRunTimeObject('MessageLog')->addNotice('ongoing details false');
			return false;
		}
	}

	//-----------------------------------------------------------------------------------------------
	protected function passingFirstTimeCredentials() {
		 if ( !empty( $_REQUEST[self::REQUEST_PROCESS])
		  and $_REQUEST[self::REQUEST_PROCESS] == 'Authenticate'
		  and ! empty($_REQUEST[self::REQUEST_TASK])
		  and $_REQUEST[self::REQUEST_TASK] == 'CheckLogin'
		 ) {
		 	Settings::GetRunTimeObject('MessageLog')->addNotice('firsttime login true');
		 	return true;
		 } else {
		 	Settings::GetRunTimeObject('MessageLog')->addNotice('firsttime login false');
		 	return false;
		 }
	}



	//-----------------------------------------------------------------------------------------------
	protected function AddHeader(){
		$process ='Header';
		$task = 'doWork';
		$action = null;
		$payload =null;

		$this->dispatcher->addPREProcess( $process, $task, $action, $payload);
	}

	//-----------------------------------------------------------------------------------------------
	protected function AddFooter(){
		$process ='Footer';
		$task = 'doWork';
		$action = null;
		$payload =null;

		$this->dispatcher->addPOSTProcess( $process, $task, $action, $payload);
	}



}



	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------
//***********************************************************************************************
//***********************************************************************************************
class Response {
	protected $process = null;
	protected $task =null;
	protected $action = null;
	protected $payload = null;

	protected $message;
	protected $errNum;  //   >=0 all is good - positive numbers are good - negative numbers are bad

	protected $shouldThrow =false;
	protected $exceptionToThrow;

	protected $canContinue =false;
	protected $continueProcess;
	protected $continueTask;
	protected $continueAction;
	protected $continuePayload;

	//-----------------------------------------------------------------------------------------------
	public function __construct( $message, $errno, $canContinue= false ){
		$this->setMessage( $message, $errno, $canContinue);
	}

	//-----------------------------------------------------------------------------------------------
	public function setProcessTaskActionPayload( $process, $task, $action=null, $payload=null){
		$this->process = $process;
		$this->task = $task;
		$this->action = $action;
		$this->payload = $payload;
	}

	//-----------------------------------------------------------------------------------------------
	public function setMessage($message, $errNum = -1, $canContinue = false){
		$this->message = $message;
		$this->errNum = $errNum;
		$this->canContinue = $canContinue;
	}

	//-----------------------------------------------------------------------------------------------
	public function setContinue($canContinue, $cProcess=null, $cTask =null, $cAction=null, $cPayload = null){
		$this->canContinue = $canContinue;
		$this->continueProcess = $cProcess;
		$this->continueTask = $cTask;
		$this->continueAction = $cAction;
		$this->continuePayload = $cPayload;
	}

	//-----------------------------------------------------------------------------------------------
	public function setException($shouldThrow = true, $exception=null){
		$this->$shouldThrow = $shouldThrow;
		$this->$exception = $exception;
	}

	//-----------------------------------------------------------------------------------------------
	public function hadFatalError(){
		return (($this->errNum<-1) or $this->shouldThrow);
	}

	//-----------------------------------------------------------------------------------------------
	public function hadRecoverableError(){
		if ( $this->hadFatalError() ){
			return false;
		} else {
			return ($this->errNum >1 );
		}
	}

	//-----------------------------------------------------------------------------------------------
	public function giveMessage(){
		return $this->message . '(' . $this->errNum . ')';
	}

	//-----------------------------------------------------------------------------------------------
	public function giveProcessTaskActivityPayload(){
		if (!empty($this-process)) {
			return array( $this->process, $this->task, $this->activity, $this->payload );
		} else {
			return null;
		}
	}

	//-----------------------------------------------------------------------------------------------
	public function giveContinueProcessTaskActivityPayload(){
		if ( $this->canContinue and ! empty( $this->continueProcess)) {
			return array( $this->continueProcess, $this->continueTask, $this->continueActivity, $this->continuePayload );
		} else {
			return null;
		}
	}

}

//***********************************************************************************************
//***********************************************************************************************
abstract class ResponseErrorCodes {
	protected static $errors = array(
			2 => 'All is good',
			1 => 'Generic Warning all is good',
			0 => 'Not an Error',
			-1 => 'Generic Warning something might be wrong',
			-2 => 'Generic Error',
		);


	public static function giveErrorMessage( $errNo){
		if ( array_key_exists( $errNo, self::$errors )){
			return self::$errors[$errNo];
		} else {
			return '-Unknown Error Code-';
		}
	}
}
