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

	/** -----------------------------------------------------------------------------------------------
	 * @example
	 * 		DBUtils::setupPDO();
	 * @return boolean
	 */
	public static function setupPDO() {
		if (!self::checkPDOSettings()) {
			return false;
		}
		$conn = Settings::getRunTime('PDO_Connection');

		if ($conn == false) {
			$conn = self::setupNewPDO();
			self::EndWriteOne();

			Settings::setRunTime('PDO_Connection', $conn);
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
		//$conn = false;
		$dsn = Settings::GetProtected('DB_DSN');
		$options = Settings::GetProtected('DB_DSN_OPTIONS');
		try {
			$conn = new \PDO($dsn, Settings::GetProtected('DB_Username'), Settings::GetProtected('DB_Password'), $options
			);
			$conn->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_UPPER);
			$conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
		} catch (\PDOException $e) {
			throw new \PDOException($e->getMessage(), (int) $e->getCode());
		}
		return $conn;
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

//Settings::GetRunTimeObject('MessageLog')->addEmergency($sql);
//Settings::GetRunTimeObject('MessageLog')->addEmergency($params);

		try {
			$conn = DBUtils::setupPDO();
			$stmt = $conn->prepare($sql);
			self::doBinding($params, $stmt);
			$r = $stmt->execute();

			$data = $stmt->fetchAll();

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
//dump::dump($sql);
//dump::dump($params);
		try {
			$conn = DBUtils::setupPDO();
			$stmt = $conn->prepare($sql);

			//dump::dump($stmt);
			self::doBinding($params, $stmt);
			$stmt->execute();
			$data = $stmt->fetchAll();
//dump::dump($data);



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
	public static function doDBUpdateSingle(string $sql, array $param): bool {
		try {
			$conn = DBUtils::setupPDO();
			$stmt = $conn->prepare($sql);

			//dump::dump($stmt);
			self::doBinding($param, $stmt);
			$r = $stmt->execute();
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

			//dump::dump($stmt);
			self::doBinding($param, $stmt);
			$conn->beginTransaction();
			$r = $stmt->execute();
			$conn->commit();
			return $conn->lastInsertId();
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

			//dump::dump($stmt);
			self::doBinding($param, $stmt);
			$conn->beginTransaction();
			$r = $stmt->execute();
			$conn->commit();
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
