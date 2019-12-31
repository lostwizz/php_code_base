<?php

/** * ********************************************************************************************
 * MenuController.class.php
 *
 * Summary (no period for file headers)
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description.
 * this class handles the Menus (or menu like stuff
 *
 *
 * @link URL
 *
 * @package ModelViewController - Menu
 * @subpackage Menu
 * @since 0.3.0
 *
 * @example

 *
 * @see MenuView.class.php
 * @see Response
 *
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\Control;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Control;

/** * **********************************************************************************************
 *   MenuController
 */
class MenuController extends Controller {

	public $process;
	public $task;
	public $data;
	public $action;
	public $payload;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param string $passedAction
	 * @param type $passedPayload
	 */
	public function __construct(string $passedAction = '', $passedPayload = null) {

		if (Settings::GetPublic('IS_DETAILED_MENU_DEBUGGING')) {
			Settings::setRunTime('MENU_DEBUGGING', Settings::GetRunTimeObject('MessageLog'));
		}

		$this->model = new \php_base\model\MenuModel($this);
		$this->data = new \php_base\data\MenuData($this);
		$this->view = new \php_base\view\MenuView($this);

		$this->action = $passedAction;
		$this->payload = $passedPayload;
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
	 * @param type $process
	 * @param type $task
	 */
	public function setProcessAndTask($process, $task) {
		$this->process = $process;
		$this->task = $task;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	public function doWork(): Response {

		Settings::GetRunTimeObject('MENU_DEBUGGING')->addNotice('at  Menu Controller doWork');

		$preparedMenu = $this->model->prepareMenu();

		$this->view->showMenu($preparedMenu);
		return Response::NoError();
	}



}
