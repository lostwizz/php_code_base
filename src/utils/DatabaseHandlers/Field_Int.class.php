<?php

/** * ********************************************************************************************
 * Field_Int.class.php
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



/*
 *

  $f = self::$Table->userid;
  $f->alignment ='right';

  echo '<table border=2><tr><td>';
  echo '<pre>', $f->giveHTMLOutput(  893) , '</pre>';
  echo '</td></tr><tr><td>';
  echo '<pre>', $f->giveHTMLOutput(7893) , '</pre>';
  echo '</td></tr><tr><td>';
  echo '<pre>', $f->giveHTMLOutput(97893) , '</pre>';
  echo '</td></tr><tr><td>';
  echo '<pre>', $f->giveHTMLOutput(93) , '</pre>';
  echo '</td></tr><tr><td>';
  echo '<pre>', $f->giveHTMLOutput(3) , '</pre>';



  echo '</td></tr></table>';
 */

namespace php_base\Utils\DatabaseHandlers;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use php_base\Utils\DatabaseHandlers\Field as Field;

Class Field_Int extends Field {

	const TYPE = \PDO::PARAM_INT;

		/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
	public function __construct( Table $parentTableObj, string $fieldName, ?array $attribs = null) {
		$this->parentTableObj = $parentTableObj;
		parent::__construct($parentTableObj, $fieldName, $attribs);
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
	 * @param type $value
	 * @return string
	 */
	public function giveHTMLOutput($value = 0): string {
		$alignment = $this->giveAttribWithDefault('alignment', 'right');
		$width = $this->giveAttribWithDefault('width', 5);
		$format = '% ' . $width . 'd';

		$r = '<div style="alignment: ' . $alignment . '">';
		$r .= \sprintf($format, $value);
		$r .= '</div>';

		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return string
	 */
	public function givePDOType(): string {
		if (empty(self::TYPE)) {
			return \PDO::PARAM_STR;
		} else {
			return self::TYPE;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @return array
	 */
	public function validateField($data): array {
		$msg = array();
		if (empty($data)) {
			$msg[] = $this->giveAttrib('prettyName') . ': is Empty';
		} else {
			if (preg_match('/[^0-9\-]/', $data) > 0) {
				$msg[] = $this->get_attribute('pretty_name') . ': Only numbers allowed';
			}
			if ($this->get_attribute('sub_type') == self::SUBTYPE_INT_GREATER_THAN_ZERO
					AND $data < 0) {
				$msg[] = $this->get_attribute('pretty_name') . ': Only numbers zero or greater allowed';
			}
		}
		return $msg;
	}

}
