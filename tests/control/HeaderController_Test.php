<?php

use PHPUnit\Framework\TestCase;

use \whitehorse\MikesCommandAndControl2\Settings\Settings as Settings;
use \whitehorse\MikesCommandAndControl2\Utils\Dump\Dump as Dump;
use \whitehorse\MikesCommandAndControl2\Control;

use whitehorse\MikesCommandAndControl2\control\HeaderController as HeaderController;

//use \whitehorse\MikesCommandAndControl2\utils\AMessage as AMessage;
//use \whitehorse\MikesCommandAndControl2\utils\MessageLog as MessageLog;

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

		$r = $o->controllerRequiredVars();
		$this->assertEquals(array(), $r);

	}
}