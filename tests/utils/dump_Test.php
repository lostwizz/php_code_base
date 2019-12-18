<?php

namespace Tests\Test;

use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Dump\DumpData as DumpData;
use \PHPUnit\Framework\TestCase;
////use \php_base\Utils\Dump\DumpExtendedClass as DumpExtendedClass;


class Dump_Test extends TestCase{


	public function testDumpData() {

		// \php_base\Utils\Dump
		$data = new DumpData();
		$dumpData = $data->ToArray();

		$expectedData =array(
						   'backTrace' => NULL,
						   'variableName' => NULL,
						   'fileName' => NULL,
						   'lineNum' => NULL,
						   'codeLine' => NULL,
						   'serverName' => NULL,
						   'title' => NULL,
						   'variable' => NULL,
						   'serverName' => null,
						   'preCodeLines' => array(),
						   'postCodeLines' => array()
						);

		$this->assertEquals(  $dumpData,$expectedData);

		$data->backTrace = 'hi';
		$dumpData = $data->ToArray();
		$expectedData['backTrace'] = 'hi';
		$this->assertEquals( $dumpData, $expectedData);
	}

	public function testDumpData1() {
		$data = new \php_base\Utils\Dump\DumpData();
		$t ='-This is a Title-';

		\Tests\Test\DumpExtendedClass::ExtendSetTitle($data, $t);
		$this->assertEquals($t, $data->title );

		$dumpData = \Tests\Test\DumpExtendedClass::ExtendBeautifyTitle($data);
		$expectedData = '<B><font color=green>-This is a Title-</B></font>' . "\n";
		$this->assertEquals($expectedData, $dumpData );
	}

	public function testDumpData2() {
		$data = new \php_base\Utils\Dump\DumpData();
		//$data->variable ='-this is a single line variable output-';
		$v  ='-this is a single line variable output-';
		\Tests\Test\DumpExtendedClass::ExtendSetVariableValue( $data, $v);
		$this->assertEquals($v, $data->variable );

		$dumpData = \Tests\Test\DumpExtendedClass::ExtendBeautifyAreaStart($data);
		$expectedData = "\n" . '<pre style="background-color: #FFFDCC; border-style: dashed; border-width: 1px; border-color: #950095;">'
		              . "\n" ;
		$this->assertEquals($expectedData, $dumpData );

		$dumpData = \Tests\Test\DumpExtendedClass::ExtendBeautifyAreaStart($data, '#FF00FF');
		$expectedData = "\n" . '<pre style="background-color: #FF00FF; border-style: dashed; border-width: 1px; border-color: #950095;">'
		              . "\n" ;
		$this->assertEquals($expectedData, $dumpData );

		$dumpData = \Tests\Test\DumpExtendedClass::ExtendBeautifyAreaStart($data, '#FF00FF', true);
		$expectedData = "\n" . '<pre style="background-color: #FF00FF; border-style: dashed; border-width: 1px; border-color: #950095;">'
		              . "\n" ;
		$this->assertEquals($expectedData, $dumpData );


		$data->variable ="\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";  //more than 10 lines (FLAT_WINDOW_LINES)
		$dumpData = \Tests\Test\DumpExtendedClass::ExtendBeautifyAreaStart($data, '#FF00FF', false);
		$expectedData = "\n\n"
						. '<pre style="background-color: #FF00FF; border-style: dashed; border-width: 1px;'
						. ' border-color: #950095;'
						. ' overflow: auto;'
						. ' padding-bottom: 0px; margin-bottom: 0px; width: 100%;'
						. ' height: 10em;'
						. '">'
						. "\n";
		$this->assertEquals($expectedData, $dumpData );

		$data->variable ="\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";  //more than 10 lines (FLAT_WINDOW_LINES)
		$dumpData = \Tests\Test\DumpExtendedClass::ExtendBeautifyAreaStart($data, '#FF00FF', true);
		$expectedData = "\n\n"
						. '<pre style="background-color: #FF00FF; border-style: dashed; border-width: 1px; border-color: #950095;'
						. ' overflow: auto;'
						. ' padding-bottom: 0px; margin-bottom: 0px; width: 100%;">'
						. "\n";
		$this->assertEquals($expectedData, $dumpData );



	}

	public function testDumpData3() {
		$data = new \php_base\Utils\Dump\DumpData();
		$data->variableName = '-This is a variable Name-';


		$dumpData = \Tests\Test\DumpExtendedClass::ExtendBeautifyAreaEnd($data);
		$expectedData ='</pre>';
		$this->assertEquals($expectedData, $dumpData );
	}

	public function testDumpData4() {
		$data = new \php_base\Utils\Dump\DumpData();
		$data->variableName = '-This is a variable Name-';

		$dumpData = \Tests\Test\DumpExtendedClass::ExtendBeautifyVariableName($data);
		$expectedData ='<font style="font-size: large; background-color: #7DEEA2; color: #950095"; font-weight: 999;>-This is a variable Name-</font>'
				. "\n";
		$this->assertEquals($expectedData, $dumpData );
	}

