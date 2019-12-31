<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace php_base\data;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\Utils as Utils;
use \php_base\Utils\Cache AS CACHE;
use \php_base\Utils\DBUtils as DBUtils;
use \php_base\Utils\DatabaseHandlers\Table as Table;
use \php_base\Utils\DatabaseHandlers\Field as Field;

/**
 * Description of MenuData
 *
 * @author merrem
 */
class MenuData extends data {

	public $Menu;
	public static $Table;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	public function __construct() {
		self::defineTable();

		Settings::GetRunTimeObject('MENU_DEBUGGING')->addNotice( 'at  Menu read data before');
		$this->doReadFromDB();
		Settings::GetRunTimeObject('MENU_DEBUGGING')->addNotice( 'at  Menu read data after');

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
	 */
	public static function defineTable() {
		self::$Table = new Table(Settings::GetProtected('MenuDefinitions'));

		Settings::GetRunTimeObject('MessageLog')->addTODO('put in the rest of the table definitions');
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return bool
	 */
	protected function doReadFromDB(): bool {
		if (CACHE::exists('MenuData')) {
			$this->Menu = CACHE::pull('MenuData');
		} else {
			$sql = 'SELECT * '
					. ' FROM ' . Settings::GetProtected('DB_Table_MenuDefinitions')
					. ' WHERE app = :app '
					. ' ORDER BY item_number '
			;


			$app = Settings::GetPublic('App Name') ;



			$params = array(':app' => ['val' => $app, 'type' => \PDO::PARAM_STR]);
			$data = DBUtils::doDBSelectMulti($sql, $params);
			if ($data != false) {
				$this->Menu = $data;
				//CACHE::add('MenuData', $this->Menu);
				return true;
			}
		}
		return false;
	}

}
