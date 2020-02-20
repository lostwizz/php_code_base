<?php

/** * ********************************************************************************************
 * settings.class.php
 *
 * Summary:static class that handles the settings for the application  -- public, protected and realtime
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description.
 * maintains 3 queues and then executes them in order -- and checks the response of the execution
 *    and may abort or continue on processing.
 *
 *
 *
 * @package ModelViewController - Dispatcher
 * @subpackage Dispatcher
 * @since 0.3.0
 *
 * @example
 *        $r = $this->dispatcher->do_work($this);
 *
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\Utils;

use \php_base\Utils\Cryption\Cryption as Cryption;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\NullAbsorber as NullAbsorber;
use \php_base\Utils\DBUtils as DBUtils;

//Dump::dump(require_once(DIR . 'utils' . DS . 'NullAbsorber.class.php'));
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
	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

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
		return isset(self::$protected[$key]) ? self::$protected[$key] : new NullAbsorber();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @return type
	 */
	public static function getPublicObject($key) {
		return isset(self::$public[$key]) ? self::$public[$key] : new NullAbsorber();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @return type
	 */
	public static function getRunTimeObject($key) {
//dump::dump(self::$runTime[$key]);
		return isset(self::$runTime[$key]) ? self::$runTime[$key] : new NullAbsorber();
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
	 * !!!note !! cannot return NullAbsober- has to return false by default
	 *          -- user getRunTimeObject instead
	 *
	 * @param type $key
	 * @return type
	 */
	public static function getRunTime($key) {
		return isset(self::$runTime[$key]) ? self::$runTime[$key] : false;
		//return isset(self::$runTime[$key]) ? self::$runTime[$key] : new NullAbsorber();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @param type $value
	 */
	public static function setProtected($key, $value) {
		self::$protected[$key] = $value;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @param type $value
	 */
	public static function setPublic($key, $value) {
		self::$public[$key] = $value;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @param type $value
	 */
	public static function setRunTime($key, $value) {
		self::$runTime[$key] = $value;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 */
	public static function unSetRunTime( $key) {
		self::$runTime[$key] = 'whyNOT';
		unset( self::$runTime[$key]);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @param type $value
	 */
	public static function setRunTimeObject($key, $value) {
		self::$runTime[$key] = $value;
	}

	/** -----------------------------------------------------------------------------------------------
	  public function __get($key) {
	  //$this->key // returns public->key
	  return isset(self::$public[$key]) ? self::$public[$key] : false;
	  }

	  /** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $key
	 * @return type
	 */
	public function __isset($key) {
		return isset(self::$public[$key]);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $value
	 * @return string
	 */
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

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $fn
	 */
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

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return string
	 */
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

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return string
	 */
	protected static function getINIProtected() {
		$encryptionClass = new Cryption();
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

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $fn
	 * @return boolean
	 */
	protected static function readINI($fn) {
		$encryptionClass = new Cryption();

		if (file_exists($fn)) {
			$a = parse_ini_file($fn, true, INI_SCANNER_RAW);
			//$b = self::decode_encrypted();
			$b = array();
			foreach ($a['PROTECTED'] as $key => $val) {
				$x = $encryptionClass->decrypt($val);
				if (substr($x, 0, 3) == '_E_') {	/// handle when string is not encrypted in the ini
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
//

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $fn
	 */
	public static function destructiveINIRestore($fn) {
		$fromINI = self::readINI($fn);
		if (is_array($fromINI)) {
			self::$public = $fromINI['PUBLIC'];
			self::$protected = $fromINI['PROTECTED'];
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $fn
	 * @param type $method
	 */
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
		$data = DBUtils::doDBSelectMulti($sql);
		return $data;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param bool $writePublic
	 * @param bool $writeProtected
	 */
	public static function dbWriteSettings(bool $writePublic = true, bool $writeProtected = false) {
		if ($writePublic) {
			self::doTableWrite(self::$public);
		}
		if ($writeProtected) {
			self::doTableWrite(self::$protected);
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $ar
	 * @param type $which
	 */
	protected static function doTableWrite(array $ar, $which = 'Public') {
		foreach ($ar as $key => $value) {
			if (self::checkExistsInDB($key)) {
				self::UpdateOne($key, $value, $which);
			} else {
				self::InsertOne($key, $value, $which);
			}
		}
		DBUtils::EndWriteOne();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	protected static function checkExistsInDB($key) {
		return false;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $k
	 * @param type $v
	 * @param type $which
	 */
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

		DBUtils::BeginWriteOne($sql);
		DBUtils::WriteOne($params);
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $v
	 * @return type
	 */
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

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $prefix  - the prefix of the settings to return
	 * @param string $class - which set of settings (public, runtime, protected)
	 * @return array
	 */
	public static function giveAllSettingsThatStartWith(string $prefix, string $class = 'PUBLIC')  :array {
		switch (strtolower($class)) {
			case 'public':
				$set = self::$public;
				break;
			case 'runtime':
				$set = self::$runTime;
				break;
			case 'protected':
				$set = self::$protected;
				break;
			default:
				break;
		}
		$result =array();

		$len = strlen($prefix);
		foreach ($set as $key => $val) {

			if (substr(strtolower($key), 0, $len) == strtolower($prefix)) {
				$result[] =  $key;
			}
		}
		return $result;
	}



	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param bool $show_protected
	 * @param bool $show_runtime
	 * @return string
	 */
	public static function dumpCR(bool $show_protected = false, bool $show_runtime = false): string {
		$s = self::dump($show_protected, $show_runtime);
		$r = str_replace('<BR>', "\r\n", $s);
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param bool $show_protected
	 * @param bool $show_runtime
	 * @return string
	 */
	public static function dumpBR(bool $show_protected = false, bool $show_runtime = false): string {
		$s = self::dump($show_protected, $show_runtime);
		$r = str_replace("\n", '<BR>', $s);
		return $r;
	}

	/** -----------------------------------------------------------------------------------------------
	 * just echo the results from this dump - it will make black on green text
	 *
	 * @param bool $showPublic
	 * @param bool $showProtected
	 * @param bool $showRuntime
	 * @return string
	 */
	public static function dump(bool $showPublic = false, bool $showProtected = false, bool $showRuntime = false): string {
		$s = '<div class="SettingsDump">';
		$s .= 'Settings::dump:<BR>';
		$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[0];
		$s .= '--'  . __METHOD__ .  '-- called from ' . $bt['file'] . '(line: '. $bt['line'] . ')' ;
		$s .= '<BR>';

		if ($showRuntime) {
			$s .= '<Br>';
			$s .= '************ Runtime Settings *********************';
			$s .= '<Br>';
			foreach (self::$runTime as $key => $val) {
				$s .= '<span class="SettingsRunTime">'
						. 'RunTime: '
						. '</span>'
						. '<span class="SettingsVar">'
						. var_export($key, true)
						. '</span>'
						. ' ==> '
						. print_r($val, true);
				$s .= '<Br>';
			}
		}
		if ($showProtected) {
			$s .= '<Br>';
			$s .= '************ Protected Settings *********************';
			$s .= '<Br>';
			foreach (self::$protected as $key => $val) {
				$s .= '<span class="SettingsProtected">'
						. 'Protected: '
						. '</span>'
						. '<span class="SettingsVar">'
						. var_export($key, true)
						. '</span>'
						. ' ==> '
						. print_r($val, true);
				$s .= '<Br>';
			}
		}
		if ($showPublic) {
			$s .= '<Br>';
			$s .= '************ Public Settings *********************';
			$s .= '<Br>';
			foreach (self::$public as $key => $val) {
				$s .= '<span class="SettingsPublic">'
						. 'Public: '
						. '</span>'
						. '<span class="SettingsVar">'
						. var_export($key, true)
						. '</span>'
						. ' ==> '
						. print_r($val, true);
				$s .= '<Br>';
			}
		}
		//$s .= '<Br>';
		$s .= '</div>';
		return $s;
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
