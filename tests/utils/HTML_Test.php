<?php

namespace Tests\Test;
use PHPUnit\Framework\TestCase;


use \whitehorse\MikesCommandAndControl2\Utils\HTML\HTML as HTML;
//use \whitehorse\MikesCommandAndControl2\Settings\Settings as Settings;
//use \whitehorse\MikesCommandAndControl2\Utils\Dump\Dump as Dump;
////use \whitehorse\MikesCommandAndControl2\Utils\Dump\DumpExtendedClass as DumpExtendedClass;


class HTML_Test extends TestCase{


////	function test_Doctype(){
////				$this->markTestIncomplete('This test has not been implemented yet' );
////	}
////
////	function test_filter_XSS(){
////		$this->markTestIncomplete('This test has not been implemented yet' );
////	}
////
////	function test_Select(){
////				$this->markTestIncomplete('This test has not been implemented yet' );
////	}

	function test_radio(){
				//$this->markTestIncomplete('This test has not been implemented yet' );
		$out = HTML::Radio("FRED", 7);
		$expect = '<input type="radio" name="FRED" value="7"/>';
		$this->assertEquals( $expect, $out);

		$out = HTML::Radio("FRED", 'somewhere over the rainbow');
		$expect = '<input type="radio" name="FRED" value="somewhere over the rainbow"/>';
		$this->assertEquals( $expect, $out);

		$out = HTML::Radio("FRED", 'somewhere over the rainbow', 'Where was Dorthy?');
		$expect = '<input type="radio" name="FRED" value="somewhere over the rainbow"/>Where was Dorthy?';
		$this->assertEquals( $expect, $out);

		$out = HTML::Radio("FRED", 'somewhere over the rainbow', 'Where was Dorthy?', true);
		$expect = '<input type="radio" name="FRED" value="somewhere over the rainbow" checked/>Where was Dorthy?';
		$this->assertEquals( $expect, $out);

		$out = HTML::Radio("FRED", 'somewhere over the rainbow', 'Where was Dorthy?', false);
		$expect = '<input type="radio" name="FRED" value="somewhere over the rainbow"/>Where was Dorthy?';
		$this->assertEquals( $expect, $out);

		$out = HTML::Radio("FRED", 'somewhere over the rainbow', null, true);
		$expect = '<input type="radio" name="FRED" value="somewhere over the rainbow" checked/>';
		$this->assertEquals( $expect, $out);


		$options =  array('alt'=>'TONY');
		$out = HTML::Radio("FRED", 'somewhere over the rainbow', null, true, $options);
		$expect = '<input type="radio" name="FRED" value="somewhere over the rainbow" checked alt="TONY"/>';
		$this->assertEquals( $expect, $out);

		$options['snow'] = 'somesnow';
		$out = HTML::Radio("FRED", 'somewhere over the rainbow', null, true, $options);
		$expect = '<input type="radio" name="FRED" value="somewhere over the rainbow" checked alt="TONY" snow="somesnow"/>';
		$this->assertEquals( $expect, $out);

		$style = 'backgroundcolor: yellow';
		$out = HTML::Radio("FRED", 'somewhere over the rainbow', null, true, $options, $style);
		$expect ='<input type="radio" name="FRED" value="somewhere over the rainbow" checked alt="TONY" snow="somesnow" style="backgroundcolor: yellow"/>';
		$this->assertEquals( $expect, $out);

		$style = array('backgroundcolor' => 'yellow');
		$out = HTML::Radio("FRED", 'somewhere over the rainbow', null, true, $options, $style);
		$expect = '<input type="radio" name="FRED" value="somewhere over the rainbow" checked alt="TONY" snow="somesnow" style="backgroundcolor: yellow; "/>';
		$this->assertEquals( $expect, $out);

		$out = HTML::Radio("FRED", 'somewhere over the rainbow', null, true, null, $style);
		$expect = '<input type="radio" name="FRED" value="somewhere over the rainbow" checked style="backgroundcolor: yellow; "/>';
		$this->assertEquals( $expect, $out);

		}


