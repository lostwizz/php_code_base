<?php

namespace Tests\Test;
use PHPUnit\Framework\TestCase;



use \php_base\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Utils as Utils;

////use \php_base\Utils\Dump\DumpExtendedClass as DumpExtendedClass;






class Utils_Test extends TestCase{
//	public function test_something()   :void {
//				$this->markTestIncomplete('This test has not been implemented yet' );
//	}


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

	function test_ShowMoney() {
		$actual = Utils::ShowMoney(.00);
		$this->assertEquals( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$0.00', $actual);

		$actual = Utils::ShowMoney(.01);
		$this->assertEquals( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$0.01', $actual);

		$actual = Utils::ShowMoney(0.21);
		$this->assertEquals( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$0.21', $actual);

		$actual = Utils::ShowMoney(3.21);
		$this->assertEquals( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$3.21', $actual);

		$actual = Utils::ShowMoney(43.21);
		$this->assertEquals( '&nbsp;&nbsp;&nbsp;&nbsp;$43.21', $actual);

		$actual = Utils::ShowMoney(543.21);
		$this->assertEquals( '&nbsp;&nbsp;&nbsp;$543.21', $actual);

		$actual = Utils::ShowMoney(6543.21);
		$this->assertEquals( '&nbsp;$6,543.21', $actual);

		$actual = Utils::ShowMoney(9876543.21);
		$this->assertEquals( '$9,876,543.21', $actual);

		//-------------------------------------------------------------------------------------------------------
		$actual = Utils::ShowMoney(.00, 7);
		$this->assertEquals( '&nbsp;&nbsp;$0.00', $actual);

		$actual = Utils::ShowMoney(.01, 7);
		$this->assertEquals( '&nbsp;&nbsp;$0.01', $actual);

		$actual = Utils::ShowMoney(0.21, 7);
		$this->assertEquals( '&nbsp;&nbsp;$0.21', $actual);

		$actual = Utils::ShowMoney(3.21, 7);
		$this->assertEquals( '&nbsp;&nbsp;$3.21', $actual);

		$actual = Utils::ShowMoney(43.21, 7);
		$this->assertEquals( '&nbsp;$43.21', $actual);

		$actual = Utils::ShowMoney(543.21, 7);
		$this->assertEquals( '$543.21', $actual);

		$actual = Utils::ShowMoney(6543.21, 7);
		$this->assertEquals( '$6,543.21', $actual);

		$actual = Utils::ShowMoney(9876543.21, 7);
		$this->assertEquals( '$9,876,543.21', $actual);
	}

	function test_GiveTempFileName() {
		$actual = Utils::GiveTempFileName('f:\temp');
		$this->assertStringStartsWith('F:\temp\tmp', $actual);

		$actual = Utils::GiveTempFileName('f:\temp', 'TEST_+');
		$this->assertStringStartsWith('F:\temp\TES', $actual);
	}


	function test_GiveCurrentDate() {
		$actual = Utils::GiveCurrentDate();
		$this->assertStringContainsString('2019', $actual);
	}

	function test_makeRandomPassword(){
		$actual = Utils::makeRandomPassword();
		$this->assertEquals( 8, strlen($actual));

		$actual = Utils::makeRandomPassword(15);
		$this->assertEquals( 15, strlen($actual));
	}

	function test_startsWith(){
		$actual = Utils::startsWith('abc', 'ab');
		$this->assertTrue($actual);

		$actual = Utils::startsWith('abc', 'abc');
		$this->assertTrue($actual);

		$actual = Utils::startsWith('abc', 'abcd');
		$this->assertFalse($actual);

		$actual = Utils::startsWith('abc', 'b');
		$this->assertFalse($actual);

		$actual = Utils::startsWith('abc', '');
		$this->assertFalse($actual);
	}


	function test_backTraceHelper() {
		//$this->markTestIncomplete('This test has not been implemented yet' );

		$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,3);

		$actual = Utils::backTraceHelper($bt, 0);
		$expected = 'Class: <B>Tests\Test\Utils_Test</B> Called: <B>test_backTraceHelper</B> in: <B>';
		$this->assertStringContainsString($expected, $actual);

		$actual = Utils::backTraceHelper($bt, 1);
		$expected = 'Class: <B>PHPUnit\Framework\TestCase</B> Called: <B>runTest</B> in: <B>';
		$this->assertStringContainsString($expected, $actual);

		$actual = Utils::backTraceHelper($bt, 2);
		$expected = 'Class: <B>PHPUnit\Framework\TestCase</B> Called: <B>runBare</B> in: <B>';
		$this->assertStringContainsString($expected, $actual);

		//fwrite($this->fp, print_r($actual, TRUE));
		//fwrite($this->fp, print_r($bt, TRUE));



	}

}
