<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace php_base\Control;

use \php_base\model\UserRoleAndPermissionsModel as UserRoleAndPermissionsModel;
use \php_base\Resolver;
use \php_base\utils\Response as Response;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Settings as Settings;
use \php_base\Utils\HTML\HTML as HTML;
use \php_base\Utils\Utils;

//use \php_base\Utils\SubSystemMessage as SubSystemMessage;

/**
 * Description of TableFun
 *
 * @author merrem
 */
class SimpleTableEditorController {

	const OK =0;
	const BAD = -1;

	public $status = self::OK;
	public $tableName;
	public $key = '';


	protected $tableDataObj;

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
	public function __construct( string $process='', string $task='', string $action ='', ?array $payload = null) {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addInfo('@@construct:  proc=' .  Utils::makePTAPpretty( $process, $task, $action, $payload));

		$this->process = $process;
		$this->task = $task;
		$this->action = ((empty($action)) ? 'displayTable' : $action);   // set default action

		$this->payload = $payload;


		$this->determineTableName();

		$this->loadTableObject();

		if ( $this->status != self::BAD){

			$this->beginTaskAndAction();

		}


	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	protected function determineTableName() : void{
		if ( !empty( $this->payload) and  !empty($this->payload['Table'])) {
			$this->tableName = $this->payload['Table'];
		} else {
			Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addAlert ('unable to determine the Table Name');
			$this->tableName = null;
			$this->status = self::BAD;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	protected function loadTableObject() :void {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addInfo('@@loadTableObject: ' . $this->tableName);
		try {
			$newDataTable = Utils::checkClass( $this->tableName);
			$this->tableDataObj = new $newDataTable( $this);
		} catch (\Exception $x) {
			Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addAlert ('unable to load the Table object');
			$this->tableDataObj = null;
			$this->status = self::BAD;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	protected function beginTaskAndAction() {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addInfo('@@beginTaskAndAction: ' . $this->task. ' - ' . $this->action);

		if (empty( $this->task )) {
			$this->runTableDisplayAndEdit();
		}
		if ( method_exists($this, $this->task)) {
			$t = $this->task;
			$this->$t();
		} else {
			Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addAlert ('unable find the task: ' . $this->task );
			$this->status = self::BAD;
		}

	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	public function runTableDisplayAndEdit(){
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addInfo('@@runTableDisplayAndEdit: ' );
		$method = $this->determineMethod();

		$this->determineActiveRowID();

dump::dump($this);
		$this->$method();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return string|null
	 */
	protected function determineMethod() : ?string {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addInfo('@@determineMethod: ' );

		$method = str_replace(' ', '_', $this->action);
		Settings::GetRuntimeObject('SIMPLE_DEBUGGING')->addDebug_5('method =' . $method);
		if ( ! method_exists($this, $method)) {
			$this->status = self::BAD;
			Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addAlert ('unable determine the method');
			return null;
		}

		return $method;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	protected function determineActiveRowID() {
		$tempAction='none';
		if ( !empty( $this->payload['Key'])) {
			$this->key = $this->payload['Key'];
		} elseif ( !empty( $this->payload['EditKey'])) {
			$exploded = \explode('=>', $this->payload['EditKey']);
			$this->key = $exploded[1];
			$tempAction='edit';
		} elseif ( !empty( $this->payload['DelKey'])) {
			$exploded = \explode('=>', $this->payload['DelKey']);
			$this->key = $exploded[1];
			$tempAction='del';
		} elseif ( !empty( $this->payload['AddtKey'])) {
			$exploded = \explode('=>', $this->payload['AddtKey']);
			$this->key = $exploded[1];
			$tempAction='add';
		} elseif ( !empty( $this->payload['SpecialKey'])) {
			$exploded = \explode('=>', $this->payload['SpecialKey']);
			$this->key = $exploded[1];
			$tempAction='special';
		} else {
			$this->key = -1;
		}

		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addInfo_2('key is determined to be: ' . $this->key . ' and '. $tempAction);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return Response
	 */
	protected function displayTable(): Response {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addDebug_5('@@DisplayTable');

		echo HTML::FormOpen('tableFun');
		echo HTML::Hidden(Resolver::REQUEST_PROCESS, $this->process);
		echo HTML::Hidden(Resolver::REQUEST_TASK, $this->task);
		echo HTML::Hidden(Resolver::REQUEST_PAYLOAD. '[Table]', $this->tableName);  // table name so it will  know which tabel to use


		$className = $this->tableDataObj;
		$d = $className->readAllData();

		$this->sortData($d);
		$this->filterData($d);

		$sortAr = $this->processPassedSort();
		$filter = $this->processPassedFilter();

		echo $this->tableDataObj->Table->showTable($d, $sortAr, $filter, $this->process, $this->task);
		echo HTML::FormClose();
		return Response::NoError();

	}



	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $data
	 * @return type
	 */
	public function sortData(array &$data) {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice_6('@@sortData');
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


	/** -----------------------------------------------------------------------------------------------
	 * run thru the list of filters and remove the data
	 * @param array $data
	 */
	public function filterData(array &$data) {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice_6('@@filterData');

		if (!empty($this->payload['filter']) and is_array($this->payload['filter'])) {  //if have a filter passed
			foreach ($this->payload['filter'] as $fld => $filterValue) {   // for each filter field see if ther is a filter
				if (!empty($filterValue)) {								// there is a filter so
					$this->filterTheData($data, strtoupper($fld), $filterValue);  // filter the data
				}
			}
		}
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array|null
	 */
	public function processPassedSort(): ?array {
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice_6('@@processPassedSort');

		$ar = array();

		//$flds = $this->tableDataObj->TableStructure->giveFields();
		$flds = $this->tableDataObj->Table->giveFields();
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
		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice_6('@@processPassedFilter');
		if (!empty($this->payload['filter']) and is_array($this->payload['filter'])) {
			$filter = $this->payload['filter'];
		} else {
			$filter = null;
		}
		return $filter;
	}




}


































//
//		//figure out which table we are working on
//		if ( ! empty($payload['Table']) ){
//			$dataTable = $payload['Table'];
//		} else if ( !empty($action)) {
//			$dataTable = $action;
//		} else {
//			dump::dump($this);
//		}
//
//		$newDataTable = Utils::checkClass( $dataTable);
//		$this->tableDataObj = new $newDataTable( $this);
//
////dump::dump($this->tableDataObj);
//
//		dump::dump(Settings::GetRunTimeObject('MessageLog'));
//
//
//		$this->process = $process;
//		$this->task = $task;
//		$this->action = $action;
//		$this->payload = $payload;
//
//		$this->runTableDisplayAndEdit(true);
//	}
//
//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * @param type $isEditAllowed
//	 * @return Response
//	 */
//	public function runTableDisplayAndEdit($isEditAllowed = false): Response { ///$tableName = 'php_base\data\UserInfoData') {
////dump::dump($this,'rund and e',  array('Show BackTrace Num Lines' => 10,'Beautify_BackgroundColor' => '#FFAA55') );
//
//		Settings::GetRuntimeObject('SIMPLE_DEBUGGING')->addNotice('@@runTableDisplayAndEdit' . ($isEditAllowed ? ' -Editable-' : '-not Editable-'));
//
//		$this->handleVarsPassedToSimpleTableEditor();
//
//		Settings::GetRunTimeObject('SIMPLE_DEBUGGING')->addDebug_5('pta =' . Utils::makePTAPpretty( $this->process, $this->task, $this->action, $this->payload));
//
//		$this->tableDataObj->action = $this->action;
//
//
//		if (!empty($this->payload)) {
//			$this->tableDataObj->payload = $this->payload;
//			$this->tableDataObj->Table->payload = $this->payload;
//		}
//		Settings::GetRunTimeObject('SIMPLE_DEBUGGING')->addDebug_5('pta =' .  Utils::makePTAPpretty( $this->process, $this->task, $this->action, $this->payload));
//
//		$method = str_replace(' ', '_', $this->action);
//		Settings::GetRuntimeObject('SIMPLE_DEBUGGING')->addDebug_5('method =' . $method);
//
//		if ( empty( $method)){
//			return Response::GenericWarning();
//		} else {
//			if (method_exists($this, $method)) {
//
//				$r = $this->$method();
//				return $r;
//			} else {
//				return Response::GenericError();
//			}
//		}
//	}
//
//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * this attempts to figure out which row key is passed in the post/get ACTION when using the simple table editor class
//	 */
//	protected function handleVarsPassedToSimpleTableEditor(): void {
//		Settings::GetRuntimeObject('SIMPLE_DEBUGGING')->addNotice('@@handleVarsPassedToSimplTableEditor - workout the PTA');
//
//		$this->process = (!empty($this->process) ? $this->process : 'SimpleTableEditor');
//		$this->task = (!empty($this->task) ? $this->task : '');
//
//		//$this->action = 'doDisplayTableForEditing';		//default
//		$route= '';
//		if (!empty($this->payload)) {
//			foreach ($this->payload as $key => $value) {
//				Settings::GetRuntimeObject('SIMPLE_DEBUGGING')->addNotice('handleVars: key=' . $key . ' Value=' . \print_r($value, true) ); //Utils::array_display_compactor($value));
//
//				if ( strpos($key, 'RowKey')!== false and !empty( $value)){
//					$this->payload['RowKey'] = $value;
//					$route = $this->router( $key);
//					$route = $this->router( $this->action);
//					unset($this->payload[$key]);  // get rid of the encoded thing -- dont need it cluttering things up
//					break;
//				}
//				if (\strpos($key, 'Key') !== false ) {
//					$exploded = \explode('=>', $key);
//					Settings::GetRuntimeObject('SIMPLE_DEBUGGING')->addNotice($exploded); //'key(id)=' . $exploded[1] . '  action=' . $exploded[0]);
//
//					$the_key = $exploded[1];
//					$this->payload['RowKey'] = $the_key;
//					$route = $this->router( $exploded[0]);
//					unset($this->payload[$key]);  // get rid of the encoded thing -- dont need it cluttering things up
//					break;
//				}
//			}
//			if (empty($route)){
//				Settings::GetRuntimeObject('SIMPLE_DEBUGGING')->addNotice(' no row-key is set so routing with out it :' .  $this->action);
//				//$this->action = $route;
//				$this->action = null;
//			}
//		} else {
//				$this->payload = null;
//		}
//		Settings::GetRuntimeObject('SIMPLE_DEBUGGING')->addNotice('route returned = ' . $route . ' action=' . $this->action);
//	}
//
//	/** ----------------------------------------------------------------------------------------------- */
//	protected function router( ?string $which) : string{
//		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addDebug_5('@@router: '  . $which . ' isnull' . (isnull($which) ? 'isnull': 'isnotnull'));
//
//		$which = str_replace(' ', '_', $which);
//		switch ($which) {
//			case 'EditKey':
//				return 'doEditRow';
//			case 'DelKey':
//				return 'doDeleteRow';
//			case 'SpecialKey':
//				return 'doSpecialRow';
//			case 'AddKey':
//				return 'doAddRow';
//			case 'Save_Edit':
//				return 'doSave_Edit';
//			case 'doDisplayTableForEditing':
//				return '';
//			default:
//				return 'doDisplayTableForEditing';
//		}
//	}
//
//	/** ----------------------------------------------------------------------------------------------- */
//	protected function doDisplayTableForEditing() : Response{
//		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addDebug_5('@@doDisplayTableForEditing');
//
//		echo HTML::FormOpen('tableFun');
//		echo HTML::Hidden(Resolver::REQUEST_PROCESS, $this->process);
//		echo HTML::Hidden(Resolver::REQUEST_TASK, $this->task);
//		echo HTML::Hidden(Resolver::REQUEST_ACTION, 'UserRoleData');  // table name so it will  know which tabel to use
//
//		$className = $this->tableDataObj;
//		$d = $className->readAllData();
//
//		$this->sortData($d);
//		$this->filterData($d);
//
//		$sortAr = $this->processPassedSort();
//		$filter = $this->processPassedFilter();
//
//		echo $this->tableDataObj->Table->showTable($d, $sortAr, $filter, $this->process, $this->task);
//
//		echo HTML::FormClose();
//		return Response::NoError();
//	}
//
//	/** -----------------------------------------------------------------------------------------------*/
//	protected function doEditRow() : Response {
//		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@doEditRow');
//
//		//echo 'At Edit<BR>';
//		echo PHP_EOL, PHP_EOL;
//		echo HTML::FormOpen('tableFun');
//		echo HTML::Hidden(Resolver::REQUEST_PROCESS, $this->process);
//		echo HTML::Hidden(Resolver::REQUEST_TASK, $this->task);
//		echo HTML::Hidden(Resolver::REQUEST_ACTION, 'Editing');
//
//		echo PHP_EOL;
//		echo HTML::Submit(Resolver::REQUEST_ACTION, 'Save Edit');
//
//		$this->tableDataObj->Table->editRowOfTable();
//		echo PHP_EOL;
//		echo HTML::Submit(Resolver::REQUEST_ACTION, 'Save Edit');
//
//		echo HTML::FormClose();
//		echo PHP_EOL, PHP_EOL;
//		return Response::NoError();
//	}
//
//
//
//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * @return Response
//	 */
//	protected function doSave_Edit() : Response {
//		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@doSave_Edit');
//
//		echo 'At Save Edit<BR>';
//
//		Settings::GetRunTimeObject('MessageLog')->addTODO('save the edit');
//
//		$r = $this->tableDataObj->Table->saveRow( $this->payload);
//		if ( $r) {
//			return Response::NoError();
//		} else {
//			return Response::GenericError();
//		}
//	}
//
//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * @return Response
//	 */
//	protected function doDeleteRow() : Response {
//		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@doDeleteRow');
//		echo 'At Delete<BR>';
//		return Response::NoError();
//	}
//
//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * @return Response
//	 */
//	protected function doSpecialRow() : Response {
//		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@doSpecialRow');
//		echo 'At Special<BR>';
//		return Response::NoError();
//	}
//
//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * @return Response
//	 */
//	protected function doAddRow() : Response {
//		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice('@@doAddRow');
//		echo 'At Add<BR>';
//		return Response::NoError();
//	}
//
//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * @return array|null
//	 */
//	public function processPassedSort(): ?array {
//		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice_6('@@processPassedSort');
//
//		$ar = array();
//
//		//$flds = $this->tableDataObj->TableStructure->giveFields();
//		$flds = $this->tableDataObj->Table->giveFields();
//		if (!empty($this->payload['sortAsc']) and is_array($this->payload['sortAsc'])) {
//			foreach ($flds as $fld) {
//				if (!empty($this->payload['sortAsc'][$fld])) {
//					$ar[$fld] = 'Asc';
//				} else {
//					$ar[$fld] = null;
//				}
//			}
//		}
//		if (!empty($this->payload['sortDesc']) and is_array($this->payload['sortDesc'])) {
//			foreach ($flds as $fld) {
//				if (!empty($this->payload['sortDesc'][$fld])) {
//					$ar[$fld] = 'Desc';
//				}
//			}
//		}
//		return $ar;
//	}
//
//	/** -----------------------------------------------------------------------------------------------
//	 * take the passed filter array and make it the working value
//	 * @return array|null
//	 */
//	public function processPassedFilter(): ?array {
//		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice_6('@@processPassedFilter');
//		if (!empty($this->payload['filter']) and is_array($this->payload['filter'])) {
//			$filter = $this->payload['filter'];
//		} else {
//			$filter = null;
//		}
//		return $filter;
//	}
//
//	/** -----------------------------------------------------------------------------------------------
//	 * run thru the list of filters and remove the data
//	 * @param array $data
//	 */
//	public function filterData(array &$data) {
//		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice_6('@@filterData');
//
//		if (!empty($this->payload['filter']) and is_array($this->payload['filter'])) {  //if have a filter passed
//			foreach ($this->payload['filter'] as $fld => $filterValue) {   // for each filter field see if ther is a filter
//				if (!empty($filterValue)) {								// there is a filter so
//					$this->filterTheData($data, strtoupper($fld), $filterValue);  // filter the data
//				}
//			}
//		}
//	}
//
//	/** -----------------------------------------------------------------------------------------------
//	 * actuall removes the rows that dont match the filter on the coumn
//	 *
//	 * @param type $data
//	 * @param type $fld
//	 * @param type $filterValue
//	 */
//	public function filterTheData(&$data, $fld, $filterValue) {
//		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice_6('@@filterTheData');
//		foreach ($data as $key => $value) {  //run thru the data
//			if (!(Utils::startsWith($value[$fld], $filterValue, true) )) { // check the data against the column with the filter data
//				unset($data[$key]);  // remove the row
//			}
//		}
//	}
//
//
//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * @param array $data
//	 * @return type
//	 */
//	public function sortData(array &$data) {
//		Settings::GetRuntimeObject ('SIMPLE_DEBUGGING')->addNotice_6('@@sortData');
//		//figure out what to sort and in which direction
//
//		if (!empty($this->payload['sortAsc'])) {
//			$this->key = strtoupper(array_keys($this->payload['sortAsc'])[0]);
//			uasort($data, function($a, $b) {
//				return $a[$this->key] <=> $b[$this->key];
//			}
//			);
//		}
//
//		if (!empty($this->payload['sortDesc'])) {
//			$this->key = strtoupper(array_keys($this->payload['sortDesc'])[0]);
//			uasort($data, function($a, $b) {
//				return $b[$this->key] <=> $a[$this->key];
//			}
//			);
//		}
//
//		return;
//	}
//
//
//
//
//
//}