	function test_Image(){
		$out = HTML::Image( 'http://gis/cityofwhitehorseresources/Images/fire_icon.png');
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" border="0" />';
		$this->assertEquals( $expect, $out);

		$options =  array('alt'=>'FRED');
		$out = HTML::Image( 'http://gis/cityofwhitehorseresources/Images/fire_icon.png', $options);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" alt="FRED" border="0" />';
		$this->assertEquals( $expect, $out);

		$options['border'] = '4';
		$out = HTML::Image( 'http://gis/cityofwhitehorseresources/Images/fire_icon.png', $options);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" alt="FRED" border="4" />';
		$this->assertEquals( $expect, $out);

		$options['something'] = 'something else';
		$out = HTML::Image( 'http://gis/cityofwhitehorseresources/Images/fire_icon.png', $options);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" alt="FRED" border="4" something="something else" />';
		$this->assertEquals( $expect, $out);

		$style = array('backgroundcolor' => 'yellow');
		$out = HTML::Image( 'http://gis/cityofwhitehorseresources/Images/fire_icon.png', null, $style);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" border="0"  style="backgroundcolor: yellow; "/>';
		$this->assertEquals( $expect, $out);


		$style['foreground-color']  = 'blue';
		$out = HTML::Image( 'http://gis/cityofwhitehorseresources/Images/fire_icon.png', null, $style);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" border="0"  style="backgroundcolor: yellow; foreground-color: blue; "/>';
		$this->assertEquals( $expect, $out);

		$options =  array('tuesday'=>'isnext');
		$style = array('sam' => 'red');
		$out = HTML::Image( 'http://gis/cityofwhitehorseresources/Images/fire_icon.png', $options, $style);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" tuesday="isnext" border="0"  style="sam: red; "/>';
		$this->assertEquals( $expect, $out);

		$style['foreground-color']  = 'blue';
		$out = HTML::Image( 'http://gis/cityofwhitehorseresources/Images/fire_icon.png', $options, $style);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" tuesday="isnext" border="0"  style="sam: red; foreground-color: blue; "/>';
		$this->assertEquals( $expect, $out);


		//$style['encode'] = 'cyan';
//		$options =  array('video'=>'FRED');

		$style = array('one'=>'oneX', 'two'=>'twoX', 'three'=>'threeX', 'four'=>'fourX');
		$out = HTML::Image( 'http://gis/cityofwhitehorseresources/Images/fire_icon.png', $options, $style);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" tuesday="isnext" border="0"  style="one: oneX; two: twoX; three: threeX; four: fourX; "/>';
		$this->assertEquals( $expect, $out);

		$style = array('one'=>'oneX', 'two'=>'twoX', 'three'=>'threeX', 'four'=>'fourX');
		$out = HTML::Image( 'c:\temp\fred.www', $options, $style);
		$expect = '<img src="c:\temp\fred.www" tuesday="isnext" border="0"  style="one: oneX; two: twoX; three: threeX; four: fourX; "/>';
		$this->assertEquals( $expect, $out);

	}

