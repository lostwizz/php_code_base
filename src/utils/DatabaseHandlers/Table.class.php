<?php

/** * ********************************************************************************************
 * Table.class.php
 *
 * Summary: holds the definition of a database table
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description:
 *    holds the definition of a database table.
 *
 *
 * @link URL
 *
 * @package Utils
 * @subpackage Database
 * @since 0.3.0
 *
 * @example
 *
 * @see Field.class.php
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\Utils\DatabaseHandlers;

use \php_base\Resolver;
use \php_base\Utils\DatabaseHandlers\Field as Field;
use \php_base\Utils\DatabaseHandlers\Field_DateTime as Field_DateTime;
use \php_base\Utils\DatabaseHandlers\Field_Int as Field_Int;
use \php_base\Utils\DatabaseHandlers\Field_Text as Field_Text;
use \php_base\Utils\DBUtils as DBUtils;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\HTML\HTML as HTML;
use \php_base\Utils\Settings as Settings;

/** * **********************************************************************************************
 *
 */
class Table {

	public $tableName;
	public $fields = array();
	public $primaryKeyFieldName;

	public $process;
	public $task;


	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $tableName
	 * @param array $attribs
	 */
	public function __construct(string $tableName, ?array $attribs = null,string $process='', string $task = '') {
		$this->tableName = $tableName;

		$this->proces = $process;
		$this->process = $task;

		if (is_array($attribs) && count($attribs) > 0) {
			$this->tableName->setAttribs($attribs);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $fieldName
	 * @param type $value
	 */
	public function __set($fieldName, $value) {
		$fieldName = strtolower($fieldName);
		//fields[$name] ;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $fieldName
	 * @return boolean
	 */
	public function __get($fieldName) {
		$fieldName = strtolower($fieldName);
		if (array_key_exists($fieldName, $this->fields)) {
			return $this->fields[$fieldName];
		} else {
			return false;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 */
	public function setPrimaryKey(string $fieldName) {
		$this->primaryKeyFieldName = $fieldName;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
	public function addFieldInt(string $fieldName, ?array $attribs = null) {
		$fieldName = strtolower($fieldName);
		$this->fields[$fieldName] = new Field_Int($fieldName, $attribs);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
	public function addFieldText(string $fieldName, ?array $attribs = null) {
		$fieldName = strtolower($fieldName);
		$this->fields[$fieldName] = new Field_Text($fieldName, $attribs);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
	public function addFieldDateTime(string $fieldName, ?array $attribs = null) {
		$fieldName = strtolower($fieldName);
		$this->fields[$fieldName] = new Field_DateTime($fieldName, $attribs);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
	public function addFieldFloat(string $fieldName, ?array $attribs = null) {
		$fieldName = strtolower($fieldName);
		$this->fields[$fieldName] = new \php_base\Utils\DatabaseHandlers\Field_Float($fieldName, $attribs);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
	public function addFieldBOOL(string $fieldName, ?array $attribs = null) {
		$fieldName = strtolower($fieldName);
		$this->fields[$fieldName] = new \php_base\Utils\DatabaseHandlers\Field_Boolean($fieldName, $attribs);
	}


	//-------------------------------------------------------------------------------------------------------------------------------
	public function dump():void {
		$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[0];
		echo '--'  . __METHOD__ .  '-- called from ' . $bt['file'] . '(line: '. $bt['line'] . ')' ;
		echo '<BR>';

		//  TODO: put some code here if there is useful info that doesnt show in a dump::dump($this)

	}




	//-------------------------------------------------------------------------------------------------------------------------------
	public function PrepareWhereClause( ?array $arWhereClause = null) : string{
		if ( empty($arWhereClause) or !is_array($arWhereClause)){
			return '1=1';
		}
		$whereResult = array();
		foreach ($arWhereClause as $fld => $value) {

		}
	}



	//-------------------------------------------------------------------------------------------------------------------------------
	public function giveFieldNamesList(): string {
		$flds = $this->giveField();
		$s = implode(', ', $flds);
		return $s;
	}

	//-------------------------------------------------------------------------------------------------------------------------------
	public function giveFields(): array {
		return array_keys($this->fields);
	}

	//-------------------------------------------------------------------------------------------------------------------------------
	public function giveHeaderRow(bool $withSortButtons = false, bool $withFilterArea = false, ?array $sortKeys =null, ?array $filters=null): string {
		$s = '';
		foreach ($this->fields as $fld => $value) {
			if ( !empty( $sortKeys[$fld] )) {
				$sortDir = $sortKeys[$fld];
			} else {
				$sortDir =null;
			}
			if ( !empty($filters[$fld])){
				$filter = $filters[$fld];
			} else {
				$filter = null;
			}
			$s .= $this->giveHeaderForField($fld, $value, $withSortButtons, $withFilterArea, $sortDir, $filter);
		}
		return $s;
	}

	//-------------------------------------------------------------------------------------------------------------------------------
	protected function giveHeaderForField(string $fldName, Field $fldValue, bool $withSortButtons = false, bool $withFilterArea = false, ?string $sortDir =null, ?string $filter=null): string {
		$s = '<th>';
		$s .= $fldValue->givePrettyName();
		if ($withSortButtons) {
			$s .= $this->giveSortButtons($fldName, $sortDir);
		}
		if ($withFilterArea) {
			$s .= $this->giveFilterArea($fldName, $filter);
		}
		$s .= '</th>';
		return $s;
	}

	//-------------------------------------------------------------------------------------------------------------------------------
	protected function giveSortButtons(string $fldName, ?string $sortDir): string {
		$s = '<BR>';
		$s .= 'sortDir='. $sortDir;
		if (!empty($sortDir) and $sortDir =='Asc') {
			$s .= HTML::Submit(Resolver::REQUEST_PAYLOAD . '[sortAsc][' . $fldName . ']', '^^');
			$s .= HTML::Hidden(Resolver::REQUEST_PAYLOAD . '[sortAsc][' . $fldName . ']', '^');
		} else {
			$s .= HTML::Submit(Resolver::REQUEST_PAYLOAD . '[sortAsc][' . $fldName . ']', '^');

		}
		if (!empty($sortDir) and $sortDir == 'Desc'){
			$s .= HTML::Submit(Resolver::REQUEST_PAYLOAD . '[sortDesc][' . $fldName . ']', 'vv');
			$s .= HTML::Hidden(Resolver::REQUEST_PAYLOAD . '[sortDesc][' . $fldName . ']', 'v');
		} else {
			$s .= HTML::Submit(Resolver::REQUEST_PAYLOAD . '[sortDesc][' . $fldName . ']', 'v');
		}
		$s .= PHP_EOL;
		return $s;
	}

	//-------------------------------------------------------------------------------------------------------------------------------
	public function giveFilterArea(string $fldName, ?string $filter): string {
		$s = '<br>';
		$s .= HTML::Text(Resolver::REQUEST_PAYLOAD . '[filter][' . $fldName . ']', $filter);
		$s .= HTML::Submit(Resolver::REQUEST_PAYLOAD . '[refresh][' . $fldName . ']', 'Apply');
		$s .= PHP_EOL;
		return $s;
	}

	//-------------------------------------------------------------------------------------------------------------------------------
	public function readAllTableData(int $limit = PHP_INT_MAX, string $orderBy = ''): array {
		$sql = 'SELECT * FROM ' . $this->tableName;
		if (!empty($orderBy)) {
			$sql .= ' ORDER BY ' . $orderBy;
		}
		$data = DBUtils::doDBSelectMulti($sql);
		return $data;
	}

	//-------------------------------------------------------------------------------------------------------------------------------
	public function showTable(array $data, ?array $sortKeys = null, ?array $filters = null): string {
		$s = '<table border=1>';
		$s .= $this->giveHeaderRow(true, true, $sortKeys, $filters);
		foreach ($data as $key => $value) {
			$s .= $this->showRowOfTable($value);
		}
		$s .= '</table>';
		return $s;
	}

	//-------------------------------------------------------------------------------------------------------------------------------
	protected function showRowOfTable(array $value) : string{
		$s = '<tr>';
		foreach ($value as $colName => $column) {
			$s .= $this->showFieldOfRow($colName, $column);
		}
		$s .= '</tr>';
		return $s;
	}

	//-------------------------------------------------------------------------------------------------------------------------------
	protected function showFieldOfRow( string $colName,  $column) : string{
		$s = '<td>';
		$s .= $column;
		$s .= '</td>';
		return $s;
	}

}
