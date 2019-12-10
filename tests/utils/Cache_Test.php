<?php

namespace Tests\Test;

use PHPUnit\Framework\TestCase;

use \php_base\Utils\Cache as Cache;
use \php_base\Utils\Settings as Settings;
//include_once (DIR . 'utils' . DSZ . 'Cache.class.php');


class Cache_Test extends TestCase{

	public $fp;

	public static function setUpBeforeClass(): void   {
		// do things before any tests start -- like change some settings

		unlink('f:\temp\data.txt');
		$_SESSION['CACHE'] = 'X';
		unset( $_SESSION['CACHE']);
		//session_start();

		Settings::SetPublic( 'Use_MessageLog', false );  //true
		Settings::SetPublic( 'Use_DBLog', false);
		Settings::SetPublic( 'Use_DBdataLog', true);
		Settings::SetPublic( 'Use_FileLog', false);  // true
		Settings::SetPublic( 'Use_SecurityLog', false);
		Settings::SetPublic( 'Use_EmailLog', false);      // true

		require_once( 'P:\Projects\_Private_Settings.php');


		Settings::SetPublic('CACHE_IS_ON', true);

	}
	 public static function tearDownAfterClass(): void {
		 //Cache::CleanupBeforSessionWrite();
		 unset( $_SESSION['CACHE']);
		 $_SESSION['CACHE'] = 'X';
		unset( $_SESSION['CACHE']);
		session_destroy();
	 }

	 public  function setup(): void{
		$this->fp = fopen('f:\temp\data.txt', 'a');
	 }

	protected   function tearDown(): void{
		fclose($this->fp);
		unset( $_SESSION['CACHE']);
	}



	function test_StartsEmpty(){
		Settings::SetPublic('CACHE_IS_ON', true);
		//$this->assertEquals(  null,$_SESSION['CACHE'], 'Check starting with an empty array');
		//$this->assertEmpty($_SESSION['CACHE']);
		$this->assertEmpty($_SESSION);
	}
	/**
	 * @depends test_StartsEmpty
	 */
	function test_add(){
		Settings::SetPublic('CACHE_IS_ON', true);
		Cache::add('oneKey', 'oneData');


//		fwrite($this->fp, print_r($_SESSION, TRUE));

		$this->assertArrayHasKey('CACHE', $_SESSION);

		$this->assertArrayHasKey('oneKey', $_SESSION['CACHE']);
		$this->assertArrayHasKey('Data', $_SESSION['CACHE']['oneKey']);

		$this->assertArrayHasKey('Data', $_SESSION['CACHE']['oneKey']);

		$this->assertArrayHasKey('Expires', $_SESSION['CACHE']['oneKey']);

		$now = 1575920000 +  Cache::DEFAULTTIMEOUTSECONDS;
		$expected = array('oneKey'=>['Data'=>'oneData', 'Expires'=> $now]);

		$this->assertEquals( $expected, $_SESSION['CACHE']);
	}

	/**
	 * @depends test_add
	 */
	function test_addOrUpdate() {
		Settings::SetPublic('CACHE_IS_ON', true);
		Cache::add('oneKey', 'oneData');
		$now = 1575940000 +  Cache::DEFAULTTIMEOUTSECONDS;

		Cache::addOrUpdate( 'oneKey', 'twoData');
		$this->assertArrayHasKey('CACHE', $_SESSION);


		$this->assertArrayHasKey('oneKey', $_SESSION['CACHE']);
		$this->assertArrayHasKey('Data', $_SESSION['CACHE']['oneKey']);

		$this->assertArrayHasKey('Data', $_SESSION['CACHE']['oneKey']);

		$this->assertArrayHasKey('Expires', $_SESSION['CACHE']['oneKey']);

		$expected = array('oneKey'=>['Data'=>'twoData', 'Expires'=> $now]);
		$this->assertEquals( $expected, $_SESSION['CACHE']);

		Cache::addOrUpdate( 'oneKey', 'twoData', 5555);

		$this->assertArrayHasKey('CACHE', $_SESSION);

		$this->assertArrayHasKey('oneKey', $_SESSION['CACHE']);
		$this->assertArrayHasKey('Data', $_SESSION['CACHE']['oneKey']);

		$this->assertArrayHasKey('Data', $_SESSION['CACHE']['oneKey']);

		$this->assertArrayHasKey('Expires', $_SESSION['CACHE']['oneKey']);

		$ts  = 1575940000 +  5555;
		$expected = array('oneKey'=>['Data'=>'twoData', 'Expires'=> $ts]);
		$this->assertEquals( $expected, $_SESSION['CACHE']);


	}

