<?php

namespace Tests\Test;
use PHPUnit\Framework\TestCase;

use \php_base\Utils\ResponseErrorCodes as ResponseErrorCodes;


class ResponseErrorCodes_Test extends TestCase{



	function test_giveErrorMessage() {
		$actual = ResponseErrorCodes::giveErrorMessage(2);
		$this->assertEquals( 'All is good', $actual);

		$actual = ResponseErrorCodes::giveErrorMessage(1);
		$this->assertEquals( 'Generic Warning all is good', $actual);

		$actual = ResponseErrorCodes::giveErrorMessage(0);
		$this->assertEquals( 'Not an Error', $actual);

		$actual = ResponseErrorCodes::giveErrorMessage(-1);
		$this->assertEquals( 'Generic Warning something might be wrong', $actual);

		$actual = ResponseErrorCodes::giveErrorMessage(-2);
		$this->assertEquals( 'Generic Error', $actual);

		// the rest of the messages are specific to the app so no testing is required
	}
}