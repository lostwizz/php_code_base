<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace php_base\control;


use \php_base\Resolver;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;
use \php_base\Utils\Settings as Settings;

use \php_base\Utils\HTML\HTML as HTML;

use \php_base\Utils\DatabaseHandlers\SimpleTableEditor as SimpleTableEditor;



/**
 * Description of dbaController
 *
 * @author merrem
 */
class dbaController extends Controller {

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
	public function __construct(string $passedAction = '', $passedPayload = null){
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
	 * this is the default method called to do anyhing (if another method is not used)
	 *
	 * @return Response
	 */
	public function doWork(): Response {
		return Response::NoError() ;
	}

	/** -----------------------------------------------------------------------------------------------
	 *  setup the process and task names so that it can be used for Permission queries
	 *
	 * @param type $process
	 * @param type $task
	 */
	public function setProcessAndTask($process =null, $task=null) {
		$this->process = $process;
		$this->task = $task;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @return type
	 */
	public function SimpleEditUserInfoData( $parent=null) {


		$x = \php_base\data\UserInfoData::$Table ;

		$editorSession = new SimpleTableEditor($x, 'DBA', 'xSimpleEditUserInfoData',$this->action, $this->payload);
		$result = $editorSession->runTableDisplayAndEdit( true );

		return Response::NoError();
	}




	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @return type
	 */
	public function two( $parent=null) {

		echo 'at dba two ';

		return Response::NoError();
	}




	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $parent
	 * @return type
	 */
	public function three( $parent=null) {

		echo 'at dba three ';

//$fldName = 'xxxxxxxxxx';

		$x =   HTML::Image(Resolver::REQUEST_PAYLOAD . '[sortAsc][' . $fldName . ']', '\static\images\A_to_Z_icon.png', 'xx', ['width'=>18] );
		dump::dump($x);

		echo  $x;

		return Response::NoError();
	}
}