	function test_Anchor(){
		$out = HTML::Anchor('c:\temp\fred.www');
		$expected = '<a href="c:\temp\fred.www">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);

		$out = HTML::Anchor('c:\temp\fred.www',null);
		$expected = '<a href="c:\temp\fred.www">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);

		$out = HTML::Anchor('c:\temp\fred.www',null,null);
		$expected = '<a href="c:\temp\fred.www">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);
		$out = HTML::Anchor('c:\temp\fred.www',null,null,null);
		$expected = '<a href="c:\temp\fred.www">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);

		$out = HTML::Anchor('c:\temp\fred.www', 'a_lbl');
		$expected = '<a href="c:\temp\fred.www">a_lbl</a>';
		$this->assertEquals($expected, $out);

		$options =  array('alt'=>'FRED');
		$out = HTML::Anchor('c:\temp\fred.www', 'a_lbl', $options);
		$expected = '<a href="c:\temp\fred.www" alt="FRED">a_lbl</a>';
		$this->assertEquals($expected, $out);

		$options =  array('alt'=>'FRED');
		$out = HTML::Anchor('c:\temp\fred.www', null, $options);
		$expected = '<a href="c:\temp\fred.www" alt="FRED">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);

		$options =  array('alt'=>'FRED');
		$out = HTML::Anchor('c:\temp\fred.www', null, $options, null);
		$expected = '<a href="c:\temp\fred.www" alt="FRED">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);


		$options =  array('alt'=>'FRED');
		$style=null;
		$out = HTML::Anchor('c:\temp\fred.www', null, $options, $style);
		$expected = '<a href="c:\temp\fred.www" alt="FRED">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);

		$options =  array('alt'=>'FRED');
		$style=null;
		$out = HTML::Anchor('c:\temp\fred.www', 'a_lblb', $options, $style);
		$expected = '<a href="c:\temp\fred.www" alt="FRED">a_lblb</a>';
		$this->assertEquals($expected, $out);


		$options =  array('alt'=>'FRED');
		$style = array('backgroundcolor' => 'yellow');
		$out = HTML::Anchor('c:\temp\fred.www', null, $options, $style);
		$expected = '<a href="c:\temp\fred.www" alt="FRED" style="backgroundcolor: yellow; ">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);

		$out = HTML::Anchor('c:\temp\fred.www', 'a_lbl', $options, $style);
		$expected = '<a href="c:\temp\fred.www" alt="FRED" style="backgroundcolor: yellow; ">a_lbl</a>';
		$this->assertEquals($expected, $out);


		$style['foreground-color']  = 'blue';
		$out = HTML::Anchor('c:\temp\fred.www', 'a_lbl', $options, $style);
		$expected = '<a href="c:\temp\fred.www" alt="FRED" style="backgroundcolor: yellow; foreground-color: blue; ">a_lbl</a>';
		$this->assertEquals($expected, $out);



	}

	function test_BR (){
		$out = HTML::br();
		$expected = '<BR />';
		$this->assertEquals( $expected, $out);

		$out = HTML::br(-1);
		$expected = '<BR />';
		$this->assertEquals( $expected, $out);

		$out = HTML::br('a');
		$expected = '<BR />';
		$this->assertEquals( $expected, $out);

		$out = HTML::br('abc');
		$expected = '<BR />';
		$this->assertEquals( $expected, $out);

		$out = HTML::br('1');
		$expected = '<BR />';
		$this->assertEquals( $expected, $out);

		$out = HTML::br('2');
		$expected = '<BR /><BR />';
		$this->assertEquals( $expected, $out);

		$out = HTML::br('5');
		$expected = '<BR /><BR /><BR /><BR /><BR />';
		$this->assertEquals( $expected, $out);


//		$this->assertTrue(false);
	}

	function test_Space (){
		$out = HTML::Space();
		$expected = '&nbsp;';
		$this->assertEquals( $expected, $out);

		$out = HTML::Space(-1);
		$expected = '&nbsp;';
		$this->assertEquals( $expected, $out);

		$out = HTML::Space('a');
		$expected = '&nbsp;';
		$this->assertEquals( $expected, $out);

		$out = HTML::Space('abc');
		$expected = '&nbsp;';
		$this->assertEquals( $expected, $out);

		$out = HTML::Space('1');
		$expected = '&nbsp;';
		$this->assertEquals( $expected, $out);

		$out = HTML::Space('2');
		$expected = '&nbsp;&nbsp;';
		$this->assertEquals( $expected, $out);

		$out = HTML::Space('5');
		$expected = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$this->assertEquals( $expected, $out);


//		$this->assertTrue(false);
	}

	function test_1_FormOpen(){
		$this->expectException(\TypeError::class);
		$this->expectExceptionMessage('Argument 1 passed to whitehorse\MikesCommandAndControl2\Utils\HTML\HTML::FormOpen() must be of the type string, null given, called ' );
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/must be of the type string/');

		$out = HTML::FormOpen(null);
		//$out = HTML::FormOpen('SAM');
	}

