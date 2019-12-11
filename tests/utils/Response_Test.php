<?php

namespace Tests\Test;
use PHPUnit\Framework\TestCase;

use \php_base\Utils\Response as Response;
use \php_base\Utils\ResponseErrorCodes as ResponseErrorCodes;



class Response_Test extends TestCase{

//	function test_filter(){
//		$this->markTestIncomplete('This test has not been implemented yet' );
//	}

	function test_noError() {

		$x = Response::NoError();

		$this->assertEquals( 0, $x->giveErrorCode() );
		$this->assertEquals( 'ok', $x->giveMessage() );

		$r = $x->giveArrayOfEverything();

		$this->assertTrue( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);
	}

	function test_GenericWarning() {

		$x = Response::GenericWarning();

		$this->assertEquals( -1, $x->giveErrorCode() );
		$this->assertEquals( 'Generic Warning', $x->giveMessage() );

		$r = $x->giveArrayOfEverything();

		$this->assertTrue( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);
	}

	function test_GenericError() {

		$x = Response::GenericError();

		$this->assertEquals( -2, $x->giveErrorCode() );
		$this->assertEquals( 'Generic Error', $x->giveMessage() );

		$r = $x->giveArrayOfEverything();

		$this->assertTrue( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);
	}

	function test_todoError() {

		$x = Response::TODO_Error();

		$this->assertEquals( ResponseErrorCodes::TODO, $x->giveErrorCode() );
		$this->assertEquals( '- TODO -', $x->giveMessage() );

		$r = $x->giveArrayOfEverything();

		$this->assertFalse( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);
	}

	function test_construct() {
		$x = new Response('some message', -123456 );

		$this->assertEquals( -123456, $x->giveErrorCode() );
		$this->assertEquals( 'some message', $x->giveMessage() );

		$r = $x->giveArrayOfEverything();

		$this->assertFalse( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

	}

}