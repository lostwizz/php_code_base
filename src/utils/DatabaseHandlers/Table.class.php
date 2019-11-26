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

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\HTML\HTML as HTML;

use php_base\Utils\DatabaseHandlers\Field as Field;
use php_base\Utils\DatabaseHandlers\Field_Int as Field_Int;
use php_base\Utils\DatabaseHandlers\Field_Text as Field_Text;
use php_base\Utils\DatabaseHandlers\Field_DateTime as Field_DateTime;

/** * **********************************************************************************************
 *
 */
class Table {

	public $tableName;
	public $fields = array();
	public $primaryKeyFieldName;

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $tableName
	 * @param array $attribs
	 */
	public function __construct(string $tableName, ?array $attribs = null) {
		$this->tableName = $tableName;

		if (is_array($attribs) && count($attribs) > 0) {
			$this->tableName->setAttribs($attribs);
		}
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
		$this->fields[$fieldName] = new Field_Float($fieldName, $attribs);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
	public function addFieldBOOL(string $fieldName, ?array $attribs = null) {
		$fieldName = strtolower($fieldName);
		$this->fields[$fieldName] = new Field_Boolean($fieldName, $attribs);
	}

	public function giveFieldNamesList() :string {
		$flds = $this->giveField();
		$s = implode(', ', $flds);
		return $s;
	}

	public function giveFields() : array {
		return array_keys($this->fields);
	}

	public function giveHeaderRow(bool $withSortButtons= false) : string{
		$s = '';
		foreach ($this->fields as $fld => $value) {
			$s .= '<th>';
			$s .= $value->givePrettyName();
			if ( $withSortButtons) {
				$s .= $this->giveSortButtons($fld);
			}
			$s .= '</th>';
		}
		return $s;
	}

	protected function giveSortButtons( $fldName ) : string  {
		$s = HTML::Button('sortAsc[' . $fldName .']', '^');
		$s .= HTML::Button('sortDesc[' . $fldName .']', 'v');
		return $s;
	}

}
