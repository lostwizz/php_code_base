<?php

namespace Tests\Test;
use PHPUnit\Framework\TestCase;

use \php_base\Utils\Settings as Settings;
use Monolog\Logger;



class Logging_SetupTest extends TestCase{

	public static function setUpBeforeClass(): void   {
		include_once( DIR . 'utils' . DS . 'settings.class.php');
		require_once( DIR . '_config' . DS . '_Settings-General.php');
		require_once( DIR . '_config' . DS . '_Settings-Database.php');
		require_once( DIR . '_config' . DS . '_Settings-protected.php');

		require_once( 'P:\Projects\_Private_Settings.php');

        // force everyhting to setup
        Settings::SetPublic( 'Use_MessageLog', true );  //true
		Settings::SetPublic( 'Use_DBLog', true);
		Settings::SetPublic( 'Use_DBdataLog', true);
		Settings::SetPublic( 'Use_FileLog', true);  // true
		Settings::SetPublic( 'Use_SecurityLog', true);
		Settings::SetPublic( 'Use_EmailLog', true);      // true


        require_once(DIR . 'utils\setup_Logging.php');
    }



	protected function setUp() :void {
		//include_once( DIR . 'utils' . DS . 'Setup' . DS . 'settings.class.php');
		require_once( DIR . '_config' . DS . '_Settings-General.php');
		require_once( DIR . '_config' . DS . '_Settings-Database.php');
		require_once( DIR . '_config' . DS . '_Settings-protected.php');

		require_once( 'P:\Projects\_Private_Settings.php');
	}





	public function testPrerequisites() :void {
		$this->assertFileExists('src/vendor/autoload.php' );
		$this->assertFileExists('src/utils\PDOHandler.php');
		$this->assertFileExists('src/utils\PDOdataHandler.php');

		$this->assertFileExists('src/Vendor\monolog\monolog\src\Monolog\Logger.php');

		$this->assertTrue(extension_loaded('pdo'),'PDO extention not available');
		//$this->assertTrue(extension_loaded('sqlsrv'),'Sql Server extention not available');
		//$this->assertTrue(extension_loaded('pdo_sqlsrv'),'PDO Sql Server extention not available');

		$this->assertEquals(Settings::GetPublic('Log_file' ), DIR . 'logs\TestApp_app.log');

		$this->assertDirectoryIsWritable(DIR . 'logs');
		$this->assertFileIsWritable(DIR . 'logs\TestApp_app.log');
	}

	public function testLogging() : void {

		//$this->assertFalse( Settings::GetPublic( 'FileLog') );

		Settings::SetPublic('Log_file',DIR . 'logs' . DS . Settings::GetPublic('App Name') . '_app.log' );
		//$this->assertEquals( 'P:\Projects\MikesCommandAndControl2\src\logs\TestApp_app.log', Settings::GetPublic( 'FileLog'));
		$this->assertIsObject(Settings::GetRuntime( 'FileLog'));

		$this->assertInstanceOf('Monolog\Logger', Settings::GetRuntime( 'FileLog') );

		$s = '-------------#$# Logging Test #$#--------------';
		Settings::GetRuntime( 'FileLog')->addRecord( Logger::ALERT, $s);


		$file = Settings::GetPublic('Log_file' );
		$data = file($file);
		$line = $data[count($data)-1];

		//echo 'Line=' , $line;

		$this->assertStringContainsString(  $s
											, $line
											, 'last line does not contain starting header  ['. $line. ']');
		$this->assertStringContainsString(  'default.ALERT'
											, $line
											, 'last line does not contain starting header  ['. $line. ']');
	}

	public function testMyLogging() :void {
		$o = Settings::GetRuntime('FileLog');
		$o->addNotice( '-------------UNIT TEST------------');

		$file = Settings::GetPublic('Log_file' );
		$data = file($file);
		$line = $data[count($data)-1];

		$this->assertStringContainsString(  '--UNIT TEST--'
											, $line
											, 'last line does not contain starting header  ['. $line. ']');
		$this->assertStringContainsString(  'default.NOTICE'
											, $line
											, 'last line does not contain starting header  ['. $line. ']');

		$s ='--Test an Error';
		$o->addError($s,array('one','two') );

		$file = Settings::GetPublic('Log_file' );
		$data = file($file);
		$line = $data[count($data)-1];

		$this->assertStringContainsString(  $s
											, $line
											, 'last line does not contain starting header  ['. $line. ']');
		$this->assertStringContainsString(  'default.ERROR'
											, $line
											, 'last line does not contain starting header  ['. $line. ']');

		$this->assertStringContainsString( '["one","two"]'
											, $line
											, 'last line does not contain starting header  ['. $line. ']');

	}

	public function testMySecurityLogging() :void {
		$o = Settings::GetRuntime('SecurityLog');

		$expected = '-------------Security UNIT TEST------------';
		$o->addNotice( $expected );

		$file = Settings::GetPublic('Security_Log_file' );
		$data = file($file);
		$line = $data[count($data)-1];

		$this->assertStringContainsString(  $expected
											, $line
											, 'last line does not contain starting header  ['. $line. ']');
		$this->assertStringContainsString(  'Security.NOTICE'
											, $line
											, 'last line does not contain starting header  ['. $line. ']');
	}

}