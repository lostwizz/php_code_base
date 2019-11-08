<?php

/** * ********************************************************************************************
 * resolver.class.php
 *
 * Summary (no period for file headers)
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description:
 * 		Database Utility Functions
 *
 * @todo Description
 *
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
abstract Class myDBUtils {

	protected static $currentPreparedStmt = null;



	/** -----------------------------------------------------------------------------------------------
	 * @example
	 * 		myDBUtils::setupPDO();
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
			$conn = new \PDO($dsn,
					  Settings::GetProtected('DB_Username'),
					  Settings::GetProtected('DB_Password'),
					  $options
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
			$conn = myDBUtils::setupPDO();
			$stmt = $conn->prepare($sql);
			self::doBinding($params, $stmt);
			$r = $stmt->execute();

			$data = $stmt->fetchAll();

			//Settings::GetRunTimeObject('MessageLog')->addCritical($data);
			$stmt->closeCursor();
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
			$conn = myDBUtils::setupPDO();
			$stmt = $conn->prepare($sql);

			//dump::dump($stmt);
			self::doBinding($params, $stmt );
			$stmt->execute();
			$data = $stmt->fetchAll();
//dump::dump($data);

			$stmt->closeCursor();
			//Settings::GetRunTimeObject('MessageLog')->addCritical($data);

			return $data;
		} catch (\PDOException $e) {
dump::dump($e->getMessage())	;
			throw new \PDOException($e->getMessage(), (int) $e->getCode());
		} catch (\Exception $e) {
dump::dump($e->getMessage())	;
			throw new \Exception($e->getMessage(), (int) $e->getCode());
		}
	}


	// -----------------------------------------------------------------------------------------------
	public static function doBinding( $params, $stmt){
		if ( is_array($params) and !empty($params)  and !empty($stmt)){
			foreach ($params as $key=> $value) {
				if ( is_array( $value )) {
					$stmt->bindParam($key, $value['val'], $value['type']);
				} else {
					$stmt->bindParam($key, $value);
				}
			}
		}

	}

	//-----------------------------------------------------------------------------------------------
	public static function doBindingSimple($params, $stmt){
		if (is_array($paramas)) {
			$i =1;
			foreach($params as $value) {
				$stmt->bindParam($i, $value);
				$i++;
			}
		}
	}

	// -----------------------------------------------------------------------------------------------
	public static function BeginWriteOne($sql) {
		$conn = myDBUtils::setupPDO();

		if ( empty( self::$currentPreparedStmt)){
			self::$currentPreparedStmt =  $conn->prepare($sql);
		}
	}

	// -----------------------------------------------------------------------------------------------
	public static function WriteOne( $params) {
		if ( empty( self::$currentPreparedStmt)){
			throw new \Exception( 'my prepared statment doesnt exist');
		}
		self::doBinding($params,self::$currentPreparedStmt  );
		return self::$currentPreparedStmt->execute();
	}


	public static function EndWriteOne(){
		self::$currentPreparedStmt = null;
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
