<?php

//**********************************************************************************************
/* settings.class.php
 *
 * static utility class that handles settings for the app -- public, protected and realtime
 *
 * @author  mike.merrett@whitehorse.ca
 * @license City of Whitehorse
 *
 * Description:
 * 		static class that handles the settings for the application  -- public, protected and realtime
 *
 *
 * @link URL
 *
 * @package utils
 * @subpackage settings
 * @since 0.3.0
 *
 * @example
 *
 * @see index.php
 * @see myNullAbsorber.class.php
 *
 * @todo Description
 *
 *
 *
 * https://www.php-fig.org/psr/
 *
 *
 */
//**********************************************************************************************

namespace php_base\Utils;

use \php_base\Utils\myCryption\myCryption as myCryption;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\myNullAbsorber as myNullAbsorber;
use \php_base\Utils\myDBUtils as myDBUtils;

//Dump::dump(require_once(DIR . 'utils' . DS . 'myNullAbsorber.class.php'));
///echo 'xxxxx"', __NAMESPACE__, '"'; // outputs "MyProject"
//
//

/** * **********************************************************************************************
 *
 */
abstract class Settings {

	static protected $protected = array(); // For DB / passwords etc
	static protected $public = array(); // For all public strings such as meta stuff for site
	static protected $runTime = array();  // for runtime vars that will realyonly exist when running

	public const INI_RESTORE_OVERWRITE = 1;

