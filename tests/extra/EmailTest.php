<?php

	declare(strict_types=1);

	namespace UnitTestFiles\Test;

	use PHPUnit\Framework\TestCase;



class Email extends TestCase {
	//public function testCanBeCreatedFromValidEmailAddress(): void {
//	       $this->assertInstanceOf(
//			           Email::class,
//          				   Email::fromString('user@example.com')
//       				   );

	//}

	public function testCannotBeCreatedFromInvalidEmailAddress(): void {
		$this->markTestIncomplete('This test has not been implemented yet' );
    	//$this->expectException(InvalidArgumentException::class);
	   // Email::fromString('invalid');
   	}

	//public function testCanBeUsedAsString(): void  {
//       	$this->assertEquals(
//				           'user@example.com',
//				           Email::fromString('user@example.com')
//					       );

   	//}

}
?>