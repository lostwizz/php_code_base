<?php

//**********************************************************************************************
//* view.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-12 09:46:20 -0700 (Thu, 12 Sep 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 12-Sep-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************

namespace php_base\View;

use \php_base\Utils\Response as Response;

//***********************************************************************************************
//***********************************************************************************************
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
	abstract public function doWork($parent = null): Response;
}