	//static public INI_RESTORE_ONLYNEW = 'n';
	//



	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @return type
	 */
	public static function hasProtectedKey($key) {
		return array_key_exists($key, self::$protected);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @return type
	 */
	public static function hasRunTimeKey($key) {
		return array_key_exists($key, self::$runTime);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @return type
	 */
	public static function hasPublicKey($key) {
		return array_key_exists($key, self::$public);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @return type
	 */
	public static function getProtectedObject($key) {
		return isset(self::$protected[$key]) ? self::$protected[$key] : new myNullAbsorber();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @return type
	 */
	public static function getPublicObject($key) {
		return isset(self::$public[$key]) ? self::$public[$key] : new myNullAbsorber();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @return type
	 */
	public static function getRunTimeObject($key) {
//dump::dump(self::$runTime[$key]);
		return isset(self::$runTime[$key]) ? self::$runTime[$key] : new myNullAbsorber();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @return type
	 */
	public static function getProtected($key) {
		return isset(self::$protected[$key]) ? self::$protected[$key] : false;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @return type
	 */
	public static function getPublic($key) {
		return isset(self::$public[$key]) ? self::$public[$key] : false;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @return type
	 */
	public static function getRunTime($key) {
		return isset(self::$runTime[$key]) ? self::$runTime[$key] : false;
	}

	//-----------------------------------------------------------------------------------------------
	public static function setProtected($key, $value) {
		self::$protected[$key] = $value;
	}

	//-----------------------------------------------------------------------------------------------
	public static function setPublic($key, $value) {
		self::$public[$key] = $value;
	}

	//-----------------------------------------------------------------------------------------------
	public static function setRunTime($key, $value) {
		self::$runTime[$key] = $value;
	}

	//-----------------------------------------------------------------------------------------------
	public function __get($key) {
		//$this->key // returns public->key
		return isset(self::$public[$key]) ? self::$public[$key] : false;
	}

	//-----------------------------------------------------------------------------------------------
	public function __isset($key) {
		return isset(self::$public[$key]);
	}

	//-----------------------------------------------------------------------------------------------
	public static function dumpCR(bool $show_protected = false, bool $show_runtime = false): string {
		$s = self::dump($show_protected, $show_runtime);
		$r = str_replace('<BR>', "\r\n", $s);
		return $r;
	}

	//-----------------------------------------------------------------------------------------------
	public static function dumpBR(bool $show_protected = false, bool $show_runtime = false): string {
		$s = self::dump($show_protected, $show_runtime);
		$r = str_replace("\n", '<BR>', $s);
		return $r;
	}

	//-----------------------------------------------------------------------------------------------
	public static function dump(bool $show_protected = false, bool $show_runtime = false): string {
		$s = 'Settings::dump:<BR>';
		if ($show_runtime) {
			foreach (self::$runTime as $key => $val) {
				$s .= 'RunTime: ' . var_export($key, true) . ' ==> ' . print_r($val, true);
				$s .= '<Br>';
			}
		}
		if ($show_protected) {
			foreach (self::$protected as $key => $val) {
				$s .= 'Protected: ' . var_export($key, true) . ' ==> ' . print_r($val, true);
				$s .= '<Br>';
			}
		}
		foreach (self::$public as $key => $val) {
			$s .= 'Public: ' . var_export($key, true) . ' ==> ' . print_r($val, true);
			$s .= '<Br>';
		}
		//$s .= '<Br>';
		return $s;
	}

	//-----------------------------------------------------------------------------------------------
	protected static function giveINISetting($value) {
		switch (gettype($value)) {
			case 'boolean':
				return $value ? '-True-' : '-False-';
			case 'NULL':
				return '-Null-';
			case 'object':
			case 'array':
				return serialize($value);
			default:
				return urlencode($value);
		}
	}

	//-----------------------------------------------------------------------------------------------
	public static function saveAsINI($fn) {
		$s = ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . "\n";
		$s .= ';;Written: ' . date(DATE_RFC822) . "\n";
		$s .= ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . "\n";

		$s .= self::getINIPublic();
		$s .= self::getINIProtected();

		$handle = fopen($fn, 'w');
		fwrite($handle, $s);
		fclose($handle);
	}

	//-----------------------------------------------------------------------------------------------
	protected static function getINIPublic() {
		$s = "\n";
		$s .= '[PUBLIC]' . "\n";
		foreach (self::$public as $key => $val) {
			//$s .= 'Public: '  . var_export( $key, true) . ' ==> ' . print_r( $val, true);
			$s .= $key . '=';
			$s .= self::giveINISetting($val);

			$s .= "\n";
			$s .= "\n";
		}
		return $s;
	}

	//-----------------------------------------------------------------------------------------------
	protected static function getINIProtected() {
		$encryptionClass = new myCryption();
		$s = "\n";
		$s .= '[PROTECTED]' . "\n";
		foreach (self::$protected as $key => $val) {
			$s .= $key . '=';

			$basicValue = self::giveINISetting($val);
			$s_encrypted = $encryptionClass->encrypt('_E_' . $basicValue);

			$s .= $s_encrypted;
			$s .= "\n";
			$s .= "\n";
		}
		return $s;
	}

	//-----------------------------------------------------------------------------------------------
	protected static function readINI($fn) {
		$encryptionClass = new myCryption();

		if (file_exists($fn)) {
			$a = parse_ini_file($fn, true, INI_SCANNER_RAW);
			//$b = self::decode_encrypted();
			$b = array();
			foreach ($a['PROTECTED'] as $key => $val) {
				$x = $encryptionClass->decrypt($val);
				if (substr($x, 0, 3) == '_E_') {			 /// handle when string is not encrypted in the ini
					$s = substr($x, 3);
				} else {
					$s = $val;
				}
				$b[$key] = $s;
			}
			$a['PROTECTED'] = $b;
			return $a;
		}
		return false;
	}

//	protected static function decode_encrypted(){
//
//	}
	//-----------------------------------------------------------------------------------------------
	public static function destructiveINIRestore($fn) {
		$fromINI = self::readINI($fn);
		if (is_array($fromINI)) {
			self::$public = $fromINI['PUBLIC'];
			self::$protected = $fromINI['PROTECTED'];
		}
	}

	//-----------------------------------------------------------------------------------------------
	public static function nonDestructiveINIRestore($fn, $method = self::INI_RESTORE_OVERWRITE) {
		$fromINI = self::readINI($fn);
		if (is_array($fromINI)) {
			foreach ($fromINI['PUBLIC'] as $key => $val) {
				if (array_key_exists($key, self::$public)) {
					if ($method == self::INI_RESTORE_OVERWRITE) {
						self::$public[$key] = $val;
					}
				} else {
					self::$public[$key] = $val;
				}
			}
			foreach ($fromINI['PROTECTED'] as $key => $val) {
				if (array_key_exists($key, self::$protected)) {
					if ($method == self::INI_RESTORE_OVERWRITE) {
						self::$protected[$key] = $val;
					}
				} else {
					self::$protected[$key] = $val;
				}
			}
		}
	}

//CREATE TABLE [dbo].[Settings](
//	[id] [bigint] NOT NULL,
//	[App] [varchar](50) NOT NULL,
//	[SettingName] [varchar](50) NULL,
//	[SettingValue] [varchar](max) NULL,
//	[SettingTypeHint] [varchar](50) NULL,
//	[Category] [varchar](50) NULL,
//	[TimeStamp] [datetime] NULL,
//	[is_active] [int] NULL,
// CONSTRAINT [PK_Settings] PRIMARY KEY CLUSTERED
//(
//	[id] ASC
//)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 100) ON [PRIMARY]
//) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
//GO
//
//ALTER TABLE [dbo].[Settings] ADD  CONSTRAINT [DF_Table_1_timestamp]  DEFAULT (getdate()) FOR [TimeStamp]
//GO
//

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param bool $updatePublic
	 * @param bool $updateProtected
	 */
	public static function dbReadAndApplySettings(bool $updatePublic = true, bool $updateProtected = true) {

//		echo '<pre>';
//		echo self::dump(true);
//		echo '</pre>';


		$data = self::doTableRead();
		self::ProcessReadSettings($data);

//		echo '<pre>';
//		echo self::dump(true);
//		echo '</pre>';
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $data
	 */
	protected static function ProcessReadSettings($data) {
		foreach ($data as $item) {
			$val = self::processReadTypeHint($item);

			switch ($item['CATEGORY']) {
				case 'Public':
					self::SetPublic($item['SETTINGNAME'], $val);
					break;
				case 'Protected':
					self::SetProtected($item['SETTINGNAME'], $val);
					break;
				default:
					break;  // do nothing
			}
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $item
	 * @return boolean
	 */
	protected static function processReadTypeHint($item) {
		switch (strtolower($item['SETTINGTYPEHINT'])) {
			case 'bool':
				if ($item['SETTINGVALUE'] == '-True-') {
					return true;
				} else {
					return false;
				}
			case 'array':
				return unserialize($item['SETTINGVALUE']);
			case 'string':
			default:
				return $item['SETTINGVALUE'];
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return type
	 */
	protected static function doTableRead() {
		$sql = 'SELECT id, app, SettingName, SettingValue, SettingTypeHint, Category, TimeStamp, is_active'
				  . ' FROM ' . Settings::GetProtected('DB_Table_Settings')
				  . " WHERE App = '" . Settings::GetPublic('App Name') . "'"
				  . " AND is_active = '1' "
		;
		$data = myDBUtils::doDBSelectMulti($sql);
		return $data;
	}

	//-----------------------------------------------------------------------------------------------
	public static function dbWriteSettings(bool $writePublic = true, bool $writeProtected = false) {
		if ($writePublic) {
			self::doTableWrite(self::$public);
		}
		if ($writeProtected) {
			self::doTableWrite(self::$protected);
		}
	}

	//-----------------------------------------------------------------------------------------------
	protected static function doTableWrite(array $ar, $which = 'Public') {
		foreach ($ar as $key => $value) {
			if (self::checkExistsInDB($key)) {
				self::UpdateOne($key, $value, $which);
			} else {
				self::InsertOne($key, $value, $which);
			}
		}
		myDBUtils::EndWriteOne();
	}

	//-----------------------------------------------------------------------------------------------
	protected static function checkExistsInDB($key) {
		return false;
	}

	//-----------------------------------------------------------------------------------------------
	protected static function InsertOne($k, $v, $which) {
		$sql = 'INSERT INTO ' . Settings::getProtected('DB_Table_Settings')
				  . ' (App, SettingName, SettingValue, SettingTypeHint, Category, is_active ) '
				  . ' Values '
				  . '( :app, :name, :value :hint, :cat, :active )'
		;

		$valueAndHints = self::processWriteTypeHint($v);
		$params = [':app' => Settings::GetPublic('App Name'),
			 ':name' => $k,
			 ':value' => $valueAndHints[0],
			 ':hint' => $valueAndHints[1],
			 ':cat' => $which,
			 ':active' => 1
		];

		myDBUtils::BeginWriteOne($sql);
		myDBUtils::WriteOne($params);
	}

	//-----------------------------------------------------------------------------------------------
	protected static function processWriteTypeHint($v) {
		if (is_array($v)) {
			return array('array', \serialize($v));
		}
		if (is_bool($v)) {
			$b = $v ? '-True-' : '-False-';
			return array('bool', $b);
		}
		return array('string', $v);
	}

}

//////	//-----------------------------------------------------------------------------------------------
//////	public static function  onlyKeysINIRestore($fn, $listOfKeys,  $method =self::INI_RESTORE_OVERWRITE){
//////		if (is_array($listOfKeys)) {
//////			$fromINI = self::readINI($fn);
//////				if ( is_array($fromINI)){
//////					foreach( $listOfKeys as $typeAndKey){
//////						list( $ty, $key) = $typeAndKey;
//////						switch (ty){
//////							case 'Protected':
//////								//if ()
//////								break;
//////							case 'Public':
//////							default:
//////
//////								break;
//////						}
//////					}
//////			}
//////		}
//////	}







////////
////////Class SettingValue{
////////	public $key;
////////	public $value;
////////	public $isSaveable;
////////
////////	private function __construct(){
////////		$this->key =null;
////////		$this->value = null;
////////		$this->isSavable = false;
////////	}
////////
////////	public function __set($value, $)
////////
////////	    public function __get($key){
////////    	//$this->key // returns public->key
////////        return isset(self::$public[$key]) ? self::$public[$key] : false;
////////    }
////////
////////	//-----------------------------------------------------------------------------------------------
////////    public function __isset($key) {
////////        return isset(self::$public[$key]);
////////    }
////////
////////}
