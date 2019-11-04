<?php

//**********************************************************************************************
//* HTML.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-12 09:46:20 -0700 (Thu, 12 Sep 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 12-Sep-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************
//  from:
/// https://github.com/queued/HTML-Helper/blob/master/class.html.php

namespace php_base\Utils\HTML;

use \php_base\Utils\Dump\Dump as Dump;

abstract Class HTML {

   private const VERSION = '0.1.0';

   //-----------------------------------------------------------------------------------------------
   public static function Version() {
      return self::VERSION;
   }

   //-----------------------------------------------------------------------------------------------
   //<tr class="logon_tr"><td class="logon_td_input">User Name: </td><td class="logon_td_input"><input type="TEXT" name="username" class="logon_username_input" value=""></td><td class="logon_td_input"></td></tr><tr class="logon_tr"><td class="logon_td_input">Password: </td><td class="logon_td_input"><input type="password" name="password" class="logon_password_input" value=""></td><td class="logon_td_input"></td></tr>					</table>
//	public static function TextBox($name, $lable=null,  $width= 50, $usingTD=true, $arOptions=null, $arStyle=null ){
//		$attr = self::parseOptions( $arOptions);
//		$style = self::parseStyle($arStyle);
//
//		$out  = '<td>';
//		$out .= $lable;
//		$out .= '</td><td>';
//		$out .=
//
//
//
//
//	}
   //-----------------------------------------------------------------------------------------------
   //'<input type=hidden name="' . ACTION_SYSTEM . '" value="' . INVOICING_SYSTEM . '">';
   public static function Hidden(string $name, string $value, $arOptions = null, $arStyle = null) {
      $n = (!empty($name)) ? ' name="' . $name . '"' : '';
      $v = (!empty($value)) ? ' value="' . $value . '"' : '';
      return self::ShowInput($n, $v, 'HIDDEN', $arOptions, $arStyle);
   }

   //-----------------------------------------------------------------------------------------------
   //<input type="TEXT" name="username" class="logon_username_input" value="">
   public static function Text($name, $value = null, $arOptions = null, $arStyle = null) {

      $name = (!empty($name)) ? ' name="' . $name . '"' : '';
      $value = (!empty($value)) ? ' value="' . $value . '"' : '';
      return self::ShowInput($name, $value, 'TEXT', $arOptions, $arStyle);
   }

   //-----------------------------------------------------------------------------------------------
//			<input type="submit" name="logon_action" value="Submit Logon" class="logon_submit_button">
//							</td>
//							<td class="logon_buttons_td">
//								<input type="reset" value="Reset" class="logon_reset_button">
//							</td>
//							<td class="logon_buttons_td">
   public static function Submit($name, $value = null, $arOptions = null, $arStyle = null) {
      $name = (!empty($name)) ? ' name="' . $name . '"' : '';
      $value = (!empty($value)) ? ' value="' . $value . '"' : '';
      return self::ShowInput($name, $value, 'Submit', $arOptions, $arStyle);
   }

   //-----------------------------------------------------------------------------------------------
   public static function Password($name, $value = null, $arOptions = null, $arStyle = null) {
      $name = (!empty($name)) ? ' name="' . $name . '"' : '';
      $value = (!empty($value)) ? ' value="' . $value . '"' : '';
      return self::ShowInput($name, $value, 'Password', $arOptions, $arStyle);
   }

   //-----------------------------------------------------------------------------------------------
   public static function Reset($value = 'reset', $arOptions = null, $arStyle = null) {
      $value = (!empty($value)) ? ' value="' . $value . '"' : '';
      return self::ShowInput('', $value, 'Reset', $arOptions, $arStyle);
   }

   //-----------------------------------------------------------------------------------------------
   protected static function ShowInput($name, $value, $type = 'TEXT', $arOptions = null, $arStyle = null) {
      $attr = self::parseOptions($arOptions);
      $style = self::parseStyle($arStyle);

      return '<Input type="' . $type . '"' . $name . $value . $attr . $style . ' >'; //. PHP_EOL;
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
   public static function Select(string $name,
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

   //-----------------------------------------------------------------------------------------------
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
            if ($key === $defaultItemValue) {
               $options .= ' selected';
            }
            $options .= '>' . $val . '</option>' . PHP_EOL;
         }
      }
      return $options;
   }

   //-----------------------------------------------------------------------------------------------
   public static function Radio(string $name,
           string $val,
           $lable = null,
           $isChecked = false,
           $arOptions = null,
           $arStyle = null) {
      $lable = (!empty($lable)) ? $lable : '';
      $attr = self::parseOptions($arOptions);
      $style = self::parseStyle($arStyle);

      $r = '<Input type="radio" name="' . $name . '" value="' . $val . '"';
      $r .= ($isChecked) ? ' checked' : '';
      return $r . $attr . $style . '/>' . $lable;
   }

   //-----------------------------------------------------------------------------------------------

   /**
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

   //-----------------------------------------------------------------------------------------------

   /**
    * Creates the <img /> tag
    *
    * @static
    * @access 	public
    * @param 	string $src Where is the image?
    * @param 	mixed $attributes Custom attributes (must be a valid attribute for the <img /> tag)
    * @return 	string The formated <img /> tag
    */
   public static function Image($url, $arOptions = null, $arStyle = null) {
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

   //-----------------------------------------------------------------------------------------------

   /**
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

   //-----------------------------------------------------------------------------------------------

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return string
	 */
	public static function HR($size =1) {
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

   //-----------------------------------------------------------------------------------------------

   /**
    * Returns non-breaking space entities
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

   //-----------------------------------------------------------------------------------------------

   /**
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

   //-----------------------------------------------------------------------------------------------
   public static function FormClose() {
      return ' </form>' . PHP_EOL;
   }

   //-----------------------------------------------------------------------------------------------
   public static function Open(string $tag, $arOptions = null, $arStyle = null) {
      $attr = self::parseOptions($arOptions);
      $style = self::parseStyle($arStyle);

      return '<' . $tag . $attr . $style . '>' . PHP_EOL;
   }

   //-----------------------------------------------------------------------------------------------
   public static function Close(string $tag) {
      return PHP_EOL . '</' . $tag . '>' . PHP_EOL;
   }

   //-----------------------------------------------------------------------------------------------

   /**
    * HTML::Filter_XSS($str, $args) -> Filter some string with the params into $args
    *
    * @static
    * @access 	public
    * @param 	string $str String to clean the possible XSS attack.
    * @param 	array $args The array with the parameters
    * @return 	string The safe string.
    */
   public static function Filter_XSS($str, $args) {
      /* Loop trough the args and apply the filters. */

      //while(list($name, $data) = each($args)) {
      foreach ($args as $name => $data) {

         $safe = false;
         $type = mb_substr($name, 0, 1);
         switch ($type) {
            case '%':
               /* %variables: HTML tags are stripped of from the string
                 before it's inserted. */
               $safe = self::filter($data, 'strip');
               break;
            case '!':
               /* !variables: HTML and special characters are escaped from the string
                 before it is used. */
               $safe = self::filter($data, 'escapeAll');
               break;
            case '@':
               /* @variables: Only HTML is escaped from the string. Special characters
                 is kept as it is. */
               $safe = self::filter($data, 'escape');
               break;
            case '&':
               /* Encode a string according to RFC 3986 for use in a URL. */
               $safe = self::filter($data, 'url');
               break;
            default:
               return null;
               break;
         }
         if ($safe !== false) {
            $str = str_replace($name, $safe, $str);
         }
      }
      return $str;
   }

   //-----------------------------------------------------------------------------------------------

   /**
    * ONLY FOR THIS CLASS (self)
    * self::filter description
    *
    * @static
    * @access 	private
    * @param  	string $str The input string to filter
    * @param  	string $mode The filter mode
    * @return 	mixed May return the filtered string or may return null if the $mode variable isn't set
    */
   protected static function filter($str, $mode) {
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
            return str_replace('/', '-', $str);
         default:
            return null;
      }
   }

   //-----------------------------------------------------------------------------------------------
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
            return ' style="' . $style . '"';
         }
      }
      return null;
   }

   //-----------------------------------------------------------------------------------------------
   protected static function parseOptions($arOptions = null) {
      if (is_string($arOptions)) {
         return (!empty($arOptions)) ? ' ' . trim($arOptions) : '';
      }

      $attr = '';
      if (is_array($arOptions)) {
         foreach ($arOptions as $key => $val) {
            $attr .= ' ' . $key . '="' . $val . '"';
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
