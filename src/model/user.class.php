<?php
//**********************************************************************************************
//* user.class.php
//*
//* $Id$
//* $Rev: 0000 $
//* $Date: 2019-08-30 11:58:13 -0700 (Fri, 30 Aug 2019) $
//*
//* DESCRIPTION:
//*
//* USAGE:
//*
//* HISTORY:
//* 30-Aug-19 M.Merrett - Created
//*
//* TODO:
//*
//***********************************************************************************************************
//***********************************************************************************************************

namespace php_base\model;


use \php_base\Settings\Settings as Settings;
use \php_base\Utils\Dump\Dump as Dump;



//***********************************************************************************************
//***********************************************************************************************
class User {

	protected $UserId = -1;
	protected $username ='';
	protected $app= Settings::GetPublic('App Name');
	protected $encryptedPassword ;
	protected $connectedIpAddress;

	protected $roles;
	protected $rolePermissions;
	protected $attributes;

	//-----------------------------------------------------------------------------------------------
	public function _construct( $username){
		$this->getUserDetails();
	}

	protected function getUserDetails(){
		$sql

	}


}