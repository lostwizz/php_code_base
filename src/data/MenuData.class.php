<?php

/** * ********************************************************************************************
 * MenuData.class.php
 *
 * Summary holds the menu definitions
 *
 *
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.3.1
 * $Id$
 *
 * Description.
 *     holds the menu definitions
 *
 *
 *
 * @package Utils - MenuData
 * @subpackage Utils
 * @since 0.3.1
 *
 * @example
 *
 * @todo Description
 *
 */
//**********************************************************************************************

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
	public  $Table;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	public function __construct() {
		Settings::getRunTimeObject('MENU_DEBUGGING')->addInfo('constructor for MenuData');

		$this->defineTable();

		Settings::GetRunTimeObject('MENU_DEBUGGING')->addNotice('at  Menu read data before');
		$this->doReadFromDB();
		Settings::GetRunTimeObject('MENU_DEBUGGING')->addNotice('at  Menu read data after');
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
	 * @return void
	 */
	public function defineTable(): void {
		Settings::getRunTimeObject('MENU_DEBUGGING')->addInfo('@@defineTable - menuData');
		Settings::getRunTimeObject('MENU_DEBUGGING')->Suspend();


		$this->Table = new Table(Settings::GetProtected('MenuDefinitions'), ['className' => __NAMESPACE__ . '\MenuData']);

		$this->Table->setPrimaryKey('id', ['prettyName' => 'Id']);

		$this->Table->addFieldInt('id', ['prettyName' => 'Id',
			'alignment' => 'right']);
		$this->Table->addFieldText('app', ['prettyName' => 'App',
			'isPassword' => false,
			'width' => 20
		]);
		$this->Table->addFieldInt('item_number', ['prettyName' => 'Item Number']);
		$this->Table->addFieldInt('parent_item_number', ['prettyName' => 'Parent Item Number']);
		$this->Table->addFieldText('name', ['prettyName' => 'Name']);
		$this->Table->addFieldBOOL('status', ['prettyName' => 'Status']);
		$this->Table->addFieldText('process', ['prettyName' => 'Process']);
		$this->Table->addFieldText('task', ['prettyName' => 'Task']);
		$this->Table->addFieldText('action', ['prettyName' => 'Action']);
		$this->Table->addFieldText('payload', ['prettyName' => 'Payload']);
		$this->Table->addFieldText('Role_Name_Required', ['prettyName' => 'Role Name Required']);

		$this->Table->addFieldText('Process_Permission_Required', ['prettyName' => 'Process Permission Required']);
		$this->Table->addFieldText('Task_Permission_Required', ['prettyName' => 'Task Permission Required']);
		$this->Table->addFieldText('Action_Permission_Required', ['prettyName' => 'Action Permission Required']);
		$this->Table->addFieldText('Field_Permmission_Required', ['prettyName' => 'Field Permmission Required']);
		$this->Table->addFieldText('Permission_Required', ['prettyName' => 'Permission Required']);


		Settings::getRunTimeObject('MENU_DEBUGGING')->Resume();

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
			$app = Settings::GetPublic('App Name');
			$params = array(':app' => ['val' => $app, 'type' => \PDO::PARAM_STR]);
			$data = DBUtils::doDBSelectMulti($sql, $params);
			if ($data != false) {
				$this->Menu = $data;
				if (Settings::GetPublic('CACHE Allow_Menu to be Cached')) {
					CACHE::add('MenuData', $this->Menu);
				}

//dump::dump( $this->Menu);
				return true;
			}
		}
		return false;
	}

}
