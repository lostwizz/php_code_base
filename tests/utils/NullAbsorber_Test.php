<?php

namespace Tests\Test;

use PHPUnit\Framework\TestCase;
use \php_base\Utils\NullAbsorber as NullAbsorber;

class NullAbsorber_Test extends TestCase {


	function test_NullAbsorber() {
		$x = new NullAbsorber();

		$this->assertFalse($x->anymethod());
		$this->assertFalse($x->anymethod2());

		$x->attrib = 'value';
		$this->assertFalse($x->attrib);

		$this->assertFalse($x->anymethod());

		$expected = array(
			0 => '__call',
			1 => '__get',
			2 => '__set',
			);
		$class_methods = \get_class_methods($x);
		$this->assertEquals($expected, $class_methods);

		$class_methods = \get_class_methods('NullAbsorber');
		$this->assertNull($class_methods );

	}

}
