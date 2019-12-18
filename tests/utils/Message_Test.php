<?php

use PHPUnit\Framework\TestCase;

use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\utils;

use \php_base\utils\AMessage as AMessage;

include_once (DIR . 'utils' . DSZ . 'messagelog.class.php');



//***********************************************************************************************
//***********************************************************************************************
class AMessageTest extends TestCase {


	//-----------------------------------------------------------------------------------------------
	public function test_levels() {
		$o = new anExtendedMessage();
		$a = $o->ExtendedGetLevels();
		$this->assertEquals('DEBUG', $a[100]);

		$expected =  array(
	        100     => 'DEBUG',
	        150 => 'TODO',
	        200      => 'INFO',
	        250    => 'NOTICE',
	        300   => 'WARNING',
	        400     => 'ERROR',
	        500  => 'CRITICAL',
	        550     => 'ALERT',
	        600 => 'EMERGENCY',
           );
      $this->assertEquals($expected, $a);

		$expected =  array(
	        anExtendedMessage::DEBUG     => 'DEBUG',
	        anExtendedMessage::INFO      => 'INFO',
	        anExtendedMessage::NOTICE    => 'NOTICE',
	        anExtendedMessage::WARNING   => 'WARNING',
	        anExtendedMessage::ERROR     => 'ERROR',
	        anExtendedMessage::CRITICAL  => 'CRITICAL',
	        anExtendedMessage::ALERT     => 'ALERT',
	        anExtendedMessage::EMERGENCY => 'EMERGENCY',
			anExtendedMessage::TODO      => 'TODO'

       	);
		$this->assertEquals($expected, $a);
	}

	//-----------------------------------------------------------------------------------------------
	public function test_constants() {
		$this->assertEquals(100 , anExtendedMessage::DEBUG);
		$this->assertEquals(150 , anExtendedMessage::TODO);
		$this->assertEquals(200 , anExtendedMessage::INFO);
		$this->assertEquals(250 , anExtendedMessage::NOTICE);
		$this->assertEquals(300 , anExtendedMessage::WARNING);
		$this->assertEquals(400 , anExtendedMessage::ERROR);
		$this->assertEquals(500 , anExtendedMessage::CRITICAL);
		$this->assertEquals(550 , anExtendedMessage::ALERT);
		$this->assertEquals(600 , anExtendedMessage::EMERGENCY);
	}


	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------
	public function ConstructWhenNotArray_DataProvider(){
	   return [
	            [['Hello World', 'someFakeTextTimeStamp', anExtendedMessage::DEBUG ],
	               ['Hello World', 'someFakeTextTimeStamp', anExtendedMessage::DEBUG, 'NotEmpty', 'DontNeedTime']],
	            [['Hello World', 'someFakeTextTimeStamp', anExtendedMessage::EMERGENCY ],
	               ['Hello World', 'someFakeTextTimeStamp', anExtendedMessage::EMERGENCY, 'NotEmpty', 'DontNeedTime']],
	            [['Hello World', null, null ],
	               ['Hello World', null, anExtendedMessage::NOTICE, 'NotEmpty', 'NeedTime']],
	            [['Hello World', 'aNewFakeTS', null ],
	               ['Hello World', 'aNewFakeTS', anExtendedMessage::NOTICE, 'NotEmpty', 'DontNeedTime']],
	            [[null, null, null ],
	               ['', null, anExtendedMessage::NOTICE, 'Empty', 'NeedTime']],
	            [[null, 'someFakeTextTimeStamp', null ],
	               ['', 'someFakeTextTimeStamp', anExtendedMessage::NOTICE, 'Empty', 'DontNeedTime']],
	            [[null, 'someFakeTextTimeStamp', anExtendedMessage::DEBUG ],
	               ['', 'someFakeTextTimeStamp', anExtendedMessage::DEBUG, 'Empty', 'DontNeedTime']],
	   ];
	}

