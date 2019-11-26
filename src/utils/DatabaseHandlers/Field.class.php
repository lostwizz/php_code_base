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
use \php_base\Utils\HTML\HTML as HTML;

/*  example of options
  //
  //		'street' => provide_field_class(
  array( 'type' =>  myFieldClass::FLD_TYPE_SELECT,
  'sub_type' => myFieldClass::FLD_SUBTYPE_STRING,
  'selection' => &$_SESSION['db_tables']['CACHE']['Data_Table_Claims_Address'],
  'selection_key' => 'street_name',
  'selection_desc' => 'street_name',
  'selection_build_class' => 'Data_Table_Claims_Address',
  'selection_build_method' =>'setup_Cache',
  'bulk_group' => 'A',
  'bulk_entry_emptyness' => myFieldClass::FLD_EMPTYNESS_NEVER,
  'length' => 255,
  'pretty_name' => 'Street Name',
  'is_key' =>  false,
  'is_password' => false,
  'is_showable' => true,
  'is_editable' => true,
  'width' => 50,
  'pdf_width' => -1,
  'alignment' => 'left',
  'height' => true,
  'is_wrapable' =>false
  )),
 */

/** * **********************************************************************************************
 *
 */
Class Field {

	const SUBTYPE_TEXTAREA = 'TEXTAREA';
	const SUBTYPE_TEXT = 'TEXT';
	const SUBTYPE_PASSWORD = 'PASSWORD';
	const SUBTYPE_HIDDEN = 'HIDDEN';
	const SUBTYPE_SELECTLIST = 'SELECT';
	const SUBTYPE_PHONENUM = 'PHONENUM';
	const SUBTYPE_MONEY = 'MONEY';
	const SUBTYPE_YESNO = 'YESNO';
	const SUBTYPE_CHECKMARK = 'CHECKMARK';
	const SUBTYPE_CHECKBOX = 'CHECKBOX';
	const SUBTYPE_TRUEFALSE = 'TRUEFALSE';
	const SUBTYPE_RADIO = 'RADIO';
	const SUBTYPE_DATENOTIME = 'DATENOTIME';
	const SUBTYPE_TIMENODATE = 'TIMENODATE';
	const SUBTYPE_DATETIME_RFC822 = 'DATETIME_RFC822';

	public $fieldName;
	public $attribs = array();
	protected $styleAttribs = ['alignment'];
	protected $optionAttribs = ['isWrapable', 'maxlength', 'size', 'rows', 'cols'];

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $fieldName
	 * @param array $attribs
	 */
	public function __construct(string $fieldName, ?array $attribs = null) {
		$this->fieldName = strtolower($fieldName);
		$this->setupDefaultAttribs();

		if (is_array($attribs) && count($attribs)) {
			$this->setAttribs($attribs);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $name
	 * @param type $value
	 * @return bool
	 */
	public function __set($name, $value): bool {
		$this->attribs[$name] = $value;
		return true;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $name
	 * @return boolean
	 */
	public function __get($name) {
		if (array_key_exists($name, $this->attribs)) {
			return $this->attribs[$name];
		} else {
			return false;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	public function setupDefaultAttribs(): void {
		$this->attribs['size'] = 30;
		$this->attribs['maxlength'] = 30;
		$this->attribs['type'] = self::SUBTYPE_TEXT;
		$this->attribs['rows'] = 3;
		$this->attribs['cols'] = 80;
		$this->attribs['isShowable'] = true;
		$this->attribs['isEditable'] = true;
		$this->attribs['decimals'] = 2;
		$this->attribs['width'] = 5;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $attribs
	 * @return void
	 */
	public function setAttribs(array $attribs): void {
		foreach ($attribs as $key => $value) {
			//echo 'key=', $key, '  value=',$value;
			$this->attribs[$key] = $value;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array|null
	 */
	public function giveHTMLstyle(): ?array {
		$r = array();
		foreach ($this->attribs as $key => $value) {
			if (array_key_exists($key, $this->styleAttribs)) {
				$r[$key] = $value;
			}
		}
		return $r;
	}

	public function giveHTMLOptions(): ?array {
		$r = array();
		foreach ($this->attribs as $key => $value) {
			if (array_key_exists($key, $this->optionAttribs)) {
				$r[$key] = $value;
			}
		}
		return $r;
	}

	public function giveAttrib(string $attribName) {
		if (array_key_exists($attribName, $this->attribs)) {
			return $this->attribs[$attribName];
		} else {
			return false;
		}
	}

	public function giveAttribWithDefault(string $attribName, $defaultValue) {
		$r = $this->giveAttrib($attribName);
		if ($r == false) {
			return $defaultValue;
		}
		return $r;
	}

	public function givePrettyName(): string{
		return $this->giveAttribWithDefault( 'prettyName',$this->fieldName );
	}






	//<Input type="TEXT"  name="ACTION_PAYLOAD[entered_username]" maxlength="30" size="30" >

	public function giveHTMLInput(string $name, string $value = ''): string {
		$arStyle = $this->giveHTMLstyle();
		$arOptions = $this->giveHTMLOptions();
		$name = ' name="' . $name . '" ';

		if ($this->giveAttrib('isEditable')) {
			if ($this->giveAttrib('subType') == self::SUBTYPE_TEXTAREA) {
				$r = HTML::TextArea($name, $value, $arOptions, $arStyle);
			} else {
				$r = HTML::ShowInput($name, $value, self::SUBTYPE_TEXT, $arOptions, $arStyle);
			}
		} else {
			return $value;
		}
		return $r;
	}

	public function giveHTMLOutput(string $value): string {
		return $value;
	}

//	public static function dump(){
//		$refl = new \ReflectionClass(__CLASS__);
//		return $refl->getConstants();
//	}
}
