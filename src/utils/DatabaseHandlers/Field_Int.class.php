<?php

namespace php_base\Utils\DatabaseHandlers;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;


use php_base\Utils\DatabaseHandlers\Field as Field;



Class Field_Int extends Field {
	public function __construct(string $fieldName){
		parent::__construct($fieldName);
	}

}
