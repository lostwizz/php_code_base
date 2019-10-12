<?php
//**********************************************************************************************
//* AuthenticateData.class
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-09-12 10:00:21 -0700 (Thu, 12 Sep 2019) $
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


namespace whitehorse\MikesCommandAndControl2\Data;


use \whitehorse\MikesCommandAndControl2\Settings\Settings as Settings;
use \whitehorse\MikesCommandAndControl2\Utils\Dump\Dump as Dump;


//***********************************************************************************************
//***********************************************************************************************
class AuthenticateData extends Data{

	//protected $controller;

//	//-----------------------------------------------------------------------------------------------
//	public function __construct($controller) {
//		$this->controller = $controller;
//	}

	public function readUser() {

		echo ' this gets the user info from the database ';

	}

	public function updateUserIPandTime(){
		echo 'this will basically update the users ip address and login timestamp';
	}

	public function updateUserPassword(){
		echo 'this will update the users password - pemission check prior to getting here';
	}

	public function addNewUser(){

	}

	public function removeUser(){

	}

	public function addUserDetail(){

	}

	public function removeUserDetail(){

	}

	public function updateUserDetail(){

	}


}