<?php

/** * ********************************************************************************************
 * AuthenticateData.class.php
 *
 * Summary: reads the userid and password data from the database or possibly file
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description:
 * Reads the userid and password from something - DB or file.
 *
 *
 * @link URL
 *
 * @package ModelViewController - Authenticate
 * @subpackage Authenticate
 * @since 0.3.0
 *
 * @example
 *
 * @see AuthenticateController.class.php
 * @see AuthenticateModel.class.php
 * @see AuthenticateView.class.php
 *
 * @todo Description
 *
 */
//**********************************************************************************************

namespace php_base\Data;

use \php_base\Utils\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;
use \php_base\Utils\Response as Response;

/** * **********************************************************************************************
 * reads the Authentication data from db or file
 */
class AuthenticateData extends Data {

	public $action;
	public $payload;

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $action
	 * @param type $payload
	 */
	public function __construct($action = '', $payload = null) {
		$this->action = $action;
		$this->payload = $payload;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @return Response
	 */
	public function doWork(): Response {

	}
//
//	public function readUser() {
//
//		echo ' this gets the user info from the database ';
//	}
//
//	public function updateUserIPandTime() {
//		echo 'this will basically update the users ip address and login timestamp';
//	}
//
//	public function updateUserPassword() {
//		echo 'this will update the users password - pemission check prior to getting here';
//	}
//
//	public function addNewUser() {
//
//	}
//
//	public function removeUser() {
//
//	}
//
//	public function addUserDetail() {
//
//	}
//
//	public function removeUserDetail() {
//
//	}
//
//	public function updateUserDetail() {
//
//	}

}
