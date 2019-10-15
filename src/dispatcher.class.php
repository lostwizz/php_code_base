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


		if ( ! $isHeaderAndFooterLess){
			$this->PREqueue->enqueue('control.HeaderController');

			$this->POSTqueue->enqueue( 'control.FooterController');
			/// this will always be done in index.php so it always shows something --$this->POSTqueue->enqueue( 'utils.messageLog.showAllMessagesInBox');
		}

	}

	//-----------------------------------------------------------------------------------------------
	public function do_work() {

//Dump::dump($this->PREqueue);

		//$this->DISPATCHqueue->enqueue('sam');

		if (  $this->decode( $this->PREqueue) === false ){
			return false;
		}

/*
Settings::GetPublic('EmailLog')->addCritical('Hey, a critical log entry!',
					array( 'bt' => debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT),
							'server' =>$_SERVER,
							'request'=> $_REQUEST,
							//'session'=>$_SESSION,
							//'env'=> $_ENV
							//'cookie' => $_COOKIE
						));
*/

//Dump::dump($this->POSTqueue);



		if ( ! $this->decode( $this->POSTqueue) ){
			return false;
		}

		return true;
	}


	//-----------------------------------------------------------------------------------------------
	private function decode( \SplQueue $theQueue){
		while ( ! $theQueue->isEmpty() ) {
			$item = $theQueue->dequeue();
			if ( ! $item ) {
				return false;
			}
			if (  $this->itemDecode( $item ) === false){
				return false;
			}
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
		switch( count( $exploded)) {
			case 1:
				echo ' what do i do here?';
				break;
			case 2:
				$r = $this->doExecute($exploded[0], $exploded[1], 'doWork', $process);
				break;
			case 3:
				$r =$this->doExecute($exploded[0], $exploded[1], $exploded[2], $process);
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

	//-----------------------------------------------------------------------------------------------
	public function addProcess( $process, $payload = null){
		$this->PREqueue->enqueue($process);
		//$this->payloads[$process] = $payload;

//Dump::dump($this->payloads,'before');
//Dump::dump($payload);
		$this->addProcessPayload($process, $payload);
//Dump::dump($this->payloads, 'after');
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

	//-----------------------------------------------------------------------------------------------
	private function doExecute( string $dir, string $class, string $method, $process){
		$class = '\\php_base\\' .$dir . '\\' . $class;

		$pname = $this->decodeProcessFromFullDescriptor($process);   //figure out the base process name
		$payload = $this->decodePayload($pname);       //send the base process the payload

		$x = new $class ( $payload);            //instanciate the process  and pass it the payload
		return $x->$method();				//run the process's method
	}

}