	/**
	* @dataProvider ConstructWhenNotArray_DataProvider
	*/
	function test_ConstructWhenNotArray(){
		//---------------------------------------
		$o = new anExtendedMessage();
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$this->assertInstanceOf( 'anExtendedMessage', $o);
		$this->assertInstanceOf('\php_base\utils\AMessage', $o);

		$t = $o->ExtendedGetText();
		$ts = $o->ExtendedGetTimeStamp();
		$l = $o->ExtendedGetLevel();

		$this->assertEmpty($t); // no text set yet
		$this->assertNotNull($ts);
		$this->assertNotNull($l);

		$this->assertStringContainsString($nowIsh, $ts);
		$this->assertEquals('', $t);
		$this->assertEquals( anExtendedMessage::NOTICE, $l );
   }
//
	//-----------------------------------------------------------------------------------------------
	public function toString_DataProvider(){
	   return [
	            [['Hello World', 'someFakeTextTimeStamp', anExtendedMessage::DEBUG ],
	               ['Hello World', 'someFakeTextTimeStamp', 'DEBUG', 'NotEmpty', 'DontNeedTime']],

	            [['Hello World', 'someFakeTextTimeStamp', anExtendedMessage::EMERGENCY ],
	               ['Hello World', 'someFakeTextTimeStamp', 'EMERGENCY', 'NotEmpty', 'DontNeedTime']],

	            [['Hello World', null, null ],
	               ['Hello World', null, 'NOTICE', 'NotEmpty', 'NeedTime']],

	            [['Hello World', 'aNewFakeTS', null ],
	               ['Hello World', 'aNewFakeTS', 'NOTICE', 'NotEmpty', 'DontNeedTime']],

	            [[null, null, null ],
	               ['', null, 'NOTICE', 'Empty', 'NeedTime']],

	            [[null, 'someFakeTextTimeStamp', null ],
	               ['', 'someFakeTextTimeStamp', 'NOTICE', 'Empty', 'DontNeedTime']],

	            [[null, 'someFakeTextTimeStamp', anExtendedMessage::DEBUG ],
	               ['', 'someFakeTextTimeStamp', 'DEBUG', 'Empty', 'DontNeedTime']],

	            [[null, '88:88', anExtendedMessage::DEBUG ],
	               ['', '88:88', 'DEBUG', 'Empty', 'DontNeedTime']],

	            [[null, '12:01', anExtendedMessage::DEBUG ],
	               ['', '12:01', 'DEBUG', 'Empty', 'DontNeedTime']],
	   ];
	}
	/**
	* @dataProvider toString_DataProvider
	*/
	function test_toString($input, $expected){
		$o = new AMessage($input[0], $input[1], $input[2]);
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';
		$x = $o->__toString();

		$this->assertStringContainsString( $expected[0], $x);
		if (  $expected[4]== 'NeedTime'){
			$this->assertStringContainsString( $nowIsh, $x );
		} else {
			$this->assertStringContainsString( $expected[1], $x);
		}
		$this->assertStringContainsString( $expected[2], $x);
   }




	//-----------------------------------------------------------------------------------------------
	public function MessageDump_DataProvider(){
	   return [
	            [[null,null,null],
	                  ['msg= time=', ' level=NOTICE<Br>','NeedTime']],
	            [['Hello World',null,null],
	                  ['msg=Hello World time=', ' level=NOTICE<Br>', 'NeedTime']],
	            [['Hello World','FakeTimeStamp',null],
	                  ['msg=Hello World time=FakeTimeStamp', ' level=NOTICE<Br>', 'DontNeedTime']],
	            [['Hello World','FakeTimeStamp',anExtendedMessage::EMERGENCY],
	                  ['msg=Hello World time=FakeTimeStamp', ' level=EMERGENCY<Br>', 'DontNeedTime']],
	            [['Hello World','FakeTimeStamp',anExtendedMessage::DEBUG],
	                  ['msg=Hello World time=FakeTimeStamp', ' level=DEBUG<Br>', 'DontNeedTime']],
         ];
   }
	/**
	* @dataProvider MessageDump_DataProvider
	*/
	function test_messagedump($input, $expected){
		$o = new AMessage( $input[0], $input[1], $input[2]);
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';

		//$this->expectOutputString('msg= time=' . $nowIsh . ' level=NOTICE<Br>');
		if ( $expected[2] == 'NeedTime') {
		   $this->expectOutputString( $expected[0] . $nowIsh .  $expected[1]);
		} else {
		   $this->expectOutputString( $expected[0] . $expected[1]);
		}
		$x = $o->dump();
	}


