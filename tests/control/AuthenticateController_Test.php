<?php

use PHPUnit\Framework\TestCase;

use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\control;

use \php_base\Control\AuthenticateController as AuthenticateController;
///		   fwrite(STDERR, print_r($input, TRUE));


class AuthenticateController_Test extends TestCase {
//	public function test_constructor(){
//		$this->markTestIncomplete('This test has not been implemented yet' );
//	}

	function test_construct() {
		$ac = new AuthenticateController();

		$this->assertEmpty( $ac->process);
		$this->assertEmpty( $ac->task);
		$this->assertEmpty( $ac->action);
		$this->assertEmpty( $ac->payload);

		$this->assertIsObject( $ac->model);
		$this->assertIsObject( $ac->view);

		$ac = new AuthenticateController('actionOne');

		$this->assertEmpty( $ac->process);
		$this->assertEmpty( $ac->task);
		$this->assertEquals( 'actionOne', $ac->action);
		$this->assertEmpty( $ac->payload);

		$this->assertIsObject( $ac->model);
		$this->assertIsObject( $ac->view);

		$ac = new AuthenticateController('actionTwo', 'payloadOne');

		$this->assertEmpty( $ac->process);
		$this->assertEmpty( $ac->task);
		$this->assertEquals( 'actionTwo', $ac->action);
		$this->assertEquals( 'payloadOne', $ac->payload);

		$this->assertIsObject( $ac->model);
		$this->assertIsObject( $ac->view);
	}

	function test_setProcessAndTask() {
		$ac = new AuthenticateController('processOne', 'taskOne');

		$this->assertEquals( 'processOne', $ac->action );
		$this->assertEquals( 'taskOne', $ac->payload);
		//$this->assertEmpty( $ac->action);
		//$this->assertEmpty( $ac->payload);
	}

}