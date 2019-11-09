<?php

/** * ********************************************************************************************
 * model.class.php
 *
 * Summary: the base class for all model classes
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
 * @package ModelViewControllerData - model
 * @subpackage Model
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


namespace php_base\Model;


use \php_base\Utils\Response as Response;

/** * **********************************************************************************************
 * base class for all the logic models
 */
class Model {
   	public $controller;


	/** -----------------------------------------------------------------------------------------------
		 * basic constructor - we track where the controller is because it has the links to the data and view classes
		 * @param type $controller
		 */
	public function __construct($controller) {
		$this->controller = $controller;
	}


	/** -----------------------------------------------------------------------------------------------
	 *  basic default method called by the dispatcher
	 * @return Response
	 */
	public function doWork() : Response {
		return Response::GenericError();
	}

}
