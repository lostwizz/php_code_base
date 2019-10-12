<?php
//**********************************************************************************************
//* AuthenticateView.class
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-12 09:55:00 -0700 (Thu, 12 Sep 2019) $
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


namespace whitehorse\MikesCommandAndControl2\View;


use \whitehorse\MikesCommandAndControl2\Settings\Settings as Settings;
use \whitehorse\MikesCommandAndControl2\Utils\Dump\Dump as Dump;

//***********************************************************************************************
//***********************************************************************************************
class AuthenticateView extends View{

//	var $controller;

//	//-----------------------------------------------------------------------------------------------
//	public function __construct($controller) {
//		$this->controller = $controller;
//	}

	//-----------------------------------------------------------------------------------------------
	public  function doWork(){
		return true;
	}

	//-----------------------------------------------------------------------------------------------
	public function showLoginPage(){
		echo 'This is the login page - aint it pretty?';
		echo 'uname = ', $this->controller->payload['username'];
	}

}