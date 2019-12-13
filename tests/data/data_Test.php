<?php

namespace Tests\Test;
use PHPUnit\Framework\TestCase;

use \php_base\data\data as Data;
use \php_base\Utils\Response as Response;


class data_Test extends TestCase{

//	function test_filter(){
//		$this->markTestIncomplete('This test has not been implemented yet' );
//	}

	public function test_Data() {
		$x= new Data(null);
		$this->assertIsObject($x);

		$actual = $x->doWork();
		$this->assertIsObject($actual);
		$this->assertInstanceOf( Response::class, $actual);
		$this->assertEquals( Response::GenericError(), $actual );
	}

}