<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace php_base\Utils\DatabaseHandlers;

use \php_base\model\UserRoleAndPermissionsModel as UserRoleAndPermissionsModel;
use \php_base\Resolver;
use \php_base\utils\Response as Response;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\HTML\HTML as HTML;
use \php_base\Utils\Utils;

/**
 * Description of TableFun
 *
 * @author merrem
 */
class SimpleTableEditor {

	protected $table;
	public $key = '';

	public $process;
	public $task;
	public $action;
	public $payload;

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $dataTable
	 * @param string $process
	 * @param string $task
	 * @param string $action
	 * @param array $payload
	 */
	public function __construct( $dataTable, string $process='', string $task= '',string $action ='', ?array $payload = null) {
//dump::dumpLong( $dataTable);
		$this->table = $dataTable;

		$this->process = $process;
		$this->task = $task;
		$this->action = $action;
		$this->payload = $payload;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $isEditAllowed
	 * @return Response
	 */
	public function runTableDisplayAndEdit( $isEditAllowed= false ) : Response { ///$tableName = 'php_base\data\UserInfoData') {

		$this->handleVarsPassedToSimpleTableEditor();

//dump::dumpLong($this);
		$method = 'do' . $this->action;
		if ( method_exists($this, $method)) {
			$r = $this->$method();
			return $r;
		} else {
			echo HTML::FormOpen('tableFun');
			echo HTML::Hidden(Resolver::REQUEST_PROCESS, $this->process);
			echo HTML::Hidden(Resolver::REQUEST_TASK, $this->task);

			$d = $this->table->readAllTableData();
			$this->sortData($d);
			$this->filterData($d);

			$sortAr = $this->processPassedSort();
			$filter = $this->processPassedFilter();

			echo $this->table->showTable($d, $sortAr, $filter, $this->process, $this->task);
			echo HTML::FormClose();

			return Response::NoError();
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * this attempts to figure out which row key is passed in the post/get ACTION when using the simple table editor class
	 *
	 * @param type $postVars
	 * @param type $getVars
	 */
	protected function handleVarsPassedToSimpleTableEditor() {
		if (!empty($this->payload)) {
			foreach ($this->payload as $key => $value) {
				if (\strpos($key, 'Key') !== false) {
					$expload = \explode('=>', $key);

					$the_key = $expload[1];

					$this->payload['RowKey'] = $the_key;

					switch ($expload[0]) {
						case 'EditKey':
							$this->action = 'EditRow';
							break;
						case 'DelKey':
							$this->action = 'DeleteRow';
							break;
						case 'SpecialKey':
							$this->action = 'SpecialRow';
							break;
						case 'AddKey':
							$this->action = 'AddRow';
							break;
						default:
							break;
					}

				}
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return Response
	 */
	protected function doEditRow() : Response {
		echo 'At Edit<BR>';
		return Response::NoError();
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return Response
	 */
	protected function doDeleteRow() : Response {
		echo 'At Delete<BR>';
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return Response
	 */
	protected function doSpecialRow() : Response {
		echo 'At Special<BR>';
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return Response
	 */
	protected function doAddRow() : Response {
		echo 'At Add<BR>';
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array|null
	 */
	public function processPassedSort(): ?array {
		$ar = array();
		$flds = $this->table->giveFields();

		if (!empty($this->payload['sortAsc']) and is_array($this->payload['sortAsc'])) {
			foreach ($flds as $fld) {
				if (!empty($this->payload['sortAsc'][$fld])) {
					$ar[$fld] = 'Asc';
				} else {
					$ar[$fld] = null;
				}
			}
		}
		if (!empty($this->payload['sortDesc']) and is_array($this->payload['sortDesc'])) {
			foreach ($flds as $fld) {
				if (!empty($this->payload['sortDesc'][$fld])) {
					$ar[$fld] = 'Desc';
				}
			}
		}
		return $ar;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array|null
	 */
	public function processPassedFilter(): ?array {
		if (!empty($this->payload['filter']) and is_array($this->payload['filter'])) {
//dump::dump( $this->payload['filter']);
			$filter = $this->payload['filter'];
		} else {
			$filter = null;
		}
		return $filter;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $data
	 */
	public function filterData(array &$data) {

		if (!empty($this->payload['filter']) and is_array($this->payload['filter'])) {
//dump::dump( $this->payload['filter']);
			foreach ($this->payload['filter'] as $fld => $value) {
				//dump::dump($value, $fld);
				if (!empty($value)) {
					$this->filterOn($data, strtoupper($fld), $value);
				}
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @param type $fld
	 * @param type $filter
	 */
	public function filterOn(&$data, $fld, $filter) {
		foreach ($data as $key => $value) {
			if (!(Utils::startsWith($value[$fld], $filter, true) )) {
				unset($data[$key]);
			}
		}
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $data
	 * @return type
	 */
	public function sortData(array &$data) {
		//figure out what to sort and in which direction

		if (!empty($this->payload['sortAsc'])) {
			$this->key = strtoupper(array_keys($this->payload['sortAsc'])[0]);
			uasort($data, function($a, $b) {
				return $a[$this->key] <=> $b[$this->key];
			}
			);
		}

		if (!empty($this->payload['sortDesc'])) {
			$this->key = strtoupper(array_keys($this->payload['sortDesc'])[0]);
			uasort($data, function($a, $b) {
				return $b[$this->key] <=> $a[$this->key];
			}
			);
		}

		return;
	}

}