	/**
	 * @depends test_add
	 * @depends test_addOrUpdate
	 */

	function test_pull() {
		Settings::SetPublic('CACHE_IS_ON', true);
		$expected = 'oneData';
		Cache::add('oneKey', $expected);

//		fwrite($this->fp, print_r($_SESSION, TRUE));

		$actual = Cache::pull('oneKey');
		$this->assertEquals( $expected, $actual);

		//fake the timestamp - This should make the cache entry time out and dissapear
		$_SESSION['CACHE']['oneKey']['Expires'] =  1575920000 - Cache::DEFAULTTIMEOUTSECONDS -  2 ;

//		fwrite($this->fp, print_r($_SESSION, TRUE));

		$expected = null;
		$actual = Cache::pull('oneKey');

		//fwrite($this->fp, print_r($actual, TRUE));
		$this->assertNull(  $actual);

		$this->assertNull( Cache::pull(''));
	}

	function test_changeExpires() {
		Settings::SetPublic('CACHE_IS_ON', true);

		Cache::add('oneKey', 'oneData');

		Cache::changeExpires('oneKey', 1111);

		$expected = 1575950000 + 1111;

		$actual = $_SESSION['CACHE']['oneKey']['Expires'] ;
		$this->assertEquals( $expected, $actual);


	}


	/*
	 * @depends test_pull
	 */
	function test_exists() {
		Settings::SetPublic('CACHE_IS_ON', true);

		$this->assertFalse( Cache::exists('fred'));

		$expected = 'oneData';
		Cache::add('oneKey', 'oneData');
		$now =  1575920000 +  Cache::DEFAULTTIMEOUTSECONDS;

		$actual = Cache::exists( 'oneKey');
		$this->assertTrue( $actual);

		//fake the timestamp - This should make the cache entry time out and dissapear
		$_SESSION['CACHE']['oneKey']['Expires'] =  1575920000 - Cache::DEFAULTTIMEOUTSECONDS -  2 ;

		$actual = Cache::exists( 'oneKey');
		$this->assertFalse( $actual);


		$this->assertFalse( Cache::exists(''));

	}

	/*
	 * @depends test_exists
	 */
	function test_hasExpired() {
		Settings::SetPublic('CACHE_IS_ON', true);
		$expected = 'oneData';
		Cache::add('oneKey', 'oneData');
		$now =  1575920000 +  Cache::DEFAULTTIMEOUTSECONDS;

//		fwrite($this->fp, print_r($_SESSION, TRUE));

		$actual = Cache::hasExpired( 'oneKey');
		$this->assertFalse ( $actual);

		//fake the timestamp - This should make the cache entry time out and dissapear
		$_SESSION['CACHE']['oneKey']['Expires'] =  1575920000 - Cache::DEFAULTTIMEOUTSECONDS -  2 ;
		$actual = Cache::hasExpired( 'oneKey');
		$this->assertTrue($actual);
	}

	/*
	 * @depends test_hasExpired
	 */
	function test_delete(){
		Settings::SetPublic('CACHE_IS_ON', true);
		Cache::add('oneKey', 'oneData');

		$actual = Cache::exists( 'oneKey');
		$this->assertTrue ( $actual);

		Cache::delete( 'oneKey');
		$actual = Cache::exists( 'oneKey');
		$this->assertFalse($actual);

		$this->assertFalse( Cache::delete(''));
	}

