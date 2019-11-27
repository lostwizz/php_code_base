<?php

/** * ********************************************************************************************
 * view.class.php
 *
 * Summary: the base class for all view classes
 *
 * @author mike.merrett@whitehorse.ca
 * @version 0.5.0
 * $Id$
 *
 * Description: base class for all the view (or output)
 *
 *
 * @link URL
 *
 * @package ModelViewControllerData - View
 * @subpackage View
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

namespace php_base\View;

use \php_base\Utils\Response as Response;

/** * **********************************************************************************************
 * base class for all the views - which handle the output (screen or printer or pdf etc. (not database output))
 */
abstract class View {

	public $controller;

	/** -----------------------------------------------------------------------------------------------
	 *
	 * @param type $controller
	 */
	public function __construct($controller) {
		$this->controller = $controller;
	}

	/** -----------------------------------------------------------------------------------------------
	 *
	 */
	abstract public function doWork($parent = null);
}
