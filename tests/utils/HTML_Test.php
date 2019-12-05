<?php

namespace Tests\Test;

use PHPUnit\Framework\TestCase;

use \php_base\Utils\HTML\HTML as HTML;

use \php_base\Utils\Settings as Settings;

//use \php_base\Settings\Settings as Settings;
//use \php_base\Utils\Dump\Dump as Dump;
//use \php_base\Utils\Dump\DumpExtendedClass as DumpExtendedClass;
//////
//////$s = HTML::Space(3);
////////'theaction'
//////$s = HTML::FormOpen('fredform',  'invoice_form' );
////////$s = htmlspecialchars($s);
//////Dump::dump($s);
//////
//////
//////$a = array('one'=>'oneX', 'two'=>'twoX', 'three'=>'threeX', 'four'=>'fourX');
//////$s = HTML::Select( 'sam', $a, 'three', true);
//////Dump::dump( $s);
//////echo $s;
//////
//////echo '-------------1-';
//////echo HTML::BR(3);
//////
//////$s = HTML::Select( 'sam', $a, 'three', false);
//////Dump::dump( $s);
//////echo $s;
//////echo '-------------2-';
//////
//////
//////$a = array('one'=>'oneX', 'two'=>'twoX', 'three'=>'threeX', 'four'=>'fourX');
//////$s = HTML::Select( 'sam', $a, null, true);
//////Dump::dump( $s);
//////echo $s;
//////
//////
//////$style =  array('width' => '15em', 'height' =>'15em');
//////// style="width: 2em; height: 2em;"
//////
//////$s = HTML::Radio( 'fred', 'fred was here' , true,null, $style);
//////Dump::dump($s);
//////echo $s , 'some radioness';
//////
//////echo HTML::Radio("FRED", 'somewhere over the rainbow');
//////
//////$out = HTML::Image( 'http://gis/cityofwhitehorseresources/Images/fire_icon.png');
//////Dump::dump($out);
//////
//////$style = array('one'=>'oneX', 'two'=>'twoX', 'three'=>'threeX', 'four'=>'fourX');
//////
//////$out = HTML::Image( 'http://gis/cityofwhitehorseresources/Images/fire_icon.png',null, $style);
//////
//////	$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" />';
//////Dump::dump($out);
//////


class HTML_Test extends TestCase {

	public static function setUpBeforeClass(): void {
		include_once( DIR . 'utils' . DSZ . 'settings.class.php');
		require_once( DIR . '_config' . DSZ . '_Settings-General.php');
		require_once( DIR . '_config' . DSZ . '_Settings-Database.php');
		require_once( DIR . '_config' . DSZ . '_Settings-protected.php');

		require_once( 'P:\Projects\_Private_Settings.php');

		// force everyhting to setup
		// Settings::SetPublic( 'Use_MessageLog', true );  //true
		Settings::SetPublic('Use_DBLog', true);
		Settings::SetPublic('Use_DBdataLog', true);
		Settings::SetPublic('Use_FileLog', true);  // true
		Settings::SetPublic('Use_SecurityLog', true);
		Settings::SetPublic('Use_EmailLog', true);	  // true
		//require_once(DIR . 'utils\setup_Logging.php');

		Settings::SetPublic('Use_MessageLog', false);  //true

	}

	function test_filter_XSS() {
		$this->markTestIncomplete('This test has not been implemented yet');
	}

	function test_filter() {
		$this->markTestIncomplete('This test has not been implemented yet');
	}

