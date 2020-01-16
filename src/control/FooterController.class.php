<?php

/** * ********************************************************************************************
 * FooterController.class.php
 *
 * Summary handles all the stuff at the bottom of the page
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description.
 * this class controls the info at the bottom of the page
 *
 *
 * @link URL
 *
 * @package ModelViewController - Footer
 * @subpackage Resolver
 * @since 0.3.0
 *
 * @example
 *
 *
 * @see HeaderController.class.php
 * @see FooterView.class.php
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
 * takes the input and makes a process/task/action out of it and Dispatcher executes
 *
 * Description.
 *
 * @since 0.0.2
 */
class FooterController extends Controller {

	public $process;
	public $task;
	public $action = null;
	public $payload = null;

	/**
	 * @var version number
	 */
	private const VERSION = '0.3.0';

	/** -----------------------------------------------------------------------------------------------
	 * initializes the footer view class and sets up the payload
	 *
	 * @param type $action
	 * @param type $payload
	 */
	public function __construct(string $process, string $task, string $action='', $payload = null) {
		$this->view = new \php_base\view\FooterView($this);

		$this->process = $process;
		$this->task = $task;
		$this->action= $action;
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
	 * this calls the method that actually shows the footer
	 *
	 * @return Response
	 */
	public function doWork() : Response {

		if ( $this->action =='two') {
			echo '!!!!!!!!!!!!!!!!!!!! two was here!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!';
		}

		if ( $this->action =='three') {
			echo '!!!!!!!!!!!!!!!!!!!! three was here!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!';
		}

		if ( $this->action =='four') {
			echo '!!!!!!!!!!!!!!!!!!!! four was here!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!';
		}


		$this->payload = 'hi';
		return $this->view->doWork( $this);
	}


}