	function test_2_FormOpen(){
		$this->expectException(\TypeError::class);
		$this->expectExceptionMessage('Argument 2 passed to whitehorse\MikesCommandAndControl2\Utils\HTML\HTML::FormOpen() must be of the type string or null, object given,' );
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/must be of the type string/');

		$x = new \stdClass();
		$out = HTML::FormOpen('SAM', $x);
		//$out = HTML::FormOpen('SAM', array());
	}

	function test_3_FormOpen(){
		$this->expectException(\TypeError::class);
		$this->expectExceptionMessage('Argument 2 passed to whitehorse\MikesCommandAndControl2\Utils\HTML\HTML::FormOpen() must be of the type string or null, array given,' );
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/must be of the type string/');

		$out = HTML::FormOpen('SAM', array());
	}

//	function test_4_FormOpen(){
//		$this->expectException(\TypeError::class);
//		$this->expectExceptionMessage('Argument 2 passed to whitehorse\MikesCommandAndControl2\Utils\HTML\HTML::FormOpen() must be of the type string or null, array given,' );
//		$this->expectExceptionCode(0);
//		$this->expectExceptionMessageRegExp('/must be of the type string/');
//
//		$out = HTML::FormOpen('SAM');
//	}


	function test_FormOpen(){

		$out = HTML::FormOpen('FRED','SAM');
		$expected = '<form action="FRED" name="SAM" method="POST" enctype="multipart/form-data">' .  PHP_EOL . PHP_EOL ;
		$this->assertEquals($expected, $out);

		$out = HTML::FormOpen('FRED', '');
		$expected = '<form action="FRED" method="POST" enctype="multipart/form-data">' .  PHP_EOL . PHP_EOL ;
		$this->assertEquals($expected, $out);

		$out = HTML::FormOpen('FRED', 'SAM', 'GET');
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="multipart/form-data">' .  PHP_EOL . PHP_EOL ;
		$this->assertEquals($expected, $out);

		$out = HTML::FormOpen('FRED', 'SAM', 'GET', '');
		$expected = '<form action="FRED" name="SAM" method="GET">' .  PHP_EOL . PHP_EOL ;
		$this->assertEquals($expected, $out);

		$out = HTML::FormOpen('FRED', 'SAM', 'GET', 'sometype');
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="sometype">' .  PHP_EOL . PHP_EOL ;
		$this->assertEquals($expected, $out);

		$out = HTML::FormOpen('FRED', null, 'GET');
		$expected = '<form action="FRED" method="GET" enctype="multipart/form-data">' .  PHP_EOL . PHP_EOL ;
		$this->assertEquals($expected, $out);

		$out = HTML::FormOpen('FRED', null, 'GET', 'sometype');
		$expected = '<form action="FRED" method="GET" enctype="sometype">' .  PHP_EOL . PHP_EOL ;
		$this->assertEquals($expected, $out);

		$options =  array('alt'=>'TONY');
		$out = HTML::FormOpen('FRED', 'SAM', 'GET', 'sometype', $options);
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="sometype" alt="TONY">' .  PHP_EOL . PHP_EOL ;
		$this->assertEquals($expected, $out);

		$options['border'] = '4';
		$out = HTML::FormOpen('FRED', 'SAM', 'GET', 'sometype', $options);
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="sometype" alt="TONY" border="4">' .  PHP_EOL . PHP_EOL ;
		$this->assertEquals($expected, $out);

		$style = 'backgroundcolor: yellow';
		$out = HTML::FormOpen('FRED', 'SAM', 'GET', 'sometype', null, $style);
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="sometype" style="backgroundcolor: yellow">' .  PHP_EOL . PHP_EOL ;
		$this->assertEquals($expected, $out);

		$style = array('backgroundcolor' => 'yellow');
		$out = HTML::FormOpen('FRED', 'SAM', 'GET', 'sometype', null, $style);
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="sometype" style="backgroundcolor: yellow; ">' .  PHP_EOL . PHP_EOL ;
		$this->assertEquals($expected, $out);

		$style['forground-color'] = 'blue';
		$out = HTML::FormOpen('FRED', 'SAM', 'GET', 'sometype', null, $style);
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="sometype" style="backgroundcolor: yellow; forground-color: blue; ">' .  PHP_EOL . PHP_EOL ;
		$this->assertEquals($expected, $out);


		$out = HTML::FormOpen('FRED', 'SAM', 'GET', 'sometype', $options, $style);
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="sometype" alt="TONY" border="4" style="backgroundcolor: yellow; forground-color: blue; ">' .  PHP_EOL . PHP_EOL ;
		$this->assertEquals($expected, $out);
	}

