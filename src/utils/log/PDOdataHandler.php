<?php

namespace Monolog\Handler;

use \PDO;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class PDOdataHandler extends AbstractProcessingHandler
{
	private $initialized = false;
	private $pdo;
	private $statement;
	private $tableName;


	public function __construct(PDO $pdo, $level = Logger::DEBUG, bool $bubble = true,  string $tableName ='')
	{
		$this->pdo = $pdo;
		parent::__construct($level, $bubble);
		$this->tableName =  $tableName;
	}

	//public function setTableName(string $tablename): void{
	//	$this->tableName = $tablename;
	//}



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

		$app =  \whitehorse\MikesCommandAndControl2\Settings\Settings::GetPublic('App Name') ?? '';
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

	private function initialize()
	{
		$sql = 'INSERT INTO [' . $this->tableName . '] ('
		 . 'timestamp, channel, machine_id, App, Level, operation,  message'
		 . ') VALUES ('
		 . ':time, :channel, :machine_id, :app, :level, :operation,  :message )' ;

		$this->statement = $this->pdo->prepare( $sql);

		$this->initialized = true;
	}
}