<?php

/** * ********************************************************************************************
 * dispatcher.class.php
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
//namespace whitehorse\MikesCommandAndControl2\Dispatcher;

namespace php_base;

use \php_base\Utils\DebugHandler as DebugHandler;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Utils as Utils;
use \php_base\Utils\Cache as CACHE;
use \php_base\Utils\SubSystemMessage as SubSystemMessage;

//use \php_base\utils\MessageLog as MessageLog;
//use \php_base\utils\AMessage as AMessage;
//use \php_base\utils\MessageBase as MessageBase;

/** * **********************************************************************************************
 *  Dispatcher executes items in the queue.
 *
 * Description.
 *     3 queues - pre, dispatcher, post - executes and checks responses
 *
 * @since 0.0.2
 */
class Dispatcher {

	/**
	 * the 3 different queues - and none
	 */
	const NONE = 0;
	const PRE = 1;
	const DISPATCH = 2;
	const POST = 3;

	public $currentQueue;          // which queue is currently being used

	/**
	 *
	 * @var \SplQueue - these are the 3 queues to execute
	 *    PREqueue - executed first  (usually contains header, authentication and permission management
	 *    DISPATCHqueue - executes after pre and before post
	 *    POSTqueue - executes after Dispatch (usualy contain footer and message box output) - and is a stack (FILO so thing can bedone here before the footer
	 *
	 */
	protected $PREqueue;
	protected $POSTqueue;   //  \SplStack
	protected $DISPATCHqueue;