	/*
	 * @depends test_delete
	 */
	function test_secondsUntilExpire(){
		Settings::SetPublic('CACHE_IS_ON', true);
		Cache::add('oneKey', 'oneData');
		$actual = Cache::secondsUntilExpire( 'oneKey');

		/* phpunit testing returns 1575920000 as now and 1575930000 as then with a 600 second timeout - all of which is totaly fake data */
		$expected = 1575930000 -   (1575920000 + 600); // = 9400;
		$this->assertEquals( $expected, $actual);

		Cache::delete( 'oneKey');
		$actual = Cache::secondsUntilExpire( 'oneKey');
		$expected = -1;
		$this->assertEquals( $expected, $actual);

		$this->assertEquals( -1, Cache::secondsUntilExpire(''));

	}

	/*
	 * @depends test_secondsUntilExpire
	 */
	function test_doesSerializeWorkOnThisObject() {
		Settings::SetPublic('CACHE_IS_ON', true);
		Cache::add('oneKey', 'oneData');

		$actual = Cache::doesSerializeWorkOnThisObject( 'oneKey');
		$this->assertTrue( $actual);

		$dsn = Settings::GetProtected('DB_DSN');
		$options = Settings::GetProtected('DB_DSN_OPTIONS');
		$conn = new \PDO($dsn, Settings::GetProtected('DB_Username'), Settings::GetProtected('DB_Password'), $options);

		Cache::add('PDO', $conn);

		//fwrite($this->fp, print_r($_SESSION, TRUE));

		$actual = Cache::doesSerializeWorkOnThisObject( $conn );
		$this->assertFalse( $actual);

		$this->assertTrue( Cache::doesSerializeWorkOnThisObject(''));
	}

	/*
	 * @depends test_doesSerializeWorkOnThisObject
	 */
	function test_cleanupBeforeSessionWrite(){
		Settings::SetPublic('CACHE_IS_ON', true);
		Cache::add('oneKey', 'oneData');

		$dsn = Settings::GetProtected('DB_DSN');
		$options = Settings::GetProtected('DB_DSN_OPTIONS');
		$conn = new \PDO($dsn, Settings::GetProtected('DB_Username'), Settings::GetProtected('DB_Password'), $options);

		Cache::add('PDO', $conn);

		$this->assertTrue( Cache::exists('PDO'));
		$this->assertTrue( Cache::exists('oneKey'));

		Cache::CleanupBeforSessionWrite();

		$this->assertFalse( Cache::exists('PDO'));
		$this->assertTrue( Cache::exists('oneKey'));
	}

	function test_cache_is_off() {
		unset ($_SESSION ['CACHE']);
		Settings::SetPublic('CACHE_IS_ON', false);

		Cache::add('oneKey', 'oneData');
		$this->assertFalse( Cache::exists('oneKey'));
//		fwrite($this->fp, print_r($_SESSION, TRUE));

		$this->assertFalse( Cache::addOrUpdate('twoKey', 'some data'));

		$this->assertNull( Cache::pull('oneKey'));
		$this->assertNull( Cache::pull('twoKey'));
		$this->assertNull( Cache::pull(''));

		$this->assertFalse( Cache::changeExpires('twoKey', 1111));

		$this->assertFalse( Cache::exists('oneKey'));
		$this->assertFalse( Cache::exists('twoKey'));
		$this->assertFalse( Cache::exists('anyKey'));
		$this->assertFalse( Cache::exists(''));

		$this->assertFalse( Cache::hasExpired('oneKey'));
		$this->assertFalse( Cache::hasExpired('twoKey'));
		$this->assertFalse( Cache::hasExpired(''));

		$this->assertTrue( Cache::delete('oneKey'));
		$this->assertTrue( Cache::delete('twoKey'));
		$this->assertTrue( Cache::delete(''));

		$this->assertEquals( -1, Cache::secondsUntilExpire('oneKey'));
		$this->assertEquals( -1, Cache::secondsUntilExpire('twoKey'));
		$this->assertEquals( -1, Cache::secondsUntilExpire(''));

		$this->assertFalse( Cache::doesSerializeWorkOnThisObject('oneKey'));
		$this->assertFalse( Cache::doesSerializeWorkOnThisObject('twoKey'));
		$this->assertFalse( Cache::doesSerializeWorkOnThisObject(''));
	}

}