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
use \php_base\Resolver as Resolver;
use \php_base\Utils\Utils;

use \php_base\Utils\DatabaseHandlers\Table as Table;

//use \php_base\Utils\SubSystemMessage as SubSystemMessage;


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
//
//Class Attributes {
//	public $name;
//	public $value;
//	public $isStyle;
//	public $isOption;
//
//	public function __construct($name, $value, $isStyle, $isOption){
//		$this->name = $name;
//		$this->value = $value;
//		$this->isStyle = $isStyle;
//		$this->isOption = $isOption;
//	}
//}

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
	const SUBTYPE_POSTALCODE = 'POSTALCODE';
	const SUBTYPE_FLAGS = 'FLAGS';
	const SUBTYPE_MONEY = 'MONEY';
	const SUBTYPE_YESNO = 'YESNO';
	const SUBTYPE_CHECKMARK = 'CHECKMARK';
	const SUBTYPE_CHECKBOX = 'CHECKBOX';
	const SUBTYPE_TRUEFALSE = 'TRUEFALSE';
	const SUBTYPE_RADIO = 'RADIO';
	const SUBTYPE_DATENOTIME = 'DATENOTIME';
	const SUBTYPE_TIMENODATE = 'TIMENODATE';
	const SUBTYPE_DATETIME_RFC822 = 'DATETIME_RFC822';
	const SUBTYPE_INT_GREATER_THAN_ZERO = 'SUBTYPE_INT_GREATER_THAN_ZERO';

	protected $parentTableObj;

	public $fieldName;
	public $attribs = array();
	protected $styleAttribs = ['alignment'];
	protected $optionAttribs = ['isWrapable', 'maxlength', 'size', 'rows', 'cols', 'visible'];

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



		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addNotice('@@field constructor: ' . $fieldName);

		$this->parentTableObj = $parentTableObj;
		$this->fieldName = strtolower($fieldName);
		$this->setupDefaultAttribs();

		if (is_array($attribs) && count($attribs)) {
			$this->setAttribs($attribs);
		}
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
	 * @param type $name
	 * @param type $value
	 * @return bool
	 */
	public function __set(string $name, $value): bool {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addDebug_5('@@__set :'. $name);
		$this->attribs[$name] = $value;
		return true;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $name
	 * @return boolean
	 */
	public function __get(string $name) {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addDebug_5('@@__get :'. $name);
		if (array_key_exists($name, $this->attribs)) {
			return $this->attribs[$name];
		} else {
			return false;
		}
	}

	/** ----------------------------------------------------------------------------------------------
	 *
	 * @return void
	 */
	public function setupDefaultAttribs(): void {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addNotice('@@setupDefaultAttribs');

		$this->attribs['size'] = 30;
		$this->attribs['maxlength'] = 30;
		$this->attribs['width'] = 5;
		$this->attribs['type'] = self::SUBTYPE_TEXT;
		$this->attribs['rows'] = 3;
		$this->attribs['cols'] = 80;
		$this->attribs['isShowable'] = true;
		$this->attribs['isEditable'] = true;
		$this->attribs['decimals'] = 2;
		$this->attribs['visible'] = true;
		$this->attribs['selectFrom'] = [];
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $attribs
	 * @return void
	 */
	public function setAttribs(array $attribs): void {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addNotice('@@setAttribs : ' . print_r($attribs, true));

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
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addDebug_5('@@giveHTMLstyle');
		$r = array();
		foreach ($this->attribs as $key => $value) {
			if (in_array($key, $this->styleAttribs)) {
				$r[$key] = $value;
			}
		}
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return array|null
	 */
	public function giveHTMLOptions(): ?array {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addDebug_5('@@giveHTMLOptions');
		$r = array();
		foreach ($this->attribs as $key => $value) {
			if (in_array($key, $this->optionAttribs)) {
				$r[$key] = $value;
			}
		}
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $attribName
	 * @return boolean
	 */
	public function giveAttrib(string $attribName) {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addDebug_5('@@giveAttrib :' . $attribName);
		if (array_key_exists($attribName, $this->attribs)) {
			return $this->attribs[$attribName];
		} else {
			return false;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $attribName
	 * @param type $defaultValue
	 * @return type
	 */
	public function giveAttribWithDefault(string $attribName, $defaultValue) {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addDebug_5('@@giveAttribWithDefault :', $attribName);
		$r = $this->giveAttrib($attribName);
		if ($r == false) {
			return $defaultValue;
		}
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return string
	 */
	public function givePrettyName(): string {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addDebug_5('@@givePrettyName');
		return $this->giveAttribWithDefault('prettyName', $this->fieldName);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $name
	 * @param string $value
	 * @return string
	 */
	public function giveHTMLInput(string $name,  $value = ''): string {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addDebug_5('@@giveHTMLInput: ' . $name);

		//Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addDebug( $name . ' isShowable: ' . ($this->isShowable?'yes':'no') . ' isEditable:'. ($this->isEditable ? 'yes':'no'));

		if ($this->isShowable) {
			$arStyle = $this->giveHTMLstyle();
			$arOptions = $this->giveHTMLOptions();
			//$name = ' name="' . $name . '" ';
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addDebug_5( $name. ' >' . Utils::array_display_compactor( $arStyle) . '<  >' . Utils::array_display_compactor( $arOptions) );

			if ($this->isEditable ) {
				switch ( $this->giveAttrib('subType') ) {
					case self::SUBTYPE_TEXTAREA:
						$r = HTML::TextArea(Resolver::REQUEST_PAYLOAD . '[' . $name . ']', $value, $arOptions, $arStyle);
						break;
					case self::SUBTYPE_SELECTLIST:
						$r = $this->giveSelectHTMLInput(Resolver::REQUEST_PAYLOAD . '[' . $name . ']', $value);
						break;
					default:
						$r = HTML::ShowInput(Resolver::REQUEST_PAYLOAD . '[' . $name . ']', $value, self::SUBTYPE_TEXT, $arOptions, $arStyle);
						break;
				}
			} else {
				$x = str_pad( $value, $this->size, ' ', STR_PAD_LEFT);
				$r = str_replace(' ', '&nbsp;', $x);
				$r .= HTML::Hidden(Resolver::REQUEST_PAYLOAD . '[' . $name . ']', $value);
				//$r = HTML::Diver( $name, $value, $arOptions, $arStyle);
				return $r;
			}
			return $r;
		} else {
			//TODO - setup hidden for those not editable
			$r = HTML::Hidden(Resolver::REQUEST_PAYLOAD . '[' . $name . ']', $value);
			return $r;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * - create a tabel for a dropdown pick list- name of table is
	 *			the attrib 'selectFrom'
	 *      the method to call to create the list is the attrib 'selectFrom'
	 *
	 * @param type $name
	 * @param type $value
	 * @return type
	 */
	public function giveSelectHTMLInput( string $name, $value){
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addDebug_5('@@giveSelectHTMLInput: ' .  $name . ' val=' . Utils::array_display_compactor($value) . '<<');
		if ( is_array($this->selectFrom )) {
			$selOptions = $this->selectFrom;
		} else {
			$class  = $this->parentTableObj->className;
			$methodName = $this->selectFrom;

			$tbl = new $class('dummyController');
			$selOptions = ($tbl)->$methodName();
		}
		$r = HTML::Select($name, $selOptions, $value, true);
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $value
	 * @return string
	 */
	public function giveHTMLOutput(string $value): string {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addNotice('@@giveHTMLOutput: '  . $value);
		return $value;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @return bool
	 */
	public function hasFilterValue($data): bool {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addNotice('@@hasFilterValue: '. print_r($data, true));
		return (!empty($data) and $data != -1);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @return string
	 */
	public function generateFilterWhereClause($data): string {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addNotice('@@generateFilterWhereClause: ' . print_r( $data, true));
		switch ($this->giveAttrib('subType')) {
			case self::SUBTYPE_FLAGS:
			case self::SUBTYPE_PHONENUM:
			case self::SUBTYPE_POSTALCODE:
				if (strncmp($data, '!', 1) == 0) {
					return $this->fieldName . " NOT LIKE '%" . substr($data, 1) . "%'";
				} else {
					return $this->fieldName . " LIKE '%" . substr($data, 1) . "%'";
				}
				break;
			default:
				return $fldName . ' = ' . $this->giveQuotedWhere($data);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $filter
	 * @return string
	 */
	public function giveFilterBox(string $filter): string {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addNotice('@@giveFilterBox: '. $filter);
		switch ($this->giveAttrib('subType')) {
			case self::SUBTYPE_FLAGS:
				$arOptions = array('size' => $this->giveAttribWithDefault('width', 10),
					'maxlength' => $this->giveAttribWithDefault('length', 15)
				);
				break;
			default:
				$arOptions = array('size' => $this->giveAttribWithDefault('width', 15),
					'maxlength' => $this->giveAttribWithDefault('length', 255)
				);
				break;
		}
		$s = HTML::Text(Resolver::REQUEST_PAYLOAD . '[filter][' . $fldName . ']', $filter);
		$s .= HTML::Open('span', 'misc_small_text');
		$s .= '(use ! for NOT IN)';
		$s .= HTML::Close('span');
		return $s;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return type
	 */
	public function givePDOType() {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addNotice('@@givePDOType ');
		if (empty(self::TYPE)) {
			return \PDO::PARAM_STR;
		} else {
			return self::TYPE;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @return type
	 */
	public function giveQuotedWhere($data) {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addNotice('@@giveQuotedWhere: '. print_r($data, true));
		return "'" . $data . "'";
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @return type
	 */
	public function giveBinding($data) {
		Settings::GetRuntimeObject( 'DBHANDLERS_DEBUGGING')->addNotice('@@giveBinding: ' . print_r($data, true));
//dump::dump($this->givePDOType());
		return $this->givePDOType();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 * @param type $fldName
	 * @return array
	 */
	public function validateField($data): array {
		Settings::GetRuntimeObject( 'DBHANDLERS_FLD_DEBUGGING')->addNotice('@@validateField: '. print_r($data, true));
		$msg = array();
		if (empty($data)) {
			$msg[] = $this->giveAttrib('prettyName') . ': is Empty';
		}
		switch ($this->giveAttrib('subType')) {
			case self::SUBTYPE_PHONENUM:
				if (preg_match('/[^0-9#@\-()x ]/', $data) > 0) {
					$msg[] = $this->giveAttribWithDefault('prettyName', $this->fieldName) . ': Only numbers, #-()x allowed';
				}
				break;
			case self::SUBTYPE_POSTALCODE:
				if (preg_match("/^[a-zA-Z][0-9][a-zA-Z] ?[0-9][a-zA-Z][0-9]$/", $data) == 0) {
					$msg[] = $this->giveAttribWithDefault('prettyName', $this->fieldName) . ': Format Error';
				}
				break;
			case self::SUBTYPE_FLAGS:
			default:
				$msg[] = $this->giveAttrib('subType');
				break;
		}
		return $msg;
	}

}
