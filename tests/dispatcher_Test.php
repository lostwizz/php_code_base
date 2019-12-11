<?php

namespace Tests\Test;

use PHPUnit\Framework\TestCase;
use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Response as Response;

class Dispatcher_Test extends TestCase {

	public $fp;

	public static function setUpBeforeClass(): void {
		unlink('f:\temp\data.txt');
	}

	public function setup(): void {
		$this->fp = fopen('f:\temp\data.txt', 'a');
	}

	protected function tearDown(): void {
		fclose($this->fp);
	}

//	function test_X(){
//		$this->markTestIncomplete('This test has not been implemented yet' );
//	}


	function test_dispatcher_construct() {
		$dispatcher = new \php_base\Dispatcher();
		$this->assertInstanceOf(\php_base\Dispatcher::class, $dispatcher);

		$queue = $dispatcher->getQueue('PRE');
		$this->assertNotNull($queue);
		$this->assertInstanceOf(\SplQueue::class, $queue);

		$queue = $dispatcher->getQueue('POST');
		$this->assertNotNull($queue);
		$this->assertInstanceOf(\SplQueue::class, $queue);

		$queue = $dispatcher->getQueue('DISPATCH');
		$this->assertNotNull($queue);
		$this->assertInstanceOf(\SplQueue::class, $queue);
	}

	function test_doWork() {
		//$this->markTestIncomplete('This test has not been implemented yet' );

		$dispatcher = new \php_base\Dispatcher();
		$actual = $dispatcher->doWork();
		$this->assertEquals(Response::NoError(), $actual);  /* since all the queues are empty is should evenutally return no error */
		$this->assertEmpty($dispatcher->PHPUNIT_tempArray );



		fwrite($this->fp, print_r($dispatcher->PHPUNIT_tempArray, TRUE));
	}

}

class Extended_Dispatcher extends \php_base\Dispatcher {
	function extended_RunThruTheQueue($theQueue){
		return parent::RunThruTheQueue($theQueue);
	}
}
