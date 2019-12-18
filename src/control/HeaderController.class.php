<?php

/** * ********************************************************************************************
 * HeaderController.class.php
 *
 * Summary handles all the stuff at the top of the page - basically everything before the menu
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description.
 * this class controls the info at the top of the page (title, version, logo etc)
 *
 *
 * @link URL
 *
 * @package ModelViewController - Header
 * @subpackage Resolver
 * @since 0.3.0
 *
 * @example
 *
 *
 * @see HeaderController.class.php
 * @see HeaderView.class.php
 * @see response.class.php
 *
 * @todo Description
 *
 */
//**********************************************************************************************


namespace php_base\Control;


use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;


/** * **********************************************************************************************
 * HeaderController mvc - handle setting up for putting a header at the top of the page
 *
 */
class HeaderController extends Controller {

	public $process;
	public $task;

	public $action;
	public $payload;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';


	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $action
	 * @param type $payload
	 */
	public function __construct($action='', $payload = null) {
		//$this->model = new \php_base\model\HeaderModel($this);
		//$this->data = new \php_base\data\HeaderData($this);
		$this->view = new \php_base\view\HeaderView($this);

		$this->payload = $payload;
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
	 *  set the process and task
	 * @param type $process
	 * @param type $task
	 */
	public function setProcessAndTask( $process, $task){
		$this->process = $process;
		$this->task = $task;
	}



	/** -----------------------------------------------------------------------------------------------
	 * this calls the method that actually shows the footer
	 *
	 * @return Response
	 */
	public function doWork() : Response {
		return $this->view->doWork(  $this  );
	}


}