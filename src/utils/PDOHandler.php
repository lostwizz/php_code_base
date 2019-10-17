<?php

namespace Monolog\Handler;

use \php_base\Utils\Settings as Settings;


use	\PDO;

use	Monolog\Logger;
use	Monolog\Handler\AbstractProcessingHandler;

class PDOHandler extends AbstractProcessingHandler
{
	private	$initialized = false;
	private	$pdo;
	private	$statement;
	private $tableName;


	public function	__construct(PDO	$pdo, $level = Logger::DEBUG, bool $bubble = true, string $tableName ='')
	{
		$this->pdo = $pdo;
		parent::__construct($level,	$bubble);
		$this->tableName = $tableName;
	}

//	public function setTableName(string $tablename): void{
//		$this->tableName = $tablename;
//	}

	protected function write(array $record): void
	{
		if (!$this->initialized) {
			$this->initialize();
		}

////echo '<pre>';
////print_r ($record);
////echo '<pre>';

		$t =$record['datetime']->format('Y-m-d H:i:s');
		$this->statement->bindParam('time',	$t);

		$this->statement->bindParam('channel', $record['channel']);

		$machine = $record['extra']['ip'] ?? '';
		$this->statement->bindParam('machine_id', $machine);

		$app = Settings::GetPublic('App Name') ??	'';
		$this->statement->bindParam('app', $app);

		$this->statement->bindParam('level', $record['level']);

		$op =  ($record['extra']['server'] ?? '')
			. ' - '
			. ($record['extra']['url'] ?? '' )
			. ' - '
			. ($record['extra']['http_method'] ?? '' )
			. ' - '
			. ($record['extra']['referrer']	?? '' );
		if (!empty($record['context'] )){
			//$op	.= implode(	', ', $record['context'] );
			$op .=http_build_query($record['context'],'',', ');
		}
		$op = $op ?? '';
		$this->statement->bindParam	('operation', $op);

		$caller = (	$record['extra']['file'] ??	'') . ' : '. ($record['extra']['line']	?? '');
		$this->statement->bindParam('caller', $caller);

		$call_caller = ($record['extra']['class']  ??'') . ' -> '. ($record['extra']['function'] ??'');
		$this->statement->bindParam('call_caller',	$call_caller);

		$thread = $record['extra']['process_id'] ??	'';
		$this->statement->bindParam('thread', $thread);

		$msg =  $record['message'] ??'';
		$this->statement->bindParam('message',$msg);

		$tracer	= $record['extra']['tracer'] ??	'';
		$this->statement->bindParam('tracer', $tracer);

//////////try {
			$r = $this->statement->execute();
//////////echo '<pre>rrrrrrrrrrrrrrrrrrrrrrrrrrrrrr';
//////////echo $r ? '-=true=-': '-=False=-';
//////////print_r( $this->pdo->errorInfo());
//////////print_r( $this->statement->errorInfo());
//////////////////print_r($this);
//////////echo 'UUUUUUUUUUUUUUUUUU<pre>';
//////////
////////// 		} catch (\PDOException $e)	{
//////////			echo '<pre>oooooooooooooooooooooooooooooo';
//////////
////////// 			echo $e->getMessage(), (int)$e->getCode();
//////////			//echo $r ? '-=true=-': '-=False=-';
//////////			//print_r ($r);
//////////			print_r ($e);
////////////print_r ($this->pdo);
//////////			echo 'UUUUUUUUUUUUUUUUUU<pre>';
//////////		}
	}

	private	function initialize()
	{
		$sql = 'INSERT INTO	' . $this->tableName
		 . ' (timestamp, channel,	machine_id,	App, Level,	operation, caller, caller_of_caller, threadNum,	message, tracer'
		 . ') VALUES ('
		 . ':time, :channel, :machine_id, :app,	:level,	:operation,	:caller, :call_caller, :thread,	:message, :tracer )' ;

		$this->statement = $this->pdo->prepare(	$sql);

		$this->initialized = true;
	}
}