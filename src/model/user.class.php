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

namespace whitehorse\MikesCommandAndControl2\model;


use \whitehorse\MikesCommandAndControl2\Settings\Settings as Settings;
use \whitehorse\MikesCommandAndControl2\Utils\Dump\Dump as Dump;



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