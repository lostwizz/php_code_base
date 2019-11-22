<?php

/** * ********************************************************************************************
 * Database.class.php
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

use php_base\Utils\DatabaseHandlers\Field as Field;

use php_base\Utils\DatabaseHandlers\Field_Int as Field_Int;
use php_base\Utils\DatabaseHandlers\Field_Text as Field_Text;
use php_base\Utils\DatabaseHandlers\Field_DateTime as Field_DateTime;



/** * **********************************************************************************************
 *
 */
class Database {

	public $Fields = array();
	public $primaryKeyFieldName;

	public function __construct() {

	}

	public function setPrimaryKey(string $fieldName) {
		$this->primaryKeyFieldName = $fieldName;
	}

	public function addFieldInt(string $fieldName) {
		$this->Fields[] = new Field_Int($fieldName);
	}

	public function addFieldText(string $fieldName) {
		$this->Fields[] = new Field_Text($fieldName);
	}

	public function addFieldDateTime(string $fieldName) {
		$this->Fields[] = new Field_DateTime($fieldName);
	}

}
