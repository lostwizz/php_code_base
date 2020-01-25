<?php

/** * ********************************************************************************************
 * HTML.class.php
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
 * @package utils
 * @subpackage HTML
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

namespace php_base\Utils\HTML;

use \php_base\Utils\Dump\Dump as Dump;

/** * **********************************************************************************************
 * static class to do some html code
 */
abstract Class HTML {



	/**
	 * @var version number
	 */
	private const VERSION = '0.3.1';

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}

	//-----------------------------------------------------------------------------------------------

		/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $name
	 * @param type $value
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return string
	 */
	public static function Button(
			string $name,
			?string $value = null,
			$arOptions = null,
			$arStyle = null,
			?string $lable = null
		): ?string {
		return self::ShowInput($name, $value, 'BUTTON', $arOptions, $arStyle, $lable);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $name
	 * @param string $val
	 * @param type $lable
	 * @param type $isChecked
	 * @param boolean $arOptions
	 * @param type $arStyle
	 * @return string
	 */
	public static function CheckBox(
			string $name,
			string $value,
			?string $lable = null,
			$isChecked = false,
			$arOptions = null,
			$arStyle = null
	): ?string {
		$lable = (!empty($lable)) ? $lable : '';
		if ($isChecked) {
			$arOptions['checked'] = true;
		}
		return self::ShowInput($name, $value, "CHECKBOX", $arOptions, $arStyle, $lable);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $name
	 * @param string|null $value
	 * @param type $arOptions
	 * @param type $arStyle
	 * @param string|null $lable
	 * @return string|null
	 */
	public static function email(
			string $name,
			?string $value = null,
			$arOptions = null,
			$arStyle = null,
			?string $lable = null
	): ?string {
		return self::ShowInput($name, $value, 'eMail', $arOptions, $arStyle, $lable);
	}

	/** -----------------------------------------------------------------------------------------------
	 * return a text with a hidden input
	 * @example  output : '<input type=hidden name="' . ACTION_SYSTEM . '" value="' . INVOICING_SYSTEM . '">';
	 * @static
	 *
	 * @param string $name
	 * @param string $value
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return string
	 */
	public static function Hidden(
			string $name,
			string $value,
			$arOptions = null,
			$arStyle = null,
			?string $lable = null
	): ?string {
		return self::ShowInput($name, $value, 'HIDDEN', $arOptions, $arStyle, $lable);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $name
	 * @param type $value
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return string
	 */
	public static function Image(
			string $name,
			string $imageFile,
			?string $value = null,
			$arOptions = null,
			$arStyle = null,
			?string $lable = null
		): ?string {
		if ( !empty( $arOptions) and is_array($arOptions)){
			$arOptions = array_merge($arOptions, ['src' => $imageFile ]);//, 'alt' =>'Submit' ]);
		} else {
			$arOptions = ['src' => $imageFile ]; //, 'alt' =>'Submit' ];
		}
		return self::ShowInput($name, $value, 'IMAGE', $arOptions, $arStyle, $lable);
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a password text box
	 * @static
	 * @param type $name
	 * @param type $value
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return string
	 */
	public static function Password(
			string $name,
			?string $value = null,
			$arOptions = null,
			$arStyle = null,
			?string $lable = null
	): ?string {
		return self::ShowInput($name, $value, 'Password', $arOptions, $arStyle, $lable);
	}


	/** -----------------------------------------------------------------------------------------------
	 * gives a radio selection
	 *
	 * @static
	 * @param string $name
	 * @param string $val
	 * @param type $lable
	 * @param type $isChecked
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return string
	 */
	public static function Radio(
			string $name,
			string $value,
			?string $lable = null,
			$isChecked = false,
			$arOptions = null,
			$arStyle = null
	): ?string {
		$lable = (!empty($lable)) ? $lable : '';
		if ($isChecked) {
			$arOptions['checked'] = true;
		}
		return self::ShowInput($name, $value, "RADIO", $arOptions, $arStyle, $lable);
	}

	/** -----------------------------------------------------------------------------------------------
	 *  gives a reset button
	 *
	 * @static
	 * @param type $value
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return string
	 */
	public static function Reset(
			string $value = 'reset',
			$arOptions = null,
			$arStyle = null
	): ?string {
		return self::ShowInput('', $value, 'Reset', $arOptions, $arStyle);
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a text box for input
	 * @static
	 * @param type $name
	 * @param type $value
	 * @param type $type
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return string
	 */
	public static function ShowInput(
			string $name,
			?string $value,
			string $type = 'TEXT',
			$arOptions = null,
			$arStyle = null,
			?string $lable = null
	): ?string {
		$possibeTypes = ['CHECKBOX','RADIO','Reset', 'Password', 'Submit', 'BUTTON', 'eMail', 'TEXT', 'HIDDEN', 'IMAGE'];

		if ( in_array($type, $possibeTypes) ){
			$name = (!empty($name)) ? ' name="' . $name . '"' : '';
			$value = (!empty($value)) ? 'value="' . $value . '"' : '';
			$attr = self::parseOptions($arOptions);
			$style = self::parseStyle($arStyle);
			$value = empty($value) ? '' : ' ' . $value;

			return '<Input type="' . $type . '"' . $name . $value . $attr . $style . '>' . $lable . PHP_EOL;
		} else {
			return null;
		}
	}

	public static function Diver(
			string $name,
			?string $value,
			$arOptions = null,
			$arStyle = null
	): ?string {
		$name = (!empty($name)) ? ' name="' . $name . '"' : '';
		$value = (!empty($value)) ? $value  : '';
		$attr = self::parseOptions($arOptions);
		$style = self::parseStyle($arStyle);
		$value = empty($value) ? '' : ' ' . $value;
		return '<Div ' . $style . $attr . $name .'>' . $value . '</Div>' . PHP_EOL;
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a submit button
	 * @static
	 * @param type $name
	 * @param type $value
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return string
	 */
	public static function Submit(
			string $name,
			?string $value = null,
			$arOptions = null,
			$arStyle = null,
			?string $lable = null
	) : ?string {
		return self::ShowInput($name, $value, 'Submit', $arOptions, $arStyle, $lable);
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a text input
	 *
	 * @static
	 * @param type $name
	 * @param type $value
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return string
	 */
	public static function Text(
			string $name,
			?string $value = null,
			$arOptions = null,
			$arStyle = null,
			?string $lable = null
	): ?string {
		return self::ShowInput($name, $value, 'TEXT', $arOptions, $arStyle, $lable);
	}




	/** -----------------------------------------------------------------------------------------------
	  // $arOptions should include rows=xx and cols=yy
	 *
	 * @param string $name
	 * @param string|null $defaultText
	 * @param array $arOptions
	 * @param array $arStyle
	 * @return string
	 */
	public static function TextArea(
			string $name,
			?string $defaultText = null,
			?array $arOptions = null,
			?array $arStyle = null
	): string {
		$attr = self::parseOptions($arOptions);
		$style = self::parseStyle($arStyle);
		return '<textarea name="' . $name . '" ' . $attr . $style . '>' . $defaultText . '</textarea>';
	}

	//-----------------------------------------------------------------------------------------------
	//<select multiple name=text size=number  tabindex=number>
	//<option selected value=text>xxxxxxxxxxxx</option>
	//</select>
	//////<select name="item_id" >
	//////<option value="-1" selected>- select -</option>
	//////<option value="43">Adult - Group Pass - Employer</option>
	//////<option value="34">Adult Passes</option>
	//////<option value="31">Adult Tickets</option>
	//////<option value="36">Advertising</option>
	//////<option value="8">Advertising - GST Exempt</option>
	//////<option value="9">Charters</option>
	//////<option value="38">Contracting out</option>
	//////<option value="37">Conventional Fares</option>
	//////<option value="39">Day Pass</option>
	//////<option value="35">Disability Passes</option>
	//////<option value="24">Disabled Tickets</option>
	//////<option value="40">Group Pass-College</option>
	//////<option value="42">Group Pass-Dept of ED Adult  P</option>
	//////<option value="41">Group Pass-Dept of ED Youth Pa</option>
	//////<option value="33">Senior Passes</option>
	//////<option value="30">Senior Tickets</option>
	//////<option value="32">Youth Passes</option>
	//////<option value="29">Youth Tickets</option>
	//////</select>

	/** -----------------------------------------------------------------------------------------------
	 * gives a select drop down box
	 * @static
	 * @param string $name
	 * @param type $values
	 * @param type $defaultItemValue
	 * @param type $addDefaultSelection
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return type
	 */
	public static function Select(
			string $name,
			$values,
			$defaultItemValue = null,
			$addDefaultSelection = true,
			$arOptions = null,
			$arStyle = null) {
		$attr = self::parseOptions($arOptions);
		$style = self::parseStyle($arStyle);

		$sel = '<Select name="' . $name . '" ' . $attr . $style . '>';
		$options = self::Options($values, $defaultItemValue, $addDefaultSelection);

		return $sel . $options . '</select>' . PHP_EOL;
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives the options for a select box
	 *
	 * @static
	 * @param type $values
	 * @param type $defaultItemValue
	 * @param type $addDefaultSelection
	 * @return string
	 */
	protected static function Options($values, $defaultItemValue = null, $addDefaultSelection = true) {
		$options = '';
		if ($addDefaultSelection) {
			$options .= '<option value="-1"';
			if (empty($defaultItemValue) or $defaultItemValue == -1) {
				$options .= ' selected';
			}
			$options .= '>- Select -</option>' . PHP_EOL;
		}
		if (is_array($values)) {
			foreach ($values as $key => $val) {
				$options .= '<option value="' . $key . '"';
				if ($key == $defaultItemValue) {
					$options .= ' selected';
				}
				$options .= '>' . $val . '</option>' . PHP_EOL;
			}
		}
		return $options;
	}

	/** -----------------------------------------------------------------------------------------------
	 * Generates a HTML document type
	 *
	 * @static
	 * @access 	public
	 * @param 	string $type Type of the document
	 * @return 	string
	 */
	public static function DocType(string $type = 'html5') {
		$doctypes = array(
			'html5' => '<!DOCTYPE html>',
			'xhtml11' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
			'xhtml1-strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
			'xhtml1-trans' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
			'xhtml1-frame' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
			'html4-strict' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
			'html4-trans' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
			'html4-frame' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
		);
		if (isset($doctypes[strtolower($type)])) {
			return $doctypes[$type] . "\n";
		} else {
			return '';
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * Creates the <img /> tag
	 *
	 * @static
	 * @access 	public
	 * @param 	string $src Where is the image?
	 * @param 	mixed $attributes Custom attributes (must be a valid attribute for the <img /> tag)
	 * @return 	string The formated <img /> tag
	 */
	public static function Img($url, $arOptions = null, $arStyle = null) {
		if (empty($arOptions['border'])) {
			if (is_null($arOptions)) {
				$arOptions = array();
			}
			$arOptions['border'] = "0";
		}

		$attr = self::parseOptions($arOptions);
		$style = self::parseStyle($arStyle);

		return '<img src="' . $url . '"' . $attr . ' ' . $style . '/>';
	}

	/** -----------------------------------------------------------------------------------------------
	 * Creates a HTML Anchor link
	 *
	 * @static
	 * @access 	public
	 * @param 	string $url the URL
	 * @param 	string $label the link value
	 * @param 	mixed $attributes Custom attributes (must be a valid attribute for the <a></a> tag)
	 * @return 	string The formated <a></a> tag
	 */
	public static function Anchor($url, $lable = null, $arOptions = null, $arStyle = null) {
		$lable = (!empty($lable)) ? $lable : $url;
		$attr = self::parseOptions($arOptions);
		$style = self::parseStyle($arStyle);

		return '<a href="' . $url . '"' . $attr . $style . '>' . $lable . '</a>';
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a horizontal line
	 *
	 * @static
	 * @return string
	 */
	public static function HR($size = 1) {
		if ($size < 1 || !is_numeric($size)) {
			$size = 1;
		}
		return '<HR />';
	}

	/** -----------------------------------------------------------------------------------------------
	 * HTML <br /> tag
	 *
	 * @static
	 * @access 	public
	 * @param 	int $count How many line breaks?
	 * @return 	string
	 */
	public static function BR($count = 1) {
		if ($count < 1 || !is_numeric($count)) {
			$count = 1;
		}
		return str_repeat('<BR />', $count);
	}

	/** -----------------------------------------------------------------------------------------------
	 * Returns non-breaking space entities repeated
	 *
	 * @static
	 * @access 	public
	 * @param 	int $count How many spaces?
	 * @return 	string
	 */
	public static function Space($count = 1) {
		if ($count < 1 || !is_numeric($count)) {
			$count = 1;
		}
		return str_repeat('&nbsp;', $count);
	}

	/** -----------------------------------------------------------------------------------------------
	 * HTML::Form() -> Creates the <form> tag with the specified variables.
	 *
	 * @static
	 * @access 	public
	 * @param 	string $action The action attribute value.
	 * @param 	array $fields What is the form fields?
	 * @param 	string $name The form name
	 * @param 	string $method The form method (post or get)
	 * @param 	string $enctype The form enctype
	 */
	public static function FormOpen(string $action,
			string $name = null,
			string $method = 'POST',
			$enctype = 'multipart/form-data',
			$arOptions = null,
			$arStyle = null
	) {
		$name = (!empty($name)) ? ' name="' . $name . '"' : null;
		$method = (!empty($method)) ? ' method="' . $method . '"' : null;
		$enctype = (!empty($enctype)) ? ' enctype="' . $enctype . '"' : null;

		$attr = self::parseOptions($arOptions);
		$style = self::parseStyle($arStyle);

		$html = '<form action="' . $action . '"' . $name . $method . $enctype . $attr . $style . '>' . PHP_EOL
				. PHP_EOL;

		return $html;
	}

	/** -----------------------------------------------------------------------------------------------
	 * closes the form tag
	 *
	 * @static
	 * @return type
	 */
	public static function FormClose() {
		return ' </form>' . PHP_EOL;
	}

	/** -----------------------------------------------------------------------------------------------
	 * open a tag of some sort
	 *
	 * @static
	 * @param string $tag
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return type
	 */
	public static function Open(string $tag, $arOptions = null, $arStyle = null) {
		$attr = self::parseOptions($arOptions);
		$style = self::parseStyle($arStyle);

		return '<' . $tag . $attr . $style . '>' . PHP_EOL;
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a close tag
	 * @static
	 * @param string $tag
	 * @return type
	 */
	public static function Close(string $tag) {
		return PHP_EOL . '</' . $tag . '>' . PHP_EOL;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return type
	 */
	public static function TR($arOptions =null, $arStyle=null){
		return self::Open('TR', $arOptions, $arStyle);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return type
	 */
	public static function TRend($arOptions =null, $arStyle=null){
		return self::Close('TR');
	}

	/** ----------------------------------------------------------------------------------------------
	 *
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return type-
	 */
	public static function TD($arOptions =null, $arStyle=null){
		return self::Open('TD', $arOptions, $arStyle);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return type
	 */
	public static function TDend($arOptions =null, $arStyle=null){
		return self::Close('TD');
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $arOptions
	 * @param type $arStyle
	 * @return type
	 */
	public static function TDendTD($arOptions =null, $arStyle=null){
		return self::TDend() . self::TD($arOptions, $arStyle);
	}


	/** -----------------------------------------------------------------------------------------------
	 * HTML::Filter_XSS($str, $args) -> Filter some string with the params into $args
	 *
	 * @static
	 * @access 	public
	 * @param 	string $str String to clean the possible XSS attack.
	 * @param 	array $args The array with the parameters
	 * @return 	string The safe string.
	 */
//	public static function Filter_XSS($str, $args) {
//		/* Loop trough the args and apply the filters. */
//
//		//while(list($name, $data) = each($args)) {
//		foreach ($args as $name => $data) {
//
//			$safe = false;
//			$type = mb_substr($name, 0, 1);
//			switch ($type) {
//				case '%':
//					/* %variables: HTML tags are stripped of from the string
//					  before it's inserted. */
//					$safe = self::filter($data, 'strip');
//					break;
//				case '!':
//					/* !variables: HTML and special characters are escaped from the string
//					  before it is used. */
//					$safe = self::filter($data, 'escapeAll');
//					break;
//				case '@':
//					/* @variables: Only HTML is escaped from the string. Special characters
//					  is kept as it is. */
//					$safe = self::filter($data, 'escape');
//					break;
//				case '&':
//					/* Encode a string according to RFC 3986 for use in a URL. */
//					$safe = self::filter($data, 'url');
//					break;
//				default:
//					return null;
//					break;
//			}
//			if ($safe !== false) {
//				$str = str_replace($name, $safe, $str);
//			}
//		}
//		return $str;
//	}

	/** -----------------------------------------------------------------------------------------------
	 * ONLY FOR THIS CLASS (self)
	 * self::filter description
	 *
	 * @static
	 * @access 	private
	 * @param  	string $str The input string to filter
	 * @param  	string $mode The filter mode
	 * @return 	mixed May return the filtered string or may return null if the $mode variable isn't set
	 */
	public static function filter( $mode, $str) {
		switch ($mode) {
			case 'strip':
				/* HTML tags are stripped from the string
				  before it is used. */
				return strip_tags($str);
			case 'escapeAll':
				/* HTML and special characters are escaped from the string
				  before it is used. */
				return htmlentities($str, ENT_QUOTES, 'UTF-8');
			case 'escape':
				/* Only HTML tags are escaped from the string. Special characters
				  is kept as is. */
				return htmlspecialchars($str, ENT_NOQUOTES, 'UTF-8');
			case 'url':
				/* Encode a string according to RFC 3986 for use in a URL. */
				return rawurlencode($str);
			case 'filename':
				/* Escape a string so it's safe to be used as filename. */
				$s = str_replace('/', '-', $str);
				$s2= str_replace(':', '-', $s);
				return str_replace( DIRECTORY_SEPARATOR,'-', $s2);
			default:
				return null;
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 * takes an array (or string) and puts a style wrapper around it
	 * @static
	 * @param type $arStyle
	 * @return type
	 */
	protected static function parseStyle($arStyle = null) {
		if (is_string($arStyle)) {
			return (!empty($arStyle)) ? ' style="' . $arStyle . '"' : null;
		}
		$style = '';
		if (is_array($arStyle)) {
			if (count($arStyle) > 0) {
				foreach ($arStyle as $key => $val) {
					$style .= $key . ': ' . $val . '; ';
				}
				return ' style="' . trim($style) . '"';
			}
		}
		return null;
	}

	/** -----------------------------------------------------------------------------------------------
	 * take the options as a string or array and returns a string for inclusion in the tag(s)
	 * @static
	 * @param type $arOptions
	 * @return string
	 */
	protected static function parseOptions($arOptions = null) {
		if (is_string($arOptions)) {
			return (!empty($arOptions)) ? ' ' . trim($arOptions) : '';
		}
//dump::dumpLong($arOptions);
		$attr = '';
		if (is_array($arOptions)) {
			foreach ($arOptions as $key => $val) {
				//if (strtolower($key) == 'checked') {
				if (is_numeric($key) ){
					$attr .= ' ' .  $val;
				} else {
					$attr .= ' ' . $key . '="' . $val . '"';
				}
			}
		}
		return $attr;
	}

//	//-----------------------------------------------------------------------------------------------
//	//'<input type=hidden name="' . ACTION_SYSTEM . '" value="' . INVOICING_SYSTEM . '">';
//	//
//	protected static function parseFields( $arFields){
//		$field = '';
//		if ( is_array( $arFields)){
//			foreach($arFields as $key =>$val) {
//				$attr  = self::parseOptions( $val);
//				$field .= '<input type="' . $key . '"' . $attr . '/>' . PHP_EOL;
//			}
//		}
//		return $field;
//	}
}