	//-----------------------------------------------------------------------------------------------
	public function MessageSet_DataProvider(){
	   return [
	            [['Hello World', 'someFakeTextTimeStamp', anExtendedMessage::DEBUG ],
	               [null, null, null],
	               ['', 'someFakeTextTimeStamp', anExtendedMessage::NOTICE, 'Empty', 'NeedTime']],

	            [['Hello World', null, anExtendedMessage::DEBUG ],
	               [null, null, null],
	               ['', null, anExtendedMessage::NOTICE, 'Empty', 'NeedTime']],

	            [['Hello World', 'someFakeTextTimeStamp', anExtendedMessage::EMERGENCY ],
	               ['Fred Was here', null, anExtendedMessage::EMERGENCY],
	               ['Fred Was here', 'someFakeTextTimeStamp', anExtendedMessage::EMERGENCY, 'NotEmpty', 'NeedTime']],

	            [['Hello World', 'someFakeTextTimeStamp', anExtendedMessage::EMERGENCY ],
	               ['Fred Was here', null, anExtendedMessage::INFO],
	               ['Fred Was here', 'someFakeTextTimeStamp', anExtendedMessage::INFO, 'NotEmpty', 'NeedTime']],

	            [['Hello World', 'someFakeTextTimeStamp', anExtendedMessage::EMERGENCY ],
	               ['Fred Was here', 'more Fakeness', null],
	               ['Fred Was here', 'more Fakeness', anExtendedMessage::NOTICE, 'NotEmpty', 'DontNeedTime']],

	            [['Hello World', 'someFakeTextTimeStamp', anExtendedMessage::EMERGENCY ],
	               ['Fred Was here', 'more Fakeness', anExtendedMessage::INFO],
	               ['Fred Was here', 'more Fakeness', anExtendedMessage::INFO, 'NotEmpty', 'DontNeedTime']],

	            [[null, null, null ],
	               [null, null, null],
	               ['', null, anExtendedMessage::NOTICE, 'Empty', 'NeedTime']],

	            [[null, 'someFakeTextTimeStamp', null ],
	               [null, null, null],
	               ['', null, anExtendedMessage::NOTICE, 'Empty', 'NeedTime']],

	            [[null, 'someFakeTextTimeStamp', anExtendedMessage::DEBUG ],
	               [null, null, null],
	               ['', null, anExtendedMessage::NOTICE, 'Empty', 'NeedTime']],
	   ];
	}

	/**
	* @dataProvider MessageSet_DataProvider
	*/
	function test_MessageSet( $input, $setting, $expected ){
		$o = new anExtendedMessage($input[0], $input[1], $input[2]);

		$o->set($setting[0], $setting[1], $setting[2]);

		//$nowIsh =  date( 'g:i:');
				//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';


		$t = $o->ExtendedGetText();
		$ts = $o->ExtendedGetTimeStamp();
		$l = $o->ExtendedGetLevel();

      if ( $expected[3] == 'NotEmpty'){
		   $this->assertNotEmpty($t);
      } else {
		   $this->assertEmpty($t);
      }
		$this->assertNotNull($ts);
		$this->assertNotNull($l);

		$this->assertEquals($expected[0], $t);
      if($expected[4] =='DontNeedTime'){
		   $this->assertEquals($expected[1], $ts);
		} else {
		   $this->assertStringContainsString($nowIsh, $ts);
		}
		$this->assertEquals( $expected[2], $l );
   }

	//-----------------------------------------------------------------------------------------------
	function test_get(){
		$o = new AMessage('Hello'); // anExtendedMessage();
		//$nowIsh =  date( 'g:i:');
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';

		$x  = $o->get();

		$this->assertIsArray($x);
		$this->assertEquals( 3, count($x) );
		$this->assertEquals( 'Hello', $x[0]);
		$this->assertEquals( AMessage::NOTICE, $x[2]);
	}

	//-----------------------------------------------------------------------------------------------
	public function style_DataProvider(){
		return [
				[ anExtendedMessage::DEBUG, 'msg_style_DEBUG' ],
				[ anExtendedMessage::INFO, 'msg_style_INFO' ],
				[ anExtendedMessage::NOTICE, 'msg_style_NOTICE' ],
				[ anExtendedMessage::WARNING, 'msg_style_WARNING' ],
				[ anExtendedMessage::ERROR, 'msg_style_ERROR' ],
				[ anExtendedMessage::CRITICAL, 'msg_style_CRITICAL' ],
				[ anExtendedMessage::ALERT, 'msg_style_ALERT' ],
				[ anExtendedMessage::EMERGENCY, 'msg_style_EMERGENCY' ],
				[ 10, 'msg_style_UNKNOWN' ],
			];
	}

