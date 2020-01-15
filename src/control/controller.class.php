<?php

/** * ********************************************************************************************
 * Controller.class.php
 *
 * Summary: the base class for all controlllers
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description: base class for all the controllers
 *
 *
 * @link URL
 *
 * @package ModelViewController - Controllers
 * @subpackage Controllers
 * @since 0.3.0
 *
 * @example
 * 							 class AuthenticateData extends Controller{  }
 *
 * @see elementName
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\Control;

use php_base\Model;
use php_base\Data;
use php_base\View;
use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

/** * **********************************************************************************************
 * base class for the controllers
 */
abstract class Controller {

	public $model;
	public $view;
	public $data;
	public $process;
	public $task;
	public $action;
	public $payload;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 * basic form of the constructor
	 */
	abstract public function __construct( string $passedProcess, string $passedTask, string $passedAction = '', $passedPayload = null);

	/** -----------------------------------------------------------------------------------------------
	 * gives a version number
	 * @static
	 * @return type
	 */
	public static function Version() {
		return self::VERSION;
	}


	/** -----------------------------------------------------------------------------------------------
	 * this is the default method called to do anyhing (if another method is not used)
	 *
	 * @return Response
	 */
	public function doWork(): Response {
		return Response::GenericError();
	}

	/** -----------------------------------------------------------------------------------------------
	 *  setup the process and task names so that it can be used for Permission queries
	 *
	 * @param type $process
	 * @param type $task
	 */
	public function setProcessAndTask($process, $task) {
		$this->process = $process;
		$this->task = $task;
	}

}
