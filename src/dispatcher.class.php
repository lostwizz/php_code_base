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


//		if ( ! $isHeaderAndFooterLess){
//			$this->PREqueue->enqueue('HeaderController.doWork');
//
//			$this->POSTqueue->enqueue( 'FooterController.doWork');
//			/// this will always be done in index.php so it always shows something --$this->POSTqueue->enqueue( 'utils.messageLog.showAllMessagesInBox');
//		}

	}

		//$processClass = new $className( $this);
	//-----------------------------------------------------------------------------------------------
	public function do_work( $parentResolver = null) {

		Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher starting prequeue' );
		if (  $this->RunThruTheQueue( $this->PREqueue) === false ){
			return false;
		}

		Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher starting normal queue' );
		$r = $this->RunThruTheQueue($this->DISPATCHqueue );
		if ( $r ===false ) {
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher got an error from the running normal queue' );
		}

		Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher starting postqueue' );
		if ( ! $this->RunThruTheQueue( $this->POSTqueue) ){
			return false;
		}

		return $r;
	}


	//-----------------------------------------------------------------------------------------------
	private function RunThruTheQueue( \SplQueue $theQueue){
		while ( ! $theQueue->isEmpty() ) {
			$item = $theQueue->dequeue();
//Dump::dump( $item)	;
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher executing [' . $item . '] 1');
			if ( ! $item ) {
				return false;
			}
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher executing [' . $item . '] 2');
			if (  $this->itemDecode( $item ) === false){
				return false;
			}
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher done executing [' . $item . '] 3');
		}
		return true;
	}

	//-----------------------------------------------------------------------------------------------
	private function itemDecode( string $process=null){
		if (empty($process) ) {
			return true;
		}
		$r = true;
		$exploded = explode( '.' , $process);
Dump::dump($exploded);
		switch( count( $exploded)) {
			case 1:
				echo ' what do i do here?';
				break;
			case 2:
				$r = $this->doExecute('control', $exploded[0], $exploded[1], 'doWork');
				break;
			case 3:
				$r =$this->doExecute('control', $exploded[0], $exploded[1], $exploded[1]);  //,$process
				break;
			default:
				echo ' cant determine what to do';
		}
		echo '<BR>';
		return $r;
	}



	//-----------------------------------------------------------------------------------------------
	protected function decodePayload($process){
		if ( !empty($this->payloads) and !empty( $this->payloads[$process])){
			return $this->payloads[$process];
		} else {
			return null;
		}
	}

	protected function buildItem( $process, $task =null, $action =null, $payload = null ){
		$process = (!empty( $process)) ?        $process . 'Controller' : '';
		$task =    (!empty( $task))    ? '.' .  $task                   : '';
		$action =  (!empty( $action )) ? '.' .  $action                 : '';

		$item = $process . $task . $action;

		return $item;
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
	public function addProcessPayload($process, $payload = null) {
		$processName = $this->decodeProcessFromFullDescriptor($process);
		if (!empty( $payload) )  {
			if (! empty( $this->payloads[$processName])) {
				$this->addToPayload( $processName, $payload);
				//$this->payloads[$processName][] = $payload;
			} else {
				$this->payloads[$processName] = $payload;
			}
		}
	}

	//-----------------------------------------------------------------------------------------------
	protected  function addToPayload( $processName, $payload){
		$this->payloads[$processName] =  array_merge($this->payloads[$processName], $payload );
	}


	//-----------------------------------------------------------------------------------------------
	private function doExecute( string $dir, string $class, string $method){
		$class = '\\php_base\\' .$dir . '\\' . $class;

		//$pname = $this->decodeProcessFromFullDescriptor($process);   //figure out the base process name
		//$payload = $this->decodePayload($pname);       //send the base process the payload
		$payload = '';   //parentResolver->payload;
//Dump::dump($pname);
//Dump::dump($class);
//Dump::dump($payload);


		$x = new $class ( $payload);            //instanciate the process  and pass it the payload

		return $x->$method($this);				//run the process's method
	}

	//-----------------------------------------------------------------------------------------------
	public function decodeProcessFromFullDescriptor($process){
		$exploded = explode( '.' , $process);
		switch( count( $exploded)) {
			case 2:
				return $process;
			case 3:
				return $exploded[0] . '.' . $exploded[1];
			default:
				return null;
		}
	}

}