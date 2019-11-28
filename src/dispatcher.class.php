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

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

/** * **********************************************************************************************
 *  Dispatcher executes items in the queue.
 *
 * Description.
 *     3 queues - pre dispatcher post - executes and checks responses
 *
 * @since 0.0.2
 */
class Dispatcher {

	/**
	 *
	 * @var type these are the 3 queues to execute
	 *    PREqueue - executed first  (usually contains header, authentication and permission management
	 *    DISPATCHqueue - executes after pre and before post
	 *    POSTqueue - executes after Dispatch (usualy contain footer and message box output
	 */
	protected $PREqueue;
	protected $POSTqueue;
	protected $DISPATCHqueue;

	//protected $payloads = array();

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	public function __construct() {       //bool $isHeaderAndFooterLess = false
		$this->PREqueue = new \SplQueue();
		$this->POSTqueue = new \SplQueue();
		$this->DISPATCHqueue = new \SplQueue();
	}

	//-----------------------------------------------------------------------------------------------
	// abort if anything returns FALSE

	/** -----------------------------------------------------------------------------------------------
	 * object constructor.
	 *
	 * Creates a new Dispatcher object and abort if anything returns FALSE
	 *
	 * @since 0.0.2

	 * @param type $parentResolver
	 * @return Response
	 */
	public function doWork($parentResolver = null): Response {

		//Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher starting prequeue' );
		$pre_result = $this->RunThruTheQueue($this->PREqueue);

		if ($pre_result->hadFatalError()) {
			Settings::GetRunTimeObject('MessageLog')->addNotice('dispatcher got an error from the running pre queue' . $pre_result->toString());
			return $pre_result;
		} else {
			// the pre queue contains authentication - so if it returns false then dont do any actuall work
			//Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher starting normal queue' );
			$dispatch_result = $this->RunThruTheQueue($this->DISPATCHqueue);
			if ($dispatch_result->hadFatalError()) {
				Settings::GetRunTimeObject('MessageLog')->addNotice('dispatcher got an error from the running normal queue' . $dispatch_result);
				return $dispatch_result;
			}
		}

		// show the footer in all cases (it has the message stack for one- so you know what happend)
		//Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher starting postqueue' );
		$post_result = $this->RunThruTheQueue($this->POSTqueue);
		if ($post_result->hadFatalError()) {
			Settings::GetRunTimeObject('MessageLog')->addNotice('dispatcher got an error from the running post queue' . $post_result);
			return $post_result;
		}

		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 * object constructor.
	 *
	 * Creates a new Dispatcher object - which will execute the PTAP.
	 *
	 * @since 0.0.2
	 * @param \SplQueue $theQueue - this is a common method for all 3 queues - so just pass the queue wanted to run
	 * @return Response
	 */
	private function RunThruTheQueue(\SplQueue $theQueue): Response {
		/** this will dump the contents of the queue - for debugging
		  $this->dumpQueue($theQueue);
		 */
 $this->dumpQueue($theQueue);

		try {
			$response = null;
			while (!$theQueue->isEmpty()) {
				$response = $this->processDetailsOfQueue($theQueue);
			}
			return $response;
		} catch (\Exception $e) {
			$enum = $e->getCode();
			if (!is_numeric($enum)) {
				$enum = -1;
			}
			return new Response('exception in running the Queue: ' . $e->getMessage(),
					   (-1 * $enum),
					   false);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $theQueue
	 * @return Response
	 */
	protected function processDetailsOfQueue($theQueue): Response {

		$item = $theQueue->dequeue();/** get the next item out of the queue */
		Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher executing [' . $item . '] 1');
		if (!empty($item)) {
			//Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher executing [' . $item . '] 2');
			$response = $this->itemDecodeAndExecute($item);
			if ($response->hadFatalError()) {
				//Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher recieved an error:' . $response->toString() );
				return $response;
			}
			//Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher done executing [' . $item . '] '. $response->toString());
		} else {
			Settings::GetRunTimeObject('MessageLog')->addNotice('dispatcher - item is empty!! why!! -but ignoring');
			$response = Response::GenericWarning();
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
		Settings::GetRunTimeObject('MessageLog')->addDEBUG('PTAP: ' . $passedProcess);

		$exploded = \explode('.', $passedProcess);
		//dump::dump($exploded);
		$response = $this->doExecute('control',
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
	private function doExecute(string $dir,
							string $class,
							?string $task,
							?string $action = '',
							$passedPayload = null
					): Response {

		if (substr($class, -10) == 'Controller') {
			$process = substr($class, 0, -10);
		} else {
			$process = $class;
		}
		if ( empty($task)) {
			$task = 'doWork';            /* the default task */
		}

		$class = '\\php_base\\' . $dir . '\\' . $class;
		Settings::GetRunTimeObject('MessageLog')->addTODO('willhave to change this from php_base:' . $class);

		$payload = (!empty($passedPayload)) ? $this->processPayloadFROMItem($passedPayload) : null;

		Settings::GetRunTimeObject('MessageLog')->addCritical( 'dispatcher do execute - new ' . $class . '->'  . $task . ' action=' . $action . ' payload='  . $passedPayload);
		$x = new $class($action, $payload); //instanciate the process  and pass it the payload

		$x->setProcessAndTask($process, $task); // sets the called class up with the Process

		// now calls basically the task with this so it can look up the class and task
		$r = $x->$task($this);  //run the process's method
//		Settings::GetRunTimeObject('MessageLog')->addCritical( 'dispatcher got ' . $r->giveMessage());
		return $r;
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

		$process = (!empty($passedprocess)) ? $passedprocess . 'Controller' : '';
		$task = (!empty($passedtask)) ? '.' . $passedtask : '.';
		$action = (!empty($passedaction)) ? '.' . $passedaction : '.';

		$payload = (!empty($passedpayload)) ? '.' . $this->processPayloadForItem($passedpayload) : '';

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
		$newPayload = str_replace( '.', '~!~', $payload);
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
	 * @see Dispatcher Class
	 *
	 * @param mixed $payload - the payload part of the PTAP in a string form
	 * @return the unstringified version of the parameter
	 */
	protected function processPayloadFROMItem($payload) {
		$r = unserialize($payload);
		$newR = str_replace('~!~', '.', $r);
		return $newR;
	}

	/** -----------------------------------------------------------------------------------------------
	 * addItemToQueue - takes an item aqnd adds to the passed queue
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
		$q->enqueue($item);
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
		if ( $item != '..') {
			$this->addItemToQueue($this->PREqueue, $item);
			//Settings::GetRunTimeObject('MessageLog')->addNotice( 'Added ' . $item . ' to the PRE Queue');
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
		if ( $item != '..') {
			$this->addItemToQueue($this->POSTqueue, $item);
			//Settings::GetRunTimeObject('MessageLog')->addNotice( 'Added ' . $item . ' to the POST Queue');
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
		if ( $item != '..') {
			$this->addItemToQueue($this->DISPATCHqueue, $item);
			//Settings::GetRunTimeObject('MessageLog')->addNotice( 'Added ' . $item . ' to the Queue');
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
	public function dumpQueue( \SPLQueue $theQueue): void {

		echo '<pre class="pre_debug_queue">';
		echo '@@@@ -dispatcher queue dump -@@@@@@@@@ count=' . $theQueue->count() . '%%' . ($theQueue->isEmpty() ? 'empty' : 'Notempty') . ' %%%%%%%%%<BR>';
		$theQueue->rewind();
		while ($theQueue->valid()) {
			echo $theQueue->current();  //;."-\n"; // Show the first one
			echo '<br>';
			$theQueue->next(); // move the cursor to the next element
		}
		$theQueue->rewind();
		echo '@@@@@@@@@@@@@%%%%%%%%%%%';
		echo '</pre>';
	}

}
