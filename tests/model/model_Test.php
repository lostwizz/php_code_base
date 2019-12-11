<?php

namespace Tests\Test;
use PHPUnit\Framework\TestCase;

use php_base\model\model as Model;
use \php_base\Utils\Response as Response;

class model_Test extends TestCase{

//	function test_filter(){
//		$this->markTestIncomplete('This test has not been implemented yet' );
//	}

	function test_model() {
		$x= new Model(null);
		$this->assertIsObject($x);

		$actual = $x->doWork();
		$this->assertIsObject($actual);
		$this->assertInstanceOf( Response::class, $actual);
		$this->assertEquals( Response::GenericError(), $actual );
	}
}