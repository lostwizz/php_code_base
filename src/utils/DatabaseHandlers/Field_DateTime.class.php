<?php

/** * ********************************************************************************************
 * Field_DateTime.class.php
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

Class Field_DateTime extends Field {

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
	 * @param type $value
	 * @return string
	 */
	public function giveHTMLOutput($value): string {
		if (!$value instanceof \DateTime) {
			$value = new \DateTime($value);
		}
		switch ($this->giveAttrib('subType')) {
			case Field::SUBTYPE_DATETIME_RFC822:
				return date_format($value, \DateTimeInterface::RFC2822);
			case Field::SUBTYPE_DATENOTIME:
				return date_format($value, 'd-M-Y');
			case Field::SUBTYPE_TIMENODATE:
				return date_format($value, 'g:i:s');
			default:
				return date_format($value, 'd-M-Y g:i:s');
		}
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

}
