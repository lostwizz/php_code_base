<?php

//namespace UnitTestFiles\Test;
use PHPUnit\Framework\TestCase;


//DirectoryExistsTest
class srcDirectoriesExistTest extends TestCase
{
	/**
	* @test
	*/
	public function testMissingDirectories() :void {

		//echo '@checking directory structure@';

		$this->assertDirectoryExists('src/_config', 'missing dir: _config');
		$this->assertDirectoryExists('src/model', 'missing dir: models');
		$this->assertDirectoryExists('src/control');

		$this->assertDirectoryExists('src/static', 'missing dir: static');
		$this->assertDirectoryExists('src/static\css', 'missing dir: static\css');
		$this->assertDirectoryExists('src/static\js', 'missing dir: static\js');

		$this->assertDirectoryExists('src/templates', 'missing dir: templates');

		$this->assertDirectoryExists('src/utils', 'missing dir: utils');
		$this->assertDirectoryExists('src/utils\log', 'missing dir: utils\log');
		//$this->assertDirectoryExists('src/utils\db', 'missing dir: utils\db');
		//$this->assertDirectoryExists('src/utils\lib', 'missing dir: utils\lib');


		$this->assertDirectoryExists('src/view', 'missing dir: views');

		$this->assertDirectoryExists('src/Vendor', 'missing dir: vendor');
		$this->assertDirectoryExists('src/Vendor\monolog', 'missing dir: vendor\monolog');
		$this->assertDirectoryExists('src/Vendor\monolog\monolog\src', 'missing dir: vendor\monolog\monolog\src');
		$this->assertDirectoryExists('src/Vendor\monolog\monolog\src\Monolog', 'missing dir: vendor\monolog\monolog\src\Monolog');


	}

		/**
	* @test
	*/
	public function testMissingFiles() :void {
		$this->assertFileExists('src/Vendor\monolog\monolog\src\Monolog\Logger.php', 'missing file: vendor\monolog\monolog\src\Monolog\Logger.php');
	}
}
