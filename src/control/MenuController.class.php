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

use \php_base\Resolver as Resolver;
use \php_base\Utils\SubSystemMessage as SubSystemMessage;

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
	 * @param string $passedProcess
	 * @param string $passedTask
	 * @param string $passedAction
	 * @param type $passedPayload
	 */
	public function __construct(string $passedProcess, string $passedTask, string $passedAction = '', $passedPayload = null) {

		Settings::getRunTimeObject('MENU_DEBUGGING')->addInfo('constructor for MenuController');

		$this->model = new \php_base\model\MenuModel($this);
		$this->data = new \php_base\data\MenuData($this);
		$this->view = new \php_base\view\MenuView($this);

		$this->process = $passedProcess;
		$this->task = $passedTask;

		$this->action = $passedAction;
		$this->payload = $passedPayload;
//dump::dumpLong($this);
	}

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}

//	/** -----------------------------------------------------------------------------------------------
//	 *
//	 * @param type $process
//	 * @param type $task
//	 */
//	public function setProcessAndTask($process, $task) {
//		$this->process = $process;
//		$this->task = $task;
//	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	public function doWork(): Response {

		//$perms = Settings::GetRunTime('userPermissionsController');
//dump::dump($perms);

		$isAuthenticated = Settings::GetRuntime ('isAuthenticated');
		Settings::GetRunTimeObject('MENU_DEBUGGING')->addNotice_6($isAuthenticated ? 'Authenticated': 'NOT authenticated');
		Settings::GetRunTimeObject('MENU_DEBUGGING')->addNotice_6($this->isAboutToLogoff()? 'about to logoff' : 'not about to logoff' );

		// final check of if the user is properly logged in and not about to logoff
		if ($isAuthenticated and (! $this->isAboutToLogoff()) ) {
			Settings::GetRunTimeObject('MENU_DEBUGGING')->addNotice_6('at  Menu Controller doWork');

			$preparedMenu = $this->model->prepareMenu();

			$this->view->showMenu($preparedMenu);

			return Response::NoError();
		} else {
			return Response::GenericWarning();
		}
	}


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return boolean
	 */
	public  function isAboutToLogoff(){
		if ( !empty($this->payload[Resolver::MENU_ITEM_LOGOFF]) and  $this->payload[Resolver::MENU_ITEM_LOGOFF] == 'YES' ) {
			return true;
		}
		return false;
	}


}
