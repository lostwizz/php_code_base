<?php

use PHPUnit\Framework\TestCase;
use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\utils;
use \php_base\utils\MessageLog as MessageLog;
use \php_base\utils\AMessage as AMessage;
use \php_base\utils\MessageBase as MessageBase;

include_once (DIR . 'utils' . DSZ . 'messagelog.class.php');

///		   fwrite(STDERR, print_r($input, TRUE));
//***********************************************************************************************
//***********************************************************************************************
class MessageLog_Test extends TestCase {

	public static function setUpBeforeClass(): void {
		include_once( DIR . 'utils' . DSZ . 'settings.class.php');
		require_once( DIR . '_config' . DSZ . '_Settings-General.php');
		require_once( DIR . '_config' . DSZ . '_Settings-Database.php');
		require_once( DIR . '_config' . DSZ . '_Settings-protected.php');

		require_once( 'P:\Projects\_Private_Settings.php');

		// force everyhting to setup
		// Settings::SetPublic( 'Use_MessageLog', true );  //true
		Settings::SetPublic('Use_MessageLog', false);  //true
		Settings::SetPublic('Use_DBLog', false);
		Settings::SetPublic('Use_DBdataLog', false);
		Settings::SetPublic('Use_FileLog', false);  // true
		Settings::SetPublic('Use_SecurityLog', false);
		Settings::SetPublic('Use_EmailLog', false);	  // true
		//require_once(DIR . 'utils\setup_Logging.php');

		Settings::SetPublic('Show MessageLog Adds_FileAndLine', false);
		Settings::SetPublic('Show MessageLog Adds', false);
	}

	public function test_Versions() {
		$this->assertEquals('0.3.0', MessageBase::Version());
		$this->assertEquals('0.3.0', MessageLog::Version());
	}

	//-----------------------------------------------------------------------------------------------
	public function test_constuctor() {
		$r = ExtendedMessageLogTest::extendedMessageQueueStatic();
		$this->assertEmpty($r);

		$r = new ExtendedMessageLogTest();
		$this->assertNotEmpty($r);
	}

	//-----------------------------------------------------------------------------------------------
	public function test_constructor() {
		//$this->markTestIncomplete('This test has not been implemented yet' );
		$o = new ExtendedMessageLogTest();
		$x = $o->extendedMessageQueue();
		$this->assertNotNull($x);

		$this->assertInstanceOf('\php_base\utils\MessageLog', $o);

		$c = $x->count();
		$this->assertEquals(0, $c);

		$x->enqueue('fred was here');
		$c = $x->count();
		$this->assertEquals(1, $c);

		$y = $x->dequeue();
		$this->assertEquals('fred was here', $y);

		$c = $x->count();
		$this->assertEquals(0, $c);
	}

	//-----------------------------------------------------------------------------------------------
	public function toString_DataProvider() {
		//$nowIsh =  date( 'g:i:30');
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';

		return [
			[[null], $nowIsh . ' (Level: NOTICE) '],
			[['fred'], $nowIsh . ' (Level: NOTICE) fred'],
			[['fred', 'faketimestamp'], 'faketimestamp (Level: NOTICE) fred'],
		];
	}

	/**
	 *  @dataProvider toString_DataProvider
	 */
	public function test_toString($input, $expected) {

		$ML = new ExtendedMessageLogTest();

		$ML->addNotice($input[0],
				(empty($input[1]) ? null : $input[1])
		);

		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString(); //->get()[0];

		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');
	}

	//-----------------------------------------------------------------------------------------------
	public function test_AddAndShow_1line() {
		$ML = new ExtendedMessageLogTest();

		$this->expectOutputString('[fakeTimeStamp] <span class="msg_style_NOTICE">NOTICE </span>: fred<br>');

		$ML->addAndShow('fred',
				'fakeTimeStamp',
				null
		);
	}

	//-----------------------------------------------------------------------------------------------
	public function test_AddAndShow_2line() {
		$ML = new ExtendedMessageLogTest();

		$this->expectOutputString('[samFakeTimeStamp] <span class="msg_style_NOTICE">NOTICE </span>: Sam&nbsp;was&nbsp;here<br>'
				. '[fakeTimeStamp] <span class="msg_style_NOTICE">NOTICE </span>: fred<br>');

		$ML->addNotice('Sam was here', 'samFakeTimeStamp');
		$ML->addAndShow('fred',
				'fakeTimeStamp',
				null
		);
	}

