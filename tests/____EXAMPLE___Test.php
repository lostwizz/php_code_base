<?php

namespace Tests\Test;
use PHPUnit\Framework\TestCase;


use \php_base\Utils\HTML\HTML as HTML;

use \php_base\Utils\Settings as Settings;

///////////////////////////////////////////////////////////////////////////////////
// use this in the src code to check if PHPUnit is running its tests or not
/////	if (  defined( "IS_PHPUNIT_TESTING")){
///////////////////////////////////////////////////////////////////////////////////


class ExampleController_Test extends TestCase{


	public static function setUpBeforeClass(): void   {
		// do things before any tests start -- like change some settings
	}

	public function setup(){}

   protected function tearDown(){}


	public static function test_something(){
		$this->markTestIncomplete('This test has not been implemented yet' );
	}


	function Select_DataProvider(){
		return [
			['', null, null, null, null, null, '<Select name="" ></select>' . PHP_EOL],   //0
			['FRED', null, null, null, null, null, '<Select name="FRED" ></select>' . PHP_EOL],  //1
			['FRED', array(), null, null, null, null, '<Select name="FRED" ></select>' . PHP_EOL], //2
			];
	}

	/**
	* @dataProvider Select_DataProvider
	*/
	function test_fromDataProvider($in1, $in2, $in3,$in4, $in5, $in6, $expected){

		$actual = HTML::Select($in1, $in2, $in3, $in4, $in5, $in6);

		$this->assertEquals($expected, $actual);

	}



	////// you can use this tag instead fo the code lines
	/***
     * @expectedException InvalidArgumentException
     */


	function test_1_Exception(){
		$this->expectException(\ArgumentCountError::class);
		$this->expectExceptionMessage('Too few arguments to function php_base\Utils\HTML\HTML::Close(), 0 passed' );
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/0 passed/');

		$out = HTML::Close( );
	}

	function test_OutputString(){
		   $this->expectOutputString( $expected, $actual);
	}



}