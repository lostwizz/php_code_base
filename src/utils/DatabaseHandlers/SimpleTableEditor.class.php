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

	protected $tableObj;
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
	public function __construct( $dataTable, string $process='', string $task='', string $action ='', ?array $payload = null) {
		if (Settings::getPublic('IS_DETAILED_SIMPLE_TABLE_EDITOR_DEBUGGING')) {
			Settings::SetRuntime ('SIMPLE_DEBUGGING' , Settings::GetRunTimeObject('MessageLog') );
		}
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@construct' . $dataTable . ' proc=' . $process . ' task=' . $task . ' act=' . $action);

		$this->tableObj = new $dataTable( $this);

		$this->process = $process;
		$this->task = $task;
		$this->action = $action;
		$this->payload = $payload;

		$this->runTableDisplayAndEdit(true);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $isEditAllowed
	 * @return Response
	 */
	public function runTableDisplayAndEdit( $isEditAllowed= false ) : Response { ///$tableName = 'php_base\data\UserInfoData') {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@runTableDisplayAndEdit' . $isEditAllowed ? ' Editable' : 'not Editable');
		$this->handleVarsPassedToSimpleTableEditor();


		Settings::GetRunTimeObject('MessageLog')->addTODO('pta ='. $this->process. '.' . $this->task. '.' . $this->action);

//dump::dumpLong($this);

		$this->tableObj->process = $this->process;

		$this->tableObj->task = $this->task;

		$this->tableObj->action = $this->action;

//dump::dump($this->payload);
//dump::dump($this->tableObj);

		$this->tableObj->payload = $this->payload;
		$this->tableObj->Table->payload = $this->payload;

//dump::dump($this->payload);
dump::dump($this->tableObj->Table->payload);



		$method = 'do' . str_replace (' ', '_',$this->action);
		Settings::GetRunTimeObject('MessageLog')->addTODO('method ='. $method);

		if ( method_exists($this, $method)) {
			$r = $this->$method();
			return $r;
		} else {
			echo HTML::FormOpen('tableFun');
			echo HTML::Hidden(Resolver::REQUEST_PROCESS, $this->process);
			echo HTML::Hidden(Resolver::REQUEST_TASK, $this->task);


//dump::dump( $this->table);

//dump::dump( \get_class_methods($this->table->Table));
			$d = ($this->tableObj->Table)->readAllTableData();

			$this->sortData($d);
			$this->filterData($d);

			$sortAr = $this->processPassedSort();
			$filter = $this->processPassedFilter();

			echo $this->tableObj->Table->showTable($d, $sortAr, $filter, $this->process, $this->task);
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
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@handleVarsPassedToSimplTableEditor' );

		$this->process = ( ! empty($this->process) ? $this->process : 'SimpleTableEditor');
		$this->task    = ( ! empty($this->task) ? $this->task : '');


		if (!empty($this->payload)) {
			foreach ($this->payload as $key => $value) {
				if (\strpos($key, 'Key') !== false) {

					$exploded = \explode('=>', $key);

//dump::dump($exploded);
					$the_key = $exploded[1];
					$this->payload['RowKey'] = $the_key;


					switch ($exploded[0]) {
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

//dump::dump($this->payload);
	}

	/** -----------------------------------------------------------------------------------------------*/
	protected function doEditRow() : Response {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@doEditRow');

		echo 'At Edit<BR>';
dump::dump($this->tableObj->Table);
		$this->tableObj->Table->editRowOfTable();
		return Response::NoError();
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return Response
	 */
	protected function doSave_Edit() : Response {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@doSave_Edit');

		//echo 'At Save Edit<BR>';
		//$this->table->editRowOfTable();

		Settings::GetRunTimeObject('MessageLog')->addTODO('save the edit');

		$r = $this->tableObj->Table->saveRow( $this->payload);
		if ( $r) {
			return Response::NoError();
		} else {
			return Response::GenericError();
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return Response
	 */
	protected function doDeleteRow() : Response {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@doDeleteRow');
		echo 'At Delete<BR>';
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return Response
	 */
	protected function doSpecialRow() : Response {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@doSpecialRow');
		echo 'At Special<BR>';
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return Response
	 */
	protected function doAddRow() : Response {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@doAddRow');
		echo 'At Add<BR>';
		return Response::NoError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array|null
	 */
	public function processPassedSort(): ?array {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@processPassedSort');

		$ar = array();
		$flds = $this->tableObj->Table->giveFields();

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
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@processPassedFilter');
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
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@filterData');

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
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@filterTheData');
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
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@sortData');
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
