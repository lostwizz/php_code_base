<?php

namespace Tests\Test;
use PHPUnit\Framework\TestCase;

use \php_base\Utils\Settings as Settings;





//Settings::saveAsINI("F:\TEMP\__TEMP.INI");

//Settings::nonDestructiveINIRestore( "F:\TEMP\__TEMP.INI");

//Settings::destructiveINIRestore( "F:\TEMP\__TEMP.INI");

//





class Settings_Test extends TestCase{
//

	protected function setUp() :void {

		include_once( DIR . 'utils' . DSZ . 'settings.class.php');
		require_once( DIR . '_config' . DSZ . '_Settings-General.php');
		require_once( DIR . '_config' . DSZ . '_Settings-Database.php');
		require_once( DIR . '_config' . DSZ . '_Settings-protected.php');
		require_once( 'P:\Projects\_Private_Settings.php');
	}


	public function testPrerequisites() :void {

		//echo '@checking settings@';

		$this->assertFileExists('src\_config\_Settings-General.php', 'missing file: _config\_Settings-General.php');
		$this->assertFileExists('src\_config\_Settings-Database.php', 'missing file: _config\_Settings-Database.php');
		$this->assertFileExists('src\_config\_Settings-protected.php', 'missing file: _config\_Settings-protected.php');

		$this->assertFileExists('P:\Projects\_Private_Settings.php' ,'missing file: P:\Projects\_Private_Settings.php');

		$this->assertFileExists('src\utils\settings.class.php', 'missing file: _config\settings.class.php');
		///$this->assertFileExists('src\utils\Setup\Setup.php', 'missing file: _config\Setup.php');
		$this->assertFileExists('src\utils\Setup_Logging.php', 'missing file: _config\Setup_Logging.php');
	}

	public function testGlobalSettings() : void{
		$x1 = Settings::GetPublic('App Name');
		$this->assertEquals('TestApp', $x1 );

		$x2 = Settings::GetPublic('App Version');
		$this->assertEquals( '2.0.0', $x2 );

		$x3 = Settings::GetPublic('App Server');
		$this->assertEquals('localhost', $x3);
	}


	public function testGeneralSettings() : void {

		$this->assertFalse( Settings::GetPublic( 'Test'));
		Settings::SetPublic( 'Test', 'testValue');

		$x = Settings::GetPublic( 'Test');
		$this->assertEquals('testValue', $x);

		Settings::SetPublic( 'Test', null);
		$x = Settings::GetPublic( 'Test');
		$this->assertNotEquals('testValue', $x);

		$this->assertFalse($x );

	}

	/**
	*
	*/
	public function testProtectedSettings() : void {
		$x1 = Settings::GetProtected('Critical_email_TO_ADDR');
		$this->assertEquals( $x1 ,'mike.merrett@whitehorse.ca');

		$x2 =  Settings::GetProtected('Critical_email_Subject');
		$this->assertEquals( $x2 ,'TestApp@localhost');

	}



	public function testLogDatabaseSettings() : void {
////		$x1 = Settings::GetProtected('Logging_Server');
////		$this->assertEquals( $x1 ,'vm-db-prd4');
////
//		$x2 = Settings::GetProtected('Logging_Database');
//		$this->assertEquals( $x2 ,'Mikes_Application_Store');

		$x3 = Settings::GetProtected('DB_Username');
		$this->assertEquals( $x3 ,'Mikes_DBLogging_User');

		$x4 = Settings::GetProtected('Logging_DB_Table');
		$this->assertEquals( $x4 ,'Application_Log');
	}

	public function testDataLogDatabaseSettings() : void {
//		$x1 = Settings::GetProtected('Data_Logging_Server');
//		$this->assertEquals( $x1 ,'vm-db-prd4');

	//	$x2 = Settings::GetProtected('Data_Logging_Database');
//		$this->assertEquals( $x2 ,'Mikes_Application_Store');

		$x3 = Settings::GetProtected('DB_Username');
		$this->assertEquals( $x3 ,'Mikes_DBLogging_User');

		$x4 = Settings::GetProtected('Data_Logging_DB_Table');
		$this->assertEquals( $x4 ,'Application_Data_Log');

	}

	public function testGiveINISetting() : void {
		$o= new anExtendedSettings();

		$ex = $o->ExtendedGiveINISetting(false);
		$this->assertEquals( $ex, '-False-');

		$ex = $o->ExtendedGiveINISetting(true);
		$this->assertEquals( $ex, '-True-');

		$ex = $o->ExtendedGiveINISetting($o);
		$this->assertEquals( $ex, 'O:29:"Tests\Test\anExtendedSettings":0:{}');

		$a = array('one'=>'oneX', 'two'=>'twoX', 'three'=>'threeX', 'four'=>'fourX');
		$ex = $o->ExtendedGiveINISetting($a);
		$this->assertEquals( $ex, 'a:4:{s:3:"one";s:4:"oneX";s:3:"two";s:4:"twoX";s:5:"three";s:6:"threeX";s:4:"four";s:5:"fourX";}');

		$ex = $o->ExtendedGiveINISetting(null);
		$this->assertEquals( $ex, '-Null-');


	}
//
//	/**
//	* @todo
//	**/
//	public function test_saveAsINI( ){
//
//	}
//
//	/**
//	* @todo
//	**/
//	public function test_getINIPublic( ){
//
//	}
//
//	/**
//	* @todo
//	**/
//	public function test_getINIProtected( ){
//
//	}
//	/**
//	* @todo
//	**/
//	public function test_readINI( ){
//
//	}
//
//	/**
//	* @todo
//	**/
//	public function test_destructiveINIRestore( ){
//
//	}
//
//	/**
//	* @todo
//	**/
//	public function test_nonDestructiveINIRestore( ){
//
//	}

}







//***********************************************************************************************
class anExtendedSettings extends Settings {

	public function ExtendedGiveINISetting($value){
		return parent::giveINISetting($value);
	}




}