	function Select_DataProvider() {
		return [
			['', null, null, null, null, null, '<Select name="" ></select>' . PHP_EOL], //0
			['FRED', null, null, null, null, null, '<Select name="FRED" ></select>' . PHP_EOL], //1
			['FRED', array(), null, null, null, null, '<Select name="FRED" ></select>' . PHP_EOL], //2
			['FRED', array('one'), null, null, null, null, '<Select name="FRED" ><option value="0">one</option>' . PHP_EOL  //3
				. '</select>' . PHP_EOL],
			['FRED', array('k1' => 'v1'), null, null, null, null, '<Select name="FRED" ><option value="k1">v1</option>' . PHP_EOL   //4
				. '</select>' . PHP_EOL],
			['FRED', array('k1' => 'v1', 'k2' => 'v2'), null, null, null, null, '<Select name="FRED" >'  //5
				. '<option value="k1">v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '</select>' . PHP_EOL],
			['FRED', array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), null, null, null, null, '<Select name="FRED" >'  //6
				. '<option value="k1">v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL
				. '</select>' . PHP_EOL],
			['FRED', array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), 0, null, null, null, '<Select name="FRED" >'   //7
				. '<option value="k1">v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL
				. '</select>' . PHP_EOL],
			['FRED', array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), 'k1', null, null, null, '<Select name="FRED" >'   //8
				. '<option value="k1" selected>v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL
				. '</select>' . PHP_EOL],
			['FRED', array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), 'k1', null, null, null, '<Select name="FRED" >'   //9
				. '<option value="k1" selected>v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL
				. '</select>' . PHP_EOL],
			['FRED', array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), 'k1', true, null, null, '<Select name="FRED" >'		 //10
				. '<option value="-1">- Select -</option>' . PHP_EOL
				. '<option value="k1" selected>v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL
				. '</select>' . PHP_EOL],
			['FRED', array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), 'k1', false, null, null, '<Select name="FRED" >'	  ///11
				. '<option value="k1" selected>v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL
				. '</select>' . PHP_EOL],
			['FRED', array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), -1, true, null, null, '<Select name="FRED" >'		/// 12
				. '<option value="-1" selected>- Select -</option>' . PHP_EOL
				. '<option value="k1">v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL
				. '</select>' . PHP_EOL],
			['FRED', array('k1' => 'v1', 0 => 'v2', 'k3' => 'v3'), 0, null, null, null, '<Select name="FRED" >'   //13
				. '<option value="k1">v1</option>' . PHP_EOL
				. '<option value="0" selected>v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL
				. '</select>' . PHP_EOL],
			['FRED', array('k1' => 'v1', 0 => 'v2', 'k3' => 'v3'), 0, null, array('alt' => 'TONY'), null, '<Select name="FRED"  alt="TONY">'   //14
				. '<option value="k1">v1</option>' . PHP_EOL
				. '<option value="0" selected>v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL
				. '</select>' . PHP_EOL],
			['FRED', array('k1' => 'v1', 0 => 'v2', 'k3' => 'v3'), 0, null, array('alt' => 'TONY'), array('backgroundcolor' => 'yellow'),
				'<Select name="FRED"  alt="TONY" style="backgroundcolor: yellow;">'   //15
				. '<option value="k1">v1</option>' . PHP_EOL
				. '<option value="0" selected>v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL
				. '</select>' . PHP_EOL],
		];
	}

	/**
	 * @dataProvider Select_DataProvider
	 */
	function test_Select($in1, $in2, $in3, $in4, $in5, $in6, $expected) {
		$a = HTML::Select($in1, $in2, $in3, $in4, $in5, $in6);
		$this->assertEquals($expected, $a);
	}

	function Options_DataProvider() {
		return [
			[array(), null, null, ''],
			['one', null, null, ''],
			[array('one'), null, null, '<option value="0">one</option>' . PHP_EOL],
			[array('k1' => 'v1'), null, null, '<option value="k1">v1</option>' . PHP_EOL],
			[array('k1' => 'v1', 'k2' => 'v2'), null, null, '<option value="k1">v1</option>'
				. PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL],
			[array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), null, null,
				'<option value="k1">v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL
			],
			[array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), 0, null,
				'<option value="k1">v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL
			],
			[array('k1' => 'v1', '0' => 'v2', 'k3' => 'v3'), 0, null,
				'<option value="k1">v1</option>' . PHP_EOL
				. '<option value="0" selected>v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL
			],
			[array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), 'k1', null, '<option value="k1" selected>v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL],
			[array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), 'k2', null, '<option value="k1">v1</option>' . PHP_EOL
				. '<option value="k2" selected>v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL],
			[array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), 'k3', null, '<option value="k1">v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3" selected>v3</option>' . PHP_EOL],
			[array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), 'k1', true,
				'<option value="-1">- Select -</option>' . PHP_EOL
				. '<option value="k1" selected>v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL],
			[array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), 'k2', true,
				'<option value="-1">- Select -</option>' . PHP_EOL
				. '<option value="k1">v1</option>' . PHP_EOL
				. '<option value="k2" selected>v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL],
			[array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), 'k3', true,
				'<option value="-1">- Select -</option>' . PHP_EOL
				. '<option value="k1">v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3" selected>v3</option>' . PHP_EOL],
			[array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), -1, true,
				'<option value="-1" selected>- Select -</option>' . PHP_EOL
				. '<option value="k1">v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL],
			[array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), -1, false,
				'<option value="k1">v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL],
			[array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), 9999, false,
				'<option value="k1">v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL],
			[array('k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'), 9999, true,
				'<option value="-1">- Select -</option>' . PHP_EOL
				. '<option value="k1">v1</option>' . PHP_EOL
				. '<option value="k2">v2</option>' . PHP_EOL
				. '<option value="k3">v3</option>' . PHP_EOL],
		];
	}

	/**
	 * @dataProvider Options_DataProvider
	 */
	function test_Options($in1, $in2, $in3, $expected) {

		$o = new ExtendedHTML();
		$a = $o->extended_Options($in1, $in2, $in3);
		$this->assertEquals($expected, $a);
	}

	function DocType_DataProvider() {
		return [
			['FRED', ''],
			[99, ''],
			[null, '<!DOCTYPE html>' . "\n"],
			['html5', '<!DOCTYPE html>' . "\n"],
			['xhtml11', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">' . "\n"],
			['xhtml1-strict', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n"],
			['xhtml1-trans', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n"],
			['xhtml1-frame', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">' . "\n"],
			['html4-strict', '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">' . "\n"],
			['html4-trans', '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n"],
			['html4-frame', '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">' . "\n"],
		];
	}

	/**
	 * @dataProvider DocType_DataProvider
	 */
	function test_DocType($input, $expected) {
		//$this->markTestIncomplete('This test has not been implemented yet' );
		if (is_null($input)) {
			$a = HTML::DocType();
		} else {
			$a = HTML::DocType($input);
		}
		$this->assertEquals($expected, $a);
	}

	function test_2_DocType() {
		$this->expectException(\TypeError::class);
		$this->expectExceptionMessage('Argument 1 passed to php_base\Utils\HTML\HTML::DocType() must be of the type string, object given');
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/object given/');

		$out = HTML::DocType(new \stdClass());
	}

	function test_3_DocType() {
		$this->expectException(\TypeError::class);
		$this->expectExceptionMessage('Argument 1 passed to php_base\Utils\HTML\HTML::DocType() must be of the type string, null given');
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/null given/');

		$out = HTML::DocType(null);
	}



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
			//$a = [$k, ["FRED", 'somewhere over the rainbow'],
			//	'<Input type="' . $k . '" name="FRED" value="somewhere over the rainbow">'];
			//		;

			$outArray[$i++]   =
			[$type, ["FRED", 'somewhere over the rainbow'],
				'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow">'];

			$outArray[$i++]   =
			[$type, ["FRED", 'somewhere over the rainbow', 'Where was Dorthy?' ],
				'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow">Where was Dorthy?'];
			$outArray[$i++]   =
			[$type, ["FRED", 'somewhere over the rainbow', 'Where was Dorthy?', false],
						'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow">Where was Dorthy?'];
			$outArray[$i++]   =
			[$type, ["FRED", 'somewhere over the rainbow', null, true],
						'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow" checked>'];
			$outArray[$i++]   =
			[$type, ["FRED", 'somewhere over the rainbow', null, true,array('alt' => 'TONY')],
						'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow" alt="TONY" checked>'];
			$outArray[$i++]   =
			[$type, ["FRED", 'somewhere over the rainbow', null, true, ['snow' => 'somesnow']],
						'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow" snow="somesnow" checked>'];
			$outArray[$i++]   =
			[$type, ["FRED", 'somewhere over the rainbow', null, true, ['snow' => 'somesnow'], 'backgroundcolor: yellow;'],
						'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow" snow="somesnow" checked style="backgroundcolor: yellow;">'];
			$outArray[$i++]   =
			[$type, ["FRED", 'somewhere over the rainbow', null, true, null, 'backgroundcolor: yellow;'],
						'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow" checked style="backgroundcolor: yellow;">'];
			$outArray[$i++]   =
			[$type, ["FRED", 'somewhere over the rainbow', null, true, null, ['backgroundcolor' => 'yellow', 'color' =>'blue']],
						'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow" checked style="backgroundcolor: yellow; color: blue;">'];

		}


		$obj = new \ArrayIterator($outArray);
		return	$obj;
	}


	/**
	* @dataProvider radio_dataProvider
	*/
	function test_arrayitorator( $which, $in, $expected){
		//$this->assertEquals( $in, $which);
		$this->assertNotEquals( $in, $which);
	}

	function xtest_radio_button( $which, $in, $expected) {

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







	function xtest_radio() {
		//$this->markTestIncomplete('This test has not been implemented yet' );




		$out = HTML::Radio("FRED", 7);
		$expect = '<Input type="RADIO" name="FRED" value="7">';
		$this->assertEquals($expect, $out);

		$out = HTML::Radio("FRED", 'somewhere over the rainbow');
		$expect = '<Input type="RADIO" name="FRED" value="somewhere over the rainbow">';
		$this->assertEquals($expect, $out);

		$out = HTML::Radio("FRED", 'somewhere over the rainbow', 'Where was Dorthy?');
		$expect = '<Input type="RADIO" name="FRED" value="somewhere over the rainbow">Where was Dorthy?';
		$this->assertEquals($expect, $out);

		$out = HTML::Radio("FRED", 'somewhere over the rainbow', 'Where was Dorthy?', true);
		$expect = '<Input type="RADIO" name="FRED" value="somewhere over the rainbow" checked>Where was Dorthy?';
		$this->assertEquals($expect, $out);

//		['Radio', ["FRED", 'somewhere over the rainbow', 'Where was Dorthy?', false],
//			'<Input type="RADIO" name="FRED" value="somewhere over the rainbow">Where was Dorthy?'],
//		['Radio', ["FRED", 'somewhere over the rainbow', null, true],
//			'<Input type="RADIO" name="FRED" value="somewhere over the rainbow" checked>'],



		$out = HTML::Radio("FRED", 'somewhere over the rainbow', 'Where was Dorthy?', false);
		$expect = '<Input type="RADIO" name="FRED" value="somewhere over the rainbow">Where was Dorthy?';
		$this->assertEquals($expect, $out);

		$out = HTML::Radio("FRED", 'somewhere over the rainbow', null, true);
		$expect = '<Input type="RADIO" name="FRED" value="somewhere over the rainbow" checked>';
		$this->assertEquals($expect, $out);




		$options = array('alt' => 'TONY');
		$out = HTML::Radio("FRED", 'somewhere over the rainbow', null, true, $options);
		$expect = '<Input type="RADIO" name="FRED" value="somewhere over the rainbow" alt="TONY" checked>';
		$this->assertEquals($expect, $out);

		$options['snow'] = 'somesnow';
		$out = HTML::Radio("FRED", 'somewhere over the rainbow', null, true, $options);
		$expect = '<Input type="RADIO" name="FRED" value="somewhere over the rainbow" alt="TONY" snow="somesnow" checked>';
		$this->assertEquals($expect, $out);



//		['Radio', ["FRED", 'somewhere over the rainbow', null, true, null, $style],
//			'<Input type="RADIO" name="FRED" value="somewhere over the rainbow" checked style="backgroundcolor: yellow; ">'],




		$style = 'backgroundcolor: yellow';
		$out = HTML::Radio("FRED", 'somewhere over the rainbow', null, true, $options, $style);
		$expect = '<Input type="RADIO" name="FRED" value="somewhere over the rainbow" alt="TONY" snow="somesnow" checked style="backgroundcolor: yellow">';
		$this->assertEquals($expect, $out);

		$style = array('backgroundcolor' => 'yellow');
		$out = HTML::Radio("FRED", 'somewhere over the rainbow', null, true, $options, $style);
		$expect = '<Input type="RADIO" name="FRED" value="somewhere over the rainbow" alt="TONY" snow="somesnow" checked style="backgroundcolor: yellow; ">';
		$this->assertEquals($expect, $out);

		$out = HTML::Radio("FRED", 'somewhere over the rainbow', null, true, null, $style);
		$expect = '<Input type="RADIO" name="FRED" value="somewhere over the rainbow" checked style="backgroundcolor: yellow; ">';
		$this->assertEquals($expect, $out);
	}

	function test_Image() {
		$out = HTML::Image('http://gis/cityofwhitehorseresources/Images/fire_icon.png');
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" border="0" />';
		$this->assertEquals($expect, $out);

		$options = array('alt' => 'FRED');
		$out = HTML::Image('http://gis/cityofwhitehorseresources/Images/fire_icon.png', $options);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" alt="FRED" border="0" />';
		$this->assertEquals($expect, $out);

		$options['border'] = '4';
		$out = HTML::Image('http://gis/cityofwhitehorseresources/Images/fire_icon.png', $options);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" alt="FRED" border="4" />';
		$this->assertEquals($expect, $out);

		$options['something'] = 'something else';
		$out = HTML::Image('http://gis/cityofwhitehorseresources/Images/fire_icon.png', $options);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" alt="FRED" border="4" something="something else" />';
		$this->assertEquals($expect, $out);

		$style = array('backgroundcolor' => 'yellow');
		$out = HTML::Image('http://gis/cityofwhitehorseresources/Images/fire_icon.png', null, $style);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" border="0"  style="backgroundcolor: yellow;"/>';
		$this->assertEquals($expect, $out);


		$style['foreground-color'] = 'blue';
		$out = HTML::Image('http://gis/cityofwhitehorseresources/Images/fire_icon.png', null, $style);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" border="0"  style="backgroundcolor: yellow; foreground-color: blue;"/>';
		$this->assertEquals($expect, $out);

		$options = array('tuesday' => 'isnext');
		$style = array('sam' => 'red');
		$out = HTML::Image('http://gis/cityofwhitehorseresources/Images/fire_icon.png', $options, $style);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" tuesday="isnext" border="0"  style="sam: red;"/>';
		$this->assertEquals($expect, $out);

		$style['foreground-color'] = 'blue';
		$out = HTML::Image('http://gis/cityofwhitehorseresources/Images/fire_icon.png', $options, $style);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" tuesday="isnext" border="0"  style="sam: red; foreground-color: blue;"/>';
		$this->assertEquals($expect, $out);


		//$style['encode'] = 'cyan';
//		$options =  array('video'=>'FRED');

		$style = array('one' => 'oneX', 'two' => 'twoX', 'three' => 'threeX', 'four' => 'fourX');
		$out = HTML::Image('http://gis/cityofwhitehorseresources/Images/fire_icon.png', $options, $style);
		$expect = '<img src="http://gis/cityofwhitehorseresources/Images/fire_icon.png" tuesday="isnext" border="0"  style="one: oneX; two: twoX; three: threeX; four: fourX;"/>';
		$this->assertEquals($expect, $out);

		$style = array('one' => 'oneX', 'two' => 'twoX', 'three' => 'threeX', 'four' => 'fourX');
		$out = HTML::Image('c:\temp\fred.www', $options, $style);
		$expect = '<img src="c:\temp\fred.www" tuesday="isnext" border="0"  style="one: oneX; two: twoX; three: threeX; four: fourX;"/>';
		$this->assertEquals($expect, $out);
	}

	function test_Anchor() {
		$out = HTML::Anchor('c:\temp\fred.www');
		$expected = '<a href="c:\temp\fred.www">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);

		$out = HTML::Anchor('c:\temp\fred.www', null);
		$expected = '<a href="c:\temp\fred.www">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);

		$out = HTML::Anchor('c:\temp\fred.www', null, null);
		$expected = '<a href="c:\temp\fred.www">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);
		$out = HTML::Anchor('c:\temp\fred.www', null, null, null);
		$expected = '<a href="c:\temp\fred.www">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);

		$out = HTML::Anchor('c:\temp\fred.www', 'a_lbl');
		$expected = '<a href="c:\temp\fred.www">a_lbl</a>';
		$this->assertEquals($expected, $out);

		$options = array('alt' => 'FRED');
		$out = HTML::Anchor('c:\temp\fred.www', 'a_lbl', $options);
		$expected = '<a href="c:\temp\fred.www" alt="FRED">a_lbl</a>';
		$this->assertEquals($expected, $out);

		$options = array('alt' => 'FRED');
		$out = HTML::Anchor('c:\temp\fred.www', null, $options);
		$expected = '<a href="c:\temp\fred.www" alt="FRED">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);

		$options = array('alt' => 'FRED');
		$out = HTML::Anchor('c:\temp\fred.www', null, $options, null);
		$expected = '<a href="c:\temp\fred.www" alt="FRED">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);


		$options = array('alt' => 'FRED');
		$style = null;
		$out = HTML::Anchor('c:\temp\fred.www', null, $options, $style);
		$expected = '<a href="c:\temp\fred.www" alt="FRED">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);

		$options = array('alt' => 'FRED');
		$style = null;
		$out = HTML::Anchor('c:\temp\fred.www', 'a_lblb', $options, $style);
		$expected = '<a href="c:\temp\fred.www" alt="FRED">a_lblb</a>';
		$this->assertEquals($expected, $out);


		$options = array('alt' => 'FRED');
		$style = array('backgroundcolor' => 'yellow');
		$out = HTML::Anchor('c:\temp\fred.www', null, $options, $style);
		$expected = '<a href="c:\temp\fred.www" alt="FRED" style="backgroundcolor: yellow;">c:\temp\fred.www</a>';
		$this->assertEquals($expected, $out);

		$out = HTML::Anchor('c:\temp\fred.www', 'a_lbl', $options, $style);
		$expected = '<a href="c:\temp\fred.www" alt="FRED" style="backgroundcolor: yellow;">a_lbl</a>';
		$this->assertEquals($expected, $out);


		$style['foreground-color'] = 'blue';
		$out = HTML::Anchor('c:\temp\fred.www', 'a_lbl', $options, $style);
		$expected = '<a href="c:\temp\fred.www" alt="FRED" style="backgroundcolor: yellow; foreground-color: blue;">a_lbl</a>';
		$this->assertEquals($expected, $out);
	}

	function test_BR() {
		$out = HTML::br();
		$expected = '<BR />';
		$this->assertEquals($expected, $out);

		$out = HTML::br(-1);
		$expected = '<BR />';
		$this->assertEquals($expected, $out);

		$out = HTML::br('a');
		$expected = '<BR />';
		$this->assertEquals($expected, $out);

		$out = HTML::br('abc');
		$expected = '<BR />';
		$this->assertEquals($expected, $out);

		$out = HTML::br('1');
		$expected = '<BR />';
		$this->assertEquals($expected, $out);

		$out = HTML::br('2');
		$expected = '<BR /><BR />';
		$this->assertEquals($expected, $out);

		$out = HTML::br('5');
		$expected = '<BR /><BR /><BR /><BR /><BR />';
		$this->assertEquals($expected, $out);


//		$this->assertTrue(false);
	}

	function test_Space() {
		$out = HTML::Space();
		$expected = '&nbsp;';
		$this->assertEquals($expected, $out);

		$out = HTML::Space(-1);
		$expected = '&nbsp;';
		$this->assertEquals($expected, $out);

		$out = HTML::Space('a');
		$expected = '&nbsp;';
		$this->assertEquals($expected, $out);

		$out = HTML::Space('abc');
		$expected = '&nbsp;';
		$this->assertEquals($expected, $out);

		$out = HTML::Space('1');
		$expected = '&nbsp;';
		$this->assertEquals($expected, $out);

		$out = HTML::Space('2');
		$expected = '&nbsp;&nbsp;';
		$this->assertEquals($expected, $out);

		$out = HTML::Space('5');
		$expected = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$this->assertEquals($expected, $out);


//		$this->assertTrue(false);
	}

	function test_1_FormOpen() {
		$this->expectException(\TypeError::class);
		$this->expectExceptionMessage('Argument 1 passed to php_base\Utils\HTML\HTML::FormOpen() must be of the type string, null given, called ');
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/must be of the type string/');

		$out = HTML::FormOpen(null);
		//$out = HTML::FormOpen('SAM');
	}

	function test_2_FormOpen() {
		$this->expectException(\TypeError::class);
		$this->expectExceptionMessage('Argument 2 passed to php_base\Utils\HTML\HTML::FormOpen() must be of the type string or null, object given,');
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/must be of the type string/');

		$x = new \stdClass();
		$out = HTML::FormOpen('SAM', $x);
		//$out = HTML::FormOpen('SAM', array());
	}

	function test_3_FormOpen() {
		$this->expectException(\TypeError::class);
		$this->expectExceptionMessage('Argument 2 passed to php_base\Utils\HTML\HTML::FormOpen() must be of the type string or null, array given,');
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/must be of the type string/');

		$out = HTML::FormOpen('SAM', array());
	}

//	function test_4_FormOpen(){
//		$this->expectException(\TypeError::class);
//		$this->expectExceptionMessage('Argument 2 passed to php_base\Utils\HTML\HTML::FormOpen() must be of the type string or null, array given,' );
//		$this->expectExceptionCode(0);
//		$this->expectExceptionMessageRegExp('/must be of the type string/');
//
//		$out = HTML::FormOpen('SAM');
//	}


	function test_FormOpen() {

		$out = HTML::FormOpen('FRED', 'SAM');
		$expected = '<form action="FRED" name="SAM" method="POST" enctype="multipart/form-data">' . PHP_EOL . PHP_EOL;
		$this->assertEquals($expected, $out);

		$out = HTML::FormOpen('FRED', '');
		$expected = '<form action="FRED" method="POST" enctype="multipart/form-data">' . PHP_EOL . PHP_EOL;
		$this->assertEquals($expected, $out);

		$out = HTML::FormOpen('FRED', 'SAM', 'GET');
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="multipart/form-data">' . PHP_EOL . PHP_EOL;
		$this->assertEquals($expected, $out);

		$out = HTML::FormOpen('FRED', 'SAM', 'GET', '');
		$expected = '<form action="FRED" name="SAM" method="GET">' . PHP_EOL . PHP_EOL;
		$this->assertEquals($expected, $out);

		$out = HTML::FormOpen('FRED', 'SAM', 'GET', 'sometype');
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="sometype">' . PHP_EOL . PHP_EOL;
		$this->assertEquals($expected, $out);

		$out = HTML::FormOpen('FRED', null, 'GET');
		$expected = '<form action="FRED" method="GET" enctype="multipart/form-data">' . PHP_EOL . PHP_EOL;
		$this->assertEquals($expected, $out);

		$out = HTML::FormOpen('FRED', null, 'GET', 'sometype');
		$expected = '<form action="FRED" method="GET" enctype="sometype">' . PHP_EOL . PHP_EOL;
		$this->assertEquals($expected, $out);

		$options = array('alt' => 'TONY');
		$out = HTML::FormOpen('FRED', 'SAM', 'GET', 'sometype', $options);
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="sometype" alt="TONY">' . PHP_EOL . PHP_EOL;
		$this->assertEquals($expected, $out);

		$options['border'] = '4';
		$out = HTML::FormOpen('FRED', 'SAM', 'GET', 'sometype', $options);
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="sometype" alt="TONY" border="4">' . PHP_EOL . PHP_EOL;
		$this->assertEquals($expected, $out);

		$style = 'backgroundcolor: yellow';
		$out = HTML::FormOpen('FRED', 'SAM', 'GET', 'sometype', null, $style);
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="sometype" style="backgroundcolor: yellow">' . PHP_EOL . PHP_EOL;
		$this->assertEquals($expected, $out);

		$style = array('backgroundcolor' => 'yellow');
		$out = HTML::FormOpen('FRED', 'SAM', 'GET', 'sometype', null, $style);
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="sometype" style="backgroundcolor: yellow;">' . PHP_EOL . PHP_EOL;
		$this->assertEquals($expected, $out);

		$style['forground-color'] = 'blue';
		$out = HTML::FormOpen('FRED', 'SAM', 'GET', 'sometype', null, $style);
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="sometype" style="backgroundcolor: yellow; forground-color: blue;">' . PHP_EOL . PHP_EOL;
		$this->assertEquals($expected, $out);


		$out = HTML::FormOpen('FRED', 'SAM', 'GET', 'sometype', $options, $style);
		$expected = '<form action="FRED" name="SAM" method="GET" enctype="sometype" alt="TONY" border="4" style="backgroundcolor: yellow; forground-color: blue;">' . PHP_EOL . PHP_EOL;
		$this->assertEquals($expected, $out);
	}

	function test_FormClose() {
		$out = HTML::FormClose();
		$expected = ' </form>' . PHP_EOL;
		$this->assertEquals($expected, $out);
	}

	function test_1_Hidden() {
		$this->expectException(\ArgumentCountError::class);
		$this->expectExceptionMessage('Too few arguments to function php_base\Utils\HTML\HTML::Hidden(), 0 passed');
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/0 passed/');

		$out = HTML::Hidden();
	}

	function test_2_Hidden() {
		$this->expectException(\ArgumentCountError::class);
		$this->expectExceptionMessage('Too few arguments to function php_base\Utils\HTML\HTML::Hidden(), 1 passed');
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/at least 2 expected/');

		$out = HTML::Hidden('SAM');
	}

	function test_Hidden() {

		$out = HTML::Hidden('SAM', 'FRED');
		$expected = '<Input type="HIDDEN" name="SAM" value="FRED">'; //. PHP_EOL ;
		$this->assertEquals($expected, $out);

		$out = HTML::Hidden('SAM', 'FRED', null);
		$expected = '<Input type="HIDDEN" name="SAM" value="FRED">'; // . PHP_EOL;
		$this->assertEquals($expected, $out);

		$options = array('alt' => 'TONY');
		$out = HTML::Hidden('SAM', 'FRED', $options);
		$expected = '<Input type="HIDDEN" name="SAM" value="FRED" alt="TONY">'; // . PHP_EOL;
		$this->assertEquals($expected, $out);

		$style = 'backgroundcolor: yellow';
		$out = HTML::Hidden('SAM', 'FRED', null, $style);
		$expected = '<Input type="HIDDEN" name="SAM" value="FRED" style="backgroundcolor: yellow">'; //. PHP_EOL;
		$this->assertEquals($expected, $out);

		$style = array('backgroundcolor' => 'yellow');
		$out = HTML::Hidden('SAM', 'FRED', null, $style);
		$expected = '<Input type="HIDDEN" name="SAM" value="FRED" style="backgroundcolor: yellow;">'; // . PHP_EOL;
		$this->assertEquals($expected, $out);

		$options = array('alt' => 'TONY');
		$style = array('backgroundcolor' => 'yellow');
		$expected = '<Input type="HIDDEN" name="SAM" value="FRED" style="backgroundcolor: yellow;">'; //. PHP_EOL;
		$this->assertEquals($expected, $out);
	}

	function test_1_Open() {
		$this->expectException(\ArgumentCountError::class);
		$this->expectExceptionMessage('Too few arguments to function php_base\Utils\HTML\HTML::Open(), 0 passed');
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/0 passed/');

		$out = HTML::Open();
	}

	function test_Open() {
		$out = \php_base\Utils\HTML\HTML::Open('SAMTAG');
		$expected = '<SAMTAG>' . PHP_EOL;
		$this->assertEquals($expected, $out);

		$options = array('alt' => 'TONY');
		$out = HTML::Open('SAMTAG', $options);
		$expected = '<SAMTAG alt="TONY">' . PHP_EOL;
		$this->assertEquals($expected, $out);

		$options['snow'] = 'somesnow';
		$out = HTML::Open('SAMTAG', $options);
		$expected = '<SAMTAG alt="TONY" snow="somesnow">' . PHP_EOL;
		$this->assertEquals($expected, $out);

		$style = 'backgroundcolor: yellow';
		$out = HTML::Open('SAMTAG', null, $style);
		$expected = '<SAMTAG style="backgroundcolor: yellow">' . PHP_EOL;
		$this->assertEquals($expected, $out);

		$style = array('backgroundcolor' => 'yellow');
		$out = HTML::Open('SAMTAG', null, $style);
		$expected = '<SAMTAG style="backgroundcolor: yellow;">' . PHP_EOL;
		$this->assertEquals($expected, $out);
	}

	function test_1_Close() {
		$this->expectException(\ArgumentCountError::class);
		$this->expectExceptionMessage('Too few arguments to function php_base\Utils\HTML\HTML::Close(), 0 passed');
		$this->expectExceptionCode(0);
		$this->expectExceptionMessageRegExp('/0 passed/');

		$out = HTML::Close();
	}

	function test_Close() {
		$out = HTML::Close('SAMTAG');
		$expected = PHP_EOL . '</SAMTAG>' . PHP_EOL;
		$this->assertEquals($expected, $out);
	}

	function test_parseOptions() {
		$o = new ExtendedHTML();

		$out = $o->extended_parseOptions();
		$this->assertEquals('', $out);

		$out = $o->extended_parseOptions(array());
		$this->assertEquals('', $out);

		$options = 'alt=FRED';
		$out = $o->extended_parseOptions($options);
		$this->assertEquals(' alt=FRED', $out);


		$options = array('alt' => 'FRED');
		$out = $o->extended_parseOptions($options);
		$this->assertEquals(' alt="FRED"', $out);

		$options['border'] = '4';
		$out = $o->extended_parseOptions($options);
		$this->assertEquals(' alt="FRED" border="4"', $out);

		$options['snow'] = 'somesnow';
		$out = $o->extended_parseOptions($options);
		$this->assertEquals(' alt="FRED" border="4" snow="somesnow"', $out);

		$options['something'] = 'something else';
		$out = $o->extended_parseOptions($options);
		$this->assertEquals(' alt="FRED" border="4" snow="somesnow" something="something else"', $out);
	}

	function test_parseStyle() {
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
		$this->assertEquals(' style="backgroundcolor: yellow;"', $out);

		$style['forground-color'] = 'blue';
		$out = $o->extended_parseStyle($style);
		$this->assertEquals(' style="backgroundcolor: yellow; forground-color: blue;"', $out);

		$style['encode'] = 'cyan';
		$out = $o->extended_parseStyle($style);
		$this->assertEquals(' style="backgroundcolor: yellow; forground-color: blue; encode: cyan;"', $out);

		$style['font'] = '13px "Arial",sans-serif';
		$out = $o->extended_parseStyle($style);
		$this->assertEquals(' style="backgroundcolor: yellow; forground-color: blue; encode: cyan; font: 13px "Arial",sans-serif;"', $out);

		$style['padding'] = '4px 4px 4px 4px';
		$out = $o->extended_parseStyle($style);
		$this->assertEquals(' style="backgroundcolor: yellow; forground-color: blue; encode: cyan; font: 13px "Arial",sans-serif; padding: 4px 4px 4px 4px;"', $out);
	}

	function test_version() {
		$out = HTML::Version();
		$expected = '0.3.0';
		$this->assertEquals($expected, $out);
	}

}

class ExtendedHTML extends HTML {

	function extended_parseOptions($ar = null) {
		return parent::parseOptions($ar);
	}

	function extended_parseStyle($ar = null) {
		return parent::parseStyle($ar);
	}

	function extended_Options($v, $defaultItemView = null, $addDefaultSelection = null) {
		return parent::Options($v, $defaultItemView, $addDefaultSelection);
	}

}


/*

class HTML_x_input implements Iterator {

	public $possibeTypes = ['CHECKBOX','RADIO','Reset', 'Password', 'Submit', 'BUTTON', 'TEXT', 'HIDDEN'];
	public $obj;


	//$it = $obj->getIterator();
	//$key =0;
	$position = 0;

	public function __construct(){
		$this->obj = new ArrayObject($possibleTypes);
	}
	public function __destruct() {
		unset( $this->obj );
	}

	public function rewind() {

	}
	public function valid(){

	}

	public function key(){

	}

	public function current(){

	}
	public function next() {


	}
}

/*

		foreach ($possibeTypes as $id => $type) {

		return [
			//['Radio', ["fred"]],
			//['Radio', ["FRED", 7],'<Input type="RADIO" name="FRED" value="7">'],
			[$type, ["FRED", 'somewhere over the rainbow'],
				'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow">'],
			[$type, ["FRED", 'somewhere over the rainbow', 'Where was Dorthy?' ],
				'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow">Where was Dorthy?'],
			[$type, ["FRED", 'somewhere over the rainbow', 'Where was Dorthy?', false],
						'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow">Where was Dorthy?'],
			[$type, ["FRED", 'somewhere over the rainbow', null, true],
						'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow" checked>'],
			[$type, ["FRED", 'somewhere over the rainbow', null, true,array('alt' => 'TONY')],
						'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow" alt="TONY" checked>'],
			[$type, ["FRED", 'somewhere over the rainbow', null, true, ['snow' => 'somesnow']],
						'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow" snow="somesnow" checked>'],
			[$type, ["FRED", 'somewhere over the rainbow', null, true, ['snow' => 'somesnow'], 'backgroundcolor: yellow;'],
						'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow" snow="somesnow" checked style="backgroundcolor: yellow;">'],
			[$type, ["FRED", 'somewhere over the rainbow', null, true, null, 'backgroundcolor: yellow;'],
						'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow" checked style="backgroundcolor: yellow;">'],

			[$type, ["FRED", 'somewhere over the rainbow', null, true, null, ['backgroundcolor' => 'yellow', 'color' =>'blue']],
						'<Input type="' . $type . '" name="FRED" value="somewhere over the rainbow" checked style="backgroundcolor: yellow; color: blue;">'],
			];
		}

	}

			['CheckBox', ["FRED", 'somewhere over the rainbow'],
						'<Input type="CHECKBOX" name="FRED" value="somewhere over the rainbow">'],
			['CheckBox', ["FRED", 'somewhere over the rainbow', 'Where was Dorthy?' ],
						'<Input type="CHECKBOX" name="FRED" value="somewhere over the rainbow">Where was Dorthy?'],
			['CheckBox', ["FRED", 'somewhere over the rainbow', 'Where was Dorthy?', false],
						'<Input type="CHECKBOX" name="FRED" value="somewhere over the rainbow">Where was Dorthy?'],
			['CheckBox', ["FRED", 'somewhere over the rainbow', null, true],
						'<Input type="CHECKBOX" name="FRED" value="somewhere over the rainbow" checked>'],
			['CheckBox', ["FRED", 'somewhere over the rainbow', null, true,array('alt' => 'TONY')],
						'<Input type="CHECKBOX" name="FRED" value="somewhere over the rainbow" alt="TONY" checked>'],
			['CheckBox', ["FRED", 'somewhere over the rainbow', null, true, ['snow' => 'somesnow']],
						'<Input type="CHECKBOX" name="FRED" value="somewhere over the rainbow" snow="somesnow" checked>'],
			['CheckBox', ["FRED", 'somewhere over the rainbow', null, true, ['snow' => 'somesnow'], 'backgroundcolor: yellow;'],
						'<Input type="CHECKBOX" name="FRED" value="somewhere over the rainbow" snow="somesnow" checked style="backgroundcolor: yellow;">'],
			['CheckBox', ["FRED", 'somewhere over the rainbow', null, true, null, 'backgroundcolor: yellow;'],
						'<Input type="CHECKBOX" name="FRED" value="somewhere over the rainbow" checked style="backgroundcolor: yellow;">'],

			['CheckBox', ["FRED", 'somewhere over the rainbow', null, true, null, ['backgroundcolor' => 'yellow', 'color' =>'blue']],
						'<Input type="CHECKBOX" name="FRED" value="somewhere over the rainbow" checked style="backgroundcolor: yellow; color: blue;">'],


			['ShowInput', ['FRED', 'somewhere over the rainbow'],
						'<Input type="TEXT" name="FRED" value="somewhere over the rainbow">'],
			['ShowInput', ["FRED", 'somewhere over the rainbow', 'TEXT' ],
						'<Input type="TEXT" name="FRED" value="somewhere over the rainbow">'],
			['ShowInput', ["FRED", 'somewhere over the rainbow', 'TEXT', 'Where was Dorthy?' ],
						'<Input type="TEXT" name="FRED" value="somewhere over the rainbow" Where was Dorthy?>'],

			['ShowInput', ["FRED", 'somewhere over the rainbow', 'TEXT', null],   //, true,array('alt' => 'TONY')],
						'<Input type="TEXT" name="FRED" value="somewhere over the rainbow">'],
			['ShowInput', ["FRED", 'somewhere over the rainbow', 'TEXT', array('alt' => 'TONY')],
						'<Input type="TEXT" name="FRED" value="somewhere over the rainbow" alt="TONY">'],


			['ShowInput', ["FRED", 'somewhere over the rainbow', 'TEXT', null, true], //,array('alt' => 'TONY')],
						'<Input type="TEXT" name="FRED" value="somewhere over the rainbow">'],

			['ShowInput', ["FRED", 'somewhere over the rainbow', 'TEXT', null, array('alt' => 'TONY')],
						'<Input type="TEXT" name="FRED" value="somewhere over the rainbow" style="alt: TONY;">'],

			['ShowInput', ["FRED", 'somewhere over the rainbow', 'TEXT', array(  'bob'=> 'george'), array('alt' => 'TONY')],
						'<Input type="TEXT" name="FRED" value="somewhere over the rainbow" bob="george" style="alt: TONY;">'],

			['ShowInput', ["FRED", 'somewhere over the rainbow', 'TEXT', array(  'bob'=> 'george','bob2'=> 'george2'), array('alt' => 'TONY')],
						'<Input type="TEXT" name="FRED" value="somewhere over the rainbow" bob="george" bob2="george2" style="alt: TONY;">'],

			['ShowInput', ["FRED", 'somewhere over the rainbow', 'TEXT', array(  'bob'=> 'george'), array('alt' => 'TONY', 'alt2' => 'TONY2')],
						'<Input type="TEXT" name="FRED" value="somewhere over the rainbow" bob="george" style="alt: TONY; alt2: TONY2;">'],

			['ShowInput', ["FRED", 'somewhere over the rainbow', 'TEXT', array(  'bob'=> 'george','bob2'=> 'george2'), array('alt' => 'TONY', 'alt2' => 'TONY2')],
						'<Input type="TEXT" name="FRED" value="somewhere over the rainbow" bob="george" bob2="george2" style="alt: TONY; alt2: TONY2;">'],

				];
	}
*/



