<?php

use PHPUnit\Framework\TestCase;

use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Control;
use \php_base\control\HeaderController as HeaderController;

//use \php_base\utils\AMessage as AMessage;
//use \php_base\utils\MessageLog as MessageLog;

//include_once (DIR . 'utils' . DS . 'messagelog.class.php');


///		   fwrite(STDERR, print_r($input, TRUE));

//***********************************************************************************************
//***********************************************************************************************
class HeaderController_Test extends TestCase {


	//-----------------------------------------------------------------------------------------------
	public function test_constuctor() {
		//$this->assertTrue(false)		;
		//$this->markTestIncomplete('This test has not been implemented yet' );

		$o = new HeaderController();


		$r = $o->action;
		$this->assertNull( $r);

		$r = $o->payload;
		$this->assertNull( $r);

	}
}