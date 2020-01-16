<?php

/** * ********************************************************************************************
 * Response.class.php
 *
 * Summary maintains 3 queues (Pre/Dispatcher/Post) and executes thing in the queues.
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description.
 * maintains 3 queues and then executes them in order -- and checks the response of the execution
 *    and may abort or continue on processing.
 *
 *
 *
 * @package ModelViewController - Dispatcher
 * @subpackage Dispatcher
 * @since 0.3.0
 *
 * @example
 *        $r = $this->dispatcher->do_work($this);
 *
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\Utils;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

/** * ********************************************************************************************
 * a class to pass the status back up the call tree
 * and give the chance to hanlde warnings instead of a true false type reurn
 */
Class Response {

	protected $process = null;
	protected $task = null;
	protected $action = null;
	protected $payload = null;
	protected $message;
	protected $errNum;  //   >=0 all is good - positive numbers are good - negative numbers are bad
	protected $shouldThrow = false;
	protected $exceptionToThrow;
	protected $failSilently = false;
	protected $canContinue = false;
	protected $continueProcess;
	protected $continueTask;
	protected $continueAction;
	protected $continuePayload;

	/** -----------------------------------------------------------------------------------------------
	 * construct the message
	 * @param string $message
	 * @param int $errno
	 * @param bool $canContinue
	 * @param type $failSilently
	 */
	public function __construct(string $message, int $errno, bool $canContinue = false, $failSilently = false) {
		$this->setMessage($message, $errno, $canContinue, $failSilently);
	}

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';
	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}


	/** -----------------------------------------------------------------------------------------------
	 * quick way to give a no error message
	 * @return \php_base\Utils\Response
	 */
	public static function NoError():self {
		return new Response('ok', 0, true);
	}

	/** -----------------------------------------------------------------------------------------------
	 * quick way to give a generic warning
	 * @return \php_base\Utils\Response
	 */
	public static function GenericWarning() :self{
		return new Response('Generic Warning', -1, true);
	}

	/** -----------------------------------------------------------------------------------------------
	 * a quick way of giving a generic error
	 * @return \php_base\Utils\Response
	 */
	public static function GenericError() :  self{
		return new Response('Generic Error', -2, true);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $which
	 * @return \php_base\Utils\Response
	 */
	public static function PermissionsError( string $which) : Response{
		return new Response('Permissions Error: ' . $which, ResponseErrorCodes::PermissionsError , true );
	}

	/** -----------------------------------------------------------------------------------------------
	 * quick way to give a todo error
	 * give a TODO error message (just a notification that some code still needs to be writen
	 * @return \php_base\Utils\Response
	 */
	public static function TODO_Error() : self{
		return new Response('- TODO -', ResponseErrorCodes::TODO);
	}

	/** -----------------------------------------------------------------------------------------------
	 * set the process/task/action/payload
	 * @param type $process
	 * @param type $task
	 * @param type $action
	 * @param type $payload
	 */
	public function setProcessTaskActionPayload(string $process, string $task, ?string $action = null, $payload = null) :void{
		$this->process = $process;
		$this->task = $task;
		$this->action = $action;
		$this->payload = $payload;
	}

	/** -----------------------------------------------------------------------------------------------
	 * set the message up
	 * @param type $message
	 * @param type $errNum
	 * @param type $canContinue
	 * @param type $failSilently
	 */
	public function setMessage(string $message, ?int $errNum = null, ?bool $canContinue = null, ?bool $failSilently = null) :void {
		$this->message = $message;
		if ( !is_null($errNum)) {
			$this->errNum = $errNum;
		}
		if ( !is_null( $canContinue)){
			$this->canContinue = $canContinue;
		}
		if ( !is_null( $failSilently)){
			$this->failSilently = $failSilently;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * set this as a continue processing type error
	 * @param type $canContinue
	 * @param type $cProcess
	 * @param type $cTask
	 * @param type $cAction
	 * @param type $cPayload
	 */
	public function setContinue( bool $canContinue, ?string $cProcess = null, ?string $cTask = null, ?string $cAction = null, $cPayload = null) :void{
		$this->canContinue = $canContinue;
		if ( !is_null($cProcess)) {
			$this->continueProcess = $cProcess;
		}
		if (!is_null( $cTask)){
			$this->continueTask = $cTask;
		}
		if (!is_null($cAction)) {
			$this->continueAction = $cAction;
		}
		if (!is_null($cPayload)) {
			$this->continuePayload = $cPayload;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * set this as an exception
	 * @param type $shouldThrow
	 * @param type $exception
	 */
	public function setException(bool $shouldThrow = true, \Exception $exception = null)  :void{
		$this->shouldThrow = $shouldThrow;
		$this->exceptionToThrow = $exception;
	}


	/** -----------------------------------------------------------------------------------------------
	 * is this a fatal error
	 * @return type
	 */
	public function hadError() :bool {
		return (($this->errNum < -1) or $this->shouldThrow);
	}

	/** -----------------------------------------------------------------------------------------------
	 * is the a noisy fail or silent fail - silent means dont output anything
	 * @return type
	 */
	public function failNoisily() :bool{
		return !$this->failSilently;
	}

	/** -----------------------------------------------------------------------------------------------
	 * is this a recoverable error
	 * @return boolean
	 */
	public function hadRecoverableError() :bool{
		if ($this->hadError()) {
			return false;
		} else {
			return ($this->errNum > 1 );
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * give the message
	 * @return type
	 */
	public function giveMessage() {
		//return $this->message . '(' . $this->errNum . ')';
		return $this->message;
	}



	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return type
	 */
	public function giveErrorCode() {
		return $this->errNum;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array
	 */
	public function giveArrayOfEverything(): array {
		return get_object_vars($this);
	}


	/** -----------------------------------------------------------------------------------------------
	 * give an array with process/task/activity/payload
	 * @return type
	 */
	public function giveProcessTaskActivityPayload() : ?array{
		if (!empty($this->process)) {
			return array($this->process, $this->task, $this->action, $this->payload);
		} else {
			return null;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives an array with a continue message and the process/task/activity/payload
	 * @return type
	 */
	public function giveContinueProcessTaskActivityPayload() {
		if ($this->canContinue and ! empty($this->continueProcess)) {
			return array($this->continueProcess, $this->continueTask, $this->continueAction, $this->continuePayload);
		} else {
			return null;
		}
	}

	public function toString() {
		return $this->__toString();
	}

	/** -----------------------------------------------------------------------------------------------
	 * convert to a string
	 * @return string
	 */
	public function __toString() {
		if (!empty($this->message) and $this->errNum != 0) {
			return 'Response: ' . $this->errNum . ': ' . $this->message;
		} else {
			return 'Response: No Error: 0';
		}
	}

}