 	function test_FormClose(){
 		$out = HTML::FormClose();
 		$expected = ' </form>' . PHP_EOL;
		$this->assertEquals($expected, $out);
 	}

	function test_1_Hidden() {
		$this->expectException(\ArgumentCountError::class);
		$this->expectExceptionMessage('Too few arguments to function whitehorse\MikesCommandAndControl2\Utils\HTML\HTML::Hidden(), 0 passed' );
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/0 passed/');

		$out = HTML::Hidden( );
	}

	function test_2_Hidden() {
		$this->expectException(\ArgumentCountError::class);
		$this->expectExceptionMessage('Too few arguments to function whitehorse\MikesCommandAndControl2\Utils\HTML\HTML::Hidden(), 1 passed' );
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/at least 2 expected/');

		$out = HTML::Hidden( 'SAM' );
	}

	function test_Hidden() {

		$out = HTML::Hidden( 'SAM', 'FRED' );
		$expected = '<input type=HIDDEN name="SAM" value="FRED">' . PHP_EOL;
		$this->assertEquals($expected, $out);

		$out = HTML::Hidden( 'SAM', 'FRED', null );
		$expected = '<input type=HIDDEN name="SAM" value="FRED">' . PHP_EOL;
		$this->assertEquals($expected, $out);

		$options =  array('alt'=>'TONY');
		$out = HTML::Hidden( 'SAM', 'FRED', $options );
		$expected = '<input type=HIDDEN name="SAM" value="FRED" alt="TONY">' . PHP_EOL;
		$this->assertEquals($expected, $out);

		$style = 'backgroundcolor: yellow';
		$out = HTML::Hidden( 'SAM', 'FRED', null, $style );
		$expected = '<input type=HIDDEN name="SAM" value="FRED" style="backgroundcolor: yellow">' . PHP_EOL;
		$this->assertEquals($expected, $out);

		$style = array('backgroundcolor' => 'yellow');
		$out = HTML::Hidden( 'SAM', 'FRED', null, $style );
		$expected = '<input type=HIDDEN name="SAM" value="FRED" style="backgroundcolor: yellow; ">' . PHP_EOL;
		$this->assertEquals($expected, $out);

		$options =  array('alt'=>'TONY');
		$style = array('backgroundcolor' => 'yellow');
		$expected = '<input type=HIDDEN name="SAM" value="FRED" style="backgroundcolor: yellow; ">' . PHP_EOL;
		$this->assertEquals($expected, $out);
	}

	function test_1_Open(){
		$this->expectException(\ArgumentCountError::class);
		$this->expectExceptionMessage('Too few arguments to function whitehorse\MikesCommandAndControl2\Utils\HTML\HTML::Open(), 0 passed' );
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/0 passed/');

		$out = HTML::Open( );
	}

	function test_Open(){
		$out = HTML::Open('SAMTAG' );
		$expected = '<SAMTAG>' . PHP_EOL;
		$this->assertEquals($expected, $out);

		$options =  array('alt'=>'TONY');
		$out = HTML::Open( 'SAMTAG',  $options );
		$expected = '<SAMTAG alt="TONY">' . PHP_EOL;
		$this->assertEquals($expected, $out);

		$options['snow'] = 'somesnow';
		$out = HTML::Open( 'SAMTAG',  $options );
		$expected = '<SAMTAG alt="TONY" snow="somesnow">' . PHP_EOL;
		$this->assertEquals($expected, $out);

		$style = 'backgroundcolor: yellow';
		$out = HTML::Open( 'SAMTAG',  null, $style );
		$expected = '<SAMTAG style="backgroundcolor: yellow">' . PHP_EOL;
		$this->assertEquals($expected, $out);

		$style = array('backgroundcolor' => 'yellow');
		$out = HTML::Open( 'SAMTAG',  null, $style );
		$expected = '<SAMTAG style="backgroundcolor: yellow; ">' . PHP_EOL;
		$this->assertEquals($expected, $out);
	}

