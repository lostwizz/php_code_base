<?php

/** * ********************************************************************************************
 * Field.class.php
 *
 * Summary: holds the column or field of a database table
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description:
 *    holds the column/field data for a database table.

 *
 *
 * @link URL
 *
 * @package Utils
 * @subpackage database
 * @since 0.3.0
 *
 * @example
 *
 * @see Database.class.php
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\Utils\DatabaseHandlers;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;

/** * **********************************************************************************************
 *
 */
Class Field {

	public $fieldName;

	public function __construct( string $fieldName){
		$this->fieldName = $fieldName;
	}







}

