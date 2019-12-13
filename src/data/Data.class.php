<?php

/** * ********************************************************************************************
 * Data.class.php
 *
 * Summary: the base class for all data classes
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
 * @package ModelViewControllerData - Data
 * @subpackage Data
 * @since 0.3.0
 *
 * @example
 * 							 class AuthenticateData extends Data{  }
 *
 * @see elementName
 *
 * @todo Description
 *
 */
//**********************************************************************************************


namespace php_base\Data;



use \php_base\Utils\Response as Response;

/** * **********************************************************************************************
 * base class for all the data readers
 */
class Data {

	public $action;
	public $payload;

	/** -----------------------------------------------------------------------------------------------
	 *  basic constructor for a data class
	 *
	 * @param type $action
	 * @param type $payload
	 */
	public function __construct($action ='', $payload = null){
		$this->action = $action;
		$this->payload = $payload;
	}

	/** -----------------------------------------------------------------------------------------------
	 *  the default method used to doanyhing (unless a method is supplied )
	 * @return Response
	 */
	public function doWork() : Response {
		return Response::GenericError();
	}
}
