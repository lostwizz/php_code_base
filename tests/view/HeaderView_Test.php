<?php
//namespace UnitTestFiles\Test;

use PHPUnit\Framework\TestCase;

use whitehorse\MikesCommandAndControl2\View\HeaderView as HeaderView;


class HeaderView_Test extends TestCase {



	public function  testHeader() 	{
		//$this->markTestIncomplete('This test has not been implemented yet' );
		$o = new HeaderView(null);
		//$this->expectOutputString( 'header here<span>App: <b></b>    On:<b></b> Ver:<b></b></span><br>');

		$this->expectOutputString( 'header here<h1>We is Debugging!!! </h1><br><span>App: <b>TestApp</b>    On:<b>localhost</b> Ver:<b>2.0.0</b></span><br>');
		$o->doWork();

	}
}