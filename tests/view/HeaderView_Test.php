<?php

//namespace UnitTestFiles\Test;

use PHPUnit\Framework\TestCase;
use php_base\View\HeaderView as HeaderView;
use \php_base\Utils\Settings as Settings;

class HeaderView_Test extends TestCase {

	public static function setUpBeforeClass(): void {
		// do things before any tests start -- like change some settings
		unlink('f:\temp\data.txt');

		include_once( DIR . 'utils' . DSZ . 'settings.class.php');
		require_once( DIR . '_config' . DSZ . '_Settings-General.php');
		require_once( DIR . '_config' . DSZ . '_Settings-Database.php');
		require_once( DIR . '_config' . DSZ . '_Settings-protected.php');

		require_once( 'P:\Projects\_Private_Settings.php');
	}

	public static function tearDownAfterClass(): void {

	}

	public $fp;
	public function setup(): void {
		$this->fp = fopen('f:\temp\data.txt', 'a');

		//fwrite($this->fp, print_r($_SESSION, TRUE));
	}

	protected function tearDown(): void {
		fclose($this->fp);
	}


	/**
	 * this test encompasses showHTMLHeader, showAllStyleSheets
	 */
	public function test_Header() {

		$hv = new HeaderView();
		$this->assertIsObject($hv);

		$hv->doWork();
		$capturedOutput = $this->getActualOutput();
		$this->assertStringContainsString( 'DOCTYPE HTML PUBLIC', $capturedOutput);

		$this->assertStringContainsString( '<html>', $capturedOutput);
		$this->assertStringContainsString( '</html>', $capturedOutput);

		$this->assertStringContainsString( '<head>', $capturedOutput);
		$this->assertStringContainsString( '</head>', $capturedOutput);

		$this->assertStringContainsString( '<title>', $capturedOutput);
		$this->assertStringContainsString( '</title>', $capturedOutput);

		$this->assertStringContainsString( '<link rel="stylesheet" href="static\css\general_style.css">', $capturedOutput);
		$this->assertStringContainsString( '<link rel="stylesheet" href="static\css\message_stack_style.css">', $capturedOutput);

		$this->assertStringContainsString( '<body>', $capturedOutput);
	}

	function test_giveTitle(){
		$hv = new HeaderView();
		$actual = $hv->giveTitle(true);
		$this->assertEquals('TestApp', $actual);
	}

	function test_giveStyleSheets(){
		$hv = new HeaderView();
		$actual = $hv->giveStyleSheets();
		$this->assertIsArray($actual);
		if (!empty($actual) and  count($actual) >0){
			foreach( $actual as $item){
				$this->assertStringContainsString( '.css', $item);
			}
		}
	}

	function test_giveJavaScriptFiles(){
		$hv = new HeaderView();
		$actual = $hv->giveJavaScriptFiles();
		$this->assertIsArray($actual);
	}

	function test_giveVersion(){
		$hv = new HeaderView();
		$actual = $hv->giveVersion();
		$this->assertEquals( Settings::GetPublic('App Version'),  $actual);
	}

	function test_giveTypeServerAndDatabase(){
		$hv = new HeaderView();
		$actual = $hv->giveTypeServerAndDatabase();

		$this->assertStringContainsString('Database: '. Settings::GetProtected('DB_Type') , $actual);
		$this->assertStringContainsString(' On: ' . Settings::GetProtected('DB_Server'),  $actual);
		$this->assertStringContainsString(' Using: ' . Settings::GetProtected('DB_Database'), $actual);
	}
}