	/**
	* @dataProvider style_DataProvider
	*/
	function test_getShowStyle($test, $expected){
		$o = new anExtendedMessage();
		$x = $o->ExtendedGetShowStyle($test);

		$this->assertSame ($expected, $x);
	}

	//-----------------------------------------------------------------------------------------------
	public function textLeader_DataProvider(){
		return [
				[ anExtendedMessage::DEBUG, 'DEBUG ' ],
				[ anExtendedMessage::INFO, 'INFO ' ],
				[ anExtendedMessage::NOTICE, 'NOTICE ' ],
				[ anExtendedMessage::WARNING, 'WARNING ' ],
				[ anExtendedMessage::ERROR, 'ERROR ' ],
				[ anExtendedMessage::CRITICAL, 'CRITICAL ' ],
				[ anExtendedMessage::ALERT, 'ALERT ' ],
				[ anExtendedMessage::EMERGENCY, 'EMERGENCY ' ],
				[ 10, 'UNKNOWN ' ],
			];
	}

	/**
	* @dataProvider textLeader_DataProvider
	*/
	function test_textLeader ($test, $expected){
		$o = new anExtendedMessage();
		$x =$o->ExtendedgetShowTextLeader( $test);
		$this->assertEquals( $expected, $x);
	}

	//-----------------------------------------------------------------------------------------------
	function getPrettyLine_Dataprovider() {
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';

		return [
	            [['Hello World', null, null ],
	               '<span class="msg_style_NOTICE">[' . $nowIsh . '] NOTICE Hello&nbsp;World</span>'],

                [['Hello World', 'someFakeTextTimeStamp', anExtendedMessage::DEBUG ],
	               '<span class="msg_style_DEBUG">[someFakeTextTimeStamp] DEBUG Hello&nbsp;World</span>'],

	            [['Hello World', 'someFakeTextTimeStamp', anExtendedMessage::EMERGENCY ],
	               '<span class="msg_style_EMERGENCY">[someFakeTextTimeStamp] EMERGENCY Hello&nbsp;World</span>'],

	            [['Hello World', 'aNewFakeTS', null ],
	               '<span class="msg_style_NOTICE">[aNewFakeTS] NOTICE Hello&nbsp;World</span>'],

	            [[null, null, null ],
	               '<span class="msg_style_NOTICE">[' . $nowIsh . '] NOTICE </span>'],

	            [[null, 'someFakeTextTimeStamp', null ],
	               '<span class="msg_style_NOTICE">[someFakeTextTimeStamp] NOTICE </span>'],

	            [[null, 'someFakeTextTimeStamp', anExtendedMessage::DEBUG ],
	               '<span class="msg_style_DEBUG">[someFakeTextTimeStamp] DEBUG </span>'],

		];
	}


	/**
	*  @dataProvider getPrettyLine_Dataprovider
	*/
	function test_getPrettyLine($input, $expected){
		$o = new anExtendedMessage($input[0], $input[1], $input[2]);
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';

		$r = $o->ExtededGetPrettyLine();
		$this->assertEquals($expected, $r , '!!!if it is the time try running the test again (should only be 1sec diff)!!!');
	}

	//-----------------------------------------------------------------------------------------------
	function test_show() {
		$o = new AMessage('Hello World');
		//$nowIsh =  date( 'g:i:s');
		$nowIsh = '23:55:30';

		$expected ='<span class="msg_style_NOTICE">[' . $nowIsh . '] NOTICE Hello&nbsp;World</span>';
		$this->expectOutputString( $expected);
		$o->show();
	}



}


//***********************************************************************************************
//***********************************************************************************************
class anExtendedMessage extends AMessage {

	public function __construct( $textOrArray=null, $timestamp=null, $level=null) {
		parent::__construct( $textOrArray, $timestamp, $level);
	}

	public function ExtendedgetShowTextLeader($z){
		return parent::getShowTextLeader($z );
	}

	public function ExtendedGetLevels(){
		return parent::$levels;
	}

	public static function XXXExtendedGetLevels(){
		return parent::$levels;
	}

	public function ExtendedGetText(){
		return $this->text;
	}
	public function ExtendedGetTimeStamp(){
		return $this->timeStamp;
	}
	public function ExtendedGetLevel(){
		return $this->level;
	}
	public function ExtendedGetShowStyle($lv){
		return $this->getShowStyle($lv);
	}

	public function ExtededGetPrettyLine(){
		return $this->getPrettyLine();
	}
}
