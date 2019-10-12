<?php
//namespace UnitTestFiles\Test;

use PHPUnit\Framework\TestCase;


use whitehorse\MikesCommandAndControl2\View\FooterView as FooterView;

class FooterView_Test extends TestCase {

	public function  testFooter () 	{
		//$this->markTestIncomplete('This test has not been implemented yet' );
		$o = new FooterView(null);
		$this->expectOutputString( '<footer><Br>--footer--<Br></footer>');
		$o->doWork();
	}
}