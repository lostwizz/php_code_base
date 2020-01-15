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
use \php_base\Utils\Settings as Settings;
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

dump::dumpLong($this);

		Settings::GetRunTimeObject('MessageLog')->addTODO('pta ='. $this->process. '.' . $this->task. '.' . $this->action);

		$this->table->process = $this->process;

		$this->table->task = $this->task;

		$this->table->action = $this->action;
		
		$this->table->payload = $this->payload;

		$method = 'do' . str_replace (' ', '_',$this->action);
		Settings::GetRunTimeObject('MessageLog')->addTODO('method ='. $method);

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

		$this->process = ( ! empty($this->process) ? $this->process : 'SimpleTableEditor');
		$this->task    = ( ! empty($this->task) ? $this->task : '');

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
					unset ( $this->payload[$key]);  // get rid of the encoded thing -- dont need it cluttering things up
				}
			}
		} else {
			$this->payload = null;
		}
	}

	protected function doEditRow() : Response {
		echo 'At Edit<BR>';
		$this->table->editRowOfTable();
		return Response::NoError();
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return Response
	 */
	protected function doSave_Edit() : Response {
		echo 'At Save Edit<BR>';
		//$this->table->editRowOfTable();

		Settings::GetRunTimeObject('MessageLog')->addTODO('save the edit');
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
	 * take the passed filter array and make it the working value
	 * @return array|null
	 */
	public function processPassedFilter(): ?array {
		if (!empty($this->payload['filter']) and is_array($this->payload['filter'])) {
			$filter = $this->payload['filter'];
		} else {
			$filter = null;
		}
		return $filter;
	}

	/** -----------------------------------------------------------------------------------------------
	 * run thru the list of filters and remove the data
	 * @param array $data
	 */
	public function filterData(array &$data) {

		if (!empty($this->payload['filter']) and is_array($this->payload['filter'])) {  //if have a filter passed
			foreach ($this->payload['filter'] as $fld => $filterValue) {   // for each filter field see if ther is a filter
				if (!empty($filterValue)) {								// there is a filter so
					$this->filterTheData($data, strtoupper($fld), $filterValue);  // filter the data
				}
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * actuall removes the rows that dont match the filter on the coumn
	 *
	 * @param type $data
	 * @param type $fld
	 * @param type $filterValue
	 */
	public function filterTheData(&$data, $fld, $filterValue) {
		foreach ($data as $key => $value) {  //run thru the data
			if (!(Utils::startsWith($value[$fld], $filterValue, true) )) { // check the data against the column with the filter data
				unset($data[$key]);  // remove the row
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
