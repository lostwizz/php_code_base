<?php

/** * ********************************************************************************************
 * resolver.class.php
 *
 * Summary (no period for file headers)
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description.
 * this class handles the interaction between what the user enters and what the rest
 *    of the server does - it handles the POST/GET responses and passes the Dispatcher
 *    the Queue items. Process/Task/Action/Payload (PTAP)
 *
 *
 * @link URL
 *
 * @package ModelViewController - Resolver
 * @subpackage Resolver
 * @since 0.3.0
 *
 * @example
 *          // now start everything running
 *               $resolver = new Resolver();
 *                $response = $resolver->doWork();
 *
 * @see elementName
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Response as Response;
use \php_base\Control\MenuController as MenuController;

use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\DebugHandler as DebugHandler;

/** * **********************************************************************************************
 * takes the input and makes a process/task/action out of it and Dispatcher executes
 *
 * Description.
 *
 * @since 0.0.2
 */
class Resolver {

	/**
	 * the constants are used in the submit  and hidden html to know what things were passed.
	 *
	 * @since 0.0.2
	 * @var string REQUEST_PROCESS  the name used for passing the Process thru the input pages.
	 * @var string REQUEST_TASK  the name used for passing the Task thru the input pages.
	 * @var string REQUEST_ACTION  the name used for passing the Action thru the input pages.
	 * @var string REQUEST_PAYLOAD  passing the payload around to keep it available.
	 */
	const REQUEST_PROCESS = 'ACTION_PROCESS';
	const REQUEST_TASK = 'ACTION_TASK';
	const REQUEST_ACTION = 'ACTION_ACTION';
	const REQUEST_PAYLOAD = 'ACTION_PAYLOAD';

	/**
	 *
	 * @var string $process  holds the current process.
	 * @var string $task holds the current task.
	 * @var string $action holds the current action.
	 * @var string $payload holds the current payload.
	 */
	public $process = null;
	public $task = null;
	public $action = null;
	public $payload = null;

	/**
	 *
	 * @var object of Dispatcher - the dispatcher instance need to send all the Process/Task/Action/Payload (PTAP) down to execute.
	 *
	 */
	public $dispatcher;

		/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';


	/** -----------------------------------------------------------------------------------------------
	 * object constructor.
	 *
	 * Creates a new Dispatcher object - which will execute the PTAP.
	 *
	 * @since 0.0.2
	 *
	 */
	public function __construct() {
		if ( Settings::GetPublic('IS_DETAILED_RESOLVER_DEBUGGING')){
			Settings::setRunTime('RESOLVER_DEBUGGING' ,Settings::GetRunTimeObject('MessageLog'));
		}
		Settings::getRunTimeObject('RESOLVER_DEBUGGING')->addAlert('constructor for resolver');

		//Settings::SetRunTime('RESOLVER_CLASS', $this);

		$this->dispatcher = new Dispatcher();
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}