	function test_1_Close(){
		$this->expectException(\ArgumentCountError::class);
		$this->expectExceptionMessage('Too few arguments to function whitehorse\MikesCommandAndControl2\Utils\HTML\HTML::Close(), 0 passed' );
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/0 passed/');

		$out = HTML::Close( );
	}

	function test_Close(){
		$out = HTML::Close('SAMTAG' );
		$expected = PHP_EOL . '</SAMTAG>' . PHP_EOL;
		$this->assertEquals($expected, $out);
	}

	function test_parseOptions(){
		$o = new ExtendedHTML();

		$out = $o->extended_parseOptions();
		$this->assertEquals('', $out);

		$out = $o->extended_parseOptions(array() );
		$this->assertEquals('', $out);

		$options =  'alt=FRED';
		$out = $o->extended_parseOptions( $options);
		$this->assertEquals(' alt=FRED', $out);


		$options =  array('alt'=>'FRED');
		$out = $o->extended_parseOptions( $options);
		$this->assertEquals(' alt="FRED"', $out);

		$options['border'] = '4';
		$out = $o->extended_parseOptions( $options);
		$this->assertEquals(' alt="FRED" border="4"', $out);

		$options['snow'] = 'somesnow';
		$out = $o->extended_parseOptions( $options);
		$this->assertEquals(' alt="FRED" border="4" snow="somesnow"', $out);

		$options['something'] = 'something else';
		$out = $o->extended_parseOptions( $options);
		$this->assertEquals(' alt="FRED" border="4" snow="somesnow" something="something else"', $out);

	}

	function test_parseStyle(){
		$o = new ExtendedHTML();

		$out = $o->extended_parseStyle();
		$this->assertEquals('', $out);

		$style = 'backgroundcolor: yellow';
		$out = $o->extended_parseStyle($style);
		$this->assertEquals(' style="backgroundcolor: yellow"', $out);

		$out = $o->extended_parseStyle(array());
		$this->assertEquals('', $out);

		$style = array('backgroundcolor' => 'yellow');
		$out = $o->extended_parseStyle($style);
		$this->assertEquals(' style="backgroundcolor: yellow; "', $out);

		$style['forground-color'] = 'blue';
		$out = $o->extended_parseStyle($style);
		$this->assertEquals(' style="backgroundcolor: yellow; forground-color: blue; "', $out);

		$style['encode'] = 'cyan';
		$out = $o->extended_parseStyle($style);
		$this->assertEquals(' style="backgroundcolor: yellow; forground-color: blue; encode: cyan; "', $out);

		$style['font'] = '13px "Arial",sans-serif';
		$out = $o->extended_parseStyle($style);
		$this->assertEquals(' style="backgroundcolor: yellow; forground-color: blue; encode: cyan; font: 13px "Arial",sans-serif; "', $out);

		$style['padding'] = '4px 4px 4px 4px';
		$out = $o->extended_parseStyle($style);
		$this->assertEquals(' style="backgroundcolor: yellow; forground-color: blue; encode: cyan; font: 13px "Arial",sans-serif; padding: 4px 4px 4px 4px; "', $out);

	}

	function test_version(){
		$out = HTML::Version();
		$expected = '0.1.0';
		$this->assertEquals($expected, $out);

	}



}

class ExtendedHTML extends HTML{

	 function extended_parseOptions($ar=null){
		return parent::parseOptions($ar);
	}

	function extended_parseStyle($ar=null){
		return parent::parseStyle($ar);
	}
}
