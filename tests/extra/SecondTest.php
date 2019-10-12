<?php
//namespace UnitTestFiles\Test;
	
use PHPUnit\Framework\TestCase;
	

class SecondTest extends TestCase {	
	
	public function  testTheSecondTest () 
	{
		$this->assertTrue( true, 'This should already work');
		
		$this->markTestIncomplete('This test has not been implemented yet' );	
	}

	/**
 	* @test
 	*/
 	public function fred() {
 		$this->assertFalse(false);
 	}

}


