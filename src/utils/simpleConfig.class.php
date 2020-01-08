<?php

/** * ********************************************************************************************
 * simpleConfig.class.php
 *
 * Summary simple list of config settings class
 *
 *
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.3.1
 * $Id$
 *
 * Description.
 *    - add settings and check if it has the settings
 *
 *
 * @package Utils
 * @subpackage simpleConfig
 * @since 0.2.0
 *
 * @example
 *				public static $config
 *				self::$config = new simpleConfig();
 *				self::$config->add('some setting', $aValue);
 *				self::$config->update( 'some setting', $aNewValue);
 *				self::$config->get( 'some setting');
 *				self::$config->isSet('some setting');
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\Utils;

/**
 * Description of simpleConfig
 *
 * @author merrem
 */
class simpleConfig {

	private $settings = array();


	/**
	 * @var version number
	 */
	private const VERSION = '0.3.1';

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	function __construct(){
		$settings = array();
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
	 * @param string $name
	 * @param type $value
	 * @param bool $force
	 * @return bool
	 */

	public function add( string $name, $val ) :bool{
		if ($this->isSet($name)) {
			return false;
		} else {
			$this->settings[$name] = $val;
			return $this->settings[$name] == $val;
		}
		return false;
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $name
	 * @param type $val
	 * @return bool
	 */
	public function update( string $name, $val): bool {
		$this->settings[$name] = $val;
		return $this->settings[$name] == $val;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $array
	 * @param bool $force
	 */
	public function addArray( array $array, bool $force=false) {
		foreach ($array as $key => $value) {
			$this->add( $key, $value, $force );
		}
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param array $array
	 */
	public function updateFromArray( array $array) {
		foreach ($array as $key => $value) {
			$this->update ( $key, $value);
		}
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $name
	 * @return bool
	 */
	public function remove( string $name) :bool {
		if ( $this->isSet($name)) {
			unset($this->settings[$name]);
			return ( ! $this->isSet($name));
		}
		return false;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return bool
	 */
	public function removeAll( ): bool{
		$this->settings = array();
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $name
	 * @return bool
	 */
	public function isSet(string  $name ) : bool{
		return array_key_exists($name, $this->settings );
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function get(string $name){
		if ( !empty($name) and isSet($name)) {
			return $this->settings[$name];
		} else {
			return false;
		}
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $var
	 */
	public function dumpV($var=null, string $title = '') {
		echo '<pre style="background-color: #E3FCFD">' ;
		echo $title , ': ';

		print_r ( $var);

		echo '</pre>';
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $var
	 * @param string $title
	 */
	public function dump($var=null, string $title = '') {
		echo '<pre style="background-color: #E3FCFD">' ;
		echo $title , ': ';
		print_r ( $this->settings);

		echo '</pre>';
	}
}
