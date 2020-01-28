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
use \php_base\Utils\DatabaseHandlers\Field_Boolean as Field_Boolean;
use \php_base\Utils\DBUtils as DBUtils;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\HTML\HTML as HTML;
use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Cache as CACHE;
use \php_base\Utils\SubSystemMessage as SubSystemMessage;
use \php_base\Utils\Utils;
//use \php_base\Utils\simpleConfig as simpleConfig;


/** * **********************************************************************************************
 *
 */
class Table {

	public $tableName;
	public $fields = array();
	public $primaryKeyFieldName;

	public $attribs = array();

	public $process;
	public $task;
	public $action;
	public $payload;



	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $tableName
	 * @param array $attribs
	 */
	public function __construct(string $tableName ='', ?array $attribs = null, string $process='', string $task = '') {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addNotice('@@constructor: ' . $tableName);

		$this->tableName = $tableName;

		$this->process = $process;
		$this->task = $task;

		if (is_array($attribs) && count($attribs) > 0) {
			$this->setAttribs($attribs);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $attribs
	 * @return void
	 */
	public function setAttribs(array $attribs): void {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addNotice('@@setAttribs: ' . print_r($attribs, true));
		foreach ($attribs as $key => $value) {
			//echo 'key=', $key, '  value=',$value;
			$this->attribs[$key] = $value;
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
	public function __set(string $fieldName, string $value) {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDebug_4('@@__set fldname='. $fieldName );
		$this->attribs[$fieldName] = strtolower($fieldName);
		//fields[$name] ;
	}

	/** -----------------------------------------------------------------------------------------------
	 *  return the field or the attribute of the passed fieldname/attribName
	 *
	 * @param type $fieldName
	 * @return boolean
	 */
	public function __get( string $fieldName) {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDebug_4('@@__get : ' . $fieldName);
		$fieldNameLower = strtolower($fieldName);
		if (array_key_exists($fieldNameLower, $this->fields)) {
			return $this->fields[$fieldNameLower];
		} else {
			if ( array_key_exists($fieldName, $this->attribs)){
				return $this->attribs[$fieldName];
			}
			return false;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 */
	public function setPrimaryKey(string $fieldName) {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addNotice('@@setPrimaryKey: ' . $fieldName);
		$this->primaryKeyFieldName = $fieldName;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
	public function addFieldInt(string $fieldName, ?array $attribs = null) {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDEBUG_4('@@addFieldInt: ' . $fieldName);
		$fieldName = strtolower($fieldName);
		$this->fields[$fieldName] = new Field_Int($this, $fieldName, $attribs);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
	public function addFieldText( string $fieldName, ?array $attribs = null) {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDEBUG_4('@@addFieldText: ' . $fieldName);
		$fieldName = strtolower($fieldName);
		$this->fields[$fieldName] = new Field_Text($this, $fieldName, $attribs);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
	public function addFieldDateTime(string $fieldName, ?array $attribs = null) {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDEBUG_4('@@addFieldDateTime: ' . $fieldName);
		$fieldName = strtolower($fieldName);
		$this->fields[$fieldName] = new Field_DateTime($this, $fieldName, $attribs);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
	public function addFieldFloat(string $fieldName, ?array $attribs = null) {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDEBUG_4('@@addFieldFloat: ' . $fieldName);
		$fieldName = strtolower($fieldName);
		$this->fields[$fieldName] = new Field_Float($this, $fieldName, $attribs);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
	public function addFieldBOOL(string $fieldName, ?array $attribs = null) {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDEBUG_4('@@addFieldBOOL: ' . $fieldName);
		$fieldName = strtolower($fieldName);
		$this->fields[$fieldName] = new Field_Boolean($this, $fieldName, $attribs);
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	public function dump():void {
		$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[0];
		echo '--'  . __METHOD__ .  '-- called from ' . $bt['file'] . '(line: '. $bt['line'] . ')' ;
		echo '<BR>';

		//  TODO: put some code here if there is useful info that doesnt show in a dump::dump($this)

	}




	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $arWhereClause
	 * @return string
	 */
	public function PrepareWhereClause( ?array $arWhereClause = null) : string{
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addNotice('@@PrepareWhereClause: ' . print_r($arWhereClause, true));
		if ( empty($arWhereClause) or !is_array($arWhereClause)){
			return '1=1';
		}
		$whereResult = array();
		foreach ($arWhereClause as $fld => $value) {

		}
	}



	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return string
	 */
	public function giveFieldNamesList(): string {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addNotice('@@giveFieldNamesList ');
		$flds = $this->giveField();
		$s = implode(', ', $flds);
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array
	 */
	public function giveFields(): array {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addNotice('@@giveFields' );
		return array_keys($this->fields);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param bool $withSortButtons
	 * @param bool $withFilterArea
	 * @param array $sortKeys
	 * @param array $filters
	 * @return string
	 */
	public function giveHeaderRow(bool $withSortButtons = false, bool $withFilterArea = false, ?array $sortKeys =null, ?array $filters=null): string {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addNotice('@@giveHeaderRow: ' );
		$s = '';

		$s .= $this->showAddButton( true);
		$s .= $this->showEditColumn( true);
		$s .= $this->showDeleteColumn( true);
		$s .= $this->showSpecialColumn( true);
//dump::dumpLong( $this->fields)		;

		foreach ($this->fields as $fld => $value) {
			$s .= $this->handleHeaderColumns( $fld, $value, $withSortButtons, $withFilterArea, $sortKeys, $filters);
		}
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param bool $isHeaderRow
	 * @return string
	 */
	protected function showAddButton( bool $isHeaderRow =false) : string{
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDEBUG_4('@@showAddButton');
		$s = '';
		if($this->isAdding){
			if ( $isHeaderRow ){
				$s = HTML::TD() . '[Add]' . HTML::TDend() ;
			} else {
				$r =  '[AddKey=>NoRow]';
				$s = HTML::TD()
						. HTML::Image( Resolver::REQUEST_PAYLOAD .  $r  ,  'static\images\b_insrow.png', null, ['width' =>18])
						. HTML::TDend();
			}
		}
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 *
	 * @param bool $isHeaderRow
	 * @return string
	 */
	protected function showEditColumn( bool $isHeaderRow =false, $rowKey = -1) : string {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDEBUG_4('@@showEditColumn');
		$s = '';
		if( $this->isEditing){
			if ( $isHeaderRow ) {
				$s = HTML::TD() . '[Edit]' . HTML::TDend() ;
			} else {
				$r =  '[EditKey=>' . $rowKey . ']';
				$s = HTML::TD()
						. HTML::Image( Resolver::REQUEST_PAYLOAD .  $r  ,  'static\images\b_edit.png', null, ['width' =>18])
						. HTML::TDend();
			}
		}
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param bool $isHeaderRow
	 * @return string
	 */
	protected function showDeleteColumn( bool $isHeaderRow =false, $rowKey = -1) : string {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDEBUG_4('@@showDeleteColumn');
		$s = '';
		if( $this->isDeleting){
			if ($isHeaderRow ){
				$s = HTML::TD() . '[Del]' . HTML::TDend() ;
			} else {
				$r =  '[DelKey=>' . $rowKey . ']';
				$s = HTML::TD()
						. HTML::Image( Resolver::REQUEST_PAYLOAD .  $r  ,  'static\images\b_drop.png', null, ['width' =>18])
						. HTML::TDend();
			}
		}
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param bool $isHeaderRow
	 * @return string
	 */
	protected function showSpecialColumn( bool $isHeaderRow =false, $rowKey = -1) {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDEBUG_4('@@showSpecialColumn');
		$s = '';
		if( $this->isSpecial){
			if ($isHeaderRow ){
				$s = HTML::TD() . '[Spl]' . HTML::TDend() ;
			} else {
				$r =  '[SpecialKey=>' . $rowKey . ']';
				$s = HTML::TD()
						. HTML::Image( Resolver::REQUEST_PAYLOAD .  $r  ,  'static\images\arrow_right.png', null, ['width' =>18])
						. HTML::TDend();
			}
		}
		return $s;
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $fld
	 * @param type $value
	 * @param bool $withSortButtons
	 * @param bool $withFilterArea
	 * @param array $sortKeys
	 * @param array $filters
	 * @return string
	 */
	protected function handleHeaderColumns( $fld, $value, bool $withSortButtons = false, bool $withFilterArea = false, ?array $sortKeys =null, ?array $filters=null ) : string{
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDEBUG_4('@@handleHeaderColumns');

//dump::dump($fld);
		$s = '';
		if ($value->isShowable){
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


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fldName
	 * @param Field $fldValue
	 * @param bool $withSortButtons
	 * @param bool $withFilterArea
	 * @param string|null $sortDir
	 * @param string|null $filter
	 * @return string
	 */
	protected function giveHeaderForField(
			string $fldName,
			Field $fldValue,
			bool $withSortButtons = false,
			bool $withFilterArea = false,
			?string $sortDir =null,
			?string $filter=null
			): string {

		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDEBUG_4('@@giveHeaderForField');
		$s = '';
		$s .= HTML::TD();
		if ($withSortButtons) {
			$s .= $this->giveSortButtons($fldName, $sortDir, $fldValue);
		}
		if ($withFilterArea) {
			$s .= $this->giveFilterArea($fldName, $filter);
		}
		$s .= HTML::TDend();
		return $s;
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fldName
	 * @param string|null $sortDir
	 * @param Field $fldValue
	 * @return string
	 */
	protected function giveSortButtons(string $fldName, ?string $sortDir, Field $fldValue): string {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDEBUG_4('@@giveSortButtons');
		$s = '<BR>';
		if (!empty($sortDir) and $sortDir =='Asc') {
			$s .= HTML::Hidden( Resolver::REQUEST_PAYLOAD . '[sortAsc][' . $fldName . ']', '^');
			$s .= HTML::Image( Resolver::REQUEST_PAYLOAD . '[sortAsc][' . $fldName . ']', '\static\images\A_to_Z_Pushed_icon.png', 'az', ['width'=>18]);
		} else {
			$s .= HTML::Image( Resolver::REQUEST_PAYLOAD . '[sortAsc][' . $fldName . ']',  '\static\images\A_to_Z_icon.png',  'az', ['width'=>18]);
		}

		$s .= HTML::space(2);
		$s .= $fldValue->givePrettyName();
		$s .= HTML::space(2);

		if (!empty($sortDir) and $sortDir == 'Desc'){
			$s .= HTML::Hidden( Resolver::REQUEST_PAYLOAD . '[sortDesc][' . $fldName . ']', 'v');
			$s .= HTML::Image( Resolver::REQUEST_PAYLOAD . '[sortDesc][' . $fldName . ']',  '\static\images\Z_to_A_Pushed_icon.png',  'za', ['width'=>18]);
		} else {
			$s .= HTML::Image( Resolver::REQUEST_PAYLOAD . '[sortDesc][' . $fldName . ']',  '\static\images\Z_to_A_icon.png',  'za', ['width'=>18]);
		}
		$s .= PHP_EOL;
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fldName
	 * @param string|null $filter
	 * @return string
	 */
	public function giveFilterArea(string $fldName, ?string $filter): string {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDEBUG_4('@@giveFilterArea');
		$s = '<br>';
		$s .= HTML::Text(Resolver::REQUEST_PAYLOAD . '[filter][' . $fldName . ']', $filter);
		$s .= HTML::Submit(Resolver::REQUEST_PAYLOAD . '[refresh][' . $fldName . ']', 'Apply');
		$s .= PHP_EOL;
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param int $limit
	 * @param string $orderBy
	 * @return array
	 */
	public function readAllTableData(int $limit = PHP_INT_MAX, string $orderBy = ''): array {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addNotice('@@readAllTableData');

		echo 'DO NOT USE THIS';
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addEmergency('Dont Use This function');
//
//
//dump::dump($this->payload['Table']);
//
//
//
//		if (CACHE::exists('Table_' , $this->tableName . '_TableReadAll')) {
//			return CACHE::pull('Table_' . $this->tableName . '_TableReadAll');
//		} else {
//			$sql = 'SELECT * FROM ' . $this->tableName;
//			if (!empty($orderBy)) {
//				$sql .= ' ORDER BY ' . $orderBy;
//			}
//			$data = DBUtils::doDBSelectMulti($sql);
//
//			if ( Settings::GetPublic('CACHE_Allow_Tables to be Cached')){
//				CACHE::add( 'Table_' . $this->tableName . '_TableReadAll', $data);
//			}
//
//			return $data;
//		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $RowData
	 * @return string
	 */
	protected function showRowOfTable(array $RowData) : string{
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addDEBUG_4('@@showRowOfTable');

		$fld = strtoupper($this->primaryKeyFieldName);
		$rowId  = $RowData[ $fld ];          // this is the primarykey of the row (used in the hidden to tell which row editting

		$s ='';
		// show the button columns
		$s .= $this->showAddButton( false);
		$s .= $this->showEditColumn( false,$rowId);
		$s .= $this->showDeleteColumn( false, $rowId);
		$s .= $this->showSpecialColumn( false, $rowId);

		// now run thru each column and get the output
		foreach ($RowData as $colName => $dataValue) {

			$o = ($this->$colName);
			$s .= $o->showField( $dataValue, true);

//			if ( ($this->$colName)->isShowable ) {
//
//dump::dump($this->colName);
//dump::dump($this->columnValue);
//				$s .= $this->showFieldOfRow( ($this->$colName)->attribs, $columnValue );
//			}
		}
		return $s;
	}



	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $data
	 * @param array $sortKeys
	 * @param array $filters
	 * @return string
	 */
	public function showTable(array $data, ?array $sortKeys = null, ?array $filters = null): string {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addNotice('@@showTable');

		$s = HTML::Open('Table', ['border' => 1, 'width' => '90%']);

		$s .= $this->giveHeaderRow(true, true, $sortKeys, $filters);
		foreach ($data as $key => $RowData) {
			$s .= HTML::TR();

			$s .= $this->showRowOfTable($RowData);
			$s .= HTML::TRend();
		}
		$s .= HTML::Close('Table');
		$s .= PHP_EOL;
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	public function editRowOfTable() {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addNotice('@@editRowOfTable');

		$className = $this->className;
		$tbl = new $className('dummyController');
		$data = ($tbl)->readAllData();

		//$data = $this->readAllTableData();
		$flds = $this->giveFields();
//dump::dumpShort($this);
		$rowNum = $this->payload['RowKey'];
		$rowOfData = $this->getRowByPrimaryKey($this->payload['RowKey'], $data);

		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addNotice('row of data to edit: ' . Utils::array_display_compactor($rowOfData));


		if (!is_array($this->payload)) {
			$editPayload = array();
		} else {
			$editPayload = $this->payload;
		}
		$editPayload['RowKey'] = $rowNum;

//		$newPayload = str_replace( '.', '~!~', $editPayload);
//		$r = serialize($newPayload);

		//echo HTML::Hidden(Resolver::REQUEST_PAYLOAD, $a);

		echo PHP_EOL, PHP_EOL;
		echo HTML::Open('Table', ['border' => 1, 'width' => '50%']);
//dump::dump( $rowOfData);
//dump::dump($this->fields);
		foreach ($this->fields as $fld => $fldObj) {
			if ($fldObj->isShowable) {
				echo HTML::TR();
				echo HTML::TD();
				echo $fldObj->prettyName;
				echo HTML::TDendTD();

				$ColOfData = $rowOfData[strtoupper($fld)];
//dump::dump( $rowOfData[strtoupper($fld)]);
				echo $fldObj->giveHTMLInput($fld, $ColOfData);

				echo HTML::TDend();
				echo HTML::TRend();
				echo PHP_EOL;
			}
		}
		echo HTML::Close('Table');
		echo PHP_EOL;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @param type $data
	 * @return type
	 */
	public function getRowByPrimaryKey($key, $data): ?array {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addNotice('@@getRowByPrimaryKey key=' . $key );

		$primaryKeyFld = strtoupper($this->primaryKeyFieldName );

		foreach($data as $row){

			if ( $row[$primaryKeyFld] == $key ){
				return $row;
			}
		}
		return null;
	}


	/** -----------------------------------------------------------------------------------------------*/
	public function saveRow(array $data) :bool {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addNotice('@@saveRow');

dump::dumpLong( $data);
		$this->tableName::doUpdateRecord($data);
		return true;
	}

}