	/** -----------------------------------------------------------------------------------------------
	 * doWork is the default method to use when calling this class object.
	 *
	 * sets up the initial PTAP to be run
	 *          - the first thing is always the MVCD for the Header
	 *          - the second thing is always verify the login in detail (or create a sign in page, or verify they are already logged on.
	 *          - sets the last task as the footer - but errors before the footer might cause the footer to never be called
	 * @since x.x.x
	 *
	 * @return Response Object - passes back up the food change any errors or successes
	 */
	public function doWork(): Response {

		if (Settings::GetPublic('IS_DEBUGGING')) {
			//dump::dumpLong( $_REQUEST);
			Dump::dumpLong(filter_input_array(\INPUT_POST, \FILTER_SANITIZE_STRING));
			Dump::dumpLong(filter_input_array(\INPUT_GET, \FILTER_SANITIZE_STRING));
			dump::dumpLong($_SESSION);
			//dump::dump( session_id());
		}

		$this->AddHeader();
		$this->AddFooter();

		$this->AddSetupAuthenticateCheck();  // always start with login checks

		$this->AddSetupUserRoleAndPermissions(); // after they have logged in now setup the user permissions

		$this->addMenu();

		$this->decodeRequestInfo();

		$this->SetupDefaultController();  // this would usually be the menu starter
		// $r should be a ResponseClass
		$r = $this->StartDispatch();
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 * StartDispatch
	 *
	 * this is where the dispatcher gets called to run -- and any errors are passed back up the chain
	 *
	 * @since 0.0.2
	 *
	 * @see Dispatcher Class
	 * @return Response Object - passes any success or failures up the chain
	 */
	protected function startDispatch(): Response {

		$r = $this->dispatcher->doWork($this);
		if ($r->hadError()) {
			//echo 'Loggon failed';
			Settings::GetRunTimeObject('MessageLog')->addNotice('resolver got: ' . $r);
			Settings::GetRuntimeObject('FileLog')->addNotice('resolver got:' . $r);
			return $r;
		}

		//Settings::GetRunTimeObject('MessageLog')->addNotice('resolver got a true');
		//Settings::GetRuntimeObject('FileLog')->addNotice('resolver got a true');
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 * setupDefaultController - if no PTAP is setup something outside the PRE or POST task then it will run this
	 *                            - usually a menu system would here
	 *
	 * creates and adds a PTAP to the dispatcher queue
	 *
	 * @since 0.0.3
	 *
	 * @see Dispatcher
	 */
	protected function setupDefaultController(): void {
		//if ( $this->dispatcher->getProcessQueueCount() <1) {
		if (!empty(Settings::GetRunTime('Currently Logged In User') )) {
			///$process = 'TEST';
			//$task = 'doWork';
			//$action = null;
			Settings::GetRunTimeObject('MessageLog')->addTODO('figure out what the default process is - probably menu system');

//			$process = 'UserRoleAndPermissions';
//			$task = 'doEdit';
//			$action = null;

			$payload = ['username' => Settings::GetRunTime('Currently Logged In User')];
			$this->addMenu($payload);
			//$process = 'Menu';
			//$task = 'doWork';
			//$action = null;

			//$this->dispatcher->addProcess($process, $task, $action, $payload);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * decodeRequestInfo - take what was passed thru GET/POST and add it to the dispacher queue.
	 *
	 * takes any info passed back in a GET/POST and validates it then gets dispatcher to add it to a queue
	 *
	 * @since 0.0.2
	 *
	 * @see Dispatcher
	 */
	public function decodeRequestInfo(): void {

		$PTAP = $this->decodeINPUTvars();


		/** if the GET/POST are not an Authenticate PTAP then do what they are
				as the checkAuthenticate is added below 		 */
		if (!( $PTAP['process'] == 'Authenticate' and $PTAP['task'] == 'checkAuthentication' )) {
			$this->dispatcher->addProcess($PTAP['process'], $PTAP['task'], $PTAP['action'], $PTAP['payload']);
		}
	}


	protected function decodeINPUTvars() {

		$postVars = \filter_input_array(\INPUT_POST, \FILTER_SANITIZE_STRING);

		$getVars =  \filter_input_array(\INPUT_GET, \FILTER_SANITIZE_STRING);

		$PTAP = array();

		$PTAP['process'] = (!empty($postVars[self::REQUEST_PROCESS])) ? $postVars[self::REQUEST_PROCESS] : null;
		$PTAP['task'] = (!empty($postVars[self::REQUEST_TASK])) ? $postVars[self::REQUEST_TASK] : null;
		$PTAP['action'] = (!empty($postVars[self::REQUEST_ACTION])) ? $postVars[self::REQUEST_ACTION] : null;
		$PTAP['payload'] = (!empty($postVars[self::REQUEST_PAYLOAD])) ? $postVars[self::REQUEST_PAYLOAD] : null;

		if ( empty($PTAP['process']) and !empty( $getVars[ MenuController::GET_TERM])) {
			$x = $getVars[MenuController::GET_TERM];
			$exploded = \explode('.', $x);
			$PTAP['process'] =	(!empty($exploded[0])) ? $exploded[0] : null;
			$PTAP['task'] =		(!empty($exploded[1])) ? $exploded[1] : null;
			$PTAP['action'] =	(!empty($exploded[2])) ? $exploded[2] : null;
			$PTAP['payload'] =	(!empty($exploded[3])) ? $exploded[3] : null;
		}
		return $PTAP;
	}



	/** -----------------------------------------------------------------------------------------------
	 * addSetupUserRoleAndPermissions - after a successful logon then setup the users permissions.
	 *
	 * causes a task to run which sets up the user permissions and gets dispatcher to add it to a queue
	 *
	 * @since 0.0.8
	 *
	 * @see Dispatcher and UserRoleAndPermission classes
	 */
	protected function addSetupUserRoleAndPermissions(): void {
		$process = 'UserRoleAndPermissions';
		$task = 'Setup';
		$action = null;
		$payload = null;
		$this->dispatcher->addPREProcess($process, $task, $action, $payload);
	}

	/** -----------------------------------------------------------------------------------------------
	 * addSetupAuthenticateCheck - always start with logged on check
	 *
	 *   always start with logged on check
	 *     now false cases -
	 *                nothing passed so show logon form
	 *                loggin on for the first time
	 *                sucessfull
	 *                unsucsessful
	 *  have already logged on and just check session? still good
	 * already logged in and timed out OR some other reason they should login again
	 *
	 * @since 0.0.6
	 *
	 * @see Dispatcher
	 */
	protected function addSetupAuthenticateCheck(): void {
		$postVars = filter_input_array(\INPUT_POST, \FILTER_SANITIZE_STRING);

		$process = 'Authenticate';
		$task = 'checkAuthentication';
		$action =null;

		$payload = (!empty($postVars[self::REQUEST_PAYLOAD])) ? $postVars[self::REQUEST_PAYLOAD] : array();
		if ( !empty($postVars[self::REQUEST_ACTION ])){
			$payload[self::REQUEST_ACTION] = $postVars[self::REQUEST_ACTION ];
		}

		$sPayload = \serialize( $payload);
//		Settings::GetRunTimeObject('MessageLog')->addNotice('adding to preQueue ' . $process . '.'  . $task .  '.' . $action . $sPayload);
		$this->dispatcher->addPREProcess($process, $task, $action, $payload);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $payload
	 * @return void
	 */
	public function addMenu($payload = null ): void {
		$process = 'Menu';
		$task = 'doWork';
		$action = null;

		$this->dispatcher->addPREProcess($process, $task, $action, $payload);
	}

	/** -----------------------------------------------------------------------------------------------
	 * addHeader - outputs the header - including html version style sheets java script etc.
	 *
	 * adds the header to the PRE Dispatcher queue
	 *
	 * @since 0.0.7
	 *
	 * @see Dispatcher HeaderController
	 */
	protected function addHeader(): void {
		$process = 'Header';
		$task = 'doWork';
		$action = null;
		$payload = null;

		$this->dispatcher->addPREProcess($process, $task, $action, $payload);
	}

	/** -----------------------------------------------------------------------------------------------
	 * addFooter - adds the footer PTAP to the dispatch post queue
	 *
	 * adds footer to the dispatcher post queue
	 *
	 * @since 0.0.5
	 *
	 * @see Dispatcher FooterController

	 */
	protected function addFooter(): void {
		$process = 'Footer';
		$task = 'doWork';
		$action = null;
		$payload = null;

		$this->dispatcher->addPOSTProcess($process, $task, $action, $payload);
	}



	private function debugy( int $ref, $msg, $var=null, $level = DebugHandler::NOTICE){
		if(  Settings::GetPublic('IS_DETAILED_RESOLVER_DEBUGGING')) {
			$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS , 2);
			$s = Utils::backTraceHelper($bt, 0);
			$s = '     - ' . $s;

			if ( is_a($var, 'php_base\Utils\Response')) {
				$v = empty($var) ? '' : $var->toString() ;

				if ( $var->hadError() ){
					$level = DebugHandler::EMERGENCY;
				} else {
					$level = DebugHandler::INFO;
				}
			}
		}

	}


}