	//* use for unit testing
	public $PHPUNIT_tempArray = array();


	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';


	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	public function __construct() {
		Settings::GetRunTimeObject('DISPATCHER_DEBUGGING')->addInfo('dispatcher constructor ');

		$this->currentQueue = dispatcher::NONE;

		$this->PREqueue = new \SplQueue();
		//$this->POSTqueue = new \SplQueue();
		$this->POSTqueue = new \SplStack();   ////////new \SplQueue();
		$this->DISPATCHqueue = new \SplQueue();

		Settings::SetRuntime('Dispatcher', $this);
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
	 * give the queue/stack wanted
	 *
	 * @param string $whichQueue
	 * @return \SpqQueue
	 */
	public function getQueue( string $whichQueue ='PRE') : \SplQueue {
		switch ($whichQueue) {
			case 'PRE':
				return $this->PREqueue;
			case 'POST':
				return $this->POSTqueue;
			default:
			case 'DISPATCH':
				return $this->DISPATCHqueue;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * abort if anything returns FALSE
	 * @since 0.0.2

	 * @param type $parentResolver
	 * @return Response
	 */
	public function doWork($parentResolver = null): Response {

		$this->debug('dispatcher starting prequeue' );
		$this->currentQueue = dispatcher::PRE;
		$pre_result = $this->RunThruTheQueue($this->PREqueue);

		if ($pre_result->hadError()) {
			$this->debug('dispatcher got an error from the running pre queue' . $pre_result);
			return $pre_result;
		} else {
			// the pre queue contains authentication - so if it returns false then dont do any anything else
			$this->debug('dispatcher starting normal queue'   );

			$this->currentQueue = dispatcher::DISPATCH;						// setup to run the main queue
			$dispatch_result = $this->RunThruTheQueue($this->DISPATCHqueue);  // start on the main queue

			if ($dispatch_result->hadError()) {
				$this->debug('dispatcher got an error from the running normal queue' . $dispatch_result);
				return $dispatch_result;
			}
		}

		$this->debug('dispatcher starting postqueue');
		// show the post queue in all cases (it has the message stack for one- so you know what happend -  and the footer)
		$this->currentQueue = dispatcher::POST;
		$post_result = $this->RunThruTheQueue($this->POSTqueue);

		if ($post_result->hadError()) {
			$this->debug('dispatcher got an error from the running post queue' . $post_result);
			return $post_result;
		}

		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @since 0.0.2
	 * @param \SplQueue $theQueue - this is a common method for all 3 queues - so just pass the queue wanted to run
	 * @return Response
	 */
	protected function RunThruTheQueue( $theQueue): Response {
		$this->debug( 'the current Queue'  );

		try {
			$response = Response::NoError();
			while ( ! $theQueue->isEmpty()) {
				if (Settings::GetPublic('IS_DETAILED_DISPATCH_QUEUE_DEBUGGING') <= LVL_Notice_9 ){
					$this->dumpQueue($theQueue, true);
				}

				$response = $this->processDetailsOfQueue($theQueue);

				$this->debug(' result of running the Queue: ' , $response);
			}
			return $response;

		} catch (\Exception $e) {
			$enum = $e->getCode();
			if (!is_numeric($enum)) {
				$enum = -1;
			}
			$this->debug('exception in running the Queue');
			return new Response('exception in running the Queue: ' . $e->getMessage(),
					   (-1 * $enum),              // errors are negative in this system
					   false);
		}
	}


	/** -----------------------------------------------------------------------------------------------
	 *  gets the item out of the queue (or stack)
	 * @param type $theQueue
	 * @return type
	 */
	protected function getItemFromQueue( $theQueue) {
		$which = $this->identifyWhichQueue( $theQueue);
		switch ($which){
			case 'PRE':
				$item = $theQueue->dequeue();/** get the next item out of the queue */
				break;
			case 'POST':
				$item = $theQueue->pop();  // post is a stack not a queue - so footer is always executed last
				break;
			case 'DISPATCH':
			default:
				$item = $theQueue->dequeue();/** get the next item out of the queue */
				break;
		}
		return $item;
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $theQueue
	 * @return Response
	 */
	protected function processDetailsOfQueue($theQueue): Response {

		$item = $this->getItemFromQueue($theQueue);

		$this->debug('dispatcher executing [' . $item . '] 1');
		if (empty($item)) {
			$response = Response::GenericWarning();
			$this->debug('dispatcher - item is empty!! why!! -but ignoring', $response);
		} else {
			$response = $this->itemDecodeAndExecute($item);

			if ($response->hadError()) {
				$this->debug('dispatcher recieved an error:', $response);
			}
		}
		return $response;
	}

	/** -----------------------------------------------------------------------------------------------
	 * item Decode and Execute.
	 *
	 * breaks down the item (from the queue) and tries to execute it
	 *
	 * @since 0.0.2
	 * @param string $passedProcess - the process class to use - if empty then have no idea what to run
	 * @return Response class
	 */
	private function itemDecodeAndExecute(string $passedProcess = null): Response {
		if (empty($passedProcess)) {
			return true;
		}
		Settings::GetRunTimeObject('DISPATCHER_DEBUGGING')->addInfo('PTAP: ' . $passedProcess);

		$exploded = \explode('.', $passedProcess);

//		if (Settings::GetPublic('IS_DETAILED_DISPATCH_QUEUE_DEBUGGING')<= LVL_Notice_2 ){
//			dump::dumpLong($exploded, 'exploded', array('Beautify_BackgroundColor' =>'#EED6FE','FLAT_WINDOWS_LINES' => 50));
//		}

		$response = $this->doExecute(//'control',
									(empty($exploded[0]) ? null : $exploded[0]),
									(empty($exploded[1]) ? null : $exploded[1]),
									(empty($exploded[2]) ? '' : $exploded[2]),
									(empty($exploded[3]) ? null : $exploded[3])
									);

		return $response;

	}

	/** -----------------------------------------------------------------------------------------------
	 * item  Execute.
	 *
	 * takes the PTAP and tries to execute it
	 *    it first strips "Controller" from the process
	 *    then adds the namespace to the process
	 *
	 * @since 0.0.2
	 * @param string $dir - the namespace
	 * @param string $class
	 * @param string $task
	 * @param string $action
	 * @param mixed $passedPayload
	 * @return Response class
	 */
	private function doExecute(//string $dir,
							?string $class,
							?string $task,
							?string $action = '',
							$passedPayload = null
					): Response {




//		if (substr($class, -10) == 'Controller') {
//			$process = substr($class, 0, -10);
//		} else {
			$process = $class;
//		}
		if ( empty($task)) {
			$task = 'doWork';            /* the default task */
		}
		if ( empty($class)){
			throw new \Exception ( 'noname class can not be instantiized');
		}
		//$class = '\\php_base\\' . $dir . '\\' . $class;
		Settings::GetRunTimeObject('DISPATCHER_DEBUGGING')->addTODO('will have to change this from php_base:' . $class);

		try {

			$class = utils::checkClass($class);

			$payload = (!empty($passedPayload)) ? $this->processPayloadFROMItem($passedPayload) : null;

			Settings::GetRunTimeObject('DISPATCHER_DEBUGGING')->addInfo_2('dispatcher do execute - new ' .  Utils::makePTAPpretty( $class, $task, $action, $passedPayload));

			$this->addToHistory($process, $task, $action, $payload );

			$instance = new $class($process, $task, $action, $payload); //instanciate the process  and pass it the payload

			// now calls basically the task with this so it can look up the class and task
			$r = $instance->$task($this);  //run the process's method

			$this->debug( 'after running process got result', $r);
		} catch (\Exception $ex) {
			$r = new Response('something went wrong while trying a doExecute'. $ex->getMessage());
		}
		return $r;
	}



	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	protected function addToHistory( $process, $task = '', $action='', $payload=''){

		if ( \session_status() == PHP_SESSION_ACTIVE){
			$_SESSION['History'][] = Utils::makePTAPpretty( $process, $task, $action, $payload);
		}
	}



	/** -----------------------------------------------------------------------------------------------
	 * buildItem creates a PTAP item from the parameters
	 * this is where the dispatcher gets called to run -- and any errors are passed back up the chain
	 *
	 * the item is basically the PTAP with periods between them into one string
	 *   it will call for the payload to be serialized so it can be a string
	 *
	 * @since 0.0.2
	 *
	 * @see runthruqueue
	 *
	 * @param string $passedprocess
	 * @param string $passedtask
	 * @param string $passedaction  (may or man not exist
	 * @param mixed $passedpayload
	 *
	 * @return the generate item
	 */
	protected function buildItem($passedprocess,
							  $passedtask = null,
							  $passedaction = null,
							  $passedpayload = null
							): string {

		//$process = (!empty($passedprocess)) ? $passedprocess . 'Controller' : '';
		$process = (!empty($passedprocess)) ? $passedprocess  : '';
		$task =    (!empty($passedtask))    ? '.' . $passedtask : '.';
		$action =  (!empty($passedaction))  ? '.' . $passedaction : '.';

		$payload = (!empty($passedpayload)) ? '.' . $this->processPayloadForItem($passedpayload) : '';

		Settings::GetRunTimeObject('DISPATCHER_DEBUGGING')->addNotice_3( Utils::makePTAPpretty( $process, $task, $action, $payload));

		$item = $process . $task . $action . $payload;
		return $item;
	}

	/** -----------------------------------------------------------------------------------------------
	 * prossessPaylaodForItem  - encodes the parameter (usually the payload) to a string
	 *
	 * makes a string out of what ever passed
	 *
	 * @since 0.0.2
	 *
	 * @see serialize
	 *
	 * @param mixed $payload - the payload part of the PTAP
	 * @return the stringified version of the parameter
	 */
	protected function processPayloadForItem($payload): string {
		$newPayload = str_replace( '.', '~!~', $payload);    // unless this is done if there are any other periods in the value it screws up my decoding
		$r = serialize($newPayload);
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 * prossessPaylaodFROMItem  - decodes the parameter (usually the payload) to a from a string
	 *
	 * unserializes what ever was passed
	 *
	 * @since 0.0.2
	 *
	 * @see \php_base\Dispatcher Class
	 *
	 * @param mixed $payload - the payload part of the PTAP in a string form
	 * @return the unstringified version of the parameter
	 */
	protected function processPayloadFROMItem($payload) {
		$r = unserialize($payload);
		$newR = str_replace('~!~', '.', $r);   // unless this is done if there are any other periods in the value it screws up my decoding
		return $newR;
	}

	/** -----------------------------------------------------------------------------------------------
	 * addItemToQueue - takes an item and adds to the passed queue
	 *
	 * add item to the queue
	 *
	 * @since 0.0.2
	 *
	 * @see SPLQueue
	 *
	 * @param $q splqueue the queue to add the item to
	 * @param $item the item to add to the queue
	 */
	protected function addItemToQueue($q, $item): void {
		Settings::GetRunTimeObject('DISPATCHER_DEBUGGING')->addNotice_9('item ' . print_r($item, true));

		$which = $this->identifyWhichQueue( $q);
		switch ($which){
			case 'POST':
				$q->push($item);   // post is a stack not a queue (so footer is always executed last
				break;
			case 'POST':
				$q->enqueue($item);
				break;
			default:
			case 'DISPATCH':
				$q->enqueue($item);
				break;
		}

	}

	/** -----------------------------------------------------------------------------------------------
	 * addPREProcess - adds the PTAP to the pre queue
	 *
	 * creates the item out of the PTAP and then adds it to the preQueue
	 *
	 * @since 0.0.2
	 *
	 * @see SPLQueue
	 *
	 * @param $process
	 * @param $task
	 * @param $action
	 * @param $payload
	 */
	public function addPREProcess($process,
							   $task = null,
							   $action = null,
							   $payload = null): void {
		$item = $this->buildItem($process,
						   $task,
						   $action,
						   $payload);
		if ( $item != '..') {        // if the item is basically empty then dont add it
			$this->addItemToQueue($this->PREqueue,  $item);
			Settings::GetRunTimeObject('DISPATCHER_DEBUGGING')->addNotice_3( 'Added ' . $item . ' to the PRE Queue');
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * addPOSTProcess - adds the PTAP to the post queue
	 *
	 * creates the item out of the PTAP and then adds it to the postQueue
	 *
	 * @since 0.0.2
	 *
	 * @see SPLQueue
	 *
	 * @param $process
	 * @param $task
	 * @param $action
	 * @param $payload
	 */
	public function addPOSTProcess($process,
								$task = null,
								$action = null,
								$payload = null): void {
		$item = $this->buildItem($process,
						   $task,
						   $action,
						   $payload);
		if ( $item != '..') {                  // if the item is basically empty then dont add it
			$this->addItemToQueue($this->POSTqueue,  $item);
			Settings::GetRunTimeObject('DISPATCHER_DEBUGGING')->addNotice_3( 'Added ' . $item . ' to the POST Queue');
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * addProcess - adds the PTAP to the dispatch queue
	 *
	 * creates the item out of the PTAP and then adds it to the postQueue
	 *
	 * @since 0.0.2
	 *
	 * @see SPLQueue
	 *
	 * @param $process
	 * @param $task
	 * @param $action
	 * @param $payload
	 * @return void
	 */
	public function addProcess($process,
							$task = null,
							$action = null,
							$payload = null) :void {
		$item = $this->buildItem($process,
						   $task,
						   $action,
						   $payload);

		if ( $item != '..') {                      // if the item is basically empty then dont add it
			$this->addItemToQueue($this->DISPATCHqueue,  $item);
			Settings::GetRunTimeObject('DISPATCHER_DEBUGGING')->addNotice_3( 'Added ' . $item . ' to the Queue');
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return int
	 */
	public function getProcessQueueCount(): int {
		return $this->DISPATCHqueue->count();
	}

	/** -----------------------------------------------------------------------------------------------
	 * identifies which queue we are working with
	 * @param \SPLQueue $q
	 * @return string
	 */
	public function identifyWhichQueue($theQueue): string{

		$which = '[[not sure which queu]]';
		if ( $theQueue === $this->PREqueue) {
			$which = 'PRE';
		}
		if ( $theQueue === $this->POSTqueue) {
			$which = 'POST';
		}
		if ( $theQueue === $this->DISPATCHqueue) {
			$which = 'DISPATCH';
		}
		return $which;
	}

	/** -----------------------------------------------------------------------------------------------
	 * dumpQueue - outputs the items in the queue (does not remove them from the queue
	 *                - may cause issues if running thro the queue because it does rewind
	 *
	 * for debugging purposes it will show all the items in the queue
	 *
	 * @since 0.0.2
	 *
	 * @see SPLQueue
	 * @param \SPLQueue $theQueue - the queue to be shown
	 * @return void
	 */
	public function dumpQueue( $theQueue= null, bool $doEcho = true): string {
		if ( empty( $theQueue )){
			$theQueue = $this->DISPATCHqueue;
		}

		$which = $this->identifyWhichQueue( $theQueue);
		$s = '';
		$s .= '<BR>';
		$s .= '<pre class="pre_debug_queue">';
		$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[0];
		$s .= '--'  . __METHOD__ .  '-- called from ' . basename($bt['file']) . '(line: '. $bt['line'] . ')' ;
		$s .= '<BR>';

		$s .= '@@@@ - ' . $which . ' - queue dump -@@@@@@@@@ count=' . $theQueue->count() . '%%' . ($theQueue->isEmpty() ? 'empty' : 'Notempty') . ' %%%%%%%%%<BR>';
		$counter = 1;
		$theQueue->rewind();
		while ($theQueue->valid()) {
			$s .= '&nbsp;&nbsp;&nbsp;';
			$s .= $counter++ . ') ';
			$s .= $theQueue->current();  //;."-\n"; // Show the first one
			$s .= '<br>';
			$theQueue->next(); // move the cursor to the next element
		}
		$theQueue->rewind();
		$s .= '@@@@@@@@@@@@@%%%%%%%%%%%';
		$s .= '</pre>';
		$s .= '<BR>';
		if ( $doEcho ){
			echo $s;
		}
		return $s;

	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $msg
	 * @param type $var
	 */
	public function debug($msg, $var = null, $level = 'Notice') {
		$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS , 2);
		$s = Utils::backTraceHelper($bt, 0);

		//$s .= Utils::backTraceHelper($bt, 1);
		//	$s = '<BR>' . PHP_EOL . $s; // . '<BR>' . PHP_EOL;
		$s = '     - ' . $s;

		//$s .= ($var instanceof \php_base\Utils\Repsonse) ? '-=YES=-' : '-=NO=-' ;
		//if ( $var instanceof \php_base\Utils\Repsonse){
		//$s .= (is_a($var, 'php_base\Utils\Response')) ? '-=YES=-' : '-=NO=-' ;
		//$s .= get_class($var);
		if ( is_a($var, 'php_base\Utils\Response')) {
			$v = empty($var) ? '' : $var->toString() ;

			if ( $var->hadError() ){
				$level = DebugHandler::ALERT;
			} else {
				$level = DebugHandler::INFO;
			}
		} else {
		//	$level = AMessage::ALERT;
			$v = empty($var) ? '' : print_r($var, true);
		}
		if (Settings::GetPublic('IS_DETAILED_DISPATCH_QUEUE_DEBUGGING') <= LVL_Notice_2 ){
			$old =Settings::GetPublic('Show MessageLog Adds_FileAndLine');
			Settings::SetPublic('Show MessageLog Adds_FileAndLine', false);

			$msg = Settings::GetRunTimeObject('MessageLog')->add( $msg . $v . $s, null, $level);

			Settings::SetPublic('Show MessageLog Adds_FileAndLine', $old);
		}
			//$this->dumpQueue($theQueue);
		if ( defined("IS_PHPUNIT_TESTING")) {

		}

	}


}