	//-----------------------------------------------------------------------------------------------
	public function add_DataProvider() {
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		return [
			//[[null], $nowIsh . ' (Level: NOTICE) '],
			[['fred'], $nowIsh . ' (Level: NOTICE) fred'],
			[['fred', 'faketimestamp'], 'faketimestamp (Level: NOTICE) fred'],
			[['fred', 'faketimestamp', ExtendedMessageBase::EMERGENCY], 'faketimestamp (Level: EMERGENCY) fred'],
		];
	}

	/**
	 * @dataProvider add_DataProvider
	 */
	public function test_add($input, $expected) {
		$ML = new ExtendedMessageLogTest();

		$ML->add((empty($input[0]) ? null : $input[0]),
				(empty($input[1]) ? null : $input[1]),
				(empty($input[2]) ? null : $input[2])
		);
		$msg = $ML->extendedMessageQueue()->dequeue();
		//print_r($msg);

		$r = $msg->__toString(); //->get()[0];

		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');
	}

	//-----------------------------------------------------------------------------------------------
	public function test_addDebug() {
		$ML = new ExtendedMessageLogTest();

		$ML->addDebug('Hello World', 'fakeTimeStamp');
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = 'fakeTimeStamp (Level: DEBUG) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addDebug();
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: DEBUG) ';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addDebug('Hello World');
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: DEBUG) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');
	}

	//-----------------------------------------------------------------------------------------------
	public function test_addInfo() {
		$ML = new ExtendedMessageLogTest();

		$ML->addInfo('Hello World', 'fakeTimeStamp');
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = 'fakeTimeStamp (Level: INFO) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addInfo();
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: INFO) ';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addInfo('Hello World');
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: INFO) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');
	}

	//-----------------------------------------------------------------------------------------------
	public function test_addNotice() {
		$ML = new ExtendedMessageLogTest();

		$ML->addNotice('Hello World', 'fakeTimeStamp');
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = 'fakeTimeStamp (Level: NOTICE) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addNotice();
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: NOTICE) ';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addNotice('Hello World');
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: NOTICE) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');
	}

	//-----------------------------------------------------------------------------------------------
	public function test_addWarning() {
		$ML = new ExtendedMessageLogTest();

		$ML->addWarning('Hello World', 'fakeTimeStamp');
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = 'fakeTimeStamp (Level: WARNING) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addWarning();
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: WARNING) ';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addWarning('Hello World');
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: WARNING) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');
	}

	//-----------------------------------------------------------------------------------------------
	public function test_addError() {
		$ML = new ExtendedMessageLogTest();

		$ML->addError('Hello World', 'fakeTimeStamp');
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = 'fakeTimeStamp (Level: ERROR) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addError();
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: ERROR) ';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addError('Hello World');
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: ERROR) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');
	}

	//-----------------------------------------------------------------------------------------------
	public function test_addCritical() {
		$ML = new ExtendedMessageLogTest();

		$ML->addCritical('Hello World', 'fakeTimeStamp');
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = 'fakeTimeStamp (Level: CRITICAL) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addCritical();
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: CRITICAL) ';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addCritical('Hello World');
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: CRITICAL) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');
	}

	//-----------------------------------------------------------------------------------------------
	public function test_addAlert() {
		$ML = new ExtendedMessageLogTest();

		$ML->addAlert('Hello World', 'fakeTimeStamp');
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = 'fakeTimeStamp (Level: ALERT) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addAlert();
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: ALERT) ';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addAlert('Hello World');
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: ALERT) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');
	}

	//-----------------------------------------------------------------------------------------------
	public function test_addEmergency() {
		$ML = new ExtendedMessageLogTest();

		$ML->addEmergency('Hello World', 'fakeTimeStamp');
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = 'fakeTimeStamp (Level: EMERGENCY) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addEmergency();
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: EMERGENCY) ';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');

		$ML->addEmergency('Hello World');
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$msg = $ML->extendedMessageQueue()->dequeue();
		$r = $msg->__toString();

		$expected = $nowIsh . ' (Level: EMERGENCY) Hello World';
		$this->assertEquals($expected, $r, 'if it is a time diff of 1sec then just rerun the tests it should work');
	}

	//-----------------------------------------------------------------------------------------------
	public function test_hasMessages() {
		$ML = new ExtendedMessageLogTest();
		$r = $ML->extendedMessageQueue()->count();
		$this->assertEquals(0, $r);

		$ML->addEmergency('Hello World', 'fakeTimeStamp');
		$r = $ML->extendedMessageQueue()->count();
		$this->assertEquals(1, $r);


		$ML->addEmergency('Hello World', 'fakeTimeStamp');
		$r = $ML->extendedMessageQueue()->count();
		$this->assertEquals(2, $r);


		$ML->addEmergency('Hello World', 'fakeTimeStamp');
		$r = $ML->extendedMessageQueue()->count();
		$this->assertEquals(3, $r);

		$r = $ML->extendedMessageQueue()->count();
		$this->assertEquals(3, $r);


		$ML->extendedMessageQueue()->dequeue();
		$r = $ML->extendedMessageQueue()->count();
		$this->assertEquals(2, $r);

		$ML->extendedMessageQueue()->dequeue();
		$r = $ML->extendedMessageQueue()->count();
		$this->assertEquals(1, $r);

		$ML->extendedMessageQueue()->dequeue();
		$r = $ML->extendedMessageQueue()->count();
		$this->assertEquals(0, $r);

		$this->expectException(RuntimeException::class);

		$ML->extendedMessageQueue()->dequeue();
		$r = $ML->extendedMessageQueue()->count();
	}

	//-----------------------------------------------------------------------------------------------
	public function test_stackSize() {
		$ML = new ExtendedMessageLogTest();
		$r = $ML->stackSize();
		$this->assertEquals(0, $r);

		$ML->addEmergency('Hello World', 'fakeTimeStamp');
		$r = $ML->stackSize();
		$this->assertEquals(1, $r);


		$ML->addEmergency('Hello World', 'fakeTimeStamp');
		$r = $ML->stackSize();
		$this->assertEquals(2, $r);


		$ML->addEmergency('Hello World', 'fakeTimeStamp');
		$r = $ML->stackSize();
		$this->assertEquals(3, $r);

		$r = $ML->stackSize();
		$this->assertEquals(3, $r);


		$ML->extendedMessageQueue()->dequeue();
		$r = $ML->stackSize();
		$this->assertEquals(2, $r);

		$ML->extendedMessageQueue()->dequeue();
		$r = $ML->stackSize();
		$this->assertEquals(1, $r);

		$ML->extendedMessageQueue()->dequeue();
		$r = $ML->stackSize();
		$this->assertEquals(0, $r);

		$this->expectException(RuntimeException::class);

		$ML->extendedMessageQueue()->dequeue();
		$r = $ML->stackSize();
	}

	//-----------------------------------------------------------------------------------------------
	public function test_getNextMessage() {
		$ML = new ExtendedMessageLogTest();
		$ML->addEmergency('Hello World', 'fakeTimeStamp');

		$r = $ML->getNextMessage();
		$rs = $r->__toString();
		$expected = 'fakeTimeStamp (Level: EMERGENCY) Hello World';
		$this->assertEquals($r, $rs);

		$ML->addEmergency('Hello World1', 'fakeTimeStamp');
		$ML->addEmergency('Hello World2', 'fakeTimeStamp');
		$ML->addEmergency('Hello World3', 'fakeTimeStamp');
		$ML->addEmergency('Hello World4', 'fakeTimeStamp');

		$r = $ML->getNextMessage();
		$rs = $r->__toString();
		$expected = 'fakeTimeStamp (Level: EMERGENCY) Hello World1';

		$r = $ML->getNextMessage();
		$rs = $r->__toString();
		$expected = 'fakeTimeStamp (Level: EMERGENCY) Hello World2';

		$r = $ML->getNextMessage();
		$rs = $r->__toString();
		$expected = 'fakeTimeStamp (Level: EMERGENCY) Hello World3';

		$r = $ML->getNextMessage();
		$rs = $r->__toString();
		$expected = 'fakeTimeStamp (Level: EMERGENCY) Hello World4';

		$r = $ML->getNextMessage();
		$this->assertFalse($r);
	}

	//-----------------------------------------------------------------------------------------------
	public function test_showNextMessage_1() {
		$ML = new ExtendedMessageLogTest();

		$ML->addEmergency('Hello World', 'fakeTimeStamp');
		$this->expectOutputString('[fakeTimeStamp] <span class="msg_style_EMERGENCY">EMERGENCY </span>: Hello&nbsp;World');
		$ML->showNextMessage();
	}

	//-----------------------------------------------------------------------------------------------
	public function test_showNextMessage_2() {
		$ML = new ExtendedMessageLogTest();

		$ML->addEmergency('Hello World1', 'fakeTimeStamp');
		$ML->addAlert('Hello World2', 'fakeTimeStamp2');

		$this->expectOutputString('[fakeTimeStamp] <span class="msg_style_EMERGENCY">EMERGENCY </span>: Hello&nbsp;World1'
				. '[fakeTimeStamp2] <span class="msg_style_ALERT">ALERT </span>: Hello&nbsp;World2');

		$ML->showNextMessage();
		$ML->showNextMessage();
	}

	//-----------------------------------------------------------------------------------------------
	public function test_showAllMessages_1() {
		$ML = new ExtendedMessageLogTest();

		$ML->addEmergency('Hello World', 'fakeTimeStamp');
		$this->expectOutputString('[fakeTimeStamp] <span class="msg_style_EMERGENCY">EMERGENCY </span>: Hello&nbsp;World<br>');
		$ML->showAllMessages();
	}

	//-----------------------------------------------------------------------------------------------
	public function test_showAllMessages_2() {
		$ML = new ExtendedMessageLogTest();

		$ML->addEmergency('Hello World1', 'fakeTimeStamp');
		$ML->addAlert('Hello World2', 'fakeTimeStamp2');

		$this->expectOutputString('[fakeTimeStamp] <span class="msg_style_EMERGENCY">EMERGENCY </span>: Hello&nbsp;World1'
				. '<br>'
				. '[fakeTimeStamp2] <span class="msg_style_ALERT">ALERT </span>: Hello&nbsp;World2'
				. '<br>'
		);
		$ML->showAllMessages();
	}

	//-----------------------------------------------------------------------------------------------
	public function test_showAllMessagesInBox_withFieldset() {
		$ML = new ExtendedMessageLogTest();

		$ML->addEmergency('Hello World1', 'fakeTimeStamp');
		$ML->addAlert('Hello World2', 'fakeTimeStamp2');

		$this->expectOutputString('<fieldset class="msg_fieldset"><Legend id="message_box_show_all_in_box" class="msg_legend">Messages</legend>'
				. '[fakeTimeStamp] <span class="msg_style_EMERGENCY">EMERGENCY </span>: Hello&nbsp;World1<br>'
				. '[fakeTimeStamp2] <span class="msg_style_ALERT">ALERT </span>: Hello&nbsp;World2<br>'
				. '</fieldset>'
		);
		$ML->showAllMessagesInBox(true);
	}

	//-----------------------------------------------------------------------------------------------
	public function test_showAllMessagesInBox_withOUTFieldset() {
		$ML = new ExtendedMessageLogTest();

		$ML->addEmergency('Hello World1', 'fakeTimeStamp');
		$ML->addAlert('Hello World2', 'fakeTimeStamp2');

		$this->expectOutputString('[fakeTimeStamp] <span class="msg_style_EMERGENCY">EMERGENCY </span>: Hello&nbsp;World1<br>'
				. '[fakeTimeStamp2] <span class="msg_style_ALERT">ALERT </span>: Hello&nbsp;World2<br>'
		);
		$ML->showAllMessagesInBox(false);
	}

}

class ExtendedMessageLogTest extends MessageLog {

	function extendedMessageQueue() {
		return parent::$messageQueue;
	}

	static function extendedMessageQueueStatic() {
		return MessageLog::$messageQueue;
	}

}

abstract class ExtendedMessageBase extends MessageBase {

	abstract function Show() : void;

	abstract function Set($value = null) :void;

	abstract function Get();
}
