<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace php_base\Utils\DatabaseHandlers;

use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\HTML\HTML as HTML;
use \php_base\Resolver;
use \php_base\model\UserRoleAndPermissionsModel as UserRoleAndPermissionsModel;

/**
 * Description of TableFun
 *
 * @author merrem
 */
class SimpleTableEditor {

	protected $table;

	public function __construct( $dataTable) {
dump::dumpLong( $dataTable);
		$this->table = $dataTable;
	}

	public function runTableDisplayAndEdit( $isEditAllowed= false ) : Response { ///$tableName = 'php_base\data\UserInfoData') {

		echo HTML::FormOpen('tableFun');
		echo HTML::Hidden(Resolver::REQUEST_PROCESS, 'test');

		//$d = $tableName::$Table->readAllTableData();
		//$d = ($table::$Table)->readAllTableData();

		dump::dumpLong( $this->table);
		$d = $this->table->readAllTableData();

		$this->sortData($d);

		$this->filterData($d);
		$sortAr = $this->processPassedSort();
		$filter = $this->processPassedFilter();
		echo $tableName::$Table->showTable($d, $sortAr, $filter);
		echo HTML::FormClose();

		return Response::NoError();
	}

	public function processPassedSort(): ?array {
		$ar = array();
//		$flds = UserInfoData::$Table->giveFields();
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

	public function processPassedFilter(): ?array {
		if (!empty($this->payload['filter']) and is_array($this->payload['filter'])) {
			$filter = $this->payload['filter'];
		} else {
			$filter = null;
		}
		return $filter;
	}

	public function filterData(array &$data) {
		if (!empty($this->payload['filter']) and is_array($this->payload['filter'])) {
			foreach ($this->payload['filter'] as $fld => $value) {
				//dump::dump($value, $fld);
				if (!empty($value)) {
					$this->filterOn($data, strtoupper($fld), $value);
				}
			}
		}
	}

//	function startsWith ($string, $startString) {
//		$len = strlen($startString);
//		return (substr($string, 0, $len) === $startString);
//	}


	public function filterOn(&$data, $fld, $filter) {
		foreach ($data as $key => $value) {
			if (!(Utils::startsWith($value[$fld], $filter, true) )) {
				unset($data[$key]);
			}
		}
	}

	public $key = '';

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
