<?php

namespace Tests\Test;
use PHPUnit\Framework\TestCase;


use \php_base\Utils\HTML\HTML as HTML;

use \php_base\Utils\Settings as Settings;

///////////////////////////////////////////////////////////////////////////////////
// use this in the src code to check if PHPUnit is running its tests or not
/////	if (  defined( "IS_PHPUNIT_TESTING")){
///////////////////////////////////////////////////////////////////////////////////


/// fwrite(STDERR, print_r($input, TRUE));


class ExampleController_Test extends TestCase {


	public static function setUpBeforeClass(): void   {
		// do things before any tests start -- like change some settings
		unlink('f:\temp\data.txt');
	}

	public static function tearDownAfterClass(): void {

	}

	public $fp;
	 public  function setup(): void{
		$this->fp = fopen('f:\temp\data.txt', 'a');

		fwrite($this->fp, print_r($_SESSION, TRUE));
	 }

	protected   function tearDown(): void{
		 fclose($this->fp);
	}


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






	// iterate thru an array dynamically
	function radio_dataProvider(){
		//$possibeTypes = ['CHECKBOX','RADIO','Reset', 'Password', 'Submit', 'BUTTON', 'TEXT', 'HIDDEN', 'FFRREEDD'];
		$possibleTypes = [
			'CHECKBOX',
			'RADIO',
			'Reset',
			'Password',
			'Submit',
			'BUTTON',
			'TEXT',
			'HIDDEN'
			];

		$outArray = array();

		$i =0;
		foreach($possibleTypes as $type ){

			$outArray[$i++]   =
			[$type, ["FRED", 'somewhere over the rainbow'],
				'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow">'];
		}

		$obj = new \ArrayIterator($outArray);
		return	$obj;
	}


		/**
	* @dataProvider radio_dataProvider
	*/
	function test_radio_button( $which, $in, $expected) {

		switch ( count($in)) {
//			case 0:
//				$actual = HTML::$which();
//				break;
//			case 1:
//				$actual = HTML::$which($in[0]);
//				break;
			case 2:
				$actual = HTML::$which($in[0], $in[1]);
				break;
			case 3:
				$actual = HTML::$which($in[0], $in[1], $in[2]);
				break;
			case 4:
				$actual = HTML::$which($in[0], $in[1], $in[2], $in[3]);
				break;
			case 5:
				$actual = HTML::$which($in[0], $in[1], $in[2], $in[3], $in[4]);
				break;
			case 6:
				$actual = HTML::$which($in[0], $in[1], $in[2], $in[3], $in[4], $in[5]);
				break;
			case 7:
				$actual = HTML::$which($in[0], $in[1], $in[2], $in[3], $in[4], $in[5], $in[6]);
				break;
		}
		$this->assertEquals( $expected, $actual );
	}




/////////////////////////////////
// example of php doc string setup
$x = <<<'EOD'
<BR>
<div id="dumpAreaStart_b" style="background-color: #FFFDCC; border-style: dashed; border-width: 1px; border-color: #950095;"><span id="varName" style="font-size: large; background-color: #7DEEA2; color: #950095; font-weight: 100;">'one',null, array('Only Return Output String' => true)</span><pre id="varData" style="font-size: large; background-color: ; color: #950095; font-weight: normal;">one</pre>
<div style="text-align: right;"><span id="LineData_A" style="font-size: small; font-style: normal; color:#FF8000; text-align: right;"> server=localhost P:\Projects\NB_projects\php_code_base\tests\utils\</span>
<span id="LineData_B" style="font-size: medium; font-style: bold; color:#8266F2; font-weight:bolder; text-align: right;">dump_Test.php (line: 61)</span></div>
</div>
EOD;
/////////////////////////////////



}