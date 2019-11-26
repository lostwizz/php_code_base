<?php

/** * ********************************************************************************************
 * Field_Float.class.php
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
  self::$Table->addFieldFloat('test', ['width'=> 10]);
  $f = self::$Table->test;
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

Class Field_Float extends Field {

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
	public function giveHTMLOutput($value = 0.00): string {
		$alignment = $this->giveAttribWithDefault('alignment', 'right');

		$width = $this->giveAttribWithDefault('width', 5);
		$decimals = $this->giveAttribWithDefault('decimals', 2);

		switch ($this->giveAttrib('subType')) {
			case self::SUBTYPE_MONEY:
				$format = '$% ' . $width . '.' . $decimals . 'f';
				break;
			default:
				$format = '% ' . $width . '.' . $decimals . 'f';
				break;
		}

		$r = '<div style="alignment: ' . $alignment . '">';
		$r .= \sprintf($format, $value);
		$r .= '</div>';

		return $r;
	}

}
