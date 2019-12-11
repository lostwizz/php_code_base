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
		$this->assertFalse( $r['shouldThrow']);
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
		$this->assertFalse( $r['shouldThrow']);
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
		$this->assertFalse( $r['shouldThrow']);
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
		$this->assertFalse( $r['shouldThrow']);
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
		$this->assertFalse( $r['shouldThrow']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);
	}

	function test_setProcessTaskActionPayload(){
		$x = new Response('some message', -123456 );

		//---------------------------
		$x->setProcessTaskActionPayload('processOne', 'taskOne');

		$actual = $x->giveProcessTaskActivityPayload();
		$this->assertEquals( array('processOne', 'taskOne', null,null), $actual);

		$r = $x->giveArrayOfEverything();

		$this->assertFalse( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals( 'some message', $r['message']);
		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertEquals( 'processOne', $r['process']);
		$this->assertEquals( 'taskOne', $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);


		//---------------------------
		$x->setProcessTaskActionPayload('processTwo', 'taskTwo', 'actionOne');

		$actual = $x->giveProcessTaskActivityPayload();
		$this->assertEquals( array('processTwo', 'taskTwo', 'actionOne',null), $actual);


		$r = $x->giveArrayOfEverything();

		$this->assertFalse( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);
		$this->assertEquals( 'some message', $r['message']);
		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertEquals( 'processTwo', $r['process']);
		$this->assertEquals( 'taskTwo', $r['task']);
		$this->assertEquals( 'actionOne', $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$x->setProcessTaskActionPayload('processThree', 'taskThree', 'actionTwo', 'payloadOne');
		$actual = $x->giveProcessTaskActivityPayload();
		$this->assertEquals( array('processThree', 'taskThree', 'actionTwo' ,'payloadOne'), $actual);

		$r = $x->giveArrayOfEverything();

		$this->assertFalse( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);
		$this->assertEquals( 'some message', $r['message']);
		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertEquals( 'processThree', $r['process']);
		$this->assertEquals( 'taskThree', $r['task']);
		$this->assertEquals( 'actionTwo', $r['action']);
		$this->assertEquals( 'payloadOne', $r['payload']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$x->setProcessTaskActionPayload('processThree', 'taskThree', 'actionTwo', array('payloadTwo', 'payloadThree'));

		$actual = $x->giveProcessTaskActivityPayload();
		$this->assertEquals( array('processThree', 'taskThree', 'actionTwo', array('payloadTwo', 'payloadThree')), $actual);


		$r = $x->giveArrayOfEverything();

		$this->assertFalse( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);
		$this->assertEquals( 'some message', $r['message']);
		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertEquals( 'processThree', $r['process']);
		$this->assertEquals( 'taskThree', $r['task']);
		$this->assertEquals( 'actionTwo', $r['action']);
		$this->assertEquals( array('payloadTwo', 'payloadThree'), $r['payload']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);
	}

	function test_setMessage(){
		$x = new Response('some message', -123456 );
		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		//---------------------------
		$x->setMessage('messageOne');
		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'messageOne', $r['message']);


		$this->assertFalse( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);


		//---------------------------
		$x->setMessage('messageTwo', 111);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'messageTwo', $r['message']);
		$this->assertEquals(  111, $r['errNum']);

		$this->assertFalse( $r['canContinue']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);
		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$x->setMessage('messageThree', 222, true);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'messageThree', $r['message']);
		$this->assertEquals(  222, $r['errNum']);
		$this->assertTrue( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);
		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$x->setMessage('messageFour', 333, true, true);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'messageFour', $r['message']);
		$this->assertEquals(  333, $r['errNum']);
		$this->assertTrue( $r['canContinue']);
		$this->assertTrue( $r['failSilently']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);
	}

	function test_setContinue(){
		$x = new Response('some message', -123456 );

		$x->setContinue(true);

		$actual = $x->giveContinueProcessTaskActivityPayload();
		$this->assertNull($actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		$this->assertTrue( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$x->setContinue(false);
		$r = $x->giveArrayOfEverything();
		$this->assertFalse( $r['canContinue']);

		$actual = $x->giveContinueProcessTaskActivityPayload();
		$this->assertNull($actual);


		//---------------------------
		$x->setContinue(true, 'processOne');

		$actual = $x->giveContinueProcessTaskActivityPayload();
		$this->assertEquals(array('processOne',null,null,null), $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		$this->assertTrue( $r['canContinue']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertEquals('processOne',  $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$x->setContinue(true, 'processTwo', 'taskOne');

		$actual = $x->giveContinueProcessTaskActivityPayload();
		$this->assertEquals(array('processTwo','taskOne',null,null), $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		$this->assertTrue( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertNull(  $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertEquals( 'processTwo', $r['continueProcess']);
		$this->assertEquals( 'taskOne', $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$x->setContinue(true, 'processThree', 'taskTwo', 'actionOne');

		$actual = $x->giveContinueProcessTaskActivityPayload();
		$this->assertEquals(array('processThree','taskTwo', 'actionOne',null), $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		$this->assertTrue( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertNull(  $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertEquals( 'processThree', $r['continueProcess']);
		$this->assertEquals( 'taskTwo', $r['continueTask']);
		$this->assertEquals( 'actionOne', $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$x->setContinue(true, 'processFour', 'taskThree', 'actionTwo', 'payloadOne');

		$actual = $x->giveContinueProcessTaskActivityPayload();
		$this->assertEquals(array('processFour','taskThree', 'actionTwo', 'payloadOne'), $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		$this->assertTrue( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertEquals( 'processFour',$r['continueProcess']);
		$this->assertEquals( 'taskThree', $r['continueTask']);
		$this->assertEquals( 'actionTwo', $r['continueAction']);
		$this->assertEquals( 'payloadOne', $r['continuePayload']);

		//---------------------------
		$x->setContinue(true, 'processFour', 'taskThree', 'actionTwo', array('payloadOne', 'payloadTwo'));

		$actual = $x->giveContinueProcessTaskActivityPayload();
		$this->assertEquals(array('processFour','taskThree', 'actionTwo', array('payloadOne', 'payloadTwo')), $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		$this->assertTrue( $r['canContinue']);

		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertEquals( 'processFour',$r['continueProcess']);
		$this->assertEquals( 'taskThree', $r['continueTask']);
		$this->assertEquals( 'actionTwo', $r['continueAction']);
		$this->assertEquals( array('payloadOne', 'payloadTwo'), $r['continuePayload']);
	}

	function test_setException() {
		$x = new Response('some message', -123456 );

		$x->setException(true);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		$this->assertFalse( $r['canContinue']);
		$this->assertTrue( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);


		//---------------------------
		$x->setException(false);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		$this->assertFalse( $r['canContinue']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$expected = new \Exception('exception Message');
		$x->setException(true, $expected);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		$this->assertFalse( $r['canContinue']);
		$this->assertTrue( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertEquals( $expected, $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$expected = new \Exception('exception Message');
		$x->setException(false, null);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		$this->assertFalse( $r['canContinue']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

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

	function test_hadError() {
		$x = new Response('some message', -123456 );

		$actual = $x->hadError();
		$this->assertTrue( $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		$this->assertFalse( $r['canContinue']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$x = new Response('some message', 789 );

		$actual = $x->hadError();
		$this->assertFalse( $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		$this->assertFalse( $r['canContinue']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  789, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);


		//---------------------------
		$x->setMessage('newErrorOne', -3);
		$actual = $x->hadError();
		$this->assertTrue( $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'newErrorOne', $r['message']);

		$this->assertFalse( $r['canContinue']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -3, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$x->setMessage('newErrorTwo', 0);
		$actual = $x->hadError();
		$this->assertFalse( $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'newErrorTwo', $r['message']);

		$this->assertFalse( $r['canContinue']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  0, $r['errNum']);

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

	function test_failNoisily() {
		$x = new Response('some message', -123456 );

		$actual = $x->failNoisily();
		$this->assertTrue( $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		$this->assertFalse( $r['canContinue']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$x->setMessage('messageOne', null, null, true);
		$actual = $x->failNoisily();
		$this->assertFalse( $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'messageOne', $r['message']);

		$this->assertFalse( $r['canContinue']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertTrue( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

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

	function test_hadRecoverableError() {
		$x = new Response('some message', -123456 );

		$actual = $x->hadRecoverableError();
		$this->assertFalse( $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'some message', $r['message']);

		$this->assertFalse( $r['canContinue']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  -123456, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$x->setMessage('messageOne', 0);
		$actual = $x->hadRecoverableError();
		$this->assertFalse( $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'messageOne', $r['message']);

		$this->assertFalse( $r['canContinue']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  0, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$x->setMessage('messageTwo', 1);
		$actual = $x->hadRecoverableError();
		$this->assertFalse( $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'messageTwo', $r['message']);

		$this->assertFalse( $r['canContinue']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  1, $r['errNum']);

		$this->assertNull( $r['process']);
		$this->assertNull( $r['task']);
		$this->assertNull( $r['action']);
		$this->assertNull( $r['payload']);
		$this->assertNull( $r['exceptionToThrow']);
		$this->assertNull( $r['continueProcess']);
		$this->assertNull( $r['continueTask']);
		$this->assertNull( $r['continueAction']);
		$this->assertNull( $r['continuePayload']);

		//---------------------------
		$x->setMessage('messageThree', 2);
		$actual = $x->hadRecoverableError();
		$this->assertTrue( $actual);

		$r = $x->giveArrayOfEverything();
		$this->assertEquals( 'messageThree', $r['message']);

		$this->assertFalse( $r['canContinue']);
		$this->assertFalse( $r['shouldThrow']);
		$this->assertFalse( $r['failSilently']);

		$this->assertEquals(  2, $r['errNum']);

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