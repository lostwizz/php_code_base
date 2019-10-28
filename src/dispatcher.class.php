<?php
//**********************************************************************************************
//* dispatcher.class.php
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

//namespace whitehorse\MikesCommandAndControl2\Dispatcher;
namespace php_base;


use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

//use \whitehorse\MikesCommandAndControl2\Authenticate as Authenticate;

//***********************************************************************************************
//***********************************************************************************************
class Dispatcher {

	protected $PREqueue ;
	protected $POSTqueue ;
	protected $DISPATCHqueue;
	protected $payloads = array();

	//-----------------------------------------------------------------------------------------------
	public function __construct( bool $isHeaderAndFooterLess = false){
		$this->PREqueue = new \SplQueue();
		$this->POSTqueue = new \SplQueue();
		$this->DISPATCHqueue= new \SplQueue();

	}

	//-----------------------------------------------------------------------------------------------
	// abort if anything returns FALSE
	public function do_work( $parentResolver = null)  : Response {

		//Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher starting prequeue' );
		$pre_result = $this->RunThruTheQueue( $this->PREqueue);

		if ($pre_result->hadFatalError()){
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher got an error from the running pre queue' . $pre_result->toString() );
			return $pre_result;
		} else {
			// the pre queue contains authentication - so if it returns false then dont do any actuall work
			//Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher starting normal queue' );
			$dispatch_result = $this->RunThruTheQueue($this->DISPATCHqueue );
			if ($dispatch_result->hadFatalError() ) {
				Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher got an error from the running normal queue' . $dispatch_result);
				return $dispatch_result;
			}
		}

		// show the footer in all cases (it has the message stack for one- so you know what happend)
		//Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher starting postqueue' );
		$post_result =  $this->RunThruTheQueue( $this->POSTqueue);
		if ( $post_result->hadFatalError()){
			Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher got an error from the running post queue' . $post_result);
			return $post_result;
		}

		return Response::NoError();
	}

	//-----------------------------------------------------------------------------------------------
	private function RunThruTheQueue( \SplQueue $theQueue) : Response {

//$this->dumpQueue($theQueue);
		try{
			$response  = null;
			while ( ! $theQueue->isEmpty() ) {
				$item = $theQueue->dequeue();
				//Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher executing [' . $item . '] 1');
				if ( ! empty($item )) {
					//Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher executing [' . $item . '] 2');
					$response = $this->itemDecodeAndExecute( $item );
					if ( $response->hadFatalError()){
						//Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher recieved an error:' . $response->toString() );
						return $response;
					}

					//Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher done executing [' . $item . '] '. $response->toString());
				} else {
					Settings::GetRunTimeObject('MessageLog')->addNotice( 'dispatcher - item is empty!! why!! -but ignoring');
					$response = Response::GenericWarning();
				}
			}
			return $response;
		} catch (\Exception $e){
//Dump::dumpLong($e)			;
			$enum = $e->getCode();
			if ( !is_numeric($enum)){
				$enum = -1;
			}
			return new Response( 'exception in running the Queue: ' . $e->getMessage(),
								 (-1* $enum),
								 false);
		}
	}

	//-----------------------------------------------------------------------------------------------
	private function itemDecodeAndExecute( string $process=null) : Response {
		if (empty($process) ) {
			return true;
		}
		$response = null;
		$exploded = explode( '.' , $process);
//Dump::dump($exploded)		;
		switch( count( $exploded)) {
			case 1:
				$response = $this->doExecute('control', $exploded[0], 'doWork');
				break;
			case 2:
				$response = $this->doExecute('control', $exploded[0], $exploded[1]);
				break;
			case 3:
				$response =$this->doExecute('control', $exploded[0], $exploded[1], $exploded[2]);  //,$process
				break;
			case 4:
				$response =$this->doExecute('control', $exploded[0], $exploded[1], $exploded[2], $exploded[3]);  //,$process
				break;
			default:
				echo ' cant determine what to do';
				$response = new Response('Invalid arguments to itemDecodeAndExecute', -1, true);
		}
		echo '<BR>';
		return $response;
	}

	//-----------------------------------------------------------------------------------------------
	//   eg. $class = TESTController
	private function doExecute( string $dir, string $class, string $task, string $action='', $payload = null)  : Response {
		if ( substr( $class, -10) == 'Controller'){
			$process = substr( $class, 0, -10);
		} else {
			$process = $class;
		}

		$class = '\\php_base\\' .$dir . '\\' . $class;
		$payload = (!empty($payload)) ? $this->processPayloadFROMItem($payload) : null;

//Dump::dump( $class);
//Dump::dump($task);

		//Settings::GetRunTimeObject('MessageLog')->addInfo( "dispatcher do execute - new $class ($action,  payload);");
		$x = new $class ($action,  $payload);            //instanciate the process  and pass it the payload

		$x->setProcessAndTask( $process, $task);

		return $x->$task($this);				//run the process's method
	}

	//-----------------------------------------------------------------------------------------------
	protected function buildItem( $process, $task =null, $action =null, $payload = null )  {
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
		//Settings::GetRunTimeObject('MessageLog')->addNotice( 'Added ' . $item . ' to the PRE Queue');
	}

	//-----------------------------------------------------------------------------------------------
	public function addPOSTProcess( $process, $task =null, $action =null, $payload = null){
		$item = $this->buildItem($process, $task, $action, $payload);
		$this->addItemToQueue($this->POSTqueue, $item );
		//Settings::GetRunTimeObject('MessageLog')->addNotice( 'Added ' . $item . ' to the POST Queue');
	}

	//-----------------------------------------------------------------------------------------------
	public function addProcess( $process, $task =null, $action =null, $payload = null){
		$item = $this->buildItem($process, $task, $action, $payload);
		$this->addItemToQueue($this->DISPATCHqueue, $item );
		//Settings::GetRunTimeObject('MessageLog')->addNotice( 'Added ' . $item . ' to the Queue');
	}

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