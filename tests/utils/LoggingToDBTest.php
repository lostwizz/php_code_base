<?php
use PHPUnit\Framework\TestCase;

use \whitehorse\MikesCommandAndControl2\Settings\Settings as Settings;
use \whitehorse\MikesCommandAndControl2\Utils\Dump\Dump as Dump;


class LogToDBTest extends TestCase {

	protected static $dbh;

	/**
 	* @requires extension pdo_sqlsrv
 	*/
 //	protected function setUpSettings() :void {
//	}

	/**
 	* @requires extension pdo_sqlsrv
 	*/
	public static function setUPBeforeClass() : void {

		require_once( DIR . '_config' . DS . '_Settings-General.php');
		require_once( DIR . '_config' . DS . '_Settings-Database.php');
		require_once( DIR . '_config' . DS . '_Settings-protected.php');
		include_once( DIR . 'utils' . DS . 'Setup' . DS . 'settings.class.php');

		if ( ! extension_loaded('pdo_sqlsrv')) {
			throw new Exception ('NOT loaded');
		}
		$dsn =  'sqlsrv:server=' .  Settings::GetProtected( 'Logging_Server')   . ';database=' .  Settings::GetProtected( 'Logging_Database');
		$options = 	array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
							PDO::ATTR_CASE=> PDO::CASE_LOWER,
							PDO::ATTR_ERRMODE =>PDO::ERRMODE_EXCEPTION
							//PDO::ATTR_PERSISTENT => true
							);
		try {
			$mssql = new \PDO($dsn,
							Settings::GetProtected('Logging_DB_Username'),
							Settings::GetProtected('Logging_DB_Password'),
							$options
							);
		} catch (\PDOException $e)				{
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
		self::$dbh = $mssql;
		//echo '************************************<pre>';
		//print_r(self::$dbh );
		//echo '</pre>';

	}

	/**
 	* @requires extension pdo_sqlsrv
 	*/
	public static function tearDownAfterClass():void {
		//self::$dbh = null;
	}

	/**
 	* @requires extension pdo_sqlsrv
 	*/
	public function testCheckLogDB() :void {
		$o = Settings::GetPublic('DBLog');
		if ($o != null) {
			$s ='-------------UNIT TEST------------';
			$o->addNotice( $s, ['username'=>'fred was here', 'super'=> 'sam was not here']);
			//$o->addRecord(\Monolog\Logger::NOTICE, $s);


			$sql ='SELECT TOP (1) *
				  FROM  [Mikes_Application_Store].[dbo].[Application_Log]
				  ORDER BY id desc';



			$this->statement = self::$dbh->prepare(	$sql);

			$this->statement->execute();
			$r= $this->statement->fetch();

			$this->assertEquals( $r['channel'], 'DBLog' );
			$this->assertEquals( $r['app'], Settings::GetPublic( 'App Name') );

			$lvl = \Monolog\Logger::NOTICE;
			$this->assertEquals( $r['level'], $lvl );

			$this->assertEquals( $r['message'], $s) ;

			$this->assertEquals($r['operation'], ' -  -  - username=fred+was+here, super=sam+was+not+here');

			$x= explode( ':' ,$r['caller']);
			$this->assertEquals($x[0], 'P');
			$this->assertEquals($x[1],'\Projects\MikesCommandAndControl2\tests\utils\log\LoggingToDBTest.php ' );

			$this->assertIsNumeric($x[2]); //, var_dump($x));

			$this->assertEquals( $r['caller_of_caller'], 'LogToDBTest -> testCheckLogDB');
		} else {
				$this->markTestIncomplete('Something is up with the DBLOG logger' );
		}
	}

	/**
 	* @requires extension pdo_sqlsrv
 	*/
	public function testCheckLogDBData_2() :void {

		$o =Settings::GetPublic('DBdataLog');
		if ($o != null) {

			$s ='-------------UNIT TEST------------';

			$o->addNotice( $s, ['username'=>'fred was here', 'super'=> 'sam was not here']);

			//$o->addRecord(\Monolog\Logger::NOTICE, $s);

			$sql ='SELECT TOP (1) *
				  FROM  [Mikes_Application_Store].[dbo].[Application_Data_Log]
				  ORDER BY id desc';

			$this->statement = self::$dbh->prepare(	$sql);

			$this->statement->execute();
			$r= $this->statement->fetch();

			$this->assertEquals( $r['channel'], 'DBData' );
			$this->assertEquals( $r['app'], Settings::GetPublic( 'App Name') );

			$lvl = \Monolog\Logger::NOTICE;
			$this->assertEquals( $r['level'], $lvl );

			$this->assertEquals( $r['message'], $s) ;

			$this->assertEquals($r['operation'], 'username=fred+was+here, super=sam+was+not+here');
		} else {
				$this->markTestIncomplete('Something is up with the DBLOG logger' );
		}

	}


}