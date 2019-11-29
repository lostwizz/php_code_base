<?php

/** * ********************************************************************************************
 * Field_Text.class.php
 *
 * Summary: static class wrapper for the html code
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description: static wrapper for the html code
 *
 *
 *
 * @package Utils
 * @subpackage database
 * @since 0.3.0
 *
 * @see  https://github.com/queued/HTML-Helper/blob/master/class.html.php
 *
 * @example
 *
 *
 * @todo Description
 *
 */
//**********************************************************************************************
//***********************************************************************************************************

namespace php_base\Utils\DatabaseHandlers;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use php_base\Utils\DatabaseHandlers\Field as Field;

Class Field_Text extends Field {

	const TYPE = \PDO::PARAM_STR;

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
	public function __construct(string $fieldName, ?array $attribs = null) {
		parent::__construct($fieldName, $attribs);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $value
	 * @return string
	 */
	public function giveHTMLOutput(string $value): string {
		return $value;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return type
	 */
	public function givePDOType() {
		if (empty(self::TYPE)) {
			return \PDO::PARAM_STR;
		} else {
			return self::TYPE;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @return string
	 */
	public function validatField($data): string {
		$msg = array();
		if (empty($data)) {
			$msg[] = $this->giveAttrib('prettyName');
		}
		switch ($this->giveAttrib('subType')) {
			case SUBTYPE_PHONENUM:
				if (preg_match('/[^0-9#@\-()x ]/', $data) > 0) {
					$msg[] = $this->get_attribute('prettyName') . ': Only numbers, #-()x allowed';
				}
				break;
			case SUBTYPE_POSTALCODE:
				if (preg_match("/^[a-zA-Z][0-9][a-zA-Z] ?[0-9][a-zA-Z][0-9]$/", $data) == 0) {
					$msg[] = $this->get_attribute('prettyName') . ': Format Error';
				}

				break;
			default:
				break;
		}
	}

}
