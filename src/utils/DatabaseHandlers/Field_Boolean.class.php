<?php

/** * ********************************************************************************************
 * Field_Boolean.class.php
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
use \php_base\Utils\HTML\HTML as HTML;

Class Field_Boolean extends Field {

	const TYPE = \PDO::PARAM_BOOL;
	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
//	public function __construct(Table $parentTableObj, string $fieldName, ?array $attribs = null) {
//		$this->parentTableObj = $parentTableObj;
//		parent::__construct($parentTableObj, $fieldName, $attribs);
//	}

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
	 * @param type $value
	 * @return string
	 */
	public function giveHTMLOutput($value): string {
		switch ($this->giveAttrib('subType')) {
			case self::SUBTYPE_YESNO:
				if ($value) {
					return 'Yes';
				} else {
					return 'No';
				}
				break;
			case self::SUBTYPE_TRUEFALSE:
				if ($value) {
					return 'True';
				} else {
					return 'False';
				}
				break;
			case self::SUBTYPE_RADIO:   // this should be a check box (
				$n = $this->giveAttribWithDefault('name', 'name');
				return HTML::Radio($n, 'nameval', 'lable', $value);
				break;
			case self::SUBTYPE_CHECKMARK:   // this should be a check mark - font windings?
			case self::SUBTYPE_CHECKBOX:
				$n = $this->giveAttribWithDefault('name', 'name');
				return HTML::CheckBox($n, 'nameval', 'lable', $value);
				break;
			default:
				if ($value) {
					return 'Y';
				} else {
					return 'N';
				}
				break;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return type
	 */
	public function givePDOType() : int {
		if (empty(self::TYPE)) {
			return \PDO::PARAM_STR;
		} else {
			return self::TYPE;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @return bool
	 */
	public function hasFilterValue($data): bool {
		return ( $data == DB_NO or $data != -1);
	}

}
