<?php

/** * ********************************************************************************************
 * PDOdataHandler.class.php
 *
 * Summary: monolog class to put the messages in a PDO database
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description.
 * maintains 3 queues and then executes them in order -- and checks the response of the execution
 *    and may abort or continue on processing.
 *
 *
 *
 * @package monolog
 * @subpackage PDOHandler
 * @since 0.3.0
 *
 * @ see monolog
 *
 * @example
 *
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace Monolog\Handler;


use \php_base\Utils\Settings as Settings;


use \PDO;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

/** * ********************************************************************************************
 *
 */
class PDOdataHandler extends AbstractProcessingHandler
{
	private $initialized = false;
	private $pdo;
	private $statement;
	private $tableName;


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param PDO $pdo
	 * @param type $level
	 * @param bool $bubble
	 * @param string $tableName
	 */
	public function __construct(PDO $pdo, $level = Logger::DEBUG, bool $bubble = true,  string $tableName ='')
	{
		$this->pdo = $pdo;
		parent::__construct($level, $bubble);
		$this->tableName =  $tableName;
	}

	//public function setTableName(string $tablename): void{
	//	$this->tableName = $tablename;
	//}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $record
	 * @return void
	 */
	protected function write(array $record): void
	{
		if (!$this->initialized) {
			$this->initialize();
		}

		$t =$record['datetime']->format('Y-m-d H:i:s');
		$this->statement->bindParam('time', $t);

		$this->statement->bindParam('channel', $record['channel']);

		$machine = $record['extra']['ip'] ?? '';
		$this->statement->bindParam ('machine_id', $machine);

		$app =  Settings::GetPublic('App Name') ?? '';
		$this->statement->bindParam('app', $app);

		$this->statement->bindParam('level', $record['level']);

		$op = '';
		if (!empty($record['context'] ) ){
			$op	.=http_build_query($record['context'],'',', ');
		}
		$this->statement->bindParam ('operation', $op);

		$this->statement->bindParam('message', $record['message']);

		$this->statement->execute();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	private function initialize()
	{
		$sql = 'INSERT INTO ' . $this->tableName . ' ('
		 . 'timestamp, channel, machine_id, App, Level, operation,  message'
		 . ') VALUES ('
		 . ':time, :channel, :machine_id, :app, :level, :operation,  :message )' ;

		$this->statement = $this->pdo->prepare( $sql);

		$this->initialized = true;
	}
}

////zzzzzzzzzzzzzzzzz