	public function testDumpData5() {
		$data = new \php_base\Utils\Dump\DumpData();
		$data->serverName = '-This is a Server Name-';
		$data->fileName= '-this is a filename-';
		$data->lineNum = 9999;

		$dumpData = \Tests\Test\DumpExtendedClass::ExtendBeautifyLineData($data);
		$dumpData = substr($dumpData,0,-1);   // have to remove the last character it is not quite a /r and cant quite match it properly

		//$expectedData ='<div align=right><font style="font-size: small; font-style: italic; color:#FF8000">  server=-This is a Server Name- -this is a filename-</font><font style="font-style: italic; font-weight: bold; color:#FF8000"> (line: 9999)</font></div>';
		$expectedData = '<div align=right><font style="font-size: small; font-style: italic; color:#417232"> </font><font style="font-size: small; font-style: italic; color:#FF8000">  server=-This is a Server Name- .\</font><font style="font-style: italic; font-weight: bold; color:#FF8000">-this is a filename- (line: 9999)</font><BR><font style="font-size: small; font-style: italic; color:#417232"> </font></div';
		$this->assertEquals($expectedData, $dumpData );
	}


	public function test_dumpSettings() {
		$this->markTestIncomplete('This test has not been implemented yet' );
	}




/*
	public function te_SetBackTrace()   :void {
		// this is very hard totest - the back trace is dependant on where you run it from
		$bt = array( 0=> array (
				    'file' => DIR .'index.php',
				    'line' => 55,
				    'function' => 'dump',
				    'class' => 'whitehorse\\php_base\\Utils\\Dump\\Dump',
				    'type' => '::',
				    'args' =>
				    array (
				      0 => 55,
				      1 => '-This is a Title-',
				      2 => true,
				    ),
				  )
			);

		$data = new \php_base\Utils\Dump\DumpData();

		DumpExtendedClass::ExtendSetBackTrace($data, $bt);
		$dumpData = $data->backTrace;
		$expectedData = DIR . 'index.php:55(dump)55, -This is a Title-, -True-' . "\n";
		$this->assertEquals($expectedData, $dumpData);

		$dumpData = DumpExtendedClass::ExtendBeautifyBackTrace($data);
		$expectedData= '<font color=#0000FF>' . DIR . 'index.php:55(dump)55, -This is a Title-, -True-' ."\n". '</font>' ."\n";

		$this->assertEquals($expectedData, $dumpData);

		$o = __Line__;
		DumpExtendedClass::ExtendSetVariableName( $data, $o, $bt);
		$this->assertEquals( DIR . 'index.php', $data->fileName );

		$this->assertEquals(55, $data->lineNum);

		$this->assertEquals('localhost', $data->serverName);

		$this->assertEquals( '//Dump::dump(__LINE__, \'-This is a Title-\',true);' ."\r\n", $data->codeLine, 'if fails then chec that line 55 in index.php is //Dump::dump....');

		$expected = "__LINE__, '-This is a Title-',true";
		$this->assertEquals( $expected, $data->variableName);

	}
}
*/
//class BackTraceTests  extends TestCase{
//
//	public function testBacktraceProcessing() :void {
//		$this->markTestIncomplete('This test has not been implemented yet' );
//	}
}

//
//namespace whitehorse\MikesCommandAndControl2\Utils;
////

//***********************************************************************************************************
class DumpExtendedClass extends Dump {

	public function testDumpPrerequisites() :void {
		$this->assertEquals( Dump::FLAT_WINDOW_LINES , 10);
	}

	public static function ExtendSetBackTrace($data, $bt){
		return parent::SetBackTrace($data, $bt);
	}
	public static function ExtendSetTitle($data, $bt){
		return parent::SetTitle($data, $bt);
	}
	public static function ExtendSetVariableName($data, $o, $bt){
		return parent::SetVariableName($data, $o, $bt);
	}
	public static function ExtendSetVariableValue($data, $bt){
		return parent::SetVariableValue($data, $bt);
	}

	public static function ExtendBeautifyTitle($o) {
		return parent::BeautifyTitle($o);
	}

	public static function ExtendBeautifyAreaStart($s, $bgColor='#FFFDCC', $skipNumLines =false){
		return parent::BeautifyAreaStart($s, $bgColor, $skipNumLines);
	}

//	public static function ExtendBeautifyAreaStart_2($s, $flat_lines){
//		parent::FLAT_WINDOW_LINES =$flat_lines;
//		return parent::BeautifyAreaStart($s);
//	}

	public static function ExtendBeautifyAreaEnd($s){
		return parent::BeautifyAreaEnd($s);
	}

	public static function ExtendBeautifyVariableName($s){
		return parent::BeautifyVariableName($s);
	}

	public static function ExtendBeautifyLineData($s){
		return parent::BeautifyLineData($s);
	}

	public static function ExtendBeautifyBackTrace($data){
		return parent::BeautifyBackTrace($data);
	}
}



//***********************************************************************************************************
class BackTraceExtendedClass extends \php_base\Utils\Dump\BackTraceProcessor {
	public static function ExtendProcessBTFunc($data){
		return parent::ProcessBTFunc($data);
	}
	public static function ExtendProcessBTArgs($data){
		return parent::ProcessBTArgs($data);
	}


}
