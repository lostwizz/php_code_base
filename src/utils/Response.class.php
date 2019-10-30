<?php
//**********************************************************************************************
//* Response.class.php
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

namespace php_base\Utils;


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;


	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------
//***********************************************************************************************
//***********************************************************************************************
Class Response {



	protected $process = null;
	protected $task =null;
	protected $action = null;
	protected $payload = null;

	protected $message;
	protected $errNum;  //   >=0 all is good - positive numbers are good - negative numbers are bad

	protected $shouldThrow =false;
	protected $exceptionToThrow;

	protected $failSilently =false;

	protected $canContinue =false;
	protected $continueProcess;
	protected $continueTask;
	protected $continueAction;
	protected $continuePayload;

	//-----------------------------------------------------------------------------------------------
	public function __construct( string $message, int $errno, bool $canContinue= false, $failSilently=false ){
		$this->setMessage( $message, $errno, $canContinue, $failSilently);
	}


	//-----------------------------------------------------------------------------------------------
	public static function NoError() {
		return new Response('ok', 0, true);
	}

	//-----------------------------------------------------------------------------------------------
	public static function GenericWarning(){
		return new Response('Generic Warning', -1, true);
	}

	//-----------------------------------------------------------------------------------------------
	public static function GenericError(){
		return new Response('Generic Error', -2, true);
	}

   public static function TODO_Error(){
      return new Response('- TODO-', ResponseErrorCodes::TODO);
   }

	//-----------------------------------------------------------------------------------------------
	public function setProcessTaskActionPayload( $process, $task, $action=null, $payload=null){
		$this->process = $process;
		$this->task = $task;
		$this->action = $action;
		$this->payload = $payload;
	}

	//-----------------------------------------------------------------------------------------------
	public function setMessage($message, $errNum = -1, $canContinue = false, $failSilently=false){
		$this->message = $message;
		$this->errNum = $errNum;
		$this->canContinue = $canContinue;
		$this->failSilently = $failSilently;
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
	public function failNoisily() {
		return ! $this->failSilently;
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

	//-----------------------------------------------------------------------------------------------
	public function toString() {
		if ( !empty($this->message) and $this->errNum !=0) {
			return 'Response: ' . $this->errNum . ': ' . $this->message;
		} else {
			return 'Response: No Error: 0';
		}
	}





}
