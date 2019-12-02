<?php
//namespace UnitTestFiles\Test;

use PHPUnit\Framework\TestCase;


use php_base\View\FooterView as FooterView;

class FooterView_Test extends TestCase {

	public function  testFooter () 	{
		//$this->markTestIncomplete('This test has not been implemented yet' );
		$o = new FooterView(null);
		$this->expectOutputString( '<footer>' . PHP_EOL . '<Br>--footer--<Br>' .PHP_EOL . '</footer>' . PHP_EOL . '</body>'. PHP_EOL );

		$o->doWork();  /** call the code which makes the output */
	}
}

/*

		echo '<footer>'. PHP_EOL;
		echo '<Br>--footer--<Br>'. PHP_EOL;
		echo '</footer>'. PHP_EOL;
		echo '</body>'. PHP_EOL;


 */