<?php

/** * ********************************************************************************************
 * DBUtils.class.php
 *
 * Summary  utiity functions related to databases
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description
 * database utilty functions
 *
 *
 *
 * @package Utils
 * @subpackage DBUtils
 * @since 0.3.0
 *
 * @example
 *
 *
 * @todo Description
 *
 * ---- this shows how to get some meta data about the columns
 * echo '<pre>';
print_r(\PDO::getAvailableDrivers());
$stmt->debugDumpParams();

for ($i =0; $i <= $stmt->columnCount(); $i++){
			$metadata = $stmt->getColumnMeta($i);
print_r($metadata);

//print $metadata['table'] . "\n";
//print $metadata['len'] . "\n";
//print $metadata['pdo_type'] . "\n";
//
//print $metadata['sqlsrv:decl_type'] . "\n";
//print $metadata['native_type'] . "\n";
//print $metadata['name'];
}
echo '</pre>';
 */
//**********************************************************************************************

namespace php_base\Utils;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

/** * **********************************************************************************************
 * static class with database utility functions
 *
 * Description.
 *
 * @since 0.5.0
 */
abstract Class DBUtils {

	protected static $currentPreparedStmt = null;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}

	/** -----------------------------------------------------------------------------------------------
	 * @example
	 * 		DBUtils::setupPDO();
	 * @return boolean
	 */
	public static function setupPDO() {

//		if ( Settings::GetPublic('IS_DETAILED_SQL_DEBUGGING')){
//			Settings::setRunTime('SQL_DEBUGGING' ,Settings::GetRunTimeObject('MessageLog'));
//		}
//		Settings::GetRunTimeObject('SQL_DEBUGGING')->addAlert('SQL - at setupPDO');

		if (!self::checkPDOSettings()) {
			return false;
		}

		if ( Cache::exists('PDO_Connection')) {
			$conn = Cache::pull('PDO_Connection');
			Settings::GetRunTimeObject('SQL_DEBUGGING')->addNotice('PDO::Conn from cache');
		} else {

		//if ($conn == false) {
			$conn = self::setupNewPDO();
			//self::EndWriteOne();

			Cache::add( 'PDO_Connection', $conn, 600);
			Settings::GetRunTimeObject('SQL_DEBUGGING')->addNotice('PDO::Conn new create and add to cache');
		}
		return $conn;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return boolean
	 * @throws Exception
	 */
	protected static function checkPDOSettings() {
		if (!extension_loaded(Settings::GetProtected('database_extension_needed'))) {
			throw new Exception('NOT loaded');
		}
		if (empty(Settings::GetProtected('DB_Username'))) {
			throw new Exception('Missing Config Data from Settings- DB_Username');
		}
		if (empty(Settings::GetProtected('DB_Password'))) {
			throw new Exception('Missing Config Data from Settings- DB_Password');
		}
		if (empty(Settings::GetProtected('DB_DSN'))) {
			throw new Exception('Missing Config DSN');
		}
		return true;
	}

	/** -----------------------------------------------------------------------------------------------
	  /**
	 * setup pdo connection to the database
	 * @return \PDO
	 * @throws \PDOException
	 */
	public static function setupNewPDO(): \PDO {
		$dsn = Settings::GetProtected('DB_DSN');
		$options = Settings::GetProtected('DB_DSN_OPTIONS');
		try {
			$conn = new \PDO($dsn, Settings::GetProtected('DB_Username'), Settings::GetProtected('DB_Password'), $options);
			$conn->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_UPPER);
			$conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
			$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
			throw new \PDOException($e->getMessage(), (int) $e->getCode());
		}
		Settings::GetRunTimeObject('SQL_DEBUGGING')->addNotice(' after setupNewPDO');
		return $conn;
	}





	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $sql
	 * @param array $params
	 * @return bool
	 * @throws \PDOException
	 * @throws \Exception
	 */
	public static function doExec( string $sql, array $params = null) :bool {
		try {
			$stmt =  $conn->prepare($sql);
			self::doBinding($params, $stmt);

			if (Settings::GetPublic('IS_DETAILED_SQL_DEBUGGING')){
				dump::dump($sql, 'SQL', array('Show BackTrace Num Lines' => 5));
				dump::dump($params);
			}

			$result = $stmt->execute();
			if (Settings::GetPublic('IS_DETAILED_SQL_DEBUGGING')){
				dump::dump( $result);
			}

		} catch (\PDOException $e) {
			throw new \PDOException($e->getMessage(), (int) $e->getCode());
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), (int) $e->getCode());
		}
		return $result;
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $sql
	 * @param array $params
	 * @return type
	 * @throws \PDOException
	 * @throws \Exception
	 */
	public static function doDBSelectSingle(string $sql, array $params = null) {

		try {
			$conn = DBUtils::setupPDO();
			$stmt = $conn->prepare($sql);

			if (Settings::GetPublic('IS_DETAILED_SQL_DEBUGGING')){
				dump::dump($sql, 'SQL', array('Show BackTrace Num Lines' => 5));
				dump::dump($params);
			}

			self::doBinding($params, $stmt);
			$r = $stmt->execute();

			$data = $stmt->fetchAll();
			if (Settings::GetPublic('IS_DETAILED_SQL_DEBUGGING')){
				dump::dump( $data);
			}

			//Settings::GetRunTimeObject('MessageLog')->addCritical($data);
			$stmt->closeCursor();
			if (empty($data[0])) {
				return null;
			}
			return $data[0];
		} catch (\PDOException $e) {
			throw new \PDOException($e->getMessage(), (int) $e->getCode());
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), (int) $e->getCode());
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $sql
	 * @param array $params
	 * @return type
	 * @throws \PDOException
	 * @throws \Exception
	 */
	public static function doDBSelectMulti(string $sql, array $params = null) {
		//Settings::GetRunTimeObject('MessageLog')->addEmergency($sql);
		try {
			$conn = DBUtils::setupPDO();
			$stmt = $conn->prepare($sql);

		if (Settings::GetPublic('IS_DETAILED_SQL_DEBUGGING')){
			dump::dump($sql, 'SQL', array('Show BackTrace Num Lines' => 5));
			dump::dump($params);
		}
			//dump::dump($stmt);
			self::doBinding($params, $stmt);
			$stmt->execute();
			$data = $stmt->fetchAll();
			if (Settings::GetPublic('IS_DETAILED_SQL_DEBUGGING')){
				dump::dump($data);
			}
			$stmt->closeCursor();
			//Settings::GetRunTimeObject('MessageLog')->addCritical($data);

			return $data;
		} catch (\PDOException $e) {
//dump::dump($e->getMessage())	;
			throw new \PDOException($e->getMessage(), (int) $e->getCode());
		} catch (\Exception $e) {
//dump::dump($e->getMessage())	;
			throw new \Exception($e->getMessage(), (int) $e->getCode());
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $params
	 * @param \PDOStatement|null $stmt
	 */
	public static function doBinding(?array $params, ?\PDOStatement $stmt) {
		if (is_array($params) and ! empty($params) and ! empty($stmt)) {
			foreach ($params as $key => $value) {
				if (is_array($value)) {
					$stmt->bindParam($key, $value['val'], $value['type']);
				} else {
					$stmt->bindParam($key, $value);
				}
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $params
	 * @param type $stmt
	 */
	public static function doBindingSimple(array $params, \PDOStatement $stmt) {
		if (is_array($paramas)) {
			$i = 1;
			foreach ($params as $value) {
				$stmt->bindParam($i, $value);
				$i++;
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $sql
	 */
	public static function BeginWriteOne(string $sql) {
		$conn = DBUtils::setupPDO();

		if (empty(self::$currentPreparedStmt)) {
			self::$currentPreparedStmt = $conn->prepare($sql);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $params
	 * @return type
	 * @throws \Exception
	 */
	public static function WriteOne(array $params) {
		if (empty(self::$currentPreparedStmt)) {
			throw new \Exception('my prepared statment doesnt exist');
		}
		self::doBinding($params, self::$currentPreparedStmt);
		return self::$currentPreparedStmt->execute();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	public static function EndWriteOne() {
		self::$currentPreparedStmt = null;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $sql
	 * @param array $param
	 * @return bool
	 * @throws \PDOException
	 * @throws \Exception
	 * @throws Exception
	 */
	public static function doDBUpdateSingle(string $sql, array $params): bool {
		try {
			$conn = DBUtils::setupPDO();
			$stmt = $conn->prepare($sql);

			if (Settings::GetPublic('IS_DETAILED_SQL_DEBUGGING')){
				dump::dump($sql, 'SQL', array('Show BackTrace Num Lines' => 5));
				dump::dump($params);
			}
			//dump::dump($stmt);
			self::doBinding($params, $stmt);
			$r = $stmt->execute();
			if (Settings::GetPublic('IS_DETAILED_SQL_DEBUGGING')){
				dump::dump( $r);
			}

			if ($r != 1) {
				throw new Exception('did not get the proper number of updates returned');
			}
			return true;
		} catch (\PDOException $e) {
//dump::dump($e->getMessage())	;
			throw new \PDOException($e->getMessage(), (int) $e->getCode());
		} catch (\Exception $e) {
//dump::dump($e->getMessage())	;
			throw new \Exception($e->getMessage(), (int) $e->getCode());
		}
		return false;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $sql
	 * @param array $param
	 * @return int
	 * @throws \PDOException
	 * @throws \Exception
	 */
	public static function doDBInsertReturnID(string $sql, array $param): int {
		try {
			$conn = DBUtils::setupPDO();
			$stmt = $conn->prepare($sql);

			if (Settings::GetPublic('IS_DETAILED_SQL_DEBUGGING')){
				dump::dump($sql, 'SQL', array('Show BackTrace Num Lines' => 5));
				dump::dump($params);
			}

			//dump::dump($stmt);
			self::doBinding($param, $stmt);
			$conn->beginTransaction();
			$r = $stmt->execute();
			if (Settings::GetPublic('IS_DETAILED_SQL_DEBUGGING')){
				dump::dump( $r);
			}

			$conn->commit();
			$last_id = $conn->lastInsertId();
			if (Settings::GetPublic('IS_DETAILED_SQL_DEBUGGING')){
				dump::dump( $last_id);
			}

			return $last_id;
		} catch (\PDOException $e) {
//dump::dump($e->getMessage())	;
			throw new \PDOException($e->getMessage(), (int) $e->getCode());
		} catch (\Exception $e) {
//dump::dump($e->getMessage())	;
			throw new \Exception($e->getMessage(), (int) $e->getCode());
		}
		return -1;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $sql
	 * @param array $param
	 * @return int
	 * @throws \PDOException
	 * @throws \Exception
	 */
	public static function doDBDelete(string $sql, array $param): int{
		try {
			$conn = DBUtils::setupPDO();
			$stmt = $conn->prepare($sql);
			if (Settings::GetPublic('IS_DETAILED_SQL_DEBUGGING')){
				dump::dump($sql, 'SQL', array('Show BackTrace Num Lines' => 5));
				dump::dump($params);
			}

			//dump::dump($stmt);
			self::doBinding($param, $stmt);
			$conn->beginTransaction();
			$r = $stmt->execute();
			$conn->commit();
			if (Settings::GetPublic('IS_DETAILED_SQL_DEBUGGING')){
				dump::dump( $r);
			}
			return $r;
		} catch (\PDOException $e) {
//dump::dump($e->getMessage())	;
			throw new \PDOException($e->getMessage(), (int) $e->getCode());
		} catch (\Exception $e) {
//dump::dump($e->getMessage())	;
			throw new \Exception($e->getMessage(), (int) $e->getCode());
		}
		return -1;
	}

	// -----------------------------------------------------------------------------------------------
	// -----------------------------------------------------------------------------------------------
	// -----------------------------------------------------------------------------------------------
}

//
//        self::DEBUG     => 'DEBUG',
//        self::INFO      => 'INFO',
//        self::NOTICE    => 'NOTICE',
//        self::WARNING   => 'WARNING',
//        self::ERROR     => 'ERROR',
//        self::CRITICAL  => 'CRITICAL',
//        self::ALERT     => 'ALERT',
//        self::EMERGENCY => 'EMERGENCY',
