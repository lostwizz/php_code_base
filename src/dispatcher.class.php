<?php

//namespace whitehorse\MikesCommandAndControl2\Dispatcher;
namespace php_base;


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

//use \whitehorse\MikesCommandAndControl2\Authenticate as Authenticate;

//***********************************************************************************************
//***********************************************************************************************
class Dispatcher {

	var $PREqueue ;
	var $POSTqueue ;
	var $DISPATCHqueue;

	var $payloads = array();

	//-----------------------------------------------------------------------------------------------
	public function __construct( bool $isHeaderAndFooterLess = false){
		$this->PREqueue = new \SplQueue();
		$this->POSTqueue = new \SplQueue();
		$this->DISPATCHqueue= new \SplQueue();

	}

	// abort if anything returns FALSE
	//-----------------------------------------------------------------------------------------------
	public function do_work( $parentResolver = null) {

		Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher starting prequeue' );
		$pre_result = $this->RunThruTheQueue( $this->PREqueue);

		$dispatch_result = false;    //pre load the failure - only success will change it
		if ( $pre_result != false  ){

			// the pre queue contains authentication - so if it returns false then dont do any actuall work
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher starting normal queue' );
			$dispatch_result = $this->RunThruTheQueue($this->DISPATCHqueue );
			if ($dispatch_result ==false ) {
				Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher got an error from the running normal queue' );
				//return false;
			}
		}

		// show the footer in all cases (it has the message stack for one- so you know what happend)
		Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher starting postqueue' );
		$post_result =  $this->RunThruTheQueue( $this->POSTqueue);


		return array( $pre_result, $dispatch_result, $post_result);
	}


	//-----------------------------------------------------------------------------------------------
	private function RunThruTheQueue( \SplQueue $theQueue){

//$this->dumpQueue($theQueue);

		while ( ! $theQueue->isEmpty() ) {
			$item = $theQueue->dequeue();
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher executing [' . $item . '] 1');
			if ( ! empty($item )) {
				Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher executing [' . $item . '] 2');
				if (  $this->itemDecodeAndExecute( $item ) === false){
					return false;
				}
				Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher done executing [' . $item . '] 3');
			} else {
				Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher - item is empty!! why!!');
			}
		}
		return true;
	}

	//-----------------------------------------------------------------------------------------------
	private function itemDecodeAndExecute( string $process=null){
		if (empty($process) ) {
			return true;
		}
		$r = true;
		$exploded = explode( '.' , $process);
		switch( count( $exploded)) {
			case 1:
				echo ' what do i do here?';
				break;
			case 2:
				$r = $this->doExecute('control', $exploded[0], $exploded[1], 'doWork');
				break;
			case 3:
				$r =$this->doExecute('control', $exploded[0], $exploded[1], $exploded[2]);  //,$process
				break;
			case 4:
				$r =$this->doExecute('control', $exploded[0], $exploded[1], $exploded[2], $exploded[3]);  //,$process
				break;
			default:
				echo ' cant determine what to do';
		}
		echo '<BR>';
		return $r;
	}

	//-----------------------------------------------------------------------------------------------
	private function doExecute( string $dir, string $class, string $task, string $action='', $payload = null){
		$class = '\\php_base\\' .$dir . '\\' . $class;
		$payload = (!empty($payload)) ? $this->processPayloadFROMItem($payload) : null;

		$x = new $class ($action,  $payload);            //instanciate the process  and pass it the payload

		return $x->$task($this);				//run the process's method
	}


	//-----------------------------------------------------------------------------------------------
//	protected function decodePayload($process){
//		if ( !empty($this->payloads) and !empty( $this->payloads[$process])){
//			return $this->payloads[$process];
//		} else {
//			return null;
//		}
//	}

	//-----------------------------------------------------------------------------------------------
	protected function buildItem( $process, $task =null, $action =null, $payload = null ){
		$process = (!empty( $process)) ?        $process . 'Controller' : '';
		$task =    (!empty( $task))    ? '.' .  $task                   : '';
		$action =  (!empty( $action )) ? '.' .  $action                 : '';

		$payload = (!empty( $payload )) ? '.' .  $this->processPayloadForItem($payload)  : '';

		$item = $process . $task . $action  . $payload;
		return $item;
	}

	//-----------------------------------------------------------------------------------------------
	protected function processPayloadForItem($payload){
		return serialize($payload);
	}

	//-----------------------------------------------------------------------------------------------
	protected function processPayloadFROMItem($payload){
		return unserialize($payload);
	}


	//-----------------------------------------------------------------------------------------------
	protected function addItemToQueue( $q, $item){
		$q->enqueue($item);
	}
	//-----------------------------------------------------------------------------------------------
	public function addPREProcess( $process, $task =null, $action =null, $payload = null){
		$item = $this->buildItem($process, $task, $action, $payload);
		$this->addItemToQueue($this->PREqueue, $item );
		Settings::GetRunTimeObject('MessageLog')->addNotice( 'Added ' . $item . ' to the PRE Queue');
	}

	//-----------------------------------------------------------------------------------------------
	public function addPOSTProcess( $process, $task =null, $action =null, $payload = null){
		$item = $this->buildItem($process, $task, $action, $payload);
		$this->addItemToQueue($this->POSTqueue, $item );
		Settings::GetRunTimeObject('MessageLog')->addNotice( 'Added ' . $item . ' to the POST Queue');
	}

	//-----------------------------------------------------------------------------------------------
	public function addProcess( $process, $task =null, $action =null, $payload = null){
		$item = $this->buildItem($process, $task, $action, $payload);
		$this->addItemToQueue($this->DISPATCHqueue, $item );
		Settings::GetRunTimeObject('MessageLog')->addNotice( 'Added ' . $item . ' to the Queue');
	}



	//-----------------------------------------------------------------------------------------------
//	public function addProcessPayload($process, $payload = null) {
//		$processName = $this->decodeProcessFromFullDescriptor($process);
//		if (!empty( $payload) )  {
//			if (! empty( $this->payloads[$processName])) {
//				$this->addToPayload( $processName, $payload);
//				//$this->payloads[$processName][] = $payload;
//			} else {
//				$this->payloads[$processName] = $payload;
//			}
//		}
//	}

	//-----------------------------------------------------------------------------------------------
//	protected  function addToPayload( $processName, $payload){
//		$this->payloads[$processName] =  array_merge($this->payloads[$processName], $payload );
//	}
//


	//-----------------------------------------------------------------------------------------------
//	public function decodeProcessFromFullDescriptor($process){
//Dump::dump( $process);
//		$exploded = explode( '.' , $process);
//		switch( count( $exploded)) {
//			case 2:
//				return $process;
//			case 3:
//				return $exploded[0] . '.' . $exploded[1];
////			case 4:
////				return $exploded[0] . '.' . $exploded[1];
//
//			default:
//				return null;
//		}
//	}



	//-----------------------------------------------------------------------------------------------
	public function dumpQueue( $theQueue) {

		echo '<pre class="pre_debug_queue">';
		echo '@@@@@@@@@@@@@ count=' .  $theQueue->count()   . '%%' . ($theQueue->isEmpty() ? 'empty' : 'Notempty') . ' %%%%%%%%%<BR>';
		$theQueue->rewind();
		while($theQueue->valid()){
		    echo $theQueue->current();  //;."-\n"; // Show the first one
		    echo '<br>';
		    $theQueue->next(); // move the cursor to the next element
		}
		$theQueue->rewind();
		echo '@@@@@@@@@@@@@%%%%%%%%%%%';
		echo '</pre>';
	